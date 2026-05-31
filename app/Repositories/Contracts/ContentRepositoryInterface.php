<?php

namespace App\Repositories\Contracts;

interface ContentRepositoryInterface
{
    // Paths
    public function allPaths(): \Illuminate\Database\Eloquent\Collection;
    public function findPath(int $id);
    public function createPath(array $data);
    public function updatePath(int $id, array $data): bool;

    // Lessons
    public function lessonsForPath(int $pathId): \Illuminate\Database\Eloquent\Collection;
    public function createLesson(array $data);
    public function updateLesson(int $id, array $data): bool;
    public function deleteLesson(int $id): bool;

    // Videos
    public function createVideo(array $data);
    public function updateVideo(int $id, array $data): bool;

    // SCORM
    public function allScorm(): \Illuminate\Database\Eloquent\Collection;
    public function createScorm(array $data);
    public function updateScorm(int $id, array $data): bool;
    public function deleteScorm(int $id): bool;

    // Quizzes
    public function findQuiz(int $id);
    public function createQuiz(array $data);
    public function updateQuiz(int $id, array $data): bool;
    public function addQuestion(int $quizId, array $data);
    public function removeQuestion(int $questionId): bool;
}
