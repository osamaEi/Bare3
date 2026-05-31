<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Path extends Model
{
    use HasFactory;

    protected $fillable = ['slug', 'title', 'description', 'icon', 'color', 'cover_image', 'sort_order', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class)->orderBy('sort_order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function badges()
    {
        return $this->hasMany(Badge::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function getLessonsCountAttribute(): int
    {
        return $this->lessons()->count();
    }

    public function getEnrollmentsCountAttribute(): int
    {
        return $this->enrollments()->count();
    }

    public function getCompletionsCountAttribute(): int
    {
        return $this->enrollments()->where('status', 'completed')->count();
    }
}
