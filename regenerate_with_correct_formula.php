<?php
/**
 * Regenerate all report cards with corrected test average calculation
 * 
 * Run in tinker: include 'regenerate_with_correct_formula.php';
 */

echo "===== REGENERATING REPORT CARDS WITH CORRECTED TEST AVERAGE FORMULA =====\n\n";

$reportCards = \App\Models\ReportCard::with('subjects.testMarks')
    ->latest('created_at')
    ->get();

if ($reportCards->isEmpty()) {
    echo "No report cards found\n";
    exit;
}

echo "Found {$reportCards->count()} report card(s) to regenerate\n\n";

$fixed = 0;

foreach ($reportCards as $reportCard) {
    foreach ($reportCard->subjects as $subject) {
        echo "Processing: {$reportCard->student->full_name} - {$subject->subject_name}\n";
        
        // Recalculate test average with corrected formula
        $testMarks = $subject->testMarks;
        
        if ($testMarks->isEmpty()) {
            $oldTestAvg = $subject->test_average;
            $subject->test_average = 0;
            $subject->test_total = 0;
            echo "  No test marks\n";
        } else {
            // Normalize each test mark to 100, then average them
            $normalizedMarks = [];
            $totalOriginalMarks = 0;
            
            foreach ($testMarks as $mark) {
                $totalOriginalMarks += $mark->original_mark;
                if ($mark->max_mark > 0) {
                    $normalized = ($mark->original_mark / $mark->max_mark) * 100;
                    $normalizedMarks[] = $normalized;
                }
            }
            
            $oldTestAvg = $subject->test_average;
            $subject->test_total = $totalOriginalMarks;
            
            if (!empty($normalizedMarks)) {
                $subject->test_average = array_sum($normalizedMarks) / count($normalizedMarks);
                $subject->test_average = min($subject->test_average, 100);
            } else {
                $subject->test_average = 0;
            }
            
            $testCount = count($normalizedMarks);
            echo "  Tests: {$testCount}, Old Average: {$oldTestAvg}, New Average: {$subject->test_average}\n";
        }
        
        // Recalculate total mark
        $oldTotal = $subject->total_mark;
        if ($subject->exam_mark > 0 && $subject->test_average > 0) {
            $subject->total_mark = ($subject->exam_mark + $subject->test_average) / 2;
        } elseif ($subject->exam_mark > 0) {
            $subject->total_mark = $subject->exam_mark;
        } elseif ($subject->test_average > 0) {
            $subject->total_mark = $subject->test_average;
        } else {
            $subject->total_mark = 0;
        }
        $subject->total_mark = min($subject->total_mark, 100);
        
        echo "  Total: {$oldTotal} → {$subject->total_mark}\n";
        
        $subject->save();
        $fixed++;
        echo "  ✓ Saved\n\n";
    }
}

echo "✓ Fixed {$fixed} report card subjects with corrected test average formula!\n";
