<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'subscription_id', 'gateway', 'gateway_tx_id',
        'amount', 'currency', 'status', 'payload', 'refunded_at',
    ];

    protected function casts(): array
    {
        return [
            'payload'     => 'array',
            'refunded_at' => 'datetime',
            'amount'      => 'decimal:2',
        ];
    }

    public function user()         { return $this->belongsTo(User::class); }
    public function subscription() { return $this->belongsTo(Subscription::class); }
    public function invoice()      { return $this->hasOne(Invoice::class, 'transaction_id'); }

    public function isSuccess():  bool { return $this->status === 'success'; }
    public function isRefunded(): bool { return $this->status === 'refunded'; }

    public function scopeSuccess($q)  { return $q->where('status', 'success'); }
    public function scopeByGateway($q, string $gateway) { return $q->where('gateway', $gateway); }
}
