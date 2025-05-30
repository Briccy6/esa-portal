<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'last_name',
        'birthday',
        'gender',
        'phone',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'id_document',
        'result_slip',
        'passport_photo',
        'course_id',
        'academic_year_id',
        'reg_number',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
    ];

    /**
     * Relationships
     */

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
