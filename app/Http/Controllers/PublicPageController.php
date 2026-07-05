<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\ContactMessage;
use App\Models\Setting;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicPageController extends Controller
{
    /** Shared homepage content (merged with admin edits) for consistent header/footer. */
    private function shared(): array
    {
        $defaults = config('homepage');
        $saved = Setting::get('homepage_content');
        $saved = is_string($saved) ? json_decode($saved, true) : $saved;
        $content = is_array($saved) ? array_replace_recursive($defaults, $saved) : $defaults;

        $url = fn ($p) => \App\Http\Controllers\Admin\AdminHomepageController::imageUrl($p);
        $content['brand']['logo']  = $url($content['brand']['logo'] ?? null);
        $content['footer']['logo'] = $url($content['footer']['logo'] ?? null);

        return $content;
    }

    public function subscribe(): Response
    {
        $content = $this->shared();

        return Inertia::render('Public/Subscribe', [
            'brand'   => $content['brand'],
            'pricing' => $content['pricing'],
            'footer'  => $content['footer'],
        ]);
    }

    public function about(): Response
    {
        $content = $this->shared();

        return Inertia::render('Public/About', [
            'brand'  => $content['brand'],
            'footer' => $content['footer'],
        ]);
    }

    public function contact(): Response
    {
        $content = $this->shared();

        return Inertia::render('Public/Contact', [
            'brand'  => $content['brand'],
            'footer' => $content['footer'],
        ]);
    }

    public function blog(): Response
    {
        $content = $this->shared();

        $posts = BlogPost::published()
            ->with('category:id,name')
            ->orderByDesc('published_at')
            ->get()
            ->map(fn ($p) => [
                'title'        => $p->title,
                'slug'         => $p->slug,
                'excerpt'      => $p->excerpt,
                'category'     => $p->category?->name,
                'views'        => $p->views_count,
                'published_at' => $p->published_at?->translatedFormat('j F Y'),
            ]);

        return Inertia::render('Public/Blog', [
            'brand'  => $content['brand'],
            'footer' => $content['footer'],
            'posts'  => $posts,
        ]);
    }

    public function blogShow(string $slug): Response
    {
        $content = $this->shared();

        $post = BlogPost::published()
            ->with(['category:id,name', 'author:id,name'])
            ->where('slug', $slug)
            ->first();

        if (! $post) {
            throw new NotFoundHttpException;
        }

        $post->incrementViews();

        $related = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->orderByDesc('published_at')
            ->limit(3)
            ->get(['title', 'slug', 'excerpt'])
            ->map(fn ($p) => [
                'title'   => $p->title,
                'slug'    => $p->slug,
                'excerpt' => $p->excerpt,
            ]);

        return Inertia::render('Public/BlogPost', [
            'brand'   => $content['brand'],
            'footer'  => $content['footer'],
            'post'    => [
                'title'        => $post->title,
                'excerpt'      => $post->excerpt,
                'content'      => $post->content,
                'category'     => $post->category?->name,
                'author'       => $post->author?->name,
                'views'        => $post->views_count,
                'published_at' => $post->published_at?->translatedFormat('j F Y'),
            ],
            'related' => $related,
        ]);
    }

    public function contactStore(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:3000',
        ]);

        ContactMessage::create($data);

        return back()->with('success', 'تم إرسال رسالتك بنجاح، سنتواصل معك قريباً.');
    }
}
