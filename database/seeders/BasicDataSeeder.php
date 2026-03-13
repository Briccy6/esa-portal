<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Teacher;
use App\Models\Branch;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class BasicDataSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Create basic courses
        $courses = [
            ['course_code' => 'MATH001', 'course_name' => 'Mathematics', 'abbreviation' => 'MATH', 'course_type' => 'Core', 'registration_fee' => 50000, 'tuition_fee' => 200000, 'practical_fee' => 0, 'trip_fee' => 0, 'discount_fee' => 0],
            ['course_code' => 'ENG001', 'course_name' => 'English Language', 'abbreviation' => 'ENG', 'course_type' => 'Core', 'registration_fee' => 50000, 'tuition_fee' => 200000, 'practical_fee' => 0, 'trip_fee' => 0, 'discount_fee' => 0],
            ['course_code' => 'SCI001', 'course_name' => 'General Science', 'abbreviation' => 'SCI', 'course_type' => 'Core', 'registration_fee' => 50000, 'tuition_fee' => 200000, 'practical_fee' => 0, 'trip_fee' => 0, 'discount_fee' => 0],
            ['course_code' => 'COMP001', 'course_name' => 'Computer Science', 'abbreviation' => 'COMP', 'course_type' => 'Elective', 'registration_fee' => 50000, 'tuition_fee' => 250000, 'practical_fee' => 0, 'trip_fee' => 0, 'discount_fee' => 0],
        ];

        foreach ($courses as $course) {
            Course::create($course);
        }

        // Create basic branches
        $branches = [
            ['name' => 'Kigali Main Campus', 'location' => 'Kigali, Rwanda'],
            ['name' => 'Kigali Branch 2', 'location' => 'Kigali, Rwanda'],
            ['name' => 'Remera Campus', 'location' => 'Remera, Rwanda'],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }

        // Create basic teachers
        $teachers = [
            ['teacher_name' => 'John Doe', 'email' => 'john.doe@esa.rw', 'phone' => '07881234567', 'address' => 'Kigali, Rwanda', 'qualification' => 'Masters in Education', 'branch_id' => 1],
            ['teacher_name' => 'Jane Smith', 'email' => 'jane.smith@esa.rw', 'phone' => '07881234568', 'address' => 'Kigali, Rwanda', 'qualification' => 'Bachelors in Education', 'branch_id' => 1],
            ['teacher_name' => 'Robert Johnson', 'email' => 'robert.johnson@esa.rw', 'phone' => '07881234569', 'address' => 'Kigali, Rwanda', 'qualification' => 'Masters in Computer Science', 'branch_id' => 2],
        ];

        foreach ($teachers as $teacher) {
            Teacher::create(array_merge($teacher, [
                'password' => Hash::make('password123'),
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Create academic years
        $years = [
            ['year' => 2026, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31'],
            ['year' => 2025, 'start_date' => '2025-01-01', 'end_date' => '2025-12-31'],
            ['year' => 2024, 'start_date' => '2024-01-01', 'end_date' => '2024-12-31'],
        ];

        foreach ($years as $year) {
            AcademicYear::create($year);
        }

        // Create terms
        $terms = [
            ['name' => 'Term 1'],
            ['name' => 'Term 2'],
            ['name' => 'Term 3'],
        ];

        foreach ($terms as $term) {
            Term::create($term);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        $this->command->info('Basic data seeded successfully!');
    }
}
