<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Mark;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Teacher;
use App\Models\Branch;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'description', 'exam_type', 'class_date', 'exam_weight', 'total_marks', 
        'academic_year_id', 'teacher_id', 'term_id', 'notes', 'exam_password', 'is_active',
        'token_cost', 'is_public', 'public_price', 'start_time', 'end_time', 'allow_public_after_end',
    ];

    protected $casts = [
        'class_date' => 'date',
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'allow_public_after_end' => 'boolean',
        'public_price' => 'decimal:2',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_exam');
    }

    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_exam');
    }

    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function settings()
    {
        return $this->hasOne(ExamSettings::class);
    }

    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class);
    }

    public function analytics()
    {
        return $this->hasMany(ExamAnalytics::class);
    }

    public function academic_year()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function examTokens()
    {
        return $this->hasMany(ExamToken::class);
    }

    /**
     * Token and access related methods
     */
    public function isAccessible()
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        // Check if exam is within active time window
        if ($this->start_time && $this->start_time->isFuture()) {
            return false;
        }

        if ($this->end_time && $this->end_time->isPast() && !$this->allow_public_after_end) {
            return false;
        }

        return true;
    }

    public function isPubliclyAccessible()
    {
        return $this->is_public && $this->isAccessible();
    }

    public function requiresTokens()
    {
        return $this->token_cost > 0;
    }

    public function requiresPayment()
    {
        return $this->is_public && $this->public_price > 0;
    }

    public function canBeAttemptedByStudent($student)
    {
        if (!$this->isAccessible()) {
            return [
                'allowed' => false,
                'reason' => 'Exam is not currently accessible',
                'action' => 'wait'
            ];
        }

        // If student is registered and has sufficient tokens or active subscription
        if ($student && $student->canAttemptExam($this)) {
            return [
                'allowed' => true,
                'reason' => 'Access granted',
                'action' => 'attempt'
            ];
        }

        // If exam is public and free
        if ($this->is_public && !$this->requiresPayment()) {
            return [
                'allowed' => true,
                'reason' => 'Public free exam',
                'action' => 'attempt'
            ];
        }

        // If exam is public but requires payment
        if ($this->is_public && $this->requiresPayment()) {
            return [
                'allowed' => false,
                'reason' => 'Payment required',
                'action' => 'pay',
                'amount' => $this->public_price
            ];
        }

        // If student exists but doesn't have enough tokens
        if ($student && $this->requiresTokens()) {
            return [
                'allowed' => false,
                'reason' => 'Insufficient tokens',
                'action' => 'buy_tokens',
                'required' => $this->token_cost,
                'has' => $student->token_balance
            ];
        }

        return [
            'allowed' => false,
            'reason' => 'Access denied',
            'action' => 'register'
        ];
    }

    public function getFormattedTokenCostAttribute()
    {
        return $this->token_cost . ' tokens';
    }

    public function getFormattedPublicPriceAttribute()
    {
        return '$' . number_format($this->public_price, 2);
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        $now = now();

        if ($this->start_time && $this->start_time->isFuture()) {
            return 'Upcoming';
        }

        if ($this->end_time && $this->end_time->isPast()) {
            return $this->allow_public_after_end ? 'Public Access' : 'Ended';
        }

        return 'Active';
    }
}
