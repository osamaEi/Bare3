<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['student_id', 'video_id', 'watch_percent', 'last_position_sec', 'completed', 'completed_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'completed'     => 'boolean',
            'completed_at'  => 'datetime',
            'updated_at'    => 'datetime',
        ];
    }

    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function video()   { return $this->belongsTo(Video::class); }

    public function meetsThreshold(): bool
    {
        return $this->watch_percent >= $this->video->watch_threshold;
    }
}
