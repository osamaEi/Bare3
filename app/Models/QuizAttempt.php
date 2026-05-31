<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['student_id', 'quiz_id', 'attempt_num', 'score', 'passed', 'started_at', 'finished_at'];

    protected function casts(): array
    {
        return [
            'passed'      => 'boolean',
            'started_at'  => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function quiz()    { return $this->belongsTo(Quiz::class); }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class, 'attempt_id');
    }
}
