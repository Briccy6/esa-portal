<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['course_name' => 'Tourism', 'abbreviation' => 'TOU', 'course_type' => 'RTB Trade'],
            ['course_name' => 'Food and Beverage', 'abbreviation' => 'FBO', 'course_type' => 'RTB Trade'],
            ['course_name' => 'MPC', 'abbreviation' => 'MPC', 'course_type' => 'REB Combination'],
            ['course_name' => 'MCB', 'abbreviation' => 'MCB', 'course_type' => 'REB Combination'],
            ['course_name' => 'Networking And Internet Technologies', 'abbreviation' => 'NIT', 'course_type' => 'RTB Trade'],
        ];

        foreach ($courses as $index => $course) {
            $courseCode = $course['abbreviation'] . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Course::create([
                'course_code' => $courseCode,
                'course_name' => $course['course_name'],
                'abbreviation' => $course['abbreviation'],
                'course_type' => $course['course_type'],
            ]);
        }
    }
}
