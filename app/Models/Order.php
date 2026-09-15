<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Policies\OrderPolice;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Model;



#[UsePolicy(OrderPolice::class)]
class Order extends Model
{
    protected $fillable = [
        'customer_id', 
        'created_by', 
        'confirmed_by',
        'cancelled_by',
        'total_amount',
        'confirmed_at',
        'cancelled_at',
        'completed_at',
        'status',
        'order_number'
        ];
    protected $casts = [
        'status' => OrderStatus::class,
        'confirmed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function confirmer()
    {
        return $this->belongsTo(User::class);
    }
    public function canceller()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
