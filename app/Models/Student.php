<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // So students can log in if needed
use Illuminate\Notifications\Notifiable;
use Carbon\CarbonImmutable;
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
        'id_number',
        'identification_type',
        'id_photo',
        'passport_photo',
        'senior6_document',
        'senior3_document',
        'other_supporting_document',
        'nesa_registered',
        'nesa_sdms_code',
        'nesa_reg_number',
        'nesa_document',
        'nesa_registration_date',
        'nesa_remarks',
        'shift',
        'shift_changed_at',
        'is_blocked',
        'block_reason',
        'blocked_at',
        'google_id',
        'google_token',
        'google_refresh_token',
        'google_avatar',
    ];

    protected $casts = [
        'senior6_document' => 'string',
        'senior3_document' => 'string',
        'other_supporting_document' => 'string',
        'id_photo' => 'string',
        'passport_photo' => 'string',
        'nesa_registered' => 'boolean',
        'nesa_registration_date' => 'date',
        'is_blocked' => 'boolean',
        'blocked_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
    ];

    public static function requiredProfileFields(): array
    {
        return [
            'full_name' => 'Full name',
            'phone' => 'Telephone number',
            'email' => 'Email address',
            'sex' => 'Sex',
            'passport_photo' => 'Profile photo',
            'identification_type' => 'Identification type',
            'id_number' => 'Identification number',
            'id_photo' => 'Identification photo',
            'education_level' => 'Education level',
        ];
    }

    public function missingProfileFields(): array
    {
        $missingFields = [];
        foreach (static::requiredProfileFields() as $field => $label) {
            if (blank($this->{$field})) {
                $missingFields[] = $label;
            }
        }

        return $missingFields;
    }

    public function hasCompletedProfile(): bool
    {
        return $this->missingProfileFields() === [];
    }

    public static function profileCompletionDeadline(): CarbonImmutable
    {
        return CarbonImmutable::create(2026, 11, 22, 12, 0, 0, 'Africa/Kigali');
    }

    /**
     * Relationships
     */

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

    /**
     * Get course term fees through the student's course
     * Fees are tied to courses and terms, not directly to students
     */
    public function courseTermFees()
    {
        return $this->hasManyThrough(
            CourseTermFee::class,
            Course::class,
            'id',          // Foreign key on courses table
            'course_id',   // Foreign key on course_term_fees table
            'course_id',   // Local key on students table
            'id'           // Local key on courses table
        );
    }

    public function transcripts()
    {
        return $this->hasMany(Transcript::class);
    }
    
    public function NESARegistration()
    {
        return $this->hasOne(NESARegistration::class);
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

    /**
     * Token related relationships and methods
     */
    public function token()
    {
        return $this->hasOne(Token::class);
    }

    public function tokenTransactions()
    {
        return $this->hasManyThrough(TokenTransaction::class, Token::class);
    }

    public function transactions()
    {
        return $this->hasMany(StudentTransaction::class);
    }

    public function examTokens()
    {
        return $this->hasMany(ExamToken::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(StudentSubscription::class);
    }

    public function activeSubscription()
    {
        return $this->hasOne(StudentSubscription::class)->active()->paid();
    }

    public function studentCard()
    {
        return $this->hasOne(StudentCard::class);
    }

    public function getTokenBalanceAttribute()
    {
        return $this->token ? $this->token->balance : 0;
    }

    public function getFormattedTokenBalanceAttribute()
    {
        return number_format($this->token_balance) . ' tokens';
    }

    public function hasTokens($amount)
    {
        return $this->token_balance >= $amount;
    }

    public function canAttemptExam($exam)
    {
        // Registered students with sufficient tokens
        if ($this->hasTokens($exam->getCalculatedTokenCost())) {
            return true;
        }

        // Check if student has active subscription that covers this exam
        if ($this->activeSubscription) {
            return true;
        }

        return false;
    }

    public function initializeTokens($defaultTokens = 10)
    {
        if (!$this->token) {
            $token = $this->token()->create([
                'balance' => $defaultTokens,
                'total_earned' => $defaultTokens,
            ]);

            $token->addTokens($defaultTokens, 'Welcome tokens for new student', 'registration');

            return $token;
        }

        return $this->token;
    }

    public function purchaseSubscription($subscriptionId, $paymentReference = null)
    {
        $subscription = Subscription::findOrFail($subscriptionId);

        $studentSubscription = $this->subscriptions()->create([
            'subscription_id' => $subscriptionId,
            'amount' => $subscription->price,
            'paid_amount' => $subscription->price,
            'tokens_included' => $subscription->tokens_included,
            'payment_reference' => $paymentReference,
            'payment_status' => $paymentReference ? 'paid' : 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addDays($subscription->duration_days),
            'is_active' => $paymentReference ? true : false,
        ]);

        if ($paymentReference) {
            $this->initializeTokens();
            $this->token->addTokens(
                $subscription->tokens_included,
                "Tokens from {$subscription->name} subscription",
                'subscription'
            );
        }

        return $studentSubscription;
    }

    /**
     * Check if student has an active subscription
     */
    public function hasActiveSubscription()
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->exists();
    }

    /**
     * Get passport photo URL - tries StudentCard first, then Student field
     */
    public function getPassportPhotoUrlAttribute(): ?string
    {
        // First, try to get from StudentCard if it has passport_photo data
        if ($this->relationLoaded('studentCard') && $this->studentCard) {
            $cardPhoto = $this->studentCard->passport_photo;
            if ($cardPhoto) {
                // Check if it's already a data URL or base64
                if (str_starts_with($cardPhoto, 'data:') || str_starts_with($cardPhoto, 'base64,')) {
                    return $cardPhoto;
                }
                // If it's a file path, try to load it as a file
                if (file_exists(public_path($cardPhoto))) {
                    return asset($cardPhoto);
                }
            }
        }

        // Fall back to Student.passport_photo if available
        if ($this->passport_photo) {
            if (file_exists(public_path($this->passport_photo))) {
                return asset($this->passport_photo);
            }
        }

        return null;
    }

    /**
     * Get total outstanding fees for the student
     */
    public function getOutstandingFees()
    {
        if ($this->course) {
            $currentTerm = \App\Models\Term::where('academic_year_id', $this->academic_year_id)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->first();

            if ($currentTerm) {
                $courseTermFee = \App\Models\CourseTermFee::where('course_id', $this->course_id)
                    ->where('term_id', $currentTerm->id)
                    ->where('is_active', true)
                    ->first();

                if ($courseTermFee) {
                    $totalFees = $courseTermFee->total_fees;
                    $paidAmount = $this->transactions()
                        ->where('type', 'school_fee')
                        ->where('status', 'successful')
                        ->whereHas('term', fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
                        ->sum('amount');

                    return max(0, $totalFees - $paidAmount);
                }
            }
        }

        return max(0, $this->outstanding_amount ?? 0);
    }

    /**
     * Clear outstanding fees after successful payment
     */
    public function clearOutstandingFees($amount = null)
    {
        if ($amount === null) {
            $amount = $this->getOutstandingFees();
        }

        // Update direct field if it exists
        if (in_array('outstanding_amount', $this->getFillable())) {
            $this->update([
                'outstanding_amount' => max(0, ($this->outstanding_amount ?? 0) - $amount)
            ]);
        }

        return $this;
    }

    /**
     * Check if student has outstanding fees
     */
    public function hasOutstandingFees()
    {
        return $this->getOutstandingFees() > 0;
    }

    /**
     * Get formatted outstanding fees string
     */
    public function getFormattedOutstandingFeesAttribute()
    {
        $amount = $this->getOutstandingFees();
        return 'RWF ' . number_format($amount, 2);
    }

    /**
     * Check if student has linked Google account
     */
    public function hasGoogleAccount()
    {
        return !empty($this->google_id);
    }

    /**
     * Get Google avatar URL
     */
    public function getGoogleAvatarUrlAttribute()
    {
        return $this->google_avatar;
    }

    /**
     * Link Google account to student
     */
    public function linkGoogleAccount($googleId, $token, $refreshToken = null, $avatar = null)
    {
        $this->update([
            'google_id' => $googleId,
            'google_token' => $token,
            'google_refresh_token' => $refreshToken,
            'google_avatar' => $avatar,
        ]);
    }

    /**
     * Unlink Google account from student
     */
    public function unlinkGoogleAccount()
    {
        $this->update([
            'google_id' => null,
            'google_token' => null,
            'google_refresh_token' => null,
            'google_avatar' => null,
        ]);
    }
}
