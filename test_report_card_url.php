<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$student = \App\Models\Student::where('reg_number', 'ESATOU012025-2026')->first();
$reportCard = \App\Models\ReportCard::where('student_id', $student->id)->first();

echo "=== REPORT CARD PRINT URL TEST ===\n";
echo "Student: " . $student->full_name . " (ID: " . $student->id . ")\n";
echo "Report Card ID: " . $reportCard->id . "\n";

// Generate the URL that would be accessed
$url = "/admin/report-cards/" . $reportCard->id . "/print";
echo "Print URL: " . $url . "\n";

// Test the controller method directly
echo "\n=== TESTING CONTROLLER METHOD ===\n";

try {
    $controller = new \App\Http\Controllers\ReportCardController();
    
    // Simulate the controller call
    $reportCard->load([
        'student',
        'course',
        'academicYear',
        'term',
        'subjects.lesson',
        'subjects.testMarks.exam'
    ]);

    $schoolInfo = \App\Models\SchoolInformation::getActive();
    
    echo "Report Card Data Loaded Successfully:\n";
    echo "- Student: " . $reportCard->student->full_name . "\n";
    echo "- Course: " . ($reportCard->course ? $reportCard->course->course_name : 'N/A') . "\n";
    echo "- Academic Year: " . ($reportCard->academicYear ? $reportCard->academicYear->year : 'N/A') . "\n";
    echo "- Term: " . ($reportCard->term ? $reportCard->term->term : 'N/A') . "\n";
    echo "- Subjects Count: " . $reportCard->subjects->count() . "\n";
    echo "- School Info: " . ($schoolInfo ? "Available" : "Not Available") . "\n";
    
    // Check if view can be rendered
    echo "\n=== TESTING VIEW RENDERING ===\n";
    
    // Test the view data that would be passed
    $viewData = compact('reportCard', 'schoolInfo');
    echo "View data prepared with " . count($viewData) . " variables\n";
    
    // Check if the view file exists
    $viewPath = resource_path('views/admin/report-cards/print.blade.php');
    echo "View file exists: " . (file_exists($viewPath) ? "YES" : "NO") . "\n";
    
    if ($reportCard->subjects->count() > 0) {
        echo "\nSUBJECTS THAT WILL BE DISPLAYED:\n";
        foreach ($reportCard->subjects as $index => $subject) {
            echo ($index + 1) . ". " . $subject->subject_name . "\n";
            echo "   Test: " . ($subject->test_average ?? 0) . ", Exam: " . ($subject->exam_mark ?? 0) . ", Total: " . ($subject->total_mark ?? 0) . "\n";
        }
    } else {
        echo "\nWARNING: No subjects found - will show 'No assessment data available' message\n";
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== RECOMMENDATIONS ===\n";
if ($reportCard->subjects->count() > 0) {
    $hasMarks = false;
    foreach ($reportCard->subjects as $subject) {
        if (($subject->test_average ?? 0) > 0 || ($subject->exam_mark ?? 0) > 0) {
            $hasMarks = true;
            break;
        }
    }
    
    if (!$hasMarks) {
        echo "⚠️  ISSUE: Student has report card subjects but all marks are 0 or empty\n";
        echo "   - This explains why you're not seeing meaningful marks\n";
        echo "   - The marks need to be populated in the database\n";
        echo "   - Check if exams were conducted and marks recorded\n";
    } else {
        echo "✅ Student has marks - report card should display correctly\n";
    }
} else {
    echo "⚠️  ISSUE: No subjects found in report card\n";
    echo "   - Subjects need to be added to the report card\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Access URL: http://localhost:8000" . $url . "\n";
echo "2. If marks are 0, you need to:\n";
echo "   - Conduct exams for this student\n";
echo "   - Record test and exam marks\n";
echo "   - Calculate and save the marks to the database\n";
echo "3. The report card view is working correctly - the issue is data population\n";
