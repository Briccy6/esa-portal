<?php

namespace Database\Seeders;

use App\Models\Term;
use Illuminate\Database\Seeder;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();

        if (! $academicYear) {
            echo "No ongoing academic year found. Please run AcademicYearsSeeder first.\n";

            return;
        }

        $definitions = [
            ['name' => 'Term 1', 'start_date' => '2025-09-01', 'end_date' => '2025-12-15'],
            ['name' => 'Term 2', 'start_date' => '2026-01-05', 'end_date' => '2026-04-10'],
            ['name' => 'Term 3', 'start_date' => '2026-04-20', 'end_date' => '2026-08-15'],
        ];

        foreach ($definitions as $def) {
            Term::firstOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'name' => $def['name'],
                ],
                [
                    'start_date' => $def['start_date'],
                    'end_date' => $def['end_date'],
                ]
            );
        }

        echo "Terms seeded successfully for academic year {$academicYear->year}\n";
    }
}
