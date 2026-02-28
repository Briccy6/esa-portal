<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


    // app/Models/PaymentType.php
class PaymentType extends Model
{
    protected $fillable = [
        'name', 'applies_to', 'is_required', 'default_amount', 'default_discount',
    ];
}

