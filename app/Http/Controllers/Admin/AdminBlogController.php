<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Blog\StorePostRequest;
use App\Http\Requests\Admin\Blog\UpdatePostRequest;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminBlogController extends Controller
{
    public function __construct(
        private readonly BlogRepositoryInterface $blog,
    ) {}

    public function index(Request $request): Response
    {
        $filters = $request->only(['status', 'category', 'search', 'per_page']);

        return Inertia::render('Admin/Blog', [
            'posts'      => $this->blog->allPosts($filters),
            'categories' => $this->blog->allCategories(),
            'tags'       => $this->blog->allTags(),
            'filters'    => $filters,
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['author_id'] = auth()->id();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $this->blog->createPost($data, $tagIds);

        return back()->with('success', 'تم حفظ المقالة');
    }

    public function update(UpdatePostRequest $request, int $id): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blog', 'public');
        }

        $tagIds = $data['tags'] ?? [];
        unset($data['tags']);

        $this->blog->updatePost($id, $data, $tagIds);

        return back()->with('success', 'تم تعديل المقالة');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->blog->deletePost($id);

        return back()->with('success', 'تم حذف المقالة');
    }

    public function publish(int $id): RedirectResponse
    {
        $this->blog->publishPost($id);

        return back()->with('success', 'تم نشر المقالة');
    }

    public function unpublish(int $id): RedirectResponse
    {
        $this->blog->unpublishPost($id);

        return back()->with('success', 'تم إلغاء نشر المقالة');
    }
}
