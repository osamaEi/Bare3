<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function allByRole(string $role, array $filters = []): LengthAwarePaginator;
    public function findById(int $id);
    public function create(array $data);
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function toggleStatus(int $id): bool;
    public function countByRole(string $role): int;
    public function countActive(): int;
    public function recentRegistrations(int $days = 30): int;
}
