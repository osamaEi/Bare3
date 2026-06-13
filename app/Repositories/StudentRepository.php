<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Path;
use App\Models\Quiz;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\ScormPackage;
use App\Models\ScormTracking;
use App\Models\User;
use App\Models\Video;
use App\Models\VideoProgress;
use App\Repositories\Contracts\StudentRepositoryInterface;
use App\Services\AchievementService;
use Illuminate\Support\Facades\DB;

class StudentRepository implements StudentRepositoryInterface
{
    public function __construct(
        private readonly AchievementService $achievements,
    ) {}

    public function dashboardStats(User $student): array
    {
        return [
            'paths_completed' => $student->enrollments()->where('status', 'completed')->count(),
            'streak' => 0, // placeholder until streak tracking lands
            'badges' => $student->badges()->count(),
            'certificates' => $student->certificates()->count(),
        ];
    }

    public function enrolledPaths(User $student): array
    {
        return $student->enrollments()
            ->with('path')
            ->latest('started_at')
            ->get()
            ->map(fn (Enrollment $e) => [
                'id' => $e->id,
                'path_id' => $e->path_id,
                'title' => $e->path->title,
                'icon' => $this->materialIcon($e->path->icon),
                'color' => $e->path->color,
                'progress' => $e->progress_percent,
                'grade_level' => $e->grade_level,
                'status' => $e->status,
            ])
            ->toArray();
    }

    public function browsePaths(User $student): array
    {
        $enrollments = $student->enrollments()->get()->keyBy('path_id');

        return Path::where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn (Path $p) => [
                'id' => $p->id,
                'slug' => $p->slug,
                'title' => $p->title,
                'description' => $p->description,
                'icon' => $this->materialIcon($p->icon),
                'color' => $p->color,
                'lessons_count' => $p->lessons_count,
                'enrolled' => $enrollments->has($p->id),
                'grade_level' => $enrollments->get($p->id)?->grade_level,
                'enrollment_id' => $enrollments->get($p->id)?->id,
            ])
            ->toArray();
    }

    public function enroll(User $student, Path $path, string $gradeLevel): Enrollment
    {
        return DB::transaction(function () use ($student, $path, $gradeLevel) {
            $enrollment = Enrollment::firstOrCreate(
                ['student_id' => $student->id, 'path_id' => $path->id],
                ['grade_level' => $gradeLevel, 'status' => 'active', 'started_at' => now()],
            );

            $lessons = $path->lessons()->forGrade($gradeLevel)->where('is_active', true)
                ->orderBy('sort_order')->get();

            foreach ($lessons as $i => $lesson) {
                LessonProgress::firstOrCreate(
                    ['student_id' => $student->id, 'lesson_id' => $lesson->id],
                    [
                        'enrollment_id' => $enrollment->id,
                        'status' => $i === 0 ? 'in_progress' : 'locked',
                        'unlocked_at' => $i === 0 ? now() : null,
                        'updated_at' => now(),
                    ],
                );
            }

            return $enrollment;
        });
    }

    public function pathJourney(User $student, Enrollment $enrollment): array
    {
        $lessons = $enrollment->path->lessons()
            ->forGrade($enrollment->grade_level)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $progress = LessonProgress::where('student_id', $student->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        // Self-heal: make sure progress rows exist and the first available
        // lesson (or any whose predecessor is completed) is unlocked.
        $prevCompleted = true; // first lesson is always reachable
        foreach ($lessons as $lesson) {
            $p = $progress->get($lesson->id);

            if (! $p) {
                $p = LessonProgress::create([
                    'student_id' => $student->id,
                    'lesson_id' => $lesson->id,
                    'enrollment_id' => $enrollment->id,
                    'status' => $prevCompleted ? 'in_progress' : 'locked',
                    'unlocked_at' => $prevCompleted ? now() : null,
                    'updated_at' => now(),
                ]);
                $progress->put($lesson->id, $p);
            } elseif ($p->status === 'locked' && $prevCompleted) {
                $p->update(['status' => 'in_progress', 'unlocked_at' => now(), 'updated_at' => now()]);
            }

            $prevCompleted = $p->status === 'completed';
        }

        return [
            'enrollment' => [
                'id' => $enrollment->id,
                'path_id' => $enrollment->path_id,
                'title' => $enrollment->path->title,
                'color' => $enrollment->path->color,
                'grade_level' => $enrollment->grade_level,
                'progress' => $enrollment->progress_percent,
                'status' => $enrollment->status,
            ],
            'lessons' => $lessons->map(function (Lesson $lesson) use ($progress) {
                $p = $progress->get($lesson->id);

                return [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'status' => $p?->status ?? 'locked',
                ];
            })->toArray(),
        ];
    }

    public function lessonPayload(User $student, Lesson $lesson): array
    {
        $lesson->load(['video', 'scormPackage', 'quiz.questions.options']);

        $progress = LessonProgress::firstOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lesson->id],
            ['status' => 'locked', 'updated_at' => now()],
        );

        $video = $lesson->video;
        $videoProgress = $video
            ? VideoProgress::where('student_id', $student->id)->where('video_id', $video->id)->first()
            : null;

        return [
            'lesson' => [
                'id' => $lesson->id,
                'title' => $lesson->title,
                'path_id' => $lesson->path_id,
            ],
            'progress' => [
                'status' => $progress->status,
                'video_completed' => (bool) $progress->video_completed,
                'scorm_completed' => (bool) $progress->scorm_completed,
                'quiz_passed' => (bool) $progress->quiz_passed,
            ],
            'video' => $video ? [
                'id' => $video->id,
                'title' => $video->title,
                'src' => $video->url ?: ($video->file_path ? asset('storage/'.$video->file_path) : null),
                'watch_threshold' => $video->watch_threshold,
                'watch_percent' => $videoProgress?->watch_percent ?? 0,
            ] : null,
            'scorm' => $lesson->scormPackage ? [
                'id' => $lesson->scormPackage->id,
                'title' => $lesson->scormPackage->title,
                'version' => $lesson->scormPackage->version,
                // رابط صفحة بداية الحزمة (بعد فكّ الضغط)
                'launch_url' => $lesson->scormPackage->package_path
                    ? asset('storage/'.trim($lesson->scormPackage->package_path, '/').'/'.ltrim($lesson->scormPackage->entry_point ?? 'index.html', '/'))
                    : null,
            ] : null,
            'quiz' => $lesson->quiz ? [
                'id' => $lesson->quiz->id,
                'title' => $lesson->quiz->title,
                'pass_mark' => $lesson->quiz->pass_mark,
                'max_attempts' => $lesson->quiz->max_attempts,
                'attempts_used' => $lesson->quiz->attemptsBy($student->id)->count(),
                'questions' => $lesson->quiz->questions->map(fn ($q) => [
                    'id' => $q->id,
                    'text' => $q->text,
                    'options' => $q->options->map(fn ($o) => ['id' => $o->id, 'text' => $o->text])->values(),
                ])->values(),
            ] : null,
        ];
    }

    public function recordVideoProgress(User $student, int $videoId, int $watchPercent, int $position): array
    {
        $video = Video::findOrFail($videoId);

        $vp = VideoProgress::firstOrNew(['student_id' => $student->id, 'video_id' => $video->id]);
        $vp->watch_percent = max($vp->watch_percent ?? 0, $watchPercent);
        $vp->last_position_sec = $position;
        $completed = $vp->watch_percent >= $video->watch_threshold;
        $vp->completed = $completed;
        if ($completed && ! $vp->completed_at) {
            $vp->completed_at = now();
        }
        $vp->updated_at = now();
        $vp->save();

        if ($completed) {
            $this->markLessonFlag($student, $video->lesson_id, 'video_completed');
        }

        return ['video_completed' => $completed, 'watch_percent' => $vp->watch_percent];
    }

    /**
     * Persist a SCORM "commit" coming from the running package.
     * The package reports its lesson_status/score via the JS API bridge,
     * which POSTs here. We store it and mark the lesson's SCORM part done
     * once the status is completed/passed.
     */
    public function completeScorm(User $student, int $scormId, array $payload = []): array
    {
        $scorm = ScormPackage::findOrFail($scormId);

        $status = $payload['status'] ?? 'completed';
        $isDone = in_array($status, ['completed', 'passed'], true);

        $tracking = ScormTracking::firstOrNew(['student_id' => $student->id, 'scorm_id' => $scorm->id]);
        $tracking->lesson_id = $scorm->lesson_id;
        $tracking->status = $status;
        $tracking->score = $payload['score'] ?? $tracking->score;
        $tracking->session_time = $payload['session_time'] ?? $tracking->session_time;
        $tracking->raw_data = $payload['raw_data'] ?? $tracking->raw_data;
        if ($isDone) {
            $tracking->completed_at = now();
        }
        $tracking->updated_at = now();
        $tracking->save();

        $completion = null;
        if ($isDone) {
            $completion = $this->markLessonFlag($student, $scorm->lesson_id, 'scorm_completed');
        }

        return ['scorm_completed' => $isDone, 'status' => $status, 'completion' => $completion];
    }

    public function submitQuiz(User $student, int $quizId, array $answers): array
    {
        $quiz = Quiz::with('questions.options')->findOrFail($quizId);

        $used = $quiz->attemptsBy($student->id)->count();
        if ($used >= $quiz->max_attempts) {
            return ['error' => 'لقد استنفدت كل المحاولات المتاحة', 'attempts_used' => $used];
        }

        return DB::transaction(function () use ($student, $quiz, $answers, $used) {
            $total = $quiz->questions->count();
            $correct = 0;

            $attempt = QuizAttempt::create([
                'student_id' => $student->id,
                'quiz_id' => $quiz->id,
                'attempt_num' => $used + 1,
                'score' => 0,
                'passed' => false,
                'started_at' => now(),
                'finished_at' => now(),
            ]);

            foreach ($quiz->questions as $question) {
                $optionId = $answers[$question->id] ?? null;
                $option = $optionId ? QuizOption::find($optionId) : null;
                $isCorrect = $option && $option->question_id === $question->id && $option->is_correct;
                if ($isCorrect) {
                    $correct++;
                }

                QuizAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'option_id' => $optionId,
                    'is_correct' => (bool) $isCorrect,
                ]);
            }

            $score = $total > 0 ? round($correct / $total * 100, 2) : 0;
            $passed = $score >= $quiz->pass_mark;

            $attempt->update(['score' => $score, 'passed' => $passed]);

            $completion = null;
            if ($passed) {
                $completion = $this->markLessonFlag($student, $quiz->lesson_id, 'quiz_passed');
            }

            return [
                'score' => $score,
                'passed' => $passed,
                'correct' => $correct,
                'total' => $total,
                'attempts_used' => $used + 1,
                'completion' => $completion,
            ];
        });
    }

    /**
     * Set a progress flag; if all three are done, complete the lesson and
     * unlock the next one. On the final lesson, award badge + certificate.
     */
    private function markLessonFlag(User $student, int $lessonId, string $flag): ?array
    {
        $progress = LessonProgress::firstOrCreate(
            ['student_id' => $student->id, 'lesson_id' => $lessonId],
            ['status' => 'in_progress', 'updated_at' => now()],
        );

        $progress->{$flag} = true;
        if ($progress->status === 'locked') {
            $progress->status = 'in_progress';
        }
        $progress->updated_at = now();
        $progress->save();

        // A lesson only requires the components it actually has.
        $lesson = Lesson::with(['video', 'scormPackage', 'quiz'])->findOrFail($lessonId);
        $videoOk = ! $lesson->video || $progress->video_completed;
        $scormOk = ! $lesson->scormPackage || $progress->scorm_completed;
        $quizOk = ! $lesson->quiz || $progress->quiz_passed;

        if (! ($videoOk && $scormOk && $quizOk)) {
            return null;
        }

        // Lesson complete
        $progress->status = 'completed';
        $progress->completed_at = now();
        $progress->save();

        $enrollment = $progress->enrollment_id
            ? Enrollment::find($progress->enrollment_id)
            : Enrollment::where('student_id', $student->id)->where('path_id', $lesson->path_id)->first();

        $result = ['lesson_completed' => true, 'badge' => null, 'certificate' => null];

        if (! $enrollment) {
            return $result;
        }

        // Unlock next lesson in the journey
        $lessons = $enrollment->path->lessons()
            ->forGrade($enrollment->grade_level)->where('is_active', true)
            ->orderBy('sort_order')->get();

        $index = $lessons->search(fn ($l) => $l->id === $lessonId);
        $next = $index !== false ? $lessons->get($index + 1) : null;

        if ($next) {
            $np = LessonProgress::firstOrCreate(
                ['student_id' => $student->id, 'lesson_id' => $next->id],
                ['enrollment_id' => $enrollment->id, 'status' => 'locked', 'updated_at' => now()],
            );
            if ($np->status === 'locked') {
                $np->status = 'in_progress';
                $np->unlocked_at = now();
                $np->updated_at = now();
                $np->save();
            }
            $result['next_lesson_id'] = $next->id;
        }

        // Whole path complete?
        $completedCount = LessonProgress::where('enrollment_id', $enrollment->id)
            ->where('status', 'completed')->count();

        if ($completedCount >= $lessons->count() && $enrollment->status !== 'completed') {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);

            $badge = $this->achievements->awardPathBadge($student, $enrollment->path_id);
            $certificate = $this->achievements->issueCertificate($student, $enrollment);

            $result['path_completed'] = true;
            $result['badge'] = $badge?->badge->only(['name', 'image']);
            $result['certificate'] = ['id' => $certificate->id, 'cert_number' => $certificate->cert_number];
        }

        return $result;
    }

    /** Map a FontAwesome class (from seeder) to a Material icon name, best-effort. */
    private function materialIcon(?string $icon): string
    {
        $map = [
            'fa-lightbulb' => 'lightbulb',
            'fa-comments' => 'forum',
            'fa-coins' => 'savings',
            'fa-heart' => 'favorite',
            'fa-laptop' => 'computer',
        ];

        foreach ($map as $fa => $mi) {
            if ($icon && str_contains($icon, $fa)) {
                return $mi;
            }
        }

        return 'menu_book';
    }
}
