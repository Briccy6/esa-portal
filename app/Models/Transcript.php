<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transcript extends Model
{
    protected $fillable = [
        'student_id',
        'course_id',
        'amount_paid',
        'total_fee',
        'payment_method',
        'fee_type',
         'payment_date',
        'term',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
