<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'path_id', 'grade_level', 'status', 'started_at', 'completed_at'];

    protected function casts(): array
    {
        return [
            'started_at'   => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function lessonProgress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }

    // Progress percentage 0-100
    public function getProgressPercentAttribute(): int
    {
        $total = $this->path->lessons()->count();
        if ($total === 0) return 0;
        $done = $this->lessonProgress()->where('status', 'completed')->count();
        return (int) round($done / $total * 100);
    }

    public function scopeCompleted($q)   { return $q->where('status', 'completed'); }
    public function scopeActive($q)      { return $q->where('status', 'active'); }
}
