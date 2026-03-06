<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING LIVE CLASS COURSE RELATION ===\n";

$liveClass = \App\Models\LiveClass::find(7);
if ($liveClass) {
    echo "Live Class ID: {$liveClass->id}\n";
    echo "Live Class Title: {$liveClass->title}\n";
    
    // Check if courses relationship exists
    $courses = $liveClass->courses;
    echo "Courses count: " . $courses->count() . "\n";
    
    if ($courses->isNotEmpty()) {
        foreach ($courses as $course) {
            echo "Course ID: {$course->id}, Name: {$course->name}\n";
        }
    } else {
        echo "No courses attached to this live class\n";
    }
    
    // Check the pivot table directly
    echo "\n=== CHECKING PIVOT TABLE ===\n";
    $pivotRecords = \DB::table('course_live_class')
        ->where('live_class_id', 7)
        ->get();
    
    echo "Pivot records count: " . $pivotRecords->count() . "\n";
    foreach ($pivotRecords as $record) {
        echo "Pivot - Course ID: {$record->course_id}, Live Class ID: {$record->live_class_id}\n";
    }
} else {
    echo "Live class not found\n";
}

echo "\n=== CHECKING STUDENT COURSE ===\n";
$student = \App\Models\Student::first();
if ($student) {
    echo "Student ID: {$student->id}\n";
    echo "Student Name: {$student->full_name}\n";
    echo "Student Course ID: {$student->course_id}\n";
    
    if ($student->course) {
        echo "Student Course Name: {$student->course->name}\n";
    }
}
