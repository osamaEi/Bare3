<?php

namespace App\Repositories;

use App\Models\Enrollment;
use App\Models\PaymentTransaction;
use App\Models\QuizAttempt;
use App\Models\Subscription;
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
        // كل المعاملات التي قام بها الأب (الدافع)
        $allTx = $parent->transactions()->with('subscription.plan')->latest()->get();

        $children = $parent->children()->get();

        $perChild = $children->map(function (User $child) use ($allTx) {
            // اشتراك الطفل الحالي
            $sub = Subscription::with('plan')->where('user_id', $child->id)->latest('starts_at')->first();

            // مدفوعات هذا الطفل: إما المستفيد في الـ payload أو المرتبطة باشتراكه
            $childTx = $allTx->filter(function ($t) use ($child, $sub) {
                $beneficiary = $t->payload['beneficiary_id'] ?? null;

                return (int) $beneficiary === $child->id
                    || ($sub && $t->subscription_id === $sub->id);
            })->map(fn ($t) => [
                'id'      => $t->id,
                'amount'  => (float) $t->amount,
                'gateway' => $t->gateway,
                'status'  => $t->status,
                'plan'    => $t->subscription?->plan?->name ?? '—',
                'date'    => $t->created_at?->format('Y-m-d') ?? '—',
            ])->values()->all();

            return [
                'id'           => $child->id,
                'name'         => $child->name,
                'subscription' => $sub ? [
                    'plan'       => $sub->plan?->name,
                    'status'     => $sub->status,
                    'starts_at'  => $sub->starts_at?->format('Y-m-d'),
                    'ends_at'    => $sub->ends_at?->format('Y-m-d'),
                    'auto_renew' => (bool) $sub->auto_renew,
                ] : null,
                'transactions' => $childTx,
            ];
        })->values()->all();

        return [
            'children' => $perChild,
            // المعاملات التي لم تُنسب لأي طفل (اشتراك الأب نفسه مثلاً)
            'other_transactions' => $allTx->filter(function ($t) use ($children) {
                $beneficiary = $t->payload['beneficiary_id'] ?? null;

                return ! $beneficiary || ! $children->contains('id', (int) $beneficiary);
            })->map(fn ($t) => [
                'id'      => $t->id,
                'amount'  => (float) $t->amount,
                'gateway' => $t->gateway,
                'status'  => $t->status,
                'plan'    => $t->subscription?->plan?->name ?? '—',
                'date'    => $t->created_at?->format('Y-m-d') ?? '—',
            ])->values()->all(),
        ];
    }
}
