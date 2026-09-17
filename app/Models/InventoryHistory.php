<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryHistory extends Model
{
    
    protected $fillable = [
        'order_id',
        'product_id',
        'created_by',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'type',
        'note'
    ];


    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}