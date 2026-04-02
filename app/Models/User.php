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
        'study_mode',
        'location'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birthday' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Check if user is a student
     */
    public function isStudent()
    {
        return $this->role === 'student';
    }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff (including accountant)
     */
    public function isStaff()
    {
        return in_array($this->role, ['staff', 'accountant', 'DOS']);
    }

    /**
     * Check if user is specifically an accountant
     */
    public function isAccountant()
    {
        return $this->role === 'accountant';
    }

    /**
     * Get user's full name
     */
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Get user's name attribute for compatibility
     */
    public function getNameAttribute()
    {
        return $this->full_name;
    }
}