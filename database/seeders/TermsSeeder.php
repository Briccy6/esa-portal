<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get the current academic year
        $academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
        
        if (!$academicYear) {
            echo "No ongoing academic year found. Please run AcademicYearsSeeder first.\n";
            return;
        }

        DB::table('terms')->insert([
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'Term 1',
                'start_date' => '2025-09-01',
                'end_date' => '2025-12-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'Term 2',
                'start_date' => '2026-01-05',
                'end_date' => '2026-04-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'academic_year_id' => $academicYear->id,
                'name' => 'Term 3',
                'start_date' => '2026-04-20',
                'end_date' => '2026-08-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        echo "Terms seeded successfully for academic year {$academicYear->year}\n";
    }
}
