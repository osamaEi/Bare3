<?php

namespace App\Repositories;

use App\Models\Certificate;
use App\Models\Enrollment;
use App\Models\Path;
use App\Models\PaymentTransaction;
use App\Models\StudentBadge;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function getStats(): array
    {
        return [
            'total_students'  => User::students()->count(),
            'total_parents'   => User::parents()->count(),
            'total_teachers'  => User::teachers()->count(),
            'active_paths'    => Path::where('is_active', true)->count(),
            'revenue_month'   => (float) PaymentTransaction::success()
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'completions'     => Certificate::whereMonth('created_at', now()->month)->count(),
            'enrollments'     => Enrollment::whereMonth('created_at', now()->month)->count(),
            'badges_earned'   => StudentBadge::whereMonth('earned_at', now()->month)->count(),
        ];
    }

    public function getRevenueChart(): array
    {
        return PaymentTransaction::success()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(fn ($r) => [
                'month'  => $r->month,
                'amount' => (float) $r->total,
            ])
            ->toArray();
    }

    public function getRecentActivity(int $limit = 10): array
    {
        $badges = StudentBadge::with(['student:id,name', 'badge:id,name'])
            ->latest('earned_at')
            ->limit($limit)
            ->get()
            ->map(fn ($sb) => [
                'type'   => 'badge',
                'user'   => $sb->student->name,
                'action' => 'حصل على وسام ' . $sb->badge->name,
                'time'   => $sb->earned_at->diffForHumans(),
            ]);

        $registrations = User::latest()
            ->limit($limit)
            ->get()
            ->map(fn ($u) => [
                'type'   => 'register',
                'user'   => $u->name,
                'action' => 'سجّل حساباً جديداً',
                'time'   => $u->created_at->diffForHumans(),
            ]);

        $payments = PaymentTransaction::with('user:id,name')
            ->success()
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn ($tx) => [
                'type'   => 'payment',
                'user'   => $tx->user->name,
                'action' => 'اشترك عبر ' . $tx->gateway . ' بـ ' . $tx->amount . ' ر.س',
                'time'   => $tx->created_at->diffForHumans(),
            ]);

        $certs = Certificate::with('student:id,name', 'path:id,title')
            ->latest('issued_at')
            ->limit($limit)
            ->get()
            ->map(fn ($c) => [
                'type'   => 'cert',
                'user'   => $c->student->name,
                'action' => 'حمّل شهادة مسار ' . $c->path->title,
                'time'   => $c->issued_at->diffForHumans(),
            ]);

        return collect([...$badges, ...$registrations, ...$payments, ...$certs])
            ->sortByDesc('time')
            ->take($limit)
            ->values()
            ->toArray();
    }

    public function getPathsDistribution(): array
    {
        $total = Enrollment::count() ?: 1;

        return Path::withCount('enrollments')
            ->get()
            ->map(fn ($path) => [
                'name'  => $path->title,
                'color' => $path->color,
                'pct'   => round($path->enrollments_count / $total * 100),
                'count' => $path->enrollments_count,
            ])
            ->toArray();
    }

    public function getGatewayBreakdown(): array
    {
        return PaymentTransaction::success()
            ->selectRaw('gateway, SUM(amount) as total, COUNT(*) as tx_count')
            ->groupBy('gateway')
            ->get()
            ->map(fn ($r) => [
                'name'     => $r->gateway,
                'amount'   => (float) $r->total,
                'tx_count' => $r->tx_count,
            ])
            ->toArray();
    }
}
