<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentDashboardController extends Controller
{
    public function __construct(
        private readonly StudentRepositoryInterface $students,
    ) {}

    public function index(Request $request): Response
    {
        $student = $request->user();

        $badges = $student->badges()
            ->orderByPivot('earned_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn ($b) => [
                'id' => $b->id,
                'name' => $b->name,
                'icon' => 'emoji_events',
                'earned' => true,
            ])
            ->toArray();

        return Inertia::render('Student/Dashboard', [
            'stats' => $this->students->dashboardStats($student),
            'paths' => $this->students->enrolledPaths($student),
            'badges' => $badges,
        ]);
    }
}
