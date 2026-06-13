<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminTicketController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['user', 'replies.user'])
            ->latest()
            ->get();

        return Inertia::render('Admin/Tickets', compact('tickets'));
    }

    public function reply(Request $request, Ticket $ticket)
    {
        $request->validate(['body' => 'required|string']);

        $ticket->replies()->create([
            'user_id' => auth()->id(),
            'body'    => $request->body,
        ]);

        $ticket->update(['status' => 'replied']);

        return back();
    }

    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);
        return back();
    }
}
