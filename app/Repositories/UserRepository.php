<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class UserRepository implements UserRepositoryInterface
{
    public function allByRole(string $role, array $filters = []): LengthAwarePaginator
    {
        return User::where('role', $role)
            ->when($filters['search'] ?? null, fn ($q, $s) =>
                $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")
            )
            ->when($filters['status'] ?? null, fn ($q, $s) =>
                $q->where('is_active', $s === 'active')
            )
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id)
    {
        return User::findOrFail($id);
    }

    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    public function update(int $id, array $data): bool
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return User::findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return User::findOrFail($id)->delete();
    }

    public function toggleStatus(int $id): bool
    {
        $user = User::findOrFail($id);
        return $user->update(['is_active' => !$user->is_active]);
    }

    public function countByRole(string $role): int
    {
        return User::where('role', $role)->count();
    }

    public function countActive(): int
    {
        return User::active()->count();
    }

    public function recentRegistrations(int $days = 30): int
    {
        return User::where('created_at', '>=', now()->subDays($days))->count();
    }
}
