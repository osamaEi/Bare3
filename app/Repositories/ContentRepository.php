<?php

namespace App\Repositories;

use App\Models\Lesson;
use App\Models\Path;
use App\Models\Quiz;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\ScormPackage;
use App\Models\Video;
use App\Repositories\Contracts\ContentRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ContentRepository implements ContentRepositoryInterface
{
    // ── Paths ──────────────────────────────────────
    public function allPaths(): Collection
    {
        return Path::withCount(['lessons', 'enrollments'])->orderBy('sort_order')->get();
    }

    public function findPath(int $id)
    {
        return Path::with('lessons.video', 'lessons.scormPackage', 'lessons.quiz')->findOrFail($id);
    }

    public function createPath(array $data)
    {
        return Path::create($data);
    }

    public function updatePath(int $id, array $data): bool
    {
        return Path::findOrFail($id)->update($data);
    }

    // ── Lessons ────────────────────────────────────
    public function lessonsForPath(int $pathId): Collection
    {
        return Lesson::where('path_id', $pathId)
            ->with(['video', 'scormPackage', 'quiz.questions.options'])
            ->orderBy('sort_order')
            ->get();
    }

    public function createLesson(array $data)
    {
        return Lesson::create($data);
    }

    public function updateLesson(int $id, array $data): bool
    {
        return Lesson::findOrFail($id)->update($data);
    }

    public function deleteLesson(int $id): bool
    {
        return Lesson::findOrFail($id)->delete();
    }

    // ── Videos ─────────────────────────────────────
    public function createVideo(array $data)
    {
        return Video::create($data);
    }

    public function updateVideo(int $id, array $data): bool
    {
        return Video::findOrFail($id)->update($data);
    }

    public function deleteVideo(int $id): bool
    {
        return Video::findOrFail($id)->delete();
    }

    // ── SCORM ──────────────────────────────────────
    public function allScorm(): Collection
    {
        return ScormPackage::with('lesson.path')->latest()->get();
    }

    public function createScorm(array $data)
    {
        return ScormPackage::create($data);
    }

    public function updateScorm(int $id, array $data): bool
    {
        return ScormPackage::findOrFail($id)->update($data);
    }

    public function deleteScorm(int $id): bool
    {
        return ScormPackage::findOrFail($id)->delete();
    }

    // ── Quizzes ────────────────────────────────────
    public function findQuiz(int $id)
    {
        return Quiz::with('questions.options')->findOrFail($id);
    }

    public function createQuiz(array $data)
    {
        return Quiz::create($data);
    }

    public function updateQuiz(int $id, array $data): bool
    {
        return Quiz::findOrFail($id)->update($data);
    }

    public function deleteQuiz(int $id): bool
    {
        return Quiz::findOrFail($id)->delete();
    }

    public function addQuestion(int $quizId, array $data)
    {
        $options = $data['options'] ?? [];
        $correct = $data['correct'] ?? 0;
        unset($data['options'], $data['correct']);

        $question = QuizQuestion::create(array_merge($data, ['quiz_id' => $quizId]));

        foreach ($options as $i => $text) {
            QuizOption::create([
                'question_id' => $question->id,
                'text' => $text,
                'is_correct' => $i === $correct,
                'sort_order' => $i,
            ]);
        }

        return $question;
    }

    public function removeQuestion(int $questionId): bool
    {
        return QuizQuestion::findOrFail($questionId)->delete();
    }

    public function replaceQuestions(int $quizId, array $questions): void
    {
        Quiz::findOrFail($quizId)->questions()->each(fn ($q) => $q->delete());

        foreach ($questions as $q) {
            $this->addQuestion($quizId, $q);
        }
    }
}
