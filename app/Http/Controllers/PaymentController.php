<?php

namespace App\Http\Controllers;

use App\Models\PaymentTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\PayTabsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function __construct(private readonly PayTabsService $paytabs) {}

    /** Checkout page — pick a plan and pay. Shared by student & parent. */
    public function checkout(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Payment/Checkout', [
            'plans' => Plan::where('is_active', true)->orderBy('price')->get()
                ->map(fn (Plan $p) => [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'price'    => (float) $p->price,
                    'currency' => $p->currency,
                    'cycle'    => $p->billing_cycle,
                    'features' => $p->features ?? [],
                ]),
            'current' => $user->activeSubscription()->with('plan')->first(),
            'gateway_ready' => $this->paytabs->isConfigured(),
            // أبناء الوالد (لو المستخدم ولي أمر) ليختار المستفيد من الاشتراك
            'children' => $user->role === 'parent'
                ? $user->children()->get(['users.id', 'name'])
                    ->map(fn ($c) => ['id' => $c->id, 'name' => $c->name])
                : [],
        ]);
    }

    /** Initiate a PayTabs hosted payment for the chosen plan. */
    public function pay(Request $request)
    {
        $data = $request->validate([
            'plan_id'  => 'required|exists:plans,id',
            'child_id' => 'nullable|exists:users,id',
        ]);
        $user = $request->user();
        $plan = Plan::findOrFail($data['plan_id']);

        if (! $this->paytabs->isConfigured()) {
            return back()->with('error', 'بوابة الدفع غير مُهيّأة حالياً. تواصل مع الإدارة.');
        }

        // المستفيد: لو الوالد اختار ابنًا من أبنائه يُفعَّل له الاشتراك، وإلا للمستخدم نفسه.
        $beneficiaryId = $user->id;
        if ($user->role === 'parent' && ! empty($data['child_id'])) {
            abort_unless($user->children()->whereKey($data['child_id'])->exists(), 403);
            $beneficiaryId = (int) $data['child_id'];
        }

        $cartId = 'SUB-'.$beneficiaryId.'-'.$plan->id.'-'.Str::upper(Str::random(6));

        // pending transaction we can reconcile against in the callback
        $tx = PaymentTransaction::create([
            'user_id'       => $user->id,
            'gateway'       => 'paytabs',
            'gateway_tx_id' => $cartId,
            'amount'        => $plan->price,
            'currency'      => $plan->currency ?: $this->paytabs->currency(),
            'status'        => 'pending',
            'payload'       => ['plan_id' => $plan->id, 'cart_id' => $cartId, 'beneficiary_id' => $beneficiaryId],
        ]);

        // PayTabs ترفض روابط localhost. أثناء التطوير المحلي ضع PAYTABS_PUBLIC_URL
        // (مثلاً رابط ngrok) في .env لبناء روابط الـ callback/return بشكل عام.
        $publicBase = rtrim((string) env('PAYTABS_PUBLIC_URL', ''), '/');
        $callbackUrl = $publicBase ? $publicBase.'/payment/callback' : route('payment.callback');
        $returnUrl   = $publicBase ? $publicBase.'/payment/return?tx='.$tx->id : route('payment.return', ['tx' => $tx->id]);

        $result = $this->paytabs->createHostedPage([
            'cart_id'     => $cartId,
            'description' => 'اشتراك في '.$plan->name,
            'amount'      => $plan->price,
            'currency'    => $plan->currency ?: $this->paytabs->currency(),
            'callback'    => $callbackUrl,
            'return'      => $returnUrl,
            'customer'    => ['name' => $user->name, 'email' => $user->email],
        ]);

        if (! $result['ok'] || ! $result['redirect_url']) {
            $tx->update(['status' => 'failed', 'payload' => array_merge($tx->payload ?? [], ['error' => $result['message']])]);

            return back()->with('error', $result['message'] ?? 'تعذّر بدء عملية الدفع');
        }

        $tx->update(['payload' => array_merge($tx->payload, ['tran_ref' => $result['tran_ref']])]);

        // Redirect the browser to the PayTabs hosted page.
        return Inertia::location($result['redirect_url']);
    }

    /** Server-to-server IPN from PayTabs — the source of truth. */
    public function callback(Request $request)
    {
        $tranRef = $request->input('tran_ref') ?? $request->input('tranRef');
        if (! $tranRef) {
            return response()->json(['ok' => false], 400);
        }

        $verify = $this->paytabs->verify($tranRef);
        $cartId = $verify['raw']['cart_id'] ?? $request->input('cart_id');

        $tx = PaymentTransaction::where('gateway_tx_id', $cartId)->first();
        if ($tx) {
            $this->finalize($tx, $verify['paid'], $tranRef, $verify['raw']);
        }

        return response()->json(['ok' => true]);
    }

    /** Browser return — verify again (in case IPN is delayed) and show result. */
    public function paymentReturn(Request $request): RedirectResponse
    {
        $tx = PaymentTransaction::find($request->query('tx'));
        if (! $tx) {
            return redirect()->route(auth()->user()->homeRoute());
        }

        // If still pending, try to reconcile now using the stored tran_ref.
        if ($tx->status === 'pending' && ! empty($tx->payload['tran_ref'])) {
            $verify = $this->paytabs->verify($tx->payload['tran_ref']);
            $this->finalize($tx, $verify['paid'], $tx->payload['tran_ref'], $verify['raw']);
            $tx->refresh();
        }

        $home = auth()->user()->homeRoute();
        $msg = $tx->status === 'success' ? 'تم الدفع بنجاح وتفعيل اشتراكك! 🎉' : 'لم تكتمل عملية الدفع.';

        return redirect()->route($home)->with($tx->status === 'success' ? 'success' : 'error', $msg);
    }

    /** Mark the transaction paid/failed and (on success) activate the subscription. */
    private function finalize(PaymentTransaction $tx, bool $paid, string $tranRef, array $raw): void
    {
        if ($tx->status === 'success') {
            return; // already processed (idempotent)
        }

        if (! $paid) {
            $tx->update(['status' => 'failed', 'payload' => array_merge($tx->payload ?? [], ['gateway_response' => $raw])]);

            return;
        }

        $planId = $tx->payload['plan_id'] ?? null;
        $plan = $planId ? Plan::find($planId) : null;
        // المستفيد من الاشتراك (الابن لو الوالد دفع له، وإلا الدافع نفسه)
        $beneficiaryId = $tx->payload['beneficiary_id'] ?? $tx->user_id;
        $beneficiary = User::find($beneficiaryId);

        $subscription = null;
        if ($plan && $beneficiary) {
            $months = $plan->billing_cycle === 'yearly' ? 12 : 1;
            $subscription = Subscription::updateOrCreate(
                ['user_id' => $beneficiary->id],
                [
                    'plan_id'    => $plan->id,
                    'status'     => 'active',
                    'starts_at'  => now(),
                    'ends_at'    => now()->addMonths($months),
                    'auto_renew' => true,
                ],
            );
        }

        $tx->update([
            'status'          => 'success',
            'subscription_id' => $subscription?->id,
            'gateway_tx_id'   => $tx->gateway_tx_id,
            'payload'         => array_merge($tx->payload ?? [], ['tran_ref' => $tranRef, 'gateway_response' => $raw]),
        ]);
    }
}
