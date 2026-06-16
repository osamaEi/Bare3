<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminNotificationController extends Controller
{
    public function index(): Response
    {
        // أرشيف الإشعارات مجمّعة حسب نفس الدفعة (العنوان + النص + الوقت + الجمهور)
        $sent = Notification::with('user:id,name,role')
            ->latest()
            ->take(300)
            ->get()
            ->map(fn ($n) => [
                'id'         => $n->id,
                'title'      => $n->title,
                'body'       => $n->body,
                'type'       => $n->type,
                'audience'   => $n->audience,
                'recipient'  => $n->user?->name,
                'role'       => $n->user?->role,
                'read'       => (bool) $n->read_at,
                'created_at' => $n->created_at?->format('Y-m-d H:i'),
            ]);

        return Inertia::render('Admin/Notifications', [
            'sent'       => $sent,
            'recipients' => User::whereIn('role', ['student', 'parent'])
                ->orderBy('name')
                ->get(['id', 'name', 'role']),
            'stats' => [
                'total' => Notification::count(),
                'unread' => Notification::unread()->count(),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'audience' => 'required|in:all,students,parents,user',
            'user_id'  => 'required_if:audience,user|nullable|exists:users,id',
            'title'    => 'required|string|max:255',
            'body'     => 'required|string|max:2000',
            'type'     => 'required|in:info,success,warning',
        ]);

        $targets = match ($data['audience']) {
            'students' => User::where('role', 'student')->pluck('id'),
            'parents'  => User::where('role', 'parent')->pluck('id'),
            'all'      => User::whereIn('role', ['student', 'parent'])->pluck('id'),
            'user'     => collect([$data['user_id']]),
        };

        $rows = $targets->map(fn ($uid) => [
            'user_id'    => $uid,
            'sent_by'    => $request->user()->id,
            'title'      => $data['title'],
            'body'       => $data['body'],
            'type'       => $data['type'],
            'audience'   => $data['audience'],
            'created_at' => now(),
            'updated_at' => now(),
        ])->all();

        if (! empty($rows)) {
            Notification::insert($rows);
        }

        return back()->with('success', 'تم إرسال الإشعار إلى '.count($rows).' مستخدم');
    }

    public function destroy(int $id): RedirectResponse
    {
        Notification::findOrFail($id)->delete();

        return back()->with('success', 'تم حذف الإشعار');
    }
}
