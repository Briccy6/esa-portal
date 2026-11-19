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
        'term_id',
        'branch_id',
    ];

    public function paid_by_staff()
    {
        return $this->belongsTo(\App\Models\Staff::class, 'paid_by');
    }

    public function branch()
    {
        return $this->belongsTo(\App\Models\Branch::class, 'branch_id');
    }

    public function term()
    {
        return $this->belongsTo(\App\Models\Term::class);
    }
}
