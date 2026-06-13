<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\QuizAttempt;
use App\Models\User;
use App\Repositories\Contracts\ParentRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class ParentRepository implements ParentRepositoryInterface
{
    public function dashboard(User $parent): array
    {
        $children = $parent->children()->get();

        return [
            'children_count' => $children->count(),
            'total_certificates' => $children->sum(fn ($c) => $c->certificates()->count()),
            'total_badges' => $children->sum(fn ($c) => $c->badges()->count()),
            'active_paths' => $children->sum(fn ($c) => $c->enrollments()->where('status', 'active')->count()),
        ];
    }

    public function children(User $parent): array
    {
        return $parent->children()->get()->map(function (User $child) {
            $enrollments = $child->enrollments()->with('path')->get();
            $avg = $enrollments->count()
                ? (int) round($enrollments->avg(fn ($e) => $e->progress_percent))
                : 0;

            return [
                'id' => $child->id,
                'name' => $child->name,
                'email' => $child->email,
                'avatar_letter' => mb_substr($child->name, 0, 1),
                'paths_count' => $enrollments->count(),
                'badges' => $child->badges()->count(),
                'certificates' => $child->certificates()->count(),
                'avg_progress' => $avg,
            ];
        })->toArray();
    }

    public function addChild(User $parent, array $data): User
    {
        $child = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'gender' => $data['gender'] ?? null,
            'is_active' => true,
        ]);

        $parent->children()->attach($child->id, ['created_at' => now()]);

        return $child;
    }

    public function childReport(User $parent, int $childId): array
    {
        $child = $parent->children()->findOrFail($childId);

        $paths = $child->enrollments()->with('path')->get()->map(fn (Enrollment $e) => [
            'id' => $e->id,
            'title' => $e->path->title,
            'color' => $e->path->color,
            'grade_level' => $e->grade_level,
            'status' => $e->status,
            'progress' => $e->progress_percent,
        ])->toArray();

        $attempts = QuizAttempt::where('student_id', $child->id)
            ->with('quiz.lesson:id,title')
            ->latest('finished_at')
            ->limit(20)
            ->get()
            ->map(fn (QuizAttempt $a) => [
                'lesson' => $a->quiz?->lesson?->title ?? '—',
                'score' => (float) $a->score,
                'passed' => (bool) $a->passed,
                'date' => $a->finished_at?->format('Y-m-d') ?? '—',
            ])->toArray();

        return [
            'child' => [
                'id' => $child->id,
                'name' => $child->name,
                'email' => $child->email,
                'badges' => $child->badges()->count(),
                'certificates' => $child->certificates()->count(),
            ],
            'paths' => $paths,
            'quiz_attempts' => $attempts,
        ];
    }

    public function billing(User $parent): array
    {
        $subscription = $parent->activeSubscription()->with('plan')->first();

        $transactions = $parent->transactions()
            ->with('subscription.plan')
            ->latest()
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'amount' => (float) $t->amount,
                'gateway' => $t->gateway,
                'status' => $t->status,
                'plan' => $t->subscription?->plan?->name ?? '—',
                'date' => $t->created_at?->format('Y-m-d') ?? '—',
            ])->toArray();

        return [
            'subscription' => $subscription ? [
                'plan' => $subscription->plan?->name,
                'status' => $subscription->status,
                'starts_at' => $subscription->starts_at?->format('Y-m-d'),
                'ends_at' => $subscription->ends_at?->format('Y-m-d'),
                'auto_renew' => (bool) $subscription->auto_renew,
            ] : null,
            'transactions' => $transactions,
        ];
    }
}
