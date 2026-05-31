<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'pass_mark', 'max_attempts', 'time_limit_minutes'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('sort_order');
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function attemptsBy(int $studentId)
    {
        return $this->attempts()->where('student_id', $studentId);
    }

    public function getQuestionsCountAttribute(): int
    {
        return $this->questions()->count();
    }

    public function getPassRateAttribute(): float
    {
        $total = $this->attempts()->count();
        if ($total === 0) return 0;
        return round($this->attempts()->where('passed', true)->count() / $total * 100, 1);
    }
}
