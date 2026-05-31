<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'city', 'contact_email', 'contact_phone', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function classrooms()
    {
        return $this->hasMany(Classroom::class);
    }

    public function teachers()
    {
        return $this->hasManyThrough(User::class, Classroom::class, 'school_id', 'id', 'id', 'teacher_id');
    }

    public function getStudentsCountAttribute(): int
    {
        return $this->classrooms()->withCount('students')->get()->sum('students_count');
    }
}
