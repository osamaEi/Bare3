<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminSettingsController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Settings', [
            'settings' => [
                'platform_name'        => config('app.name'),
                'platform_email'       => config('mail.from.address'),
                'pass_mark_default'    => 70,
                'video_threshold'      => 80,
                'max_quiz_attempts'    => 3,
                'notification_email'   => true,
                'notification_in_app'  => true,
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'platform_name'     => 'required|string|max:100',
            'pass_mark_default' => 'integer|min:50|max:100',
            'video_threshold'   => 'integer|min:50|max:100',
            'max_quiz_attempts' => 'integer|min:1|max:10',
        ]);

        // Store in config or DB settings table as needed

        return back()->with('success', 'تم حفظ الإعدادات');
    }
}
