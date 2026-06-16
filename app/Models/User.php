<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
        'phone', 'role', 'avatar', 'gender', 'date_of_birth', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // ── Scopes ──
    public function scopeStudents($q)
    {
        return $q->where('role', 'student');
    }

    public function scopeParentRole($q)
    {
        return $q->where('role', 'parent');
    }

    public function scopeTeachers($q)
    {
        return $q->where('role', 'teacher');
    }

    public function scopeAdmins($q)
    {
        return $q->where('role', 'admin');
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    // ── Helpers ──
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isParent(): bool
    {
        return $this->role === 'parent';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    /** Route name of this user's home dashboard, by role. */
    public function homeRoute(): string
    {
        return match ($this->role) {
            'admin' => 'admin.dashboard',
            'parent' => 'parent.dashboard',
            default => 'student.dashboard',
        };
    }

    // ── Relations ──
    public function children()
    {
        return $this->belongsToMany(User::class, 'parent_children', 'parent_id', 'child_id');
    }

    public function parents()
    {
        return $this->belongsToMany(User::class, 'parent_children', 'child_id', 'parent_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'student_id');
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'student_badges', 'student_id', 'badge_id')->withPivot('earned_at', 'seen');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'student_id');
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latestOfMany();
    }

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'classroom_students', 'student_id');
    }

    public function taughtClassrooms()
    {
        return $this->hasMany(Classroom::class, 'teacher_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest();
    }
}
