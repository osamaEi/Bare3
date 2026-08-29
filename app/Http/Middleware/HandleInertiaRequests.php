<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error'   => fn () => $request->session()->get('error'),
            ],
            'publicPaths' => fn () => \App\Models\Path::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['slug', 'title'])
                ->map(fn ($path) => [
                    'slug'  => $path->slug,
                    'title' => $path->title,
                ]),
            // إشعارات مختصرة للقائمة المنسدلة في الشريط العلوي
            'notifications' => fn () => $request->user()
                ? [
                    'items' => $request->user()->notifications()
                        ->latest()
                        ->limit(8)
                        ->get()
                        ->map(fn ($n) => [
                            'id'         => $n->id,
                            'title'      => $n->title,
                            'body'       => $n->body,
                            'type'       => $n->type,
                            'read'       => (bool) $n->read_at,
                            'created_at' => $n->created_at?->diffForHumans(),
                        ]),
                    'unread' => $request->user()->notifications()->unread()->count(),
                ]
                : null,
        ];
    }
}
