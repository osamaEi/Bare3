<?php

namespace App\Repositories\Contracts;

use App\Models\User;

interface ParentRepositoryInterface
{
    public function dashboard(User $parent): array;

    public function children(User $parent): array;

    public function addChild(User $parent, array $data): User;

    public function childReport(User $parent, int $childId): array;

    public function billing(User $parent): array;
}
