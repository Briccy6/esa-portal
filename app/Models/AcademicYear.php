<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Resource;
use App\Models\Teacher;
use Carbon\Carbon;
use Carbon\CarbonInterface;
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

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'academic_year_teacher', 'academic_year_id', 'teacher_id');
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'academic_year_branch', 'academic_year_id', 'branch_id');
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'academic_year_resource', 'academic_year_id', 'resource_id');
    }

    public function scopeActive($query, $date = null)
    {
        return $query->ongoing($date);
    }

    public function resolveStatus($date = null): string
    {
        $today = $date instanceof CarbonInterface
            ? Carbon::parse($date)
            : ($date ? Carbon::parse($date) : Carbon::today());

        if ($this->starting_date && $today->lt(Carbon::parse($this->starting_date))) {
            return 'upcoming';
        }

        if ($this->expiry_date && $today->gt(Carbon::parse($this->expiry_date))) {
            return 'ended';
        }

        return 'active';
    }

    public function getStatusAttribute(): string
    {
        return $this->resolveStatus();
    }

    public function getIsOngoingAttribute($value): bool
    {
        return $this->resolveStatus() === 'active';
    }

    public function scopeOngoing($query, $date = null)
    {
        $today = $date instanceof CarbonInterface
            ? Carbon::parse($date)
            : ($date ? Carbon::parse($date) : Carbon::today());

        return $query
            ->whereDate('starting_date', '<=', $today->toDateString())
            ->whereDate('expiry_date', '>=', $today->toDateString());
    }

    public static function ensureSingleOngoing(int $activeId): void
    {
        $activeYear = static::find($activeId);

        if (! $activeYear) {
            return;
        }

        $activeYear->update(['is_ongoing' => $activeYear->resolveStatus() === 'active']);
        static::where('id', '!=', $activeId)->update(['is_ongoing' => false]);
    }
}
