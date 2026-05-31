<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface BlogRepositoryInterface
{
    public function allPosts(array $filters = []): LengthAwarePaginator;
    public function findPost(int $id);
    public function findPostBySlug(string $slug);
    public function createPost(array $data, array $tagIds = []);
    public function updatePost(int $id, array $data, array $tagIds = []): bool;
    public function deletePost(int $id): bool;
    public function publishPost(int $id): bool;
    public function unpublishPost(int $id): bool;
    public function allCategories(): \Illuminate\Database\Eloquent\Collection;
    public function allTags(): \Illuminate\Database\Eloquent\Collection;
    public function latestPublished(int $limit = 5): \Illuminate\Database\Eloquent\Collection;
}
