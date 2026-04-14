<?php
/**
 * Diagnostic script to find lessons with incorrect 100 marks
 * 
 * Usage in Tinker:
 * include 'find_incorrect_100_marks.php';
 */

echo "===== Finding Report Card Subjects with Test/Exam = 100 =====\n\n";

// Find all report card subjects with both test_average and exam_mark at 100
$problematicSubjects = \App\Models\ReportCardSubject::where('test_average', 100)
    ->where('exam_mark', 100)
    ->with(['reportCard.student', 'reportCard.term', 'lesson'])
    ->latest('created_at')
    ->limit(10)
    ->get();

if ($problematicSubjects->isEmpty()) {
    echo "No subjects found with both test_average and exam_mark = 100\n\n";
} else {
    echo "Found " . $problematicSubjects->count() . " problematic subjects:\n\n";
    
    foreach ($problematicSubjects as $subject) {
        echo "Report Card Subject ID: {$subject->id}\n";
        echo "  Student: {$subject->reportCard->student->full_name}\n";
        echo "  Lesson: {$subject->lesson->title}\n";
        echo "  Term: {$subject->reportCard->term->name}\n";
        echo "  Test Average (DB): {$subject->test_average}\n";
        echo "  Exam Mark (DB): {$subject->exam_mark}\n";
        echo "  Total Mark (DB): {$subject->total_mark}\n";
        echo "\n";
        
        // Check raw marks in marks table
        echo "  Raw marks in database:\n";
        
        $testMarks = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
            ->where('lesson_id', $subject->lesson_id)
            ->where('term_id', $subject->reportCard->term_id)
            ->where('assessment_type', 'test')
            ->get();
        
        if ($testMarks->isEmpty()) {
            echo "    TEST: No test marks found\n";
        } else {
            echo "    TEST marks: ";
            foreach ($testMarks as $mark) {
                echo "{$mark->value}/{$mark->marks_out_of} ";
            }
            echo "\n";
        }
        
        $examMarks = \App\Models\Mark::where('student_id', $subject->reportCard->student_id)
            ->where('lesson_id', $subject->lesson_id)
            ->where('term_id', $subject->reportCard->term_id)
            ->where('assessment_type', 'exam')
            ->get();
        
        if ($examMarks->isEmpty()) {
            echo "    EXAM: No exam marks found\n";
        } else {
            echo "    EXAM marks: ";
            foreach ($examMarks as $mark) {
                echo "{$mark->value}/{$mark->marks_out_of} ";
            }
            echo "\n";
        }
        echo "\n";
    }
}
