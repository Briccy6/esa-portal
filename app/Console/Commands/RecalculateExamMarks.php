<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ReportCard;
use App\Models\Mark;
use App\Support\AssessmentMarkMath;

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
                    ->with('exam')
                    ->get();

                if ($examMarks->isEmpty()) {
                    $newExamMark = 0;
                    $this->line("  {$subject->subject_name}: No exam marks found → setting to 0");
                } else {
                    $newExamMark = AssessmentMarkMath::aggregatedExamPercentFromMarks($examMarks);
                    $this->line("  {$subject->subject_name}: aggregated exam % → {$newExamMark}");
                    foreach ($examMarks as $mark) {
                        $mofo = $mark->marks_out_of && $mark->marks_out_of > 0 ? $mark->marks_out_of :
                            ($mark->exam ? $mark->exam->total_marks : 100);
                        $this->line("    - {$mark->value}/{$mofo}");
                    }
                }

                $oldExam = (float) $subject->exam_mark;
                $subject->exam_mark = $newExamMark;
                $subject->calculateTestAverage();
                $subject->calculateTotalMark();
                $this->line("    ✓ Exam {$oldExam} → {$newExamMark}, test_avg {$subject->test_average}, total_mark {$subject->total_mark}");
            }

            $reportCard->refresh();
            $reportCard->load('subjects');
            if ($reportCard->subjects->isNotEmpty()) {
                $reportCard->total_marks = $reportCard->subjects->sum('total_mark');
                $reportCard->average_marks = (float) $reportCard->subjects->avg('total_mark');
                $reportCard->save();
            }
        }

        $this->info('✓ Recalculation complete!');
    }
}
