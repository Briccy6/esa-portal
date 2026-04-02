<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the letter generation with the improved logic
$student = App\Models\Student::find(270);

echo "Testing improved course name handling:\n\n";

// Simulate the generateEnrollmentContent logic
$courseName = 'Not Specified';
$branchName = 'Not Specified';
$academicYear = 'Not Specified';

// Get course name with fallback
if ($student->course_id) {
    if ($student->course && !empty($student->course->name)) {
        $courseName = $student->course->name;
    } else {
        $courseName = "Course ID: {$student->course_id} (Name not available)";
    }
}

// Get branch name with fallback
if ($student->branch_id) {
    if ($student->branch && !empty($student->branch->name)) {
        $branchName = $student->branch->name;
    } else {
        $branchName = "Branch ID: {$student->branch_id} (Name not available)";
    }
}

// Get academic year with fallback
if ($student->academic_year_id) {
    if ($student->academicYear && !empty($student->academicYear->name)) {
        $academicYear = $student->academicYear->name;
    } else {
        $academicYear = "Academic Year ID: {$student->academic_year_id} (Name not available)";
    }
}

echo "Student: {$student->full_name}\n";
echo "Course: {$courseName}\n";
echo "Branch: {$branchName}\n";
echo "Academic Year: {$academicYear}\n";
echo "Registration Number: {$student->reg_number}\n";

// Test the letter content
$content = "This letter is to certify that {$student->full_name} is currently enrolled at our institution.

Student Details:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}
- ID Number: " . ($student->id_number ?: 'Not Provided') . "

The student was admitted on " . $student->created_at->format('F j, Y') . " and is currently in good academic standing.";

echo "\n--- Sample Letter Content ---\n";
echo $content;
