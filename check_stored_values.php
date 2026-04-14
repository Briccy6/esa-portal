<?php
/**
 * Check what values are actually stored for Mathematics
 * 
 * Run in tinker: include 'check_stored_values.php';
 */

echo "===== CHECKING STORED VALUES FOR MATHEMATICS =====\n\n";

// Find Mathematics subject in report cards
$mathSubjects = \App\Models\ReportCardSubject::whereHas('lesson', function($q) {
    $q->where('title', 'like', '%MATHEMATICS%');
})
->with(['reportCard.student', 'reportCard.term', 'lesson'])
->latest('created_at')
->limit(5)
->get();

if ($mathSubjects->isEmpty()) {
    echo "No Mathematics subjects found\n";
    exit;
}

foreach ($mathSubjects as $subject) {
    echo "================================\n";
    echo "Student: {$subject->reportCard->student->full_name}\n";
    echo "Subject: {$subject->subject_name}\n";
    echo "Term: {$subject->reportCard->term->name}\n";
    echo "Subject ID: {$subject->id}\n\n";
    
    echo "STORED VALUES IN DATABASE:\n";
    echo "  test_average: {$subject->test_average}\n";
    echo "  exam_mark: {$subject->exam_mark}\n";
    echo "  total_mark: {$subject->total_mark}\n\n";
    
    echo "CALCULATION CHECK:\n";
    $calc = ($subject->test_average + $subject->exam_mark) / 2;
    echo "  (test_average + exam_mark) / 2 = ({$subject->test_average} + {$subject->exam_mark}) / 2 = {$calc}\n";
    
    if (abs($calc - $subject->total_mark) > 0.1) {
        echo "  ⚠ WARNING: total_mark ({$subject->total_mark}) ≠ calculated average ({$calc})\n";
    } else {
        echo "  ✓ total_mark matches calculated average\n";
    }
    echo "\n";
}
