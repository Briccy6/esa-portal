<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolFeeSegment;

class SchoolFee extends Model
{
    protected $fillable = ['academic_year_id','term_id', 'amount'];
  public function student()
    {
        return $this->belongsTo(Student::class);
    }

  
     public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }
}
