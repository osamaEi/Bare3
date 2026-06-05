<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface ContentRepositoryInterface
{
    // Paths
    public function allPaths(): Collection;

    public function findPath(int $id);

    public function createPath(array $data);

    public function updatePath(int $id, array $data): bool;

    // Lessons
    public function lessonsForPath(int $pathId): Collection;

    public function createLesson(array $data);

    public function updateLesson(int $id, array $data): bool;

    public function deleteLesson(int $id): bool;

    // Videos
    public function createVideo(array $data);

    public function updateVideo(int $id, array $data): bool;

    public function deleteVideo(int $id): bool;

    // SCORM
    public function allScorm(): Collection;

    public function createScorm(array $data);

    public function updateScorm(int $id, array $data): bool;

    public function deleteScorm(int $id): bool;

    // Quizzes
    public function findQuiz(int $id);

    public function createQuiz(array $data);

    public function updateQuiz(int $id, array $data): bool;

    public function deleteQuiz(int $id): bool;

    public function addQuestion(int $quizId, array $data);

    public function removeQuestion(int $questionId): bool;

    public function replaceQuestions(int $quizId, array $questions): void;
}
