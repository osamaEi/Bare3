<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Models\Setting;
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
