<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolFeeSegment;

class SchoolFee extends Model
{
    protected $fillable = ['academic_year_id','term_id', 'amount'];

    /**
     * SchoolFee is related to AcademicYear and Term only
     * NOT directly to Student - use CourseTermFee for course/student specific fees
     */
    
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
