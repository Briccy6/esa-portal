<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AcademicYear;
use Carbon\Carbon;

class UpdateAcademicYearStatus extends Command
{
    protected $signature = 'academic_year:update-status';
    protected $description = 'Update the is_ongoing status of academic years based on current date';

    public function handle()
    {
        $today = Carbon::today();

        AcademicYear::all()->each(function ($year) use ($today) {
            $isOngoing = $today->between($year->start_date, $year->end_date);
            $year->update(['is_ongoing' => $isOngoing]);
        });

        $this->info('Academic years status updated successfully!');
    }
}
