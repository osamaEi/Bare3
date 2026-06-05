<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; 

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = ['path_id', 'title', 'description', 'grade_level', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function video()
    {
        return $this->hasOne(Video::class);
    }

    public function scormPackage()
    {
        return $this->hasOne(ScormPackage::class);
    }

    public function quiz()
    {
        return $this->hasOne(Quiz::class);
    }

    public function progress()
    {
        return $this->hasMany(LessonProgress::class);
    }

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public function scopeForGrade($q, string $grade)
    {
        return $q->whereIn('grade_level', [$grade, 'all']);
    }
}
