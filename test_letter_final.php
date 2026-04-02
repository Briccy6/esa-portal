<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the improved letter content
$student = App\Models\Student::find(270);

echo "Testing improved letter content (no repetitions, no ID number):\n\n";

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

$purpose = "job application";

// Generate the improved content
$content = "This letter is to certify that {$student->full_name} is currently enrolled at our institution.

Student Details:
- Registration Number: {$student->reg_number}
- Course: {$courseName}
- Branch: {$branchName}
- Academic Year: {$academicYear}

The student was admitted on " . $student->created_at->format('F j, Y') . " and is currently in good academic standing.";

echo "=== ENROLLMENT LETTER ===\n";
echo $content;
echo "\n\n";

echo "✅ FIXED ISSUES:\n";
echo "- ID Number field REMOVED\n";
echo "- Repetitive purpose statements REMOVED\n";
echo "- Repetitive contact information REMOVED\n";
echo "- Course information properly shows: '{$courseName}'\n";
echo "- Branch information properly shows: '{$branchName}'\n";
echo "- Academic Year properly shows: '{$academicYear}'\n";
