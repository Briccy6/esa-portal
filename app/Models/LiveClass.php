<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveClass extends Model
{
    protected $fillable = [
        'summary',
        'title',
        'description',
        'meet_link',
        'start_time',
        'end_time',
        'teacher_id',
        'status',
        'meeting_id',
        'password',
        'access_type', // 'students_only', 'public', 'both'
        'public_fee', // Fee for public users
        'currency', // Currency for public fee
        'required_payment_percentage', // Minimum payment percentage for students
        'allow_public_requests', // Allow public users to request access
        'max_participants', // Maximum number of participants
        'auto_approve_online_students', // Auto-approve online branch students
        'require_student_approval', // Require approval for non-online students
        'device_restriction', // Restrict to one device per email
        'recording_enabled', // Enable recording
        'chat_enabled', // Enable chat
        'screen_share_enabled' // Enable screen sharing
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'public_fee' => 'decimal:2',
        'required_payment_percentage' => 'integer',
        'max_participants' => 'integer',
        'allow_public_requests' => 'boolean',
        'auto_approve_online_students' => 'boolean',
        'require_student_approval' => 'boolean',
        'device_restriction' => 'boolean',
        'recording_enabled' => 'boolean',
        'chat_enabled' => 'boolean',
        'screen_share_enabled' => 'boolean',
    ];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'live_class_branches');
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'live_class_courses');
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'live_class_lessons');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_time', '>', now());
    }

    public function scopeLive($query)
    {
        return $query->where('start_time', '<=', now())
                    ->where('end_time', '>', now())
                    ->where('status', 'active');
    }

    public function participants()
    {
        return $this->hasMany(LiveClassParticipant::class);
    }

    public function requests()
    {
        return $this->hasMany(LiveClassRequest::class);
    }

    public function publicPayments()
    {
        return $this->hasMany(LiveClassPayment::class)->where('user_type', 'public');
    }

    // Access control methods
    public function canStudentJoin($student)
    {
        // Check if class is active
        if ($this->status !== 'active') {
            return ['allowed' => false, 'reason' => 'Class is not active'];
        }

        // Check if student is enrolled in the course
        if ($this->courses()->count() > 0) {
            $studentCourseIds = $this->courses()->pluck('courses.id')->toArray();
            if (!in_array($student->course_id, $studentCourseIds)) {
                return ['allowed' => false, 'reason' => 'You are not enrolled in any of the courses for this class'];
            }
        }

        // Check payment percentage requirement
        if ($this->required_payment_percentage > 0) {
            $paymentPercentage = $this->getStudentPaymentPercentage($student);
            if ($paymentPercentage < $this->required_payment_percentage) {
                return [
                    'allowed' => false, 
                    'reason' => 'You must pay at least ' . $this->required_payment_percentage . '% of fees to join this class',
                    'required_percentage' => $this->required_payment_percentage,
                    'current_percentage' => $paymentPercentage
                ];
            }
        }

        // Check branch type
        if ($this->branches()->count() > 0) {
            $hasOnlineBranch = $this->branches()->where('name', 'online')->exists();
            $studentBranchIds = $this->branches()->pluck('branches.id')->toArray();
            
            // If student is not in any of the selected branches
            if (!in_array($student->branch_id, $studentBranchIds)) {
                return ['allowed' => false, 'reason' => 'You are not enrolled in any of the branches for this class'];
            }
            
            // For non-online branches, require approval
            if (!$hasOnlineBranch && $this->require_student_approval) {
                $existingRequest = $this->requests()
                    ->where('user_id', $student->id)
                    ->where('user_type', 'student')
                    ->first();
                
                if (!$existingRequest || $existingRequest->status !== 'approved') {
                    return ['allowed' => false, 'reason' => 'You need approval to join this class', 'requires_approval' => true];
                }
            }
        }

        // Check device restriction
        if ($this->device_restriction) {
            $existingSession = $this->participants()
                ->where('user_id', $student->id)
                ->where('user_type', 'student')
                ->where('is_active', true)
                ->whereNull('left_at') // Only check if they haven't properly left
                ->first();
            
            if ($existingSession) {
                return ['allowed' => false, 'reason' => 'You are already joined from another device'];
            }
        }

        // Check if student has left and can rejoin
        $previousSession = $this->participants()
            ->where('user_id', $student->id)
            ->where('user_type', 'student')
            ->whereNotNull('left_at')
            ->first();
            
        if ($previousSession) {
            // Student has left before, they can rejoin
            return ['allowed' => true, 'can_rejoin' => true];
        }

        // Check maximum participants
        if ($this->max_participants) {
            $currentParticipants = $this->participants()->where('is_active', true)->count();
            if ($currentParticipants >= $this->max_participants) {
                return ['allowed' => false, 'reason' => 'Class is full'];
            }
        }

        return ['allowed' => true];
    }

    public function canPublicUserJoin($email)
    {
        // Check if class allows public access
        if (!in_array($this->access_type, ['public', 'both'])) {
            return ['allowed' => false, 'reason' => 'This class is not open to the public'];
        }

        // Check if class is active
        if ($this->status !== 'active') {
            return ['allowed' => false, 'reason' => 'Class is not active'];
        }

        // Check if public requests are allowed
        if (!$this->allow_public_requests) {
            return ['allowed' => false, 'reason' => 'Public access is not enabled for this class'];
        }

        // Check if payment is required
        if ($this->public_fee > 0) {
            $payment = $this->publicPayments()
                ->where('email', $email)
                ->where('status', 'completed')
                ->first();
            
            if (!$payment) {
                return [
                    'allowed' => false, 
                    'reason' => 'Payment required to join this class',
                    'requires_payment' => true,
                    'fee' => $this->public_fee,
                    'currency' => $this->currency
                ];
            }
        }

        // Check device restriction
        if ($this->device_restriction) {
            $existingSession = $this->participants()
                ->where('email', $email)
                ->where('user_type', 'public')
                ->where('is_active', true)
                ->first();
            
            if ($existingSession) {
                return ['allowed' => false, 'reason' => 'This email is already joined from another device'];
            }
        }

        // Check maximum participants
        if ($this->max_participants) {
            $currentParticipants = $this->participants()->where('is_active', true)->count();
            if ($currentParticipants >= $this->max_participants) {
                return ['allowed' => false, 'reason' => 'Class is full'];
            }
        }

        return ['allowed' => true];
    }

    public function getStudentPaymentPercentage($student)
    {
        $totalFees = \App\Models\SchoolFee::where('academic_year_id', $student->academic_year_id)
            ->sum('amount');
        
        $paidFees = \App\Models\StudentTransaction::where('student_id', $student->id)
            ->where('status', 'successful')
            ->sum('amount');

        if ($totalFees <= 0) return 100;

        return min(100, ($paidFees / $totalFees) * 100);
    }

    public function getActiveParticipantsCount()
    {
        return $this->participants()->where('is_active', true)->count();
    }

    public function isFull()
    {
        return $this->max_participants && $this->getActiveParticipantsCount() >= $this->max_participants;
    }
}
