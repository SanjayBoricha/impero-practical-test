<?php

namespace App\Models;

use App\Enums\PaymentTypes;
use App\Enums\SalePinTypes;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['pin_type', 'payment_type', 'customer_name', 'full_address', 'order_date', 'price', 'quantity', 'product_name'];

    protected $casts = [
        'payment_type' => PaymentTypes::class,
        'pin_type' => SalePinTypes::class
    ];
}
