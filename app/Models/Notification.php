<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_id',
        'message',
        'is_read',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ===== RELATIONSHIPS =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'order_id');
    }

    // ===== HELPER =====
    public static function sendTo(int $userId, string $message, ?string $orderId = null): void
    {
        static::create([
            'user_id'  => $userId,
            'order_id' => $orderId,
            'message'  => $message,
            'is_read'  => false,
        ]);
    }
}
