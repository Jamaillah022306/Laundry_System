<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'machine_number',
        'type',             // washer | dryer
        'status',           // available | in_use | maintenance
        'current_order_id',
    ];

    // ===== RELATIONSHIPS =====
    public function currentOrder()
    {
        return $this->belongsTo(Order::class, 'current_order_id', 'order_id');
    }

    // ===== SCOPES =====
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }
}
