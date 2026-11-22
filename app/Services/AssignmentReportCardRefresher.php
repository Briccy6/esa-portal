<?php

namespace App\Services;

use App\Http\Controllers\ReportCardController;
use App\Models\LessonAssignment;
use App\Models\ReportCard;
use App\Models\Student;

class AssignmentReportCardRefresher
{
    public function refresh(LessonAssignment $assignment, Student $student): void
    {
        $reportCards = ReportCard::where('student_id', $student->id)
            ->where('course_id', $student->course_id)
            ->where('term_id', $assignment->term_id)
            ->get();

        foreach ($reportCards as $reportCard) {
            foreach ($reportCard->subjects as $subject) {
                $subject->testMarks()->delete();
                $subject->delete();
            }

            app(ReportCardController::class)->generateReportCardData($reportCard);
        }
    }
}
