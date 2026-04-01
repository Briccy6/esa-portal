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
        
        // Truncate tables to avoid duplicates
        DB::table('teachers')->truncate();
        DB::table('branches')->truncate();
        DB::table('courses')->truncate();
        DB::table('academic_years')->truncate();
        DB::table('terms')->truncate();
        
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
            ['teacher_name' => 'John Doe', 'email' => 'john.doe@esa.rw', 'telephone' => '07881234567', 'teacher_id_number' => 'TID001', 'id_number' => 'ID001', 'photo' => '', 'department' => 'All', 'level_of_education' => 'Masters in Education', 'branch_id' => 1, 'salary' => 500000],
            ['teacher_name' => 'Jane Smith', 'email' => 'jane.smith@esa.rw', 'telephone' => '07881234568', 'teacher_id_number' => 'TID002', 'id_number' => 'ID002', 'photo' => '', 'department' => 'All', 'level_of_education' => 'Bachelors in Education', 'branch_id' => 1, 'salary' => 450000],
            ['teacher_name' => 'Robert Johnson', 'email' => 'robert.johnson@esa.rw', 'telephone' => '07881234569', 'teacher_id_number' => 'TID003', 'id_number' => 'ID003', 'photo' => '', 'department' => 'Training', 'level_of_education' => 'Masters in Computer Science', 'branch_id' => 2, 'salary' => 600000],
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
            ['year' => 2026, 'starting_date' => '2026-01-01', 'expiry_date' => '2026-12-31', 'is_ongoing' => 1],
            ['year' => 2025, 'starting_date' => '2025-01-01', 'expiry_date' => '2025-12-31', 'is_ongoing' => 0],
            ['year' => 2024, 'starting_date' => '2024-01-01', 'expiry_date' => '2024-12-31', 'is_ongoing' => 0],
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
