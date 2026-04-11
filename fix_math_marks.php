<?php
/**
 * Fix Mathematics marks calculation
 * 
 * Run in tinker: include 'fix_math_marks.php';
 */

echo "===== FIXING MATHEMATICS MARKS =====\n\n";

// Find the Math lesson
$mathLesson = \App\Models\Lesson::where('title', 'like', '%MATHEMATICS%')->first();
if (!$mathLesson) {
    echo "Mathematics lesson not found\n";
    exit;
}

echo "Found lesson: {$mathLesson->title} (ID: {$mathLesson->id})\n\n";

// Find all report card subjects for Mathematics
$mathSubjects = \App\Models\ReportCardSubject::where('lesson_id', $mathLesson->id)
    ->where('subject_name', 'like', '%MATHEMATICS%')
    ->with(['reportCard.student', 'reportCard.term', 'testMarks'])
    ->get();

if ($mathSubjects->isEmpty()) {
    echo "No Mathematics report card subjects found\n";
    exit;
}

echo "Found {$mathSubjects->count()} Mathematics report card subjects to fix:\n\n";

$fixed = 0;

foreach ($mathSubjects as $subject) {
    echo "Fixing: {$subject->reportCard->student->full_name} - {$subject->subject_name}\n";
    echo "  Term: {$subject->reportCard->term->name}\n";
    echo "  Current values: Test Ave={$subject->test_average}, Exam={$subject->exam_mark}\n";
    
    // Get all raw marks
    $testMarksRaw = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
        ->where('lesson_id', $subject->lesson_id)
        ->where('term_id', $subject->reportCard->term_id)
        ->where('assessment_type', 'test')
        ->get();
    
    $examMarksRaw = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
        ->where('lesson_id', $subject->lesson_id)
        ->where('term_id', $subject->reportCard->term_id)
        ->where('assessment_type', 'exam')
        ->get();
    
    // Calculate test average
    $newTestAverage = 0;
    if ($testMarksRaw->isNotEmpty()) {
        $totalTestValue = $testMarksRaw->sum('value');
        $totalTestOutOf = $testMarksRaw->sum(function($m) {
            return ($m->marks_out_of && $m->marks_out_of > 0) ? $m->marks_out_of : 100;
        });
        
        if ($totalTestOutOf > 0) {
            $newTestAverage = ($totalTestValue / $totalTestOutOf) * 100;
            $newTestAverage = min($newTestAverage, 100);
        }
        echo "  Test Calc: ($totalTestValue / $totalTestOutOf) * 100 = {$newTestAverage}\n";
    } else {
        echo "  Test Marks: None found\n";
    }
    
    // Calculate exam mark
    $newExamMark = 0;
    if ($examMarksRaw->isNotEmpty()) {
        $totalExamValue = $examMarksRaw->sum('value');
        $totalExamOutOf = $examMarksRaw->sum(function($m) {
            return ($m->marks_out_of && $m->marks_out_of > 0) ? $m->marks_out_of : 100;
        });
        
        if ($totalExamOutOf > 0) {
            $newExamMark = ($totalExamValue / $totalExamOutOf) * 100;
            $newExamMark = min($newExamMark, 100);
        }
        echo "  Exam Calc: ($totalExamValue / $totalExamOutOf) * 100 = {$newExamMark}\n";
    } else {
        echo "  Exam Marks: None found\n";
    }
    
    // Calculate total (average of test and exam)
    $newTotalMark = ($newTestAverage + $newExamMark) / 2;
    echo "  Total (AVG): ({$newTestAverage} + {$newExamMark}) / 2 = {$newTotalMark}\n";
    
    // Update if changed
    if ($newTestAverage != $subject->test_average || $newExamMark != $subject->exam_mark) {
        $subject->test_average = $newTestAverage;
        $subject->exam_mark = $newExamMark;
        $subject->total_mark = $newTotalMark;
        $subject->save();
        echo "  ✓ UPDATED\n";
        $fixed++;
    } else {
        echo "  ✓ Already correct\n";
    }
    echo "\n";
}

echo "✓ Fixed {$fixed} Mathematics subjects!\n";
