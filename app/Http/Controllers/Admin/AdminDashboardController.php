<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboard,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Dashboard', [
            'stats'           => $this->dashboard->getStats(),
            'revenue_chart'   => $this->dashboard->getRevenueChart(),
            'recent_activity' => $this->dashboard->getRecentActivity(8),
            'paths_dist'      => $this->dashboard->getPathsDistribution(),
            'gateway_split'   => $this->dashboard->getGatewayBreakdown(),
            'progress_stats'  => $this->getProgressStats(),
            'top_students'    => $this->getTopStudents(),
        ]);
    }

    private function getProgressStats(): array
    {
        $totalLessons    = LessonProgress::count();
        $completedLesson = LessonProgress::where('status', 'completed')->count();
        $totalQuizzes    = QuizAttempt::count();
        $passedQuizzes   = QuizAttempt::where('passed', true)->count();

        return [
            'total_enrollments'     => Enrollment::count(),
            'active_enrollments'    => Enrollment::where('status', 'active')->count(),
            'completed_enrollments' => Enrollment::where('status', 'completed')->count(),
            'avg_progress'          => $totalLessons > 0 ? round($completedLesson / $totalLessons * 100) : 0,
            'total_lessons'         => $totalLessons,
            'completed_lessons'     => $completedLesson,
            'total_quizzes'         => $totalQuizzes,
            'passed_quizzes'        => $passedQuizzes,
            'quiz_pass_rate'        => $totalQuizzes > 0 ? round($passedQuizzes / $totalQuizzes * 100) : 0,
        ];
    }

    private function getTopStudents(): array
    {
        return User::students()
            ->withCount([
                'enrollments',
                'enrollments as completed_count' => fn ($q) => $q->where('status', 'completed'),
                'badges',
                'certificates',
            ])
            ->orderByDesc('completed_count')
            ->limit(8)
            ->get()
            ->map(fn ($s) => [
                'id'           => $s->id,
                'name'         => $s->name,
                'email'        => $s->email,
                'enrollments'  => $s->enrollments_count,
                'completed'    => $s->completed_count,
                'badges'       => $s->badges_count,
                'certificates' => $s->certificates_count,
                'is_active'    => $s->is_active,
            ])
            ->toArray();
    }
}
