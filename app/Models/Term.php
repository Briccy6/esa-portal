<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'term',
        'starting_date',
        'ending_date',
    ];

    protected $casts = [
        'starting_date' => 'date',
        'ending_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

  
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schoolFees()
    {
        return $this->hasMany(SchoolFee::class);
    }
}
