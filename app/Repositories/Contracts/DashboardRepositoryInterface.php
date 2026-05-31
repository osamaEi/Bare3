<?php

namespace App\Repositories\Contracts;

interface DashboardRepositoryInterface
{
    public function getStats(): array;
    public function getRevenueChart(): array;
    public function getRecentActivity(int $limit = 10): array;
    public function getPathsDistribution(): array;
    public function getGatewayBreakdown(): array;
}
