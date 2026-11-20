<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentCard extends Model
{
    protected $fillable = [
        'student_id',
        'reg_number',
        'course',
        'branch',
        'passport_photo',
        'qr_code',
        'verification_url',
        'academic_year',
        'card_generated_by',
        'generated_at',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
