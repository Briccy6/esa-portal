<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'expense_date',
        'expense_type',
        'amount',
        'description',
        'paid_by',
        'paid_to',
        'paid_to_contact',
        'payment_method',
    ];

    public function paid_by_staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'paid_by');
    }
}
