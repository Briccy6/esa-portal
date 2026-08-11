<?php

namespace App\Models;

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
