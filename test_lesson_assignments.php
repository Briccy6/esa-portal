<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Test lesson assignments
$lesson = \App\Models\Lesson::with(['branchCourses' => function($query) {
    $query->with(['branch', 'course', 'teacher']);
}])->find(1);

if ($lesson) {
    echo "Lesson: " . $lesson->title . "\n";
    echo "Branch Courses count: " . $lesson->branchCourses->count() . "\n";
    foreach ($lesson->branchCourses as $bc) {
        echo "  - Branch: " . ($bc->branch ? $bc->branch->name : 'N/A') . 
             ", Course: " . ($bc->course ? $bc->course->course_name : 'N/A') . 
             ", Teacher: " . ($bc->teacher ? $bc->teacher->teacher_name : 'N/A') . "\n";
    }
} else {
    echo "No lesson found with ID 1\n";
}

// Test assignments table
echo "\n--- All LessonBranchCourse records ---\n";
$allAssignments = \App\Models\LessonBranchCourse::with(['branch', 'course', 'teacher', 'lesson'])->get();
foreach ($allAssignments as $assignment) {
    echo "Lesson: " . ($assignment->lesson ? $assignment->lesson->title : 'N/A') . 
         " | Branch: " . ($assignment->branch ? $assignment->branch->name : 'N/A') . 
         " | Course: " . ($assignment->course ? $assignment->course->course_name : 'N/A') . 
         " | Teacher: " . ($assignment->teacher ? $assignment->teacher->teacher_name : 'N/A') . "\n";
}
