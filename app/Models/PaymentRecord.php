<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    protected $fillable = [
        'student_id', 'type', 'token', 'expires_at', 'is_paid'
    ];

    protected $dates = ['expires_at'];
}
