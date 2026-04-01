<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Courses...\n";

$courses = DB::table('courses')->get();

echo "Total courses: " . $courses->count() . "\n";

if ($courses->count() > 0) {
    foreach ($courses as $course) {
        echo "ID: {$course->id}, Name: {$course->course_name}\n";
    }
} else {
    echo "No courses found. Creating a default course...\n";
    DB::table('courses')->insert([
        'course_name' => 'General Course',
        'description' => 'Default course for testing',
        'academic_year_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Created default course with ID: 1\n";
}
