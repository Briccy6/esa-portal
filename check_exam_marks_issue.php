<?php
/**
 * Diagnostic script to check exam marks for a student
 * Run: php artisan tinker < check_exam_marks_issue.php
 * Or manually in tinker:
 */

// Find the student (KWIZERA Emmanuel or whoever you're checking)
$student = \App\Models\Student::where('full_name', 'like', '%KWIZERA%')->first();
// OR: $student = \App\Models\Student::find(438); // specific student ID

if (!$student) {
    echo "Student not found\n";
    exit;
}

echo "===== STUDENT: {$student->full_name} =====\n";
echo "ID: {$student->id}, Course: {$student->course_id}, Branch: {$student->branch_id}\n\n";

// Get the most recent report card
$reportCard = \App\Models\ReportCard::where('student_id', $student->id)
    ->orderByDesc('created_at')
    ->first();

if (!$reportCard) {
    echo "No report card found\n";
    exit;
}

echo "===== REPORT CARD =====\n";
echo "ID: {$reportCard->id}\n";
echo "Term: {$reportCard->term->name}\n";
echo "Created: {$reportCard->created_at}\n\n";

// Check all raw marks for this student
$allMarks = \App\Models\Mark::where('student_id', $student->id)
    ->where('term_id', $reportCard->term_id)
    ->with(['lesson', 'exam'])
    ->get();

echo "===== ALL MARKS FOR TERM =====\n";
foreach ($allMarks as $mark) {
    echo "Lesson: {$mark->lesson->title}\n";
    echo "  Type: {$mark->assessment_type}\n";
    echo "  Value: {$mark->value}/{$mark->marks_out_of}\n";
    if ($mark->exam) {
        echo "  Exam Total Marks: {$mark->exam->total_marks}\n";
    }
    echo "\n";
}

// Check report card subjects
echo "===== REPORT CARD SUBJECTS =====\n";
foreach ($reportCard->subjects as $subject) {
    echo "Subject: {$subject->subject_name}\n";
    echo "  Exam Mark (stored): {$subject->exam_mark}\n";
    echo "  Test Average: {$subject->test_average}\n";
    echo "  Total Mark: {$subject->total_mark}\n";
    
    // Check raw marks for this lesson
    $lessonMarks = \App\Models\Mark::where('student_id', $student->id)
        ->where('lesson_id', $subject->lesson_id)
        ->where('term_id', $reportCard->term_id)
        ->get();
    
    $examMarks = $lessonMarks->where('assessment_type', 'exam');
    if ($examMarks->isNotEmpty()) {
        echo "  Raw Exam Marks:\n";
        foreach ($examMarks as $em) {
            echo "    Value: {$em->value}, Out Of: {$em->marks_out_of}\n";
        }
    } else {
        echo "  No exam marks found in database\n";
    }
    echo "\n";
}
