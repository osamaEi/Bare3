<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['invoice_number', 'user_id', 'transaction_id', 'amount', 'tax_amount', 'pdf_path', 'issued_at'];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'issued_at'  => 'datetime',
        ];
    }

    public function user()        { return $this->belongsTo(User::class); }
    public function transaction() { return $this->belongsTo(PaymentTransaction::class, 'transaction_id'); }

    public function getTotalAttribute(): float
    {
        return (float) $this->amount + (float) $this->tax_amount;
    }
}
