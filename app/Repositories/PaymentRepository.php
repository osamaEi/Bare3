<?php

namespace App\Repositories;

use App\Models\PaymentTransaction;
use App\Repositories\Contracts\PaymentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class PaymentRepository implements PaymentRepositoryInterface
{
    public function allTransactions(array $filters = []): LengthAwarePaginator
    {
        return PaymentTransaction::with('user:id,name', 'subscription.plan:id,name')
            ->when($filters['gateway'] ?? null, fn ($q, $g) => $q->where('gateway', $g))
            ->when($filters['status']  ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['search']  ?? null, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%$s%"))
            )
            ->latest()
            ->paginate($filters['per_page'] ?? 20);
    }

    public function findTransaction(int $id)
    {
        return PaymentTransaction::with('user', 'subscription.plan', 'invoice')->findOrFail($id);
    }

    public function totalRevenue(?string $period = null): float
    {
        $q = PaymentTransaction::success();

        return match ($period) {
            'month'   => (float) $q->whereMonth('created_at', now()->month)->sum('amount'),
            'year'    => (float) $q->whereYear('created_at', now()->year)->sum('amount'),
            'week'    => (float) $q->where('created_at', '>=', now()->startOfWeek())->sum('amount'),
            default   => (float) $q->sum('amount'),
        };
    }

    public function revenueByGateway(): array
    {
        return PaymentTransaction::success()
            ->selectRaw('gateway, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('gateway')
            ->get()
            ->keyBy('gateway')
            ->map(fn ($r) => ['total' => (float) $r->total, 'count' => $r->count])
            ->toArray();
    }

    public function revenueByMonth(int $months = 6): array
    {
        return PaymentTransaction::success()
            ->selectRaw("DATE_FORMAT(created_at,'%Y-%m') as month, SUM(amount) as total")
            ->where('created_at', '>=', now()->subMonths($months)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($r) => ['month' => $r->month, 'amount' => (float) $r->total])
            ->toArray();
    }

    public function revenueByPlan(): array
    {
        return PaymentTransaction::success()
            ->join('subscriptions', 'payment_transactions.subscription_id', '=', 'subscriptions.id')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->selectRaw('plans.name, SUM(payment_transactions.amount) as total')
            ->groupBy('plans.id', 'plans.name')
            ->get()
            ->map(fn ($r) => ['name' => $r->name, 'total' => (float) $r->total])
            ->toArray();
    }

    public function refund(int $transactionId): bool
    {
        return PaymentTransaction::findOrFail($transactionId)
            ->update(['status' => 'refunded', 'refunded_at' => now()]);
    }

    public function countByStatus(string $status): int
    {
        return PaymentTransaction::where('status', $status)->count();
    }
}
