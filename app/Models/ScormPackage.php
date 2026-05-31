<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScormPackage extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'version', 'package_path', 'entry_point', 'file_size_kb', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function tracking()
    {
        return $this->hasMany(ScormTracking::class, 'scorm_id');
    }

    public function trackingFor(int $studentId)
    {
        return $this->hasOne(ScormTracking::class, 'scorm_id')->where('student_id', $studentId);
    }

    public function getFileSizeMbAttribute(): string
    {
        return round($this->file_size_kb / 1024, 1) . ' MB';
    }
}
