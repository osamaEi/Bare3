<?php

namespace App\Repositories;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BlogRepository implements BlogRepositoryInterface
{
    public function allPosts(array $filters = []): LengthAwarePaginator
    {
        return BlogPost::with('category:id,name', 'author:id,name')
            ->when($filters['status']   ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($filters['category'] ?? null, fn ($q, $c) => $q->where('category_id', $c))
            ->when($filters['search']   ?? null, fn ($q, $s) =>
                $q->where('title', 'like', "%$s%")
            )
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findPost(int $id)
    {
        return BlogPost::with('category', 'author', 'tags')->findOrFail($id);
    }

    public function findPostBySlug(string $slug)
    {
        return BlogPost::with('category', 'author', 'tags')->where('slug', $slug)->firstOrFail();
    }

    public function createPost(array $data, array $tagIds = [])
    {
        $data['slug'] = Str::slug($data['title']) . '-' . time();
        $post = BlogPost::create($data);
        if ($tagIds) $post->tags()->sync($tagIds);
        return $post;
    }

    public function updatePost(int $id, array $data, array $tagIds = []): bool
    {
        $post = BlogPost::findOrFail($id);
        if ($tagIds) $post->tags()->sync($tagIds);
        return $post->update($data);
    }

    public function deletePost(int $id): bool
    {
        return BlogPost::findOrFail($id)->delete();
    }

    public function publishPost(int $id): bool
    {
        return BlogPost::findOrFail($id)->update([
            'status'       => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublishPost(int $id): bool
    {
        return BlogPost::findOrFail($id)->update(['status' => 'draft', 'published_at' => null]);
    }

    public function allCategories(): Collection
    {
        return BlogCategory::withCount('posts')->get();
    }

    public function allTags(): Collection
    {
        return BlogTag::withCount('posts')->get();
    }

    public function latestPublished(int $limit = 5): Collection
    {
        return BlogPost::published()->with('category:id,name', 'author:id,name')
            ->latest('published_at')
            ->limit($limit)
            ->get();
    }
}
