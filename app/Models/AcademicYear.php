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
     public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

  
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function scopeOngoing($query)
    {
        return $query->where('is_ongoing', true);
    }

    public static function ensureSingleOngoing(int $activeId): void
    {
        static::where('id', '!=', $activeId)->update(['is_ongoing' => false]);
    }
}
