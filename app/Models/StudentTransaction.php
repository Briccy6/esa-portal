<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentTransaction extends Model
{
    protected $fillable = [
        'student_id', 'tx_ref', 'transaction_id', 'type', 'amount', 'currency', 'status', 'meta', 'paid_at'
    ];

    protected $casts = [
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
