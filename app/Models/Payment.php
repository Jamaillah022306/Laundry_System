<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_id',
        'order_id',
        'amount',
        'method',
        'reference_number',
        'status',
        'reference_number',
    ];

    // ===== AUTO-GENERATE PAYMENT ID =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            if (empty($payment->payment_id)) {
                $last = static::orderBy('id', 'desc')
                            ->whereRaw("payment_id REGEXP '^PAY-[0-9]+$'")
                            ->first();
                $next = $last ? (intval(substr($last->payment_id, 4)) + 1) : 1;
                $payment->payment_id = 'PAY-' . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }
}