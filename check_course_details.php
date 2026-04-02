<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check if course ID 18 exists
$course = App\Models\Course::find(18);
if ($course) {
    echo "Course ID 18 found:\n";
    echo "Name: '" . $course->name . "'\n";
    echo "Code: '" . $course->code . "'\n";
    echo "Description: '" . $course->description . "'\n";
} else {
    echo "Course ID 18 NOT FOUND\n";
}

// List all courses
echo "\n--- All Courses ---\n";
$courses = App\Models\Course::all();
foreach ($courses as $course) {
    echo "ID: {$course->id}, Name: '{$course->name}', Code: '{$course->code}'\n";
}
