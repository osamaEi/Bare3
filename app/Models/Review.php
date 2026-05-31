<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'role', 'location', 'rating', 'title', 'body', 'is_featured', 'is_approved'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_approved' => 'boolean',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function scopeApproved($q)  { return $q->where('is_approved', true); }
    public function scopeFeatured($q)  { return $q->where('is_featured', true); }
}
