<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $table = 'academic_years';

    protected $fillable = [
        'year',
        'starting_date',
        'expiry_date',
        'is_ongoing',
    ];

    protected $casts = [
        'starting_date' => 'date',
        'expiry_date' => 'date',
        'is_ongoing' => 'boolean',
    ];
}
