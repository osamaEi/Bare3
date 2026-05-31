<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'file_path', 'url', 'duration_seconds', 'thumbnail', 'watch_threshold'];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function progress()
    {
        return $this->hasMany(VideoProgress::class);
    }

    public function progressFor(int $studentId)
    {
        return $this->hasOne(VideoProgress::class)->where('student_id', $studentId);
    }

    public function getDurationFormattedAttribute(): string
    {
        $m = intdiv($this->duration_seconds, 60);
        $s = $this->duration_seconds % 60;
        return sprintf('%d:%02d', $m, $s);
    }
}
