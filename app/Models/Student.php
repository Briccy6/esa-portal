<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // So students can log in if needed
use Illuminate\Notifications\Notifiable;
use App\Models\Branch;
use App\Notifications\StudentResetPassword;

class Student extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'sex',
        'email',
        'phone',
        'branch_id',
        'course_id',
        'reg_number',
        'password',
        'academic_year_id',
        'senior6_document',
        'senior3_document',
        'other_supporting_document',
        'nesa_registered',
        'nesa_sdms_code',
        'nesa_reg_number',
        'nesa_document',
        'nesa_registration_date',
        'nesa_remarks',
    ];

    protected $casts = [
        'senior6_document' => 'string',
        'senior3_document' => 'string',
        'other_supporting_document' => 'string',
        'id_photo' => 'string',
        'passport_photo' => 'string',
        'nesa_registered' => 'boolean',
        'nesa_registration_date' => 'date',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Relationships
     */

    public function schoolFees()
    {
        return $this->hasMany(SchoolFee::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class); // Assuming Branch model exists
    }

    
    // public function courses()
    // {
    //     return $this->belongsToMany(Course::class, 'course_student', 'student_id', 'course_id')
    //         ->withTimestamps();
    // }
    
 
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function transcripts()
    {
        return $this->hasMany(Transcript::class);
    }
     use Notifiable;
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new StudentResetPassword($token));
    }
  
    public function isStudent()
    {
        return true;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function communitySpaces()
    {
        return $this->belongsToMany(\App\Models\CommunitySpace::class, 'community_space_student')
            ->withPivot('status', 'joined_at', 'removed_at')
            ->withTimestamps();
    }

    /**
     * NESA Registration related methods
     */
    public function isNesaRegistered()
    {
        return $this->nesa_registered;
    }

    public function markAsNesaRegistered($sdmsCode, $regNumber, $documentPath = null, $remarks = null)
    {
        $this->update([
            'nesa_registered' => true,
            'nesa_sdms_code' => $sdmsCode,
            'nesa_reg_number' => $regNumber,
            'nesa_document' => $documentPath,
            'nesa_registration_date' => now(),
            'nesa_remarks' => $remarks,
        ]);
    }

    public function updateNesaRegistration($data)
    {
        $this->update($data);
    }

    public function scopeNesaRegistered($query)
    {
        return $query->where('nesa_registered', true);
    }

    public function scopeNotNesaRegistered($query)
    {
        return $query->where('nesa_registered', false);
    }

    public function getNesaStatusAttribute()
    {
        return $this->nesa_registered ? 'Registered' : 'Not Registered';
    }
}
