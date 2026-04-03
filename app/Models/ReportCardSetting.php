<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReportCardSetting extends Model
{
    protected $fillable = [
        'academic_year',
        'term',
        'report_card_available_at',
        'description',
        'is_active'
    ];

    protected $casts = [
        'report_card_available_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Get the active report card setting for the current academic year and term
     */
    public static function getActive()
    {
        // First, try to get setting for configured academic year and term
        $setting = self::where('academic_year', config('app.academic_year', '2025-2026'))
                        ->where('term', config('app.current_term', 'Term 1'))
                        ->where('is_active', true)
                        ->first();
        
        // If not found, get the most recently created active setting
        if (!$setting) {
            $setting = self::where('is_active', true)
                            ->orderBy('created_at', 'desc')
                            ->first();
        }
        
        return $setting;
    }

    /**
     * Check if report cards are currently available
     */
    public function isAvailable()
    {
        return $this->is_active && $this->report_card_available_at->isPast();
    }

    /**
     * Get time remaining until report cards are available
     */
    public function getTimeRemaining()
    {
        if ($this->isAvailable()) {
            return null; // Already available
        }

        return $this->report_card_available_at->diffForHumans(now(), true);
    }

    /**
     * Get countdown data for JavaScript
     */
    public function getCountdownData()
    {
        if ($this->isAvailable()) {
            return null;
        }

        $now = Carbon::now();
        $target = $this->report_card_available_at;
        
        if ($target->isPast()) {
            return null;
        }

        return [
            'months' => $now->diffInMonths($target),
            'weeks' => $now->diffInWeeks($target) % 4,
            'days' => $now->diffInDays($target) % 7,
            'hours' => $now->diffInHours($target) % 24,
            'minutes' => $now->diffInMinutes($target) % 60,
            'seconds' => $now->diffInSeconds($target) % 60,
            'total_seconds' => $now->diffInSeconds($target),
            'target_date' => $target->toISOString()
        ];
    }
}
