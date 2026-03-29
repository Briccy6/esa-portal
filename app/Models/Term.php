<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
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

    /**
     * Legacy alias: older views used `term` while the column is `name`.
     */
    public function getTermAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }
}
