<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'author_id', 'title', 'slug', 'excerpt',
        'content', 'featured_image', 'seo_title', 'seo_description',
        'status', 'views_count', 'published_at',
    ];

    protected function casts(): array
    {
        return ['published_at' => 'datetime'];
    }

    public function category() { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function author()   { return $this->belongsTo(User::class, 'author_id'); }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'post_tag', 'post_id', 'tag_id');
    }

    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeDraft($q)     { return $q->where('status', 'draft'); }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function isPublished(): bool { return $this->status === 'published'; }
}
