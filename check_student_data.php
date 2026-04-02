<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = App\Models\Student::find(270);

echo "Student ID: " . $student->id . "\n";
echo "Student Name: " . $student->full_name . "\n";
echo "Course ID: " . $student->course_id . "\n";
echo "Branch ID: " . $student->branch_id . "\n";
echo "Academic Year ID: " . $student->academic_year_id . "\n";

// Check course
if ($student->course) {
    echo "Course Name: " . $student->course->name . "\n";
} else {
    echo "Course: NOT FOUND\n";
}

// Check branch
if ($student->branch) {
    echo "Branch Name: " . $student->branch->name . "\n";
} else {
    echo "Branch: NOT FOUND\n";
}

// Check academic year
if ($student->academicYear) {
    echo "Academic Year: " . $student->academicYear->name . "\n";
} else {
    echo "Academic Year: NOT FOUND\n";
}

// Check with eager loading
$studentWithRelations = App\Models\Student::with(['course', 'branch', 'academicYear'])->find(270);
echo "\n--- With Eager Loading ---\n";
if ($studentWithRelations->course) {
    echo "Course Name: " . $studentWithRelations->course->name . "\n";
} else {
    echo "Course: NOT FOUND (with eager loading)\n";
}
