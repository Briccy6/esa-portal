<?php

namespace App\Http\Controllers;

use App\Models\ReportCard;
use App\Models\ReportCardSubject;
use App\Models\ReportCardTestMark;
use App\Models\Student;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Mark;
use App\Models\Exam;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\SchoolInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    public function index()
    {
        $reportCards = ReportCard::with(['student', 'course', 'academicYear', 'term'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.report-cards.index', compact('reportCards'));
    }

    public function create()
    {
        $students = Student::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();
        $terms = Term::all();

        return view('admin.report-cards.create', compact('students', 'courses', 'academicYears', 'terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Check if report card already exists
        $existing = ReportCard::where('student_id', $request->student_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('term_id', $request->term_id)
            ->where('course_id', $request->course_id)
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('error', 'Report card already exists for this student, academic year, term, and course combination.');
        }

        $reportCard = ReportCard::create($request->all());
        $this->generateReportCardData($reportCard);

        return redirect()->route('admin.report-cards.show', $reportCard)
            ->with('success', 'Report card generated successfully!');
    }

    public function show(ReportCard $reportCard)
    {
        $reportCard->load([
            'student',
            'course',
            'academicYear',
            'term',
            'subjects.lesson',
            'subjects.testMarks.exam'
        ]);

        $schoolInfo = SchoolInformation::getActive();

        return view('admin.report-cards.show', compact('reportCard', 'schoolInfo'));
    }

    public function edit(ReportCard $reportCard)
    {
        $reportCard->load(['subjects.lesson', 'subjects.testMarks.exam']);
        return view('admin.report-cards.edit', compact('reportCard'));
    }

    public function update(Request $request, ReportCard $reportCard)
    {
        $request->validate([
            'remarks' => 'nullable|string',
            'status' => 'required|in:pending,approved,printed',
        ]);

        if ($request->status === 'approved' && $reportCard->status !== 'approved') {
            $reportCard->approved_by = auth('admin')->id();
            $reportCard->approved_at = now();
            $reportCard->calculateClassPosition();
        }

        $reportCard->update($request->all());

        return redirect()->route('admin.report-cards.show', $reportCard)
            ->with('success', 'Report card updated successfully!');
    }

    public function destroy(ReportCard $reportCard)
    {
        $reportCard->delete();
        return redirect()->route('admin.report-cards.index')
            ->with('success', 'Report card deleted successfully!');
    }

    public function generateForClass(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $students = Student::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($students as $student) {
            $existing = ReportCard::where('student_id', $student->id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('term_id', $request->term_id)
                ->where('course_id', $request->course_id)
                ->first();

            if (!$existing) {
                $reportCard = ReportCard::create([
                    'student_id' => $student->id,
                    'academic_year_id' => $request->academic_year_id,
                    'term_id' => $request->term_id,
                    'course_id' => $request->course_id,
                ]);
                $this->generateReportCardData($reportCard);
                $generated++;
            } else {
                $skipped++;
            }
        }

        return redirect()->back()
            ->with('success', "Generated {$generated} report cards. Skipped {$skipped} that already exist.");
    }

   public function printView(ReportCard $reportCard)
{
    // Load only essential relationships for HTML view
    $reportCard->load([
        'student',
        'course', 
        'academicYear',
        'term',
        'subjects.lesson'
    ]);

    // Get school info without heavy processing
    $schoolInfo = SchoolInformation::getActive();

    return view('admin.report-cards.print', compact('reportCard', 'schoolInfo'));
}

   public function printReport(ReportCard $reportCard)
{
    $reportCard->load([
        'student',
        'course',
        'academicYear',
        'term',
        'subjects.lesson',
        'subjects.testMarks.exam'
    ]);

    $schoolInfo = SchoolInformation::getActive();

    // Resolve student passport photo to base64 for DomPDF
    $studentPhotoBase64 = null;
    $studentPhotoMime   = 'image/jpeg';

    if ($reportCard->student->passport_photo) {
        $photoPath = $reportCard->student->passport_photo;

        // If it's already a full URL (e.g. stored on S3 / external)
        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            try {
                $imageData = file_get_contents($photoPath);
                if ($imageData !== false) {
                    $studentPhotoMime   = mime_content_type('data://application/octet-stream;base64,' . base64_encode($imageData)) ?? 'image/jpeg';
                    $studentPhotoBase64 = base64_encode($imageData);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load remote student photo: ' . $e->getMessage());
            }
        } else {
            // Local file — resolve absolute path
            // Strip leading slash if present, then resolve via storage or public
            $localPath = public_path($photoPath);

            if (!file_exists($localPath)) {
                // Try storage path
                $localPath = storage_path('app/public/' . ltrim($photoPath, '/'));
            }

            if (file_exists($localPath)) {
                $imageData = file_get_contents($localPath);
                $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
                $studentPhotoMime   = match($ext) {
                    'png'  => 'image/png',
                    'gif'  => 'image/gif',
                    'webp' => 'image/webp',
                    default => 'image/jpeg',
                };
                $studentPhotoBase64 = base64_encode($imageData);
            }
        }
    }

    $pdf = Pdf::loadView('admin.report-cards.print', compact(
            'reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'
        ))
        ->setPaper('a4')
        ->setOption([
            'margin-top'        => 10,
            'margin-bottom'     => 10,
            'isRemoteEnabled'   => true,   // fallback safety
            'isHtml5ParserEnabled' => true,
        ]);

    return $pdf->stream("report-card-{$reportCard->student->full_name}-{$reportCard->academicYear->year}-term-{$reportCard->term->term}.pdf");
}

    public function printClassReports(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
        ]);

        $reportCards = ReportCard::with([
                'student',
                'course',
                'academicYear',
                'term',
                'subjects.lesson',
                'subjects.testMarks.exam',
            ])
            ->where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->where('term_id', $request->term_id)
            ->where('status', 'approved')
            ->join('students', 'report_cards.student_id', '=', 'students.id')
            ->orderBy('students.full_name')
            ->select('report_cards.*')
            ->get();

        $schoolInfo = SchoolInformation::getActive();

        $pdf = Pdf::loadView('admin.report-cards.print-class', compact('reportCards', 'schoolInfo'))
            ->setPaper('a4')
            ->setOption(['margin-top' => 10, 'margin-bottom' => 10]);

        $course = Course::find($request->course_id);
        $academicYear = AcademicYear::find($request->academic_year_id);
        $term = Term::find($request->term_id);

        return $pdf->stream("class-reports-{$course->course_name}-{$academicYear->year}-term-{$term->term}.pdf");
    }

    public function generateReportCardData(ReportCard $reportCard)
    {
        // Get lessons for this course
        $lessons = Lesson::where('course_id', $reportCard->course_id)->get();
        
        \Log::info('Processing report card: ' . json_encode([
            'report_card_id' => $reportCard->id,
            'course_id' => $reportCard->course_id,
            'lessons_count' => $lessons->count()
        ]));

        $totalMarks = 0;
        $subjectCount = 0;

        foreach ($lessons as $lesson) {
            \Log::info('Processing lesson: ' . json_encode([
                'lesson_id' => $lesson->id,
                'lesson_title' => $lesson->title,
                'lesson_exists' => !is_null($lesson),
            ]));

            // Get ALL marks for this student and lesson for the specific term
            $allMarks = Mark::where('student_id', $reportCard->student_id)
                ->where('lesson_id', $lesson->id)
                ->where('term_id', $reportCard->term_id)
                ->with('exam')
                ->get();

            \Log::info('Found marks for lesson: ' . json_encode([
                'lesson_id' => $lesson->id,
                'total_marks_count' => $allMarks->count(),
                'marks' => $allMarks->map(function($mark) {
                    return [
                        'assessment_type' => $mark->assessment_type,
                        'value' => $mark->value,
                        'marks_out_of' => $mark->marks_out_of,
                        'exam_id' => $mark->exam_id
                    ];
                })->toArray()
            ]));

            // Separate exam marks and test marks
            $examMarks = $allMarks->where('assessment_type', 'exam');
            $testMarks = $allMarks->where('assessment_type', 'test');

            // Calculate exam mark (normalized to 100)
            $examMark = 0;
            if ($examMarks->isNotEmpty()) {
                $totalExamMark = $examMarks->sum('value');
                $totalExamMax = $examMarks->sum('marks_out_of');
                
                if ($totalExamMax > 0) {
                    $examMark = ($totalExamMark / $totalExamMax) * 100;
                }
                
                \Log::info('Exam marks calculated: ' . json_encode([
                    'lesson_id' => $lesson->id,
                    'exam_marks_count' => $examMarks->count(),
                    'total_exam_mark' => $totalExamMark,
                    'total_exam_max' => $totalExamMax,
                    'normalized_exam_mark' => $examMark
                ]));
            }

            // Create report card subject
            if ($lesson) {
                \Log::info('Creating report card subject: ' . json_encode([
                    'lesson_id' => $lesson->id,
                    'lesson_title' => $lesson->title,
                    'exam_mark' => $examMark
                ]));

                $reportCardSubject = ReportCardSubject::create([
                    'report_card_id' => $reportCard->id,
                    'lesson_id' => $lesson->id,
                    'subject_name' => $lesson->title ?? 'Unknown Subject',
                    'exam_mark' => $examMark,
                ]);

                // Process test marks
                foreach ($testMarks as $index => $mark) {
                    $maxMark = $mark->marks_out_of ?? 100;
                    $normalizedMark = $maxMark > 0 ? ($mark->value / $maxMark) * 100 : 0;
                    
                    // Create a meaningful test name
                    $testName = 'Test ' . ($index + 1);
                    if ($mark->test_date) {
                        $testName .= ' (' . date('M j', strtotime($mark->test_date)) . ')';
                    }
                    if ($mark->exam && $mark->exam->title) {
                        $testName = $mark->exam->title;
                    }
                    
                    \Log::info('Creating test mark: ' . json_encode([
                        'mark_id' => $mark->id,
                        'test_name' => $testName,
                        'original_mark' => $mark->value,
                        'max_mark' => $maxMark,
                        'normalized_mark' => $normalizedMark,
                        'exam_id' => $mark->exam_id
                    ]));
                    
                    ReportCardTestMark::create([
                        'report_card_subject_id' => $reportCardSubject->id,
                        'exam_id' => $mark->exam_id, // Allow NULL for test marks
                        'test_name' => $testName,
                        'original_mark' => $mark->value,
                        'max_mark' => $maxMark,
                        'normalized_mark' => $normalizedMark,
                        'weight' => 1.0,
                    ]);
                }

                // Calculate test averages and totals
                $reportCardSubject->calculateTestAverage();
                $reportCardSubject->calculateTotalMark();

                $totalMarks += $reportCardSubject->total_mark;
                $subjectCount++;

                \Log::info('Report card subject completed: ' . json_encode([
                    'subject_id' => $reportCardSubject->id,
                    'total_mark' => $reportCardSubject->total_mark,
                    'test_average' => $reportCardSubject->test_average
                ]));
            }
        }

        // Update report card totals
        if ($subjectCount > 0) {
            $reportCard->total_marks = $totalMarks;
            $reportCard->average_marks = $totalMarks / $subjectCount;
            $reportCard->save();
            
            // Calculate class position based on average marks
            $reportCard->calculateClassPosition();
            
            \Log::info('Report card totals updated: ' . json_encode([
                'report_card_id' => $reportCard->id,
                'total_marks' => $totalMarks,
                'average_marks' => $reportCard->average_marks,
                'subject_count' => $subjectCount
            ]));
        }
    }
}
