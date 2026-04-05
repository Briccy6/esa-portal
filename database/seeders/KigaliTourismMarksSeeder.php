<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Student;
use App\Models\Lesson;
use App\Models\Exam;
use App\Models\Mark;
use App\Models\ReportCard;
use App\Models\ReportCardSubject;
use App\Models\ReportCardTestMark;

class KigaliTourismMarksSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure an academic year exists
        $year = AcademicYear::firstOrCreate(
            ['year' => '2025-2026'],
            [
                'starting_date' => '2025-09-01',
                'expiry_date' => '2026-08-31',
                'is_ongoing' => true,
            ]
        );

        // All terms for this year (e.g. from TermsSeeder) get demo marks so multi-term reports are complete
        $terms = Term::query()
            ->where('academic_year_id', $year->id)
            ->orderBy('start_date')
            ->orderBy('id')
            ->get();

        if ($terms->isEmpty()) {
            $terms = collect([
                Term::firstOrCreate(
                    ['academic_year_id' => $year->id, 'name' => 'Term 1'],
                    ['start_date' => '2025-09-01', 'end_date' => '2025-12-31']
                ),
                Term::firstOrCreate(
                    ['academic_year_id' => $year->id, 'name' => 'Term 2'],
                    ['start_date' => '2026-01-01', 'end_date' => '2026-04-30']
                ),
                Term::firstOrCreate(
                    ['academic_year_id' => $year->id, 'name' => 'Term 3'],
                    ['start_date' => '2026-05-01', 'end_date' => '2026-08-31']
                ),
            ]);
        }

        // Ensure Kigali branch exists
        $branch = Branch::firstOrCreate(
            ['name' => 'Kigali'],
            ['location' => 'Kigali']
        );

        // Ensure Tourism course exists and is linked to the academic year
        $course = Course::firstOrCreate(
            ['course_name' => 'Tourism'],
            [
                'abbreviation' => 'TOU',
                'course_type' => 'RTB Trade',
                'academic_year_id' => $year->id,
            ]
        );

        // Demo student in Kigali Tourism
        $student = Student::firstOrCreate(
            ['reg_number' => 'KT001'],
            [
                'full_name' => 'Demo Kigali Tourism Student',
                'email' => 'kigali.tourism@example.com',
                'phone' => '0788000000',
                'branch_id' => $branch->id,
                'course_id' => $course->id,
                'academic_year_id' => $year->id,
                'password' => Hash::make('password'),
            ]
        );

        // Create a few Tourism lessons
        $lessonTitles = [
            'Introduction to Tourism',
            'Hospitality Operations',
            'Tour Guiding Techniques',
        ];

        $lessons = [];
        foreach ($lessonTitles as $title) {
            $lessons[] = Lesson::firstOrCreate(
                [
                    'course_id' => $course->id,
                    'title' => $title,
                ],
                [
                    'lesson_weight' => 1,
                    'max_test_marks' => 20,
                    'max_exam_marks' => 100,
                ]
            );
        }

        foreach ($terms as $index => $term) {
            $baseTest = min(95, 65 + ($index * 5));
            $baseExam = min(100, 85 + ($index * 2));
            $this->seedMarksForTerm($term, $year, $branch, $course, $student, $lessons, $baseTest, $baseExam);
        }
    }

    protected function seedMarksForTerm(
        Term $term,
        AcademicYear $year,
        Branch $branch,
        Course $course,
        Student $student,
        array $lessons,
        int $baseTest,
        int $baseExam
    ): void {
        // Create or find a report card for this term
        $reportCard = ReportCard::firstOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $year->id,
                'term_id' => $term->id,
                'course_id' => $course->id,
            ],
            [
                'status' => 'approved',
            ]
        );

        foreach ($lessons as $index => $lesson) {
            // One main exam per lesson for this term
            $exam = Exam::firstOrCreate(
                [
                    'title' => "{$lesson->title} Final Exam ({$term->name})",
                    'exam_type' => 'exam',
                    'academic_year_id' => $year->id,
                    'term_id' => $term->id,
                ],
                [
                    'total_marks' => 100,
                    'is_active' => true,
                    'class_date' => now()->addDays($index * 7),
                    'exam_password' => 'exam_' . strtolower(str_replace(' ', '', $lesson->title)) . '_' . strtolower(str_replace(' ', '', $term->name)),
                    'exam_weight' => 100,
                ]
            );

            $exam->courses()->syncWithoutDetaching([$course->id]);
            $exam->lessons()->syncWithoutDetaching([$lesson->id]);

            // A couple of tests per lesson for this term
            $test1 = Exam::firstOrCreate(
                [
                    'title' => "{$lesson->title} Test 1 ({$term->name})",
                    'exam_type' => 'test',
                    'academic_year_id' => $year->id,
                    'term_id' => $term->id,
                ],
                [
                    'total_marks' => 20,
                    'is_active' => true,
                    'class_date' => now()->addDays($index * 7 + 1),
                    'exam_password' => 'test1_' . strtolower(str_replace(' ', '', $lesson->title)) . '_' . strtolower(str_replace(' ', '', $term->name)),
                    'exam_weight' => 20,
                ]
            );
            $test1->courses()->syncWithoutDetaching([$course->id]);
            $test1->lessons()->syncWithoutDetaching([$lesson->id]);

            $test2 = Exam::firstOrCreate(
                [
                    'title' => "{$lesson->title} Test 2 ({$term->name})",
                    'exam_type' => 'test',
                    'academic_year_id' => $year->id,
                    'term_id' => $term->id,
                ],
                [
                    'total_marks' => 20,
                    'is_active' => true,
                    'class_date' => now()->addDays($index * 7 + 2),
                    'exam_password' => 'test2_' . strtolower(str_replace(' ', '', $lesson->title)) . '_' . strtolower(str_replace(' ', '', $term->name)),
                    'exam_weight' => 20,
                ]
            );
            $test2->courses()->syncWithoutDetaching([$course->id]);
            $test2->lessons()->syncWithoutDetaching([$lesson->id]);

            // Create marks (stored as raw values; normalization happens in report logic)
            $examScore = min(100, $baseExam + ($index * 2)); // slight variation
            // lesson_id, term_id, assessment_type required for ReportCardController + marks screens
            Mark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'branch_id' => $branch->id,
                    'exam_id' => $exam->id,
                ],
                [
                    'lesson_id' => $lesson->id,
                    'term_id' => $term->id,
                    'assessment_type' => 'exam',
                    'marks_out_of' => 100,
                    'value' => $examScore,
                    'status' => 'recorded',
                ]
            );

            $test1Score = min(20, (int) round($baseTest / 2));
            $test2Score = min(20, (int) round(($baseTest + 10) / 2));

            Mark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'branch_id' => $branch->id,
                    'exam_id' => $test1->id,
                ],
                [
                    'lesson_id' => $lesson->id,
                    'term_id' => $term->id,
                    'assessment_type' => 'test',
                    'marks_out_of' => 20,
                    'value' => $test1Score,
                    'status' => 'recorded',
                ]
            );

            Mark::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'branch_id' => $branch->id,
                    'exam_id' => $test2->id,
                ],
                [
                    'lesson_id' => $lesson->id,
                    'term_id' => $term->id,
                    'assessment_type' => 'test',
                    'marks_out_of' => 20,
                    'value' => $test2Score,
                    'status' => 'recorded',
                ]
            );
        }

        // Build report card subjects and totals based on Marks for this term
        $this->generateReportCardData($reportCard);
    }

    /**
     * Copy of the core aggregation logic from ReportCardController::generateReportCardData
     * so that seeded marks are converted into report card subjects for previews.
     */
    protected function generateReportCardData(ReportCard $reportCard): void
    {
        // Clear any existing subjects for idempotency when re-running the seeder
        $reportCard->subjects()->each(function (ReportCardSubject $subject) {
            $subject->testMarks()->delete();
            $subject->delete();
        });

        // Get lessons for this student's branch and course from lesson_branch_course table
        $lessons = \App\Models\LessonBranchCourse::where('course_id', $reportCard->course_id)
            ->where('branch_id', $reportCard->student->branch_id)
            ->with('lesson')
            ->get()
            ->pluck('lesson');

        $totalMarks = 0;
        $subjectCount = 0;

        foreach ($lessons as $lesson) {
            // Exam marks for this lesson and term
            $examMarks = Mark::where('student_id', $reportCard->student_id)
                ->whereHas('exam', function ($query) use ($lesson, $reportCard) {
                    $query->where('exam_type', 'exam')
                        ->where('academic_year_id', $reportCard->academic_year_id)
                        ->where('term_id', $reportCard->term_id);
                })
                ->whereHas('exam.lessons', function ($query) use ($lesson) {
                    $query->where('lessons.id', $lesson->id);
                })
                ->with('exam')
                ->get();

            $examMark = 0;
            if ($examMarks->isNotEmpty()) {
                // Take only the first exam mark and normalize to 100
                $firstExamMark = $examMarks->first();
                $totalExamMarks = $firstExamMark->exam->total_marks ?? 100;
                
                if ($totalExamMarks > 0) {
                    $examMark = ($firstExamMark->value / $totalExamMarks) * 100;
                }
            }

            // Test marks for this lesson and term
            $testMarks = Mark::where('student_id', $reportCard->student_id)
                ->whereHas('exam', function ($query) use ($lesson, $reportCard) {
                    $query->where('exam_type', 'test')
                        ->where('academic_year_id', $reportCard->academic_year_id)
                        ->where('term_id', $reportCard->term_id);
                })
                ->whereHas('exam.lessons', function ($query) use ($lesson) {
                    $query->where('lessons.id', $lesson->id);
                })
                ->with('exam')
                ->get();

            if ($lesson) {
                $reportCardSubject = ReportCardSubject::create([
                    'report_card_id' => $reportCard->id,
                    'lesson_id' => $lesson->id,
                    'subject_name' => $lesson->title ?? 'Unknown Subject',
                    'exam_mark' => $examMark,
                ]);

                foreach ($testMarks as $mark) {
                    $maxMark = $mark->exam->total_marks ?? 100;
                    $normalizedMark = $maxMark > 0 ? ($mark->value / $maxMark) * 100 : 0;

                    ReportCardTestMark::create([
                        'report_card_subject_id' => $reportCardSubject->id,
                        'exam_id' => $mark->exam_id,
                        'test_name' => $mark->exam->title ?? 'Test',
                        'original_mark' => $mark->value,
                        'max_mark' => $maxMark,
                        'normalized_mark' => $normalizedMark,
                        'weight' => 1.0,
                    ]);
                }

                $reportCardSubject->calculateTestAverage();
                $reportCardSubject->calculateTotalMark();

                $totalMarks += $reportCardSubject->total_mark;
                $subjectCount++;
            }
        }

        if ($subjectCount > 0) {
            $reportCard->total_marks = $totalMarks;
            $reportCard->average_marks = $totalMarks / $subjectCount;
            $reportCard->save();

            ReportCard::recalculatePositionsForCohort(
                (int) $reportCard->course_id,
                (int) $reportCard->academic_year_id,
                (int) $reportCard->term_id
            );
        }
    }
}

