<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScormTracking extends Model
{
    use HasFactory;

    protected $table = 'scorm_tracking';

    public $timestamps = false;

    protected $fillable = ['student_id', 'scorm_id', 'lesson_id', 'status', 'score', 'raw_data', 'session_time', 'completed_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'raw_data' => 'array',
            'completed_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function scormPackage()
    {
        return $this->belongsTo(ScormPackage::class, 'scorm_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function isCompleted(): bool
    {
        return in_array($this->status, ['completed', 'passed']);
    }
}
