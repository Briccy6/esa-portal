<?php
/**
 * Check Mathematics marks for KWIZERA Emmanuel
 * 
 * Run in tinker: include 'debug_math_marks.php';
 */

echo "===== DEBUGGING MATHEMATICS MARKS =====\n\n";

// Find the student
$student = \App\Models\Student::where('full_name', 'like', '%KWIZERA%')->first();
if (!$student) {
    echo "Student KWIZERA not found\n";
    exit;
}

echo "Student: {$student->full_name}\n";

// Find Mathematics lesson
$mathLesson = \App\Models\Lesson::where('title', 'like', '%MATHEMATICS%')->first();
if (!$mathLesson) {
    echo "Mathematics lesson not found\n";
    exit;
}

echo "Lesson: {$mathLesson->title} (ID: {$mathLesson->id})\n\n";

// Get latest report card for this student
$reportCard = \App\Models\ReportCard::where('student_id', $student->id)
    ->orderByDesc('created_at')
    ->first();

if (!$reportCard) {
    echo "No report card found\n";
    exit;
}

echo "Report Card ID: {$reportCard->id}\n";
echo "Term: {$reportCard->term->name}\n\n";

// Check raw marks in marks table
echo "===== RAW MARKS IN TABLE =====\n";
$allMarks = \App\Models\Mark::where('student_id', $student->id)
    ->where('lesson_id', $mathLesson->id)
    ->where('term_id', $reportCard->term_id)
    ->get();

if ($allMarks->isEmpty()) {
    echo "No marks found\n";
} else {
    $testMarks = $allMarks->where('assessment_type', 'test');
    $examMarks = $allMarks->where('assessment_type', 'exam');
    
    echo "TEST Marks:\n";
    if ($testMarks->isEmpty()) {
        echo "  None\n";
    } else {
        foreach ($testMarks as $mark) {
            echo "  ID {$mark->id}: {$mark->value} / {$mark->marks_out_of}\n";
        }
    }
    
    echo "\nEXAM Marks:\n";
    if ($examMarks->isEmpty()) {
        echo "  None\n";
    } else {
        foreach ($examMarks as $mark) {
            echo "  ID {$mark->id}: {$mark->value} / {$mark->marks_out_of}\n";
        }
    }
}

// Check what's stored in report_card_subjects
echo "\n===== STORED IN REPORT_CARD_SUBJECTS =====\n";
$reportCardSubject = \App\Models\ReportCardSubject::where('report_card_id', $reportCard->id)
    ->where('lesson_id', $mathLesson->id)
    ->first();

if ($reportCardSubject) {
    echo "Found report card subject ID: {$reportCardSubject->id}\n";
    echo "  Subject Name: {$reportCardSubject->subject_name}\n";
    echo "  Test Average (stored): {$reportCardSubject->test_average}\n";
    echo "  Exam Mark (stored): {$reportCardSubject->exam_mark}\n";
    echo "  Total Mark (stored): {$reportCardSubject->total_mark}\n\n";
    
    // Check test marks records
    echo "===== TEST MARKS IN REPORT_CARD_TEST_MARKS =====\n";
    $testMarkRecords = \App\Models\ReportCardTestMark::where('report_card_subject_id', $reportCardSubject->id)
        ->get();
    
    if ($testMarkRecords->isEmpty()) {
        echo "No test mark records\n";
    } else {
        foreach ($testMarkRecords as $tm) {
            echo "Test: {$tm->test_name}\n";
            echo "  Original: {$tm->original_mark} / {$tm->max_mark}\n";
            echo "  Normalized: {$tm->normalized_mark}\n";
        }
    }
} else {
    echo "No report card subject found for Mathematics\n";
}

echo "\n===== CALCULATION CHECK =====\n";
$testMarksFromDB = \App\Models\Mark::where('student_id', $student->id)
    ->where('lesson_id', $mathLesson->id)
    ->where('term_id', $reportCard->term_id)
    ->where('assessment_type', 'test')
    ->get();

$examMarksFromDB = \App\Models\Mark::where('student_id', $student->id)
    ->where('lesson_id', $mathLesson->id)
    ->where('term_id', $reportCard->term_id)
    ->where('assessment_type', 'exam')
    ->get();

if (!$testMarksFromDB->isEmpty()) {
    $totalTestValue = $testMarksFromDB->sum('value');
    $totalTestOutOf = $testMarksFromDB->sum('marks_out_of');
    $calculatedTestAvg = ($totalTestValue / $totalTestOutOf) * 100;
    echo "Test Average CALCULATED: ($totalTestValue / $totalTestOutOf) * 100 = {$calculatedTestAvg}\n";
} else {
    echo "No test marks found\n";
}

if (!$examMarksFromDB->isEmpty()) {
    $totalExamValue = $examMarksFromDB->sum('value');
    $totalExamOutOf = $examMarksFromDB->sum('marks_out_of');
    $calculatedExamMark = ($totalExamValue / $totalExamOutOf) * 100;
    echo "Exam Mark CALCULATED: ($totalExamValue / $totalExamOutOf) * 100 = {$calculatedExamMark}\n";
} else {
    echo "No exam marks found\n";
}
