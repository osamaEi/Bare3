<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Repositories\Contracts\StudentRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LessonController extends Controller
{
    public function __construct(
        private readonly StudentRepositoryInterface $students,
    ) {}

    public function show(Request $request, Lesson $lesson): Response
    {
        $student = $request->user();

        // Block access to locked lessons
        $progress = LessonProgress::where('student_id', $student->id)
            ->where('lesson_id', $lesson->id)
            ->first();

        abort_if($progress && $progress->status === 'locked', 403, 'هذا الدرس مقفل');

        return Inertia::render('Student/Lesson', [
            'data' => $this->students->lessonPayload($student, $lesson),
        ]);
    }

    public function videoProgress(Request $request): JsonResponse
    {
        $data = $request->validate([
            'video_id' => 'required|exists:videos,id',
            'watch_percent' => 'required|integer|min:0|max:100',
            'position' => 'required|integer|min:0',
        ]);

        return response()->json(
            $this->students->recordVideoProgress($request->user(), $data['video_id'], $data['watch_percent'], $data['position'])
        );
    }

    public function completeScorm(Request $request): JsonResponse
    {
        $data = $request->validate([
            'scorm_id' => 'required|exists:scorm_packages,id',
            'status' => 'nullable|string',
            'score' => 'nullable|numeric',
            'session_time' => 'nullable|string',
            'raw_data' => 'nullable|array',
        ]);

        return response()->json(
            $this->students->completeScorm($request->user(), $data['scorm_id'], [
                'status' => $data['status'] ?? 'completed',
                'score' => $data['score'] ?? null,
                'session_time' => $data['session_time'] ?? null,
                'raw_data' => $data['raw_data'] ?? null,
            ])
        );
    }

    public function submitQuiz(Request $request): JsonResponse
    {
        $data = $request->validate([
            'quiz_id' => 'required|exists:quizzes,id',
            'answers' => 'required|array',
        ]);

        return response()->json(
            $this->students->submitQuiz($request->user(), $data['quiz_id'], $data['answers'])
        );
    }
}
