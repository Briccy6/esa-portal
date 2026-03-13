<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$regNumber = 'ESATOU012025-2026';

echo "=== SEARCHING FOR STUDENT: $regNumber ===\n";

// Find student by registration number
$student = \App\Models\Student::where('reg_number', $regNumber)->first();

if (!$student) {
    echo "Student with registration number '$regNumber' not found.\n";
    
    // Show available students for reference
    echo "\n=== AVAILABLE STUDENTS ===\n";
    $students = \App\Models\Student::limit(5)->get(['id', 'reg_number', 'full_name']);
    foreach ($students as $s) {
        echo "ID: {$s->id}, Reg: {$s->reg_number}, Name: {$s->full_name}\n";
    }
    exit;
}

echo "Student Found:\n";
echo "  ID: " . $student->id . "\n";
echo "  Name: " . $student->full_name . "\n";
echo "  Reg Number: " . $student->reg_number . "\n";
echo "  Course: " . ($student->course ? $student->course->course_name : 'N/A') . "\n";

// Find report cards for this student
echo "\n=== REPORT CARDS FOR THIS STUDENT ===\n";
$reportCards = \App\Models\ReportCard::where('student_id', $student->id)
    ->with(['academicYear', 'term', 'course'])
    ->get();

if ($reportCards->isEmpty()) {
    echo "No report cards found for this student.\n";
} else {
    foreach ($reportCards as $rc) {
        echo "Report Card ID: {$rc->id}\n";
        echo "  Academic Year: " . ($rc->academicYear ? $rc->academicYear->year : 'N/A') . "\n";
        echo "  Term: " . ($rc->term ? $rc->term->term : 'N/A') . "\n";
        echo "  Course: " . ($rc->course ? $rc->course->course_name : 'N/A') . "\n";
        echo "  Average: " . $rc->average_marks . "%\n";
        echo "  Status: " . $rc->status . "\n";
        echo "  Subjects Count: " . $rc->subjects()->count() . "\n";
        echo "\n";
    }
}

// Test the first report card if it exists
$firstReportCard = $reportCards->first();
if ($firstReportCard) {
    echo "=== TESTING FIRST REPORT CARD (ID: {$firstReportCard->id}) ===\n";
    
    // Load with same relationships as the controller
    $firstReportCard->load([
        'student',
        'course',
        'academicYear',
        'term',
        'subjects.lesson',
        'subjects.testMarks.exam'
    ]);
    
    echo "Student: " . $firstReportCard->student->full_name . "\n";
    echo "Subjects Count: " . $firstReportCard->subjects->count() . "\n";
    
    if ($firstReportCard->subjects->count() > 0) {
        echo "\nSUBJECTS AND MARKS:\n";
        echo str_repeat("-", 70) . "\n";
        printf("%-20s %-8s %-8s %-8s %-8s %-8s\n", 
            "Subject", "Test", "Exam", "Total", "Grade", "Result");
        echo str_repeat("-", 70) . "\n";
        
        foreach ($firstReportCard->subjects as $subject) {
            $result = ($subject->grade === 'A+' || $subject->grade === 'A' || $subject->grade === 'B+' || $subject->grade === 'B') ? 'C' : 'NYC';
            printf("%-20s %-8s %-8s %-8s %-8s %-8s\n", 
                substr($subject->subject_name, 0, 18),
                $subject->test_average ?? '-',
                $subject->exam_mark ?? '-',
                $subject->total_mark ?? '-',
                $subject->grade ?? '-',
                $result
            );
        }
    } else {
        echo "No subjects found for this report card.\n";
    }
}
