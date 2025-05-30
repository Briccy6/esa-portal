<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcademicYearsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('academic_years')->insert([
            'year' => '2025-2026',
            'starting_date' => '2025-09-01',
            'expiry_date' => '2026-08-31',
            'is_ongoing' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
