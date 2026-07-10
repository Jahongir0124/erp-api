<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'category_id',
        'sku',
        'price',
        'description',
        'quantity',
        'status'
    ];


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
