<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'student_id', 'lesson_id', 'enrollment_id',
        'video_completed', 'scorm_completed', 'quiz_passed',
        'status', 'unlocked_at', 'completed_at', 'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'video_completed' => 'boolean',
            'scorm_completed' => 'boolean',
            'quiz_passed'     => 'boolean',
            'unlocked_at'     => 'datetime',
            'completed_at'    => 'datetime',
            'updated_at'      => 'datetime',
        ];
    }

    public function student()    { return $this->belongsTo(User::class, 'student_id'); }
    public function lesson()     { return $this->belongsTo(Lesson::class); }
    public function enrollment() { return $this->belongsTo(Enrollment::class); }

    public function isCompleted(): bool { return $this->status === 'completed'; }
    public function isLocked():    bool { return $this->status === 'locked'; }
}
