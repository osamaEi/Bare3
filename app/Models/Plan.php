<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'type', 'price', 'currency', 'billing_cycle', 'features', 'is_active'];

    protected function casts(): array
    {
        return [
            'features'  => 'array',
            'is_active' => 'boolean',
            'price'     => 'decimal:2',
        ];
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function getActiveSubscribersCountAttribute(): int
    {
        return $this->subscriptions()->where('status', 'active')->count();
    }
}
