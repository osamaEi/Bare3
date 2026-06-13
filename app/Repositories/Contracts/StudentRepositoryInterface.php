<?php

namespace App\Repositories\Contracts;

use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\Path;
use App\Models\User;

interface StudentRepositoryInterface
{
    public function dashboardStats(User $student): array;

    public function enrolledPaths(User $student): array;

    public function browsePaths(User $student): array;

    public function enroll(User $student, Path $path, string $gradeLevel): Enrollment;

    public function pathJourney(User $student, Enrollment $enrollment): array;

    public function lessonPayload(User $student, Lesson $lesson): array;

    public function recordVideoProgress(User $student, int $videoId, int $watchPercent, int $position): array;

    public function completeScorm(User $student, int $scormId, array $payload = []): array;

    public function submitQuiz(User $student, int $quizId, array $answers): array;
}
