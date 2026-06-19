<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class AdminContactController extends Controller
{
    public function index(): Response
    {
        $messages = ContactMessage::latest()->get()->map(fn ($m) => [
            'id'         => $m->id,
            'name'       => $m->name,
            'email'      => $m->email,
            'phone'      => $m->phone,
            'subject'    => $m->subject,
            'message'    => $m->message,
            'is_read'    => $m->is_read,
            'created_at' => $m->created_at?->format('Y-m-d H:i'),
        ]);

        return Inertia::render('Admin/ContactMessages', [
            'messages' => $messages,
            'unread'   => ContactMessage::unread()->count(),
        ]);
    }

    public function markRead(int $id): RedirectResponse
    {
        ContactMessage::whereKey($id)->update(['is_read' => true]);

        return back();
    }

    public function destroy(int $id): RedirectResponse
    {
        ContactMessage::findOrFail($id)->delete();

        return back()->with('success', 'تم حذف الرسالة');
    }
}
