<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboard,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats'           => $this->dashboard->getStats(),
            'revenue_chart'   => $this->dashboard->getRevenueChart(),
            'recent_activity' => $this->dashboard->getRecentActivity(8),
            'paths_dist'      => $this->dashboard->getPathsDistribution(),
            'gateway_split'   => $this->dashboard->getGatewayBreakdown(),
        ]);
    }
}
