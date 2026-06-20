<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class AdminPlanController extends Controller
{
    public function index(): Response
    {
        $plans = Plan::withCount('subscriptions')
            ->orderBy('price')
            ->get()
            ->map(fn (Plan $p) => [
                'id'            => $p->id,
                'name'          => $p->name,
                'slug'          => $p->slug,
                'type'          => $p->type,
                'price'         => (float) $p->price,
                'currency'      => $p->currency,
                'billing_cycle' => $p->billing_cycle,
                'features'      => $p->features ?? [],
                'is_active'     => $p->is_active,
                'subscribers'   => $p->subscriptions_count,
            ]);

        return Inertia::render('Admin/Plans', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $data['slug'] = $this->uniqueSlug($data['name']);

        Plan::create($data);

        return back()->with('success', 'تم إضافة الباقة');
    }

    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validateData($request);
        $plan->update($data);

        return back()->with('success', 'تم تحديث الباقة');
    }

    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists()) {
            return back()->with('error', 'لا يمكن حذف باقة مرتبطة باشتراكات. عطّلها بدلاً من ذلك.');
        }

        $plan->delete();

        return back()->with('success', 'تم حذف الباقة');
    }

    private function validateData(Request $request): array
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'type'          => 'required|in:individual,school',
            'price'         => 'required|numeric|min:0',
            'currency'      => 'required|string|max:10',
            'billing_cycle' => 'required|in:monthly,yearly',
            'features'      => 'array',
            'features.*'    => 'string|max:255',
            'is_active'     => 'boolean',
        ]);

        // إزالة المميزات الفارغة
        $data['features'] = array_values(array_filter($data['features'] ?? [], fn ($f) => trim($f) !== ''));

        return $data;
    }

    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'plan';
        $slug = $base;
        $i = 1;
        while (Plan::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
