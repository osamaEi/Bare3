<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentBadge extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['student_id', 'badge_id', 'earned_at', 'seen'];

    protected function casts(): array
    {
        return [
            'earned_at' => 'datetime',
            'seen'      => 'boolean',
        ];
    }

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function badge()   { return $this->belongsTo(Badge::class); }
}
