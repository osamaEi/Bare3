<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class AdminHomepageController extends Controller
{
    /** Merge saved content over the config defaults. */
    private function mergedContent(): array
    {
        $defaults = config('homepage');
        $saved = Setting::get('homepage_content');
        $saved = is_string($saved) ? json_decode($saved, true) : $saved;

        return is_array($saved) ? array_replace_recursive($defaults, $saved) : $defaults;
    }

    /** Turn a stored image path into a public URL (leave absolute/asset paths as-is). */
    public static function imageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        // External URL or already-public path (starts with /images, http, etc.)
        if (str_starts_with($path, 'http') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public function index(): Response
    {
        return Inertia::render('Admin/Homepage', [
            'content' => $this->mergedContent(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        // The whole content tree arrives as a JSON string; images arrive as files
        // keyed by their dot-path inside the tree (e.g. "hero.image", "features.0.image").
        $content = json_decode($request->input('content', '{}'), true);
        if (! is_array($content)) {
            $content = [];
        }

        // Start from current saved (merged with defaults) so partial edits are safe.
        $base = $this->mergedContent();
        $content = array_replace_recursive($base, $content);

        // Process uploaded images: each file input name is the dot-path target.
        foreach ($request->allFiles() as $key => $file) {
            if (! str_starts_with($key, 'image__')) {
                continue;
            }
            $dotPath = substr($key, strlen('image__'));
            $stored = $file->store('homepage', 'public');
            Arr::set($content, $dotPath, $stored);
        }

        Setting::put('homepage_content', json_encode($content, JSON_UNESCAPED_UNICODE));

        return back()->with('success', 'تم حفظ محتوى الصفحة الرئيسية');
    }
}
