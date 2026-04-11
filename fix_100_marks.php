<?php
/**
 * Fix script to recalculate incorrect 100 marks for a specific subject
 * 
 * Finds subjects with test_average=100 and exam_mark=100, then recalculates
 * them from the actual raw marks in the marks table.
 */

echo "===== Fixing Report Card Subjects with incorrect 100 marks =====\n\n";

// Find problematic subjects
$problematicSubjects = \App\Models\ReportCardSubject::where('test_average', 100)
    ->where('exam_mark', 100)
    ->with(['reportCard.student', 'reportCard.term', 'lesson', 'testMarks'])
    ->get();

if ($problematicSubjects->isEmpty()) {
    echo "✓ No problematic subjects found\n";
    exit;
}

echo "Found " . $problematicSubjects->count() . " subjects to fix:\n\n";

$fixed = 0;

foreach ($problematicSubjects as $subject) {
    echo "Fixing Subject ID {$subject->id}: {$subject->lesson->title}\n";
    echo "  Student: {$subject->reportCard->student->full_name}\n";
    
    // Get raw marks
    $testMarks = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
        ->where('lesson_id', $subject->lesson_id)
        ->where('term_id', $subject->reportCard->term_id)
        ->where('assessment_type', 'test')
        ->get();
    
    $examMarks = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
        ->where('lesson_id', $subject->lesson_id)
        ->where('term_id', $subject->reportCard->term_id)
        ->where('assessment_type', 'exam')
        ->get();
    
    // Recalculate test average
    $newTestAverage = 0;
    if ($testMarks->isNotEmpty()) {
        $totalTestValue = $testMarks->sum('value');
        $totalTestOutOf = $testMarks->sum(function($mark) {
            $mofo = ($mark->marks_out_of && $mark->marks_out_of > 0) ? $mark->marks_out_of : 100;
            if ($mofo < $mark->value) {
                $mofo = 100;
            }
            return $mofo;
        });
        
        if ($totalTestOutOf > 0) {
            $newTestAverage = ($totalTestValue / $totalTestOutOf) * 100;
        }
        $newTestAverage = min($newTestAverage, 100);
    }
    
    // Recalculate exam mark
    $newExamMark = 0;
    if ($examMarks->isNotEmpty()) {
        $totalExamValue = $examMarks->sum('value');
        $totalExamOutOf = $examMarks->sum(function($mark) {
            $mofo = ($mark->marks_out_of && $mark->marks_out_of > 0) ? $mark->marks_out_of : 100;
            if ($mofo < $mark->value) {
                $mofo = 100;
            }
            return $mofo;
        });
        
        if ($totalExamOutOf > 0) {
            $newExamMark = ($totalExamValue / $totalExamOutOf) * 100;
        }
        $newExamMark = min($newExamMark, 100);
    }
    
    echo "  OLD: Test={$subject->test_average}, Exam={$subject->exam_mark}\n";
    echo "  NEW: Test={$newTestAverage}, Exam={$newExamMark}\n";
    
    // Update if changed
    if ($newTestAverage != $subject->test_average || $newExamMark != $subject->exam_mark) {
        $subject->test_average = $newTestAverage;
        $subject->exam_mark = $newExamMark;
        $subject->calculateTotalMark();
        echo "  ✓ FIXED\n";
        $fixed++;
    } else {
        echo "  ✓ Already correct\n";
    }
    echo "\n";
}

echo "✓ Fixed {$fixed} subjects!\n";
