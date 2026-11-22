<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTermBranchFee extends Model
{
    protected $table = 'course_term_branch_fees';

    protected $fillable = [
        'course_id', 'term_id', 'branch_id',
        'registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee', 'student_card_fee', 'is_active',
        'is_practical_mandatory', 'is_trip_mandatory', 'practical_due_date', 'trip_due_date'
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'practical_fee' => 'decimal:2',
        'trip_fee' => 'decimal:2',
        'student_card_fee' => 'decimal:2',
        'is_active' => 'boolean',
        'is_practical_mandatory' => 'boolean',
        'is_trip_mandatory' => 'boolean',
        'practical_due_date' => 'date',
        'trip_due_date' => 'date',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function getTotalFeesAttribute()
    {
        return $this->registration_fee + $this->tuition_fee + $this->practical_fee + $this->trip_fee + $this->student_card_fee;
    }
}
