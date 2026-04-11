<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportCard;
use App\Models\Mark;

class RecalculateExamMarks extends Command
{
    protected $signature = 'report-cards:recalculate-exam-marks {--student_id=} {--report_card_id=}';
    protected $description = 'Recalculate exam marks in report card subjects from raw marks';

    public function handle()
    {
        $this->info('Recalculating exam marks...');

        $query = ReportCard::query();
        
        if ($this->option('report_card_id')) {
            $query->where('id', $this->option('report_card_id'));
            $this->info('Filtering by report_card_id: ' . $this->option('report_card_id'));
        }
        
        if ($this->option('student_id')) {
            $query->where('student_id', $this->option('student_id'));
            $this->info('Filtering by student_id: ' . $this->option('student_id'));
        }

        $reportCards = $query->get();
        $this->info("Found {$reportCards->count()} report card(s) to process");

        foreach ($reportCards as $reportCard) {
            $this->line("Processing Report Card ID: {$reportCard->id}, Student: {$reportCard->student->full_name}");

            foreach ($reportCard->subjects as $subject) {
                // Get all exam marks for this lesson and term from marks table
                $examMarks = Mark::where('student_id', $reportCard->student_id)
                    ->where('lesson_id', $subject->lesson_id)
                    ->where('term_id', $reportCard->term_id)
                    ->where('assessment_type', 'exam')
                    ->get();

                if ($examMarks->isEmpty()) {
                    $newExamMark = 0;
                    $this->line("  {$subject->subject_name}: No exam marks found → setting to 0");
                } else {
                    // Sum all exam values
                    $totalExamValue = $examMarks->sum('value');
                    
                    // Sum all marks_out_of, using 100 as default if not set
                    $totalExamOutOf = $examMarks->sum(function($mark) {
                        if ($mark->marks_out_of && $mark->marks_out_of > 0) {
                            return $mark->marks_out_of;
                        }
                        $examTotalMarks = $mark->exam ? $mark->exam->total_marks : 100;
                        return $examTotalMarks > 0 ? $examTotalMarks : 100;
                    });

                    // Normalize to 100-point scale
                    $newExamMark = $totalExamOutOf > 0 ? ($totalExamValue / $totalExamOutOf) * 100 : 0;
                    
                    // Cap at 100
                    $newExamMark = min($newExamMark, 100);

                    $this->line("  {$subject->subject_name}: {$totalExamValue}/{$totalExamOutOf} → {$newExamMark}");
                    
                    // Show raw marks breakdown
                    foreach ($examMarks as $mark) {
                        $mofo = $mark->marks_out_of && $mark->marks_out_of > 0 ? $mark->marks_out_of : 
                                ($mark->exam ? $mark->exam->total_marks : 100);
                        $this->line("    - {$mark->value}/{$mofo}");
                    }
                }

                // Update if changed
                if ($subject->exam_mark != $newExamMark) {
                    $old = $subject->exam_mark;
                    $subject->exam_mark = $newExamMark;
                    $subject->calculateTotalMark();
                    $this->line("    ✓ Updated from {$old} to {$newExamMark}");
                }
            }
        }

        $this->info('✓ Recalculation complete!');
    }
}
