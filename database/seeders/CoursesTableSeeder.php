<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;

class CoursesTableSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['course_name' => 'Tourism', 'abbreviation' => 'TOU', 'course_type' => 'RTB Trade', 'registration_fee' => 50000, 'tuition_fee' => 200000, 'practical_fee' => 50000, 'trip_fee' => 100000],
            ['course_name' => 'Food and Beverage', 'abbreviation' => 'FBO', 'course_type' => 'RTB Trade', 'registration_fee' => 45000, 'tuition_fee' => 180000, 'practical_fee' => 60000, 'trip_fee' => 80000],
            ['course_name' => 'MPC', 'abbreviation' => 'MPC', 'course_type' => 'REB Combination', 'registration_fee' => 40000, 'tuition_fee' => 150000, 'practical_fee' => 30000, 'trip_fee' => 50000],
            ['course_name' => 'MCB', 'abbreviation' => 'MCB', 'course_type' => 'REB Combination', 'registration_fee' => 40000, 'tuition_fee' => 150000, 'practical_fee' => 30000, 'trip_fee' => 50000],
            ['course_name' => 'Networking And Internet Technologies', 'abbreviation' => 'NIT', 'course_type' => 'RTB Trade', 'registration_fee' => 60000, 'tuition_fee' => 250000, 'practical_fee' => 80000, 'trip_fee' => 120000],
        ];

        foreach ($courses as $index => $course) {
            $courseCode = $course['abbreviation'] . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Course::create([
                'course_code' => $courseCode,
                'course_name' => $course['course_name'],
                'abbreviation' => $course['abbreviation'],
                'course_type' => $course['course_type'],
                'registration_fee' => $course['registration_fee'],
                'tuition_fee' => $course['tuition_fee'],
                'practical_fee' => $course['practical_fee'],
                'trip_fee' => $course['trip_fee'],
                'discount_fee' => 0,
            ]);
        }
    }
}
