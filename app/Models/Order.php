<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'service_id',
        'machine_id',
        'service',
        'weight',
        'amount',
        'pickup_date',
        'received_at',
        'washing_at',
        'ready_at',
        'claimed_at',
        'status',
        'cashier_id',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'received_at' => 'datetime',
        'washing_at'  => 'datetime',
        'ready_at'    => 'datetime',
        'claimed_at'  => 'datetime',
        'weight'      => 'decimal:2',
        'amount'      => 'decimal:2',
    ];

    // ===== AUTO-GENERATE ORDER ID =====
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_id)) {
                $last = static::orderBy('id', 'desc')
                            ->whereRaw("order_id REGEXP '^ORD-[0-9]+$'")
                            ->first();
                $next = $last ? (intval(substr($last->order_id, 4)) + 1) : 1;
                $order->order_id = 'ORD-' . str_pad($next, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // ===== RELATIONSHIPS =====
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'order_id', 'order_id');
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // ===== SCOPES =====
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    // ===== COMPUTE AMOUNT =====
    public static function computeAmount(string $service, float $weight): float
    {
        $rates = [
            'Wash'             => 35,
            'Dry'              => 30,
            'Wash & Dry'       => 60,
            'Fold'             => 20,
            'Wash, Dry & Fold' => 75,
            'Ironing'          => 50,
            'Dry Cleaning'     => 120,
        ];

        $rate = $rates[$service] ?? 50;
        return round($rate * $weight, 2);
    }
}