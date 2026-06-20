<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSubscriptionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'plan_id', 'search']);

        $subscriptions = Subscription::with(['user:id,name,email,role', 'plan:id,name,price,billing_cycle'])
            ->when($filters['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['plan_id'] ?? null, fn ($q, $p) => $q->where('plan_id', $p))
            ->when($filters['search'] ?? null, fn ($q, $s) => $q->whereHas('user', fn ($u) =>
                $u->where('name', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%")))
            ->latest('starts_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Subscription $s) => [
                'id'         => $s->id,
                'user'       => $s->user?->name,
                'email'      => $s->user?->email,
                'plan'       => $s->plan?->name,
                'price'      => $s->plan?->price,
                'cycle'      => $s->plan?->billing_cycle,
                'status'     => $s->status,
                'starts_at'  => $s->starts_at?->toDateString(),
                'ends_at'    => $s->ends_at?->toDateString(),
                'auto_renew' => $s->auto_renew,
                'days_left'  => $s->ends_at ? now()->startOfDay()->diffInDays($s->ends_at, false) : null,
            ]);

        return Inertia::render('Admin/Subscriptions', [
            'subscriptions' => $subscriptions,
            'plans'         => Plan::orderBy('price')->get(['id', 'name', 'price', 'billing_cycle']),
            'students'      => User::whereIn('role', ['student', 'parent'])->orderBy('name')->get(['id', 'name', 'role']),
            'filters'       => $filters,
            'stats'         => [
                'total'     => Subscription::count(),
                'active'    => Subscription::where('status', 'active')->count(),
                'expired'   => Subscription::where('status', 'expired')->count(),
                'cancelled' => Subscription::where('status', 'cancelled')->count(),
                'mrr'       => (float) Subscription::where('status', 'active')
                    ->join('plans', 'plans.id', '=', 'subscriptions.plan_id')
                    ->where('plans.billing_cycle', 'monthly')
                    ->sum('plans.price'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id'    => 'required|exists:users,id',
            'plan_id'    => 'required|exists:plans,id',
            'status'     => 'required|in:active,expired,cancelled,trial',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after_or_equal:starts_at',
            'auto_renew' => 'boolean',
        ]);

        Subscription::create($data);

        return back()->with('success', 'تم إنشاء الاشتراك بنجاح');
    }

    public function update(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'plan_id'    => 'required|exists:plans,id',
            'status'     => 'required|in:active,expired,cancelled,trial',
            'starts_at'  => 'required|date',
            'ends_at'    => 'required|date|after_or_equal:starts_at',
            'auto_renew' => 'boolean',
        ]);

        $subscription->update($data);

        return back()->with('success', 'تم تحديث الاشتراك');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return back()->with('success', 'تم حذف الاشتراك');
    }
}
