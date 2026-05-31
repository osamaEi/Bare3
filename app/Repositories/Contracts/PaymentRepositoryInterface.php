<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface PaymentRepositoryInterface
{
    public function allTransactions(array $filters = []): LengthAwarePaginator;
    public function findTransaction(int $id);
    public function totalRevenue(?string $period = null): float;
    public function revenueByGateway(): array;
    public function revenueByMonth(int $months = 6): array;
    public function revenueByPlan(): array;
    public function refund(int $transactionId): bool;
    public function countByStatus(string $status): int;
}
