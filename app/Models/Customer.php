<?php

namespace App\Models;

use App\Enums\CustomerStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        "phone_number",
        "address",
        "email",
        "company",
        "notes"
    ];

    protected $casts = [
        'status' => CustomerStatus::class
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
