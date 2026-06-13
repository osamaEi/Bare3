<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\LessonProgress;
use App\Models\QuizAttempt;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminStudentProgressController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search', '');

        $students = User::students()
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            }))
            ->withCount([
                'enrollments',
                'enrollments as completed_enrollments_count' => fn ($q) => $q->where('status', 'completed'),
                'badges',
                'certificates',
            ])
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/StudentProgress', [
            'students'    => $students,
            'filters'     => ['search' => $search],
            'total_stats' => [
                'students'    => User::students()->count(),
                'enrollments' => Enrollment::count(),
                'completed'   => Enrollment::where('status', 'completed')->count(),
                'quiz_passed' => QuizAttempt::where('passed', true)->count(),
            ],
        ]);
    }

    public function show(User $student)
    {
        abort_unless($student->role === 'student', 404);

        $enrollments = Enrollment::where('student_id', $student->id)
            ->with([
                'path',
                'lessonProgress.lesson',
            ])
            ->latest('started_at')
            ->get()
            ->map(function (Enrollment $e) use ($student) {
                $totalLessons = $e->lessonProgress->count();
                $completedLessons = $e->lessonProgress->where('status', 'completed')->count();
                $progress = $totalLessons > 0 ? round($completedLessons / $totalLessons * 100) : 0;

                // quiz attempts for this enrollment's path lessons
                $lessonIds = $e->lessonProgress->pluck('lesson_id');
                $quizAttempts = QuizAttempt::where('student_id', $student->id)
                    ->whereHas('quiz', fn ($q) => $q->whereIn('lesson_id', $lessonIds))
                    ->with('quiz.lesson')
                    ->orderBy('started_at')
                    ->get()
                    ->map(fn ($a) => [
                        'id'          => $a->id,
                        'quiz_title'  => $a->quiz->title ?? 'اختبار',
                        'lesson'      => $a->quiz->lesson->title ?? '',
                        'attempt_num' => $a->attempt_num,
                        'score'       => $a->score,
                        'passed'      => $a->passed,
                        'finished_at' => $a->finished_at?->toDateTimeString(),
                    ]);

                return [
                    'id'                => $e->id,
                    'path_id'           => $e->path_id,
                    'path_title'        => $e->path->title,
                    'path_color'        => $e->path->color ?? '#38BDF8',
                    'grade_level'       => $e->grade_level,
                    'status'            => $e->status,
                    'progress'          => $progress,
                    'total_lessons'     => $totalLessons,
                    'completed_lessons' => $completedLessons,
                    'started_at'        => $e->started_at?->toDateString(),
                    'completed_at'      => $e->completed_at?->toDateString(),
                    'lessons'           => $e->lessonProgress->sortBy('lesson.sort_order')->values()->map(fn ($lp) => [
                        'id'              => $lp->id,
                        'lesson_title'    => $lp->lesson->title ?? 'درس',
                        'status'          => $lp->status,
                        'video_completed' => $lp->video_completed,
                        'scorm_completed' => $lp->scorm_completed,
                        'quiz_passed'     => $lp->quiz_passed,
                        'completed_at'    => $lp->completed_at?->toDateTimeString(),
                    ]),
                    'quiz_attempts' => $quizAttempts,
                ];
            });

        $badges = $student->badges()->withPivot('earned_at')->get()->map(fn ($b) => [
            'id'        => $b->id,
            'name'      => $b->name,
            'icon'      => $b->icon,
            'earned_at' => $b->pivot->earned_at,
        ]);

        $certificates = $student->certificates()->with('path')->get()->map(fn ($c) => [
            'id'            => $c->id,
            'path_title'    => $c->path->title ?? '',
            'cert_number'   => $c->cert_number,
            'issued_at'     => $c->issued_at?->toDateString(),
        ]);

        return Inertia::render('Admin/StudentProgressDetail', [
            'student'      => [
                'id'         => $student->id,
                'name'       => $student->name,
                'email'      => $student->email,
                'phone'      => $student->phone,
                'gender'     => $student->gender,
                'is_active'  => $student->is_active,
                'created_at' => $student->created_at->toDateString(),
            ],
            'enrollments'  => $enrollments,
            'badges'       => $badges,
            'certificates' => $certificates,
            'summary'      => [
                'total_enrollments'    => $enrollments->count(),
                'completed_enrollments' => $enrollments->where('status', 'completed')->count(),
                'total_lessons'        => $enrollments->sum('total_lessons'),
                'completed_lessons'    => $enrollments->sum('completed_lessons'),
                'badges_count'         => $badges->count(),
                'certificates_count'   => $certificates->count(),
                'quiz_attempts'        => QuizAttempt::where('student_id', $student->id)->count(),
                'quiz_passed'          => QuizAttempt::where('student_id', $student->id)->where('passed', true)->count(),
            ],
        ]);
    }
}
