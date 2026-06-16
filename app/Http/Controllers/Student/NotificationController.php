<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(Request $request): Response
    {
        $items = $request->user()->notifications()->with('sender:id,name')->get()->map(fn (Notification $n) => [
            'id'         => $n->id,
            'title'      => $n->title,
            'body'       => $n->body,
            'type'       => $n->type,
            'read'       => (bool) $n->read_at,
            'sender'     => $n->sender?->name,
            'created_at' => $n->created_at?->diffForHumans(),
        ]);

        return Inertia::render('Student/Notifications', [
            'notifications' => $items,
            'unread_count'  => $request->user()->notifications()->unread()->count(),
        ]);
    }

    public function markRead(Request $request, int $id): RedirectResponse
    {
        $request->user()->notifications()->where('id', $id)->update(['read_at' => now()]);

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->notifications()->unread()->update(['read_at' => now()]);

        return back();
    }
}
