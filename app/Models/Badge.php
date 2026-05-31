<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = ['path_id', 'name', 'description', 'image', 'type'];

    public function path()
    {
        return $this->belongsTo(Path::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'student_badges', 'badge_id', 'student_id')
                    ->withPivot('earned_at', 'seen');
    }
}
