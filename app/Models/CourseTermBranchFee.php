<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTermBranchFee extends Model
{
    protected $table = 'course_term_branch_fees';

    protected $fillable = [
        'course_id', 'term_id', 'branch_id',
        'registration_fee', 'tuition_fee', 'practical_fee', 'trip_fee', 'student_card_fee', 'is_active'
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
}
