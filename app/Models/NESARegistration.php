<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NESARegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'registration_date',
        'sdms_code',
        'registration_number',
        'status',
        'remarks',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'status' => 'boolean',
    ];

    /**
     * Get the student that owns the NESA registration
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
