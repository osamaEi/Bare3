<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BadgeController extends Controller
{
    public function index(Request $request): Response
    {
        $student = $request->user();

        $earned = $student->badges()->get()->keyBy('id');

        $badges = Badge::with('path')->get()->map(fn (Badge $b) => [
            'id' => $b->id,
            'name' => $b->name,
            'desc' => $b->description,
            'path' => $b->path?->title,
            'icon' => 'emoji_events',
            'image' => $b->image ? \Illuminate\Support\Facades\Storage::disk('public')->url($b->image) : null,
            'earned' => $earned->has($b->id),
        ])->toArray();

        return Inertia::render('Student/Badges', [
            'badges' => $badges,
            'earned_count' => $earned->count(),
            'total_count' => count($badges),
        ]);
    }
}
