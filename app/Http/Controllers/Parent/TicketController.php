<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with('replies.user')
            ->latest()
            ->get();

        return Inertia::render('Parent/Tickets', compact('tickets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'subject' => 'required|string|max:255',
            'body'    => 'required|string',
        ]);

        Ticket::create([...$data, 'user_id' => auth()->id()]);

        return back()->with('success', 'تم إرسال التذكرة بنجاح');
    }

    public function reply(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);

        $request->validate(['body' => 'required|string']);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        if ($ticket->status === 'closed') {
            $ticket->update(['status' => 'open']);
        }

        return back();
    }

    public function review(Request $request, Ticket $ticket)
    {
        abort_unless($ticket->user_id === auth()->id(), 403);
        abort_unless($ticket->status === 'replied', 403);

        $request->validate([
            'rating'         => 'required|integer|min:1|max:5',
            'review_comment' => 'nullable|string|max:1000',
        ]);

        $ticket->update([
            'rating'         => $request->rating,
            'review_comment' => $request->review_comment,
            'status'         => 'closed',
        ]);

        return back()->with('success', 'شكراً على تقييمك');
    }
}
