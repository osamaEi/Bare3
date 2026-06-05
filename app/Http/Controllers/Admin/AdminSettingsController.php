<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
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
                'platform_name' => Setting::get('platform_name', config('app.name')),
                'platform_email' => Setting::get('platform_email', config('mail.from.address')),
                'pass_mark_default' => (int) Setting::get('pass_mark_default', 70),
                'video_threshold' => (int) Setting::get('video_threshold', 80),
                'max_quiz_attempts' => (int) Setting::get('max_quiz_attempts', 3),
                'notification_email' => (bool) Setting::get('notification_email', true),
                'notification_in_app' => (bool) Setting::get('notification_in_app', true),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'platform_name' => 'required|string|max:100',
            'platform_email' => 'nullable|email',
            'pass_mark_default' => 'integer|min:50|max:100',
            'video_threshold' => 'integer|min:50|max:100',
            'max_quiz_attempts' => 'integer|min:1|max:10',
            'notification_email' => 'boolean',
            'notification_in_app' => 'boolean',
        ]);

        foreach ($data as $key => $value) {
            Setting::put($key, is_bool($value) ? ($value ? '1' : '0') : $value);
        }

        return back()->with('success', 'تم حفظ الإعدادات');
    }
}
