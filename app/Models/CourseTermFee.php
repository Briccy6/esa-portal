<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTermFee extends Model
{
    protected $fillable = [
        'course_id',
        'term_id',
        'registration_fee',
        'tuition_fee',
        'practical_fee',
        'trip_fee',
        'student_card_fee',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'registration_fee' => 'decimal:2',
        'tuition_fee' => 'decimal:2',
        'practical_fee' => 'decimal:2',
        'trip_fee' => 'decimal:2',
        'student_card_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function getTotalFeesAttribute()
    {
        return $this->registration_fee + $this->tuition_fee + $this->practical_fee + $this->trip_fee + $this->student_card_fee;
    }

    public function getRequiredFeesAttribute()
    {
        return [
            'registration' => [
                'amount' => $this->registration_fee,
                'name' => 'Registration Fee',
                'required' => $this->registration_fee > 0
            ],
            'tuition' => [
                'amount' => $this->tuition_fee,
                'name' => 'Tuition Fee',
                'required' => $this->tuition_fee > 0
            ],
            'practical' => [
                'amount' => $this->practical_fee,
                'name' => 'Practical Fee',
                'required' => $this->practical_fee > 0
            ],
            'trip' => [
                'amount' => $this->trip_fee,
                'name' => 'Trip Fee',
                'required' => $this->trip_fee > 0
            ],
            'student_card' => [
                'amount' => $this->student_card_fee,
                'name' => 'Student Card',
                'required' => $this->student_card_fee > 0
            ],
        ];
    }
}
