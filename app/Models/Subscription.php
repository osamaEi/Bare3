<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'plan_id', 'status', 'starts_at', 'ends_at', 'trial_ends_at', 'auto_renew'];

    protected function casts(): array
    {
        return [
            'starts_at'     => 'datetime',
            'ends_at'       => 'datetime',
            'trial_ends_at' => 'datetime',
            'auto_renew'    => 'boolean',
        ];
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function plan()         { return $this->belongsTo(Plan::class); }
    public function transactions() { return $this->hasMany(PaymentTransaction::class); }

    public function isActive():    bool { return $this->status === 'active'; }
    public function isExpired():   bool { return $this->status === 'expired'; }
    public function isCancelled(): bool { return $this->status === 'cancelled'; }

    public function scopeActive($q)   { return $q->where('status', 'active'); }
    public function scopeExpired($q)  { return $q->where('status', 'expired'); }
}
