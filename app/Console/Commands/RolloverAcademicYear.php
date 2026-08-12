<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RolloverAcademicYear extends Command
{
    protected $signature = 'academic-year:rollover {year} {starting_date} {expiry_date}';

    protected $description = 'Create a new academic year and copy existing course, teacher, branch, and resource associations to it.';

    public function handle(): int
    {
        $year = $this->argument('year');
        $startingDate = $this->argument('starting_date');
        $expiryDate = $this->argument('expiry_date');

        $this->info('Creating new academic year...');

        $validated = $this->validateDates($startingDate, $expiryDate);
        if (! $validated) {
            return self::FAILURE;
        }

        if (AcademicYear::where('year', $year)->exists()) {
            $this->error('Academic year already exists.');
            return self::FAILURE;
        }

        if ($this->hasOverlappingRange($startingDate, $expiryDate)) {
            $this->error('Academic year dates overlap with an existing academic year.');
            return self::FAILURE;
        }

        $newYear = AcademicYear::create([
            'year' => $year,
            'starting_date' => $startingDate,
            'expiry_date' => $expiryDate,
            'is_ongoing' => false,
        ]);

        $this->copyExistingAssociations($newYear);

        if ($newYear->resolveStatus() === 'active') {
            AcademicYear::ensureSingleOngoing($newYear->id);
        }

        $this->info('Academic year created and associations rolled over successfully.');
        return self::SUCCESS;
    }

    protected function validateDates(string $start, string $end): bool
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $end)) {
            $this->error('Dates must be in YYYY-MM-DD format.');
            return false;
        }

        if ($start > $end) {
            $this->error('Starting date must be before or equal to expiry date.');
            return false;
        }

        return true;
    }

    protected function hasOverlappingRange(string $start, string $end): bool
    {
        return AcademicYear::where(function ($query) use ($start, $end) {
            $query->whereBetween('starting_date', [$start, $end])
                ->orWhereBetween('expiry_date', [$start, $end])
                ->orWhere(function ($query) use ($start, $end) {
                    $query->where('starting_date', '<=', $start)
                          ->where('expiry_date', '>=', $end);
                });
        })->exists();
    }

    protected function copyExistingAssociations(AcademicYear $newYear): void
    {
        $courses = $this->getDistinctIds('courses', 'id');
        $teachers = $this->getDistinctIds('teacher_course', 'teacher_id');
        $branches = $this->getDistinctIds('branch_course', 'branch_id');
        $resources = $this->getDistinctIds('resources', 'id');

        foreach ($courses as $courseId) {
            DB::table('academic_year_course')->updateOrInsert([
                'academic_year_id' => $newYear->id,
                'course_id' => $courseId,
            ]);
        }

        if ($teachers->isNotEmpty()) {
            foreach ($teachers as $teacherId) {
                DB::table('academic_year_teacher')->updateOrInsert([
                    'academic_year_id' => $newYear->id,
                    'teacher_id' => $teacherId,
                ]);
            }
        } else {
            $this->info('No teacher associations copied because teacher_course pivot table is unavailable or empty.');
        }

        if ($branches->isNotEmpty()) {
            foreach ($branches as $branchId) {
                DB::table('academic_year_branch')->updateOrInsert([
                    'academic_year_id' => $newYear->id,
                    'branch_id' => $branchId,
                ]);
            }
        } else {
            $this->info('No branch associations copied because branch_course pivot table is unavailable or empty.');
        }

        foreach ($resources as $resourceId) {
            DB::table('academic_year_resource')->updateOrInsert([
                'academic_year_id' => $newYear->id,
                'resource_id' => $resourceId,
            ]);
        }
    }

    protected function getDistinctIds(string $table, string $column)
    {
        if (! Schema::hasTable($table)) {
            return collect();
        }

        return DB::table($table)->distinct()->pluck($column);
    }
}
