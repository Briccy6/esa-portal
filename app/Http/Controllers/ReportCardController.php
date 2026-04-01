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
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    public function index()
    {
        $all = ReportCard::query()
            ->with(['student', 'course', 'academicYear', 'term'])
            ->orderByDesc('created_at')
            ->get();

        $groups = $all
            ->groupBy(fn (ReportCard $rc) => $rc->student_id.'-'.$rc->course_id.'-'.$rc->academic_year_id)
            ->map(function ($items) {
                return $items->sortBy(function (ReportCard $rc) {
                    return $rc->term?->start_date?->timestamp ?? $rc->term_id;
                })->values();
            })
            ->sortByDesc(function ($items) {
                return $items->max('updated_at') ?? $items->max('created_at');
            })
            ->values();

        $perPage = 20;
        $page = max(1, (int) request()->get('page', 1));
        $total = $groups->count();
        $slice = $groups->slice(($page - 1) * $perPage, $perPage)->values();

        $reportCardGroups = new LengthAwarePaginator(
            $slice,
            $total,
            $perPage,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        $terms = Term::query()
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('id')
            ->get();

        return view('admin.report-cards.index', compact('reportCardGroups', 'terms'));
    }

    /**
     * All report cards for one student + course + academic year.
     * Optional query: term_ids[] or term_id — when set, only those terms are included
     * (matches what the admin selected for generation or what you pass from the list/detail screen).
     */
    protected function consolidatedReportCards(Request $request): Collection
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'integer|exists:terms,id',
            'term_id' => 'nullable|integer|exists:terms,id',
        ]);

        $query = ReportCard::query()
            ->where('student_id', $request->integer('student_id'))
            ->where('course_id', $request->integer('course_id'))
            ->where('academic_year_id', $request->integer('academic_year_id'))
            ->with([
                'student',
                'course',
                'academicYear',
                'term',
                'approvedBy',
                'subjects.lesson',
                'subjects.testMarks.exam',
            ]);

        $termIds = $this->normalizeTermIds($request);
        if ($termIds !== []) {
            $query->whereIn('term_id', $termIds);
        }

        return $query->get()
            ->sortBy(fn (ReportCard $rc) => $rc->term?->start_date?->timestamp ?? $rc->term_id)
            ->values();
    }

    /**
     * Query params to reuse for consolidated view ↔ print links (preserves term filter).
     *
     * @return array<string, mixed>
     */
    protected function consolidatedRouteParams(Request $request): array
    {
        $params = [
            'student_id' => $request->integer('student_id'),
            'course_id' => $request->integer('course_id'),
            'academic_year_id' => $request->integer('academic_year_id'),
        ];
        $termIds = $this->normalizeTermIds($request);
        if ($termIds !== []) {
            $params['term_ids'] = $termIds;
        }

        return $params;
    }

    /**
     * One logical table: subjects as rows; each term has TEST / EXAM / TOT columns.
     *
     * @return array{terms: array, rows: array, totals_by_term: array, position_by_term: array, average_by_term: array}
     */
    protected function buildConsolidatedAssessmentMatrix(Collection $reportCards): array
    {
        $terms = $reportCards->map(fn (ReportCard $rc) => [
            'id' => (int) $rc->term_id,
            'name' => $rc->term?->name ?? 'Term #'.$rc->term_id,
            'report_card' => $rc,
        ])->values()->all();

        $rowMap = [];
        foreach ($reportCards as $rc) {
            foreach ($rc->subjects as $sub) {
                $key = $sub->lesson_id ? 'l'.$sub->lesson_id : 'n'.md5((string) $sub->subject_name);
                if (! isset($rowMap[$key])) {
                    $rowMap[$key] = [
                        'module_code' => $sub->subject_code ?? $sub->subject_name,
                        'competence_title' => $sub->subject_name,
                        'credits' => (int) ($sub->credits ?? 3),
                        'sort' => (string) $sub->subject_name,
                    ];
                }
            }
        }
        uasort($rowMap, fn ($a, $b) => strcmp($a['sort'], $b['sort']));

        $rows = [];
        foreach ($rowMap as $key => $meta) {
            $byTermId = [];
            foreach ($reportCards as $rc) {
                $tid = (int) $rc->term_id;
                $sub = $rc->subjects->first(function ($s) use ($key) {
                    $k = $s->lesson_id ? 'l'.$s->lesson_id : 'n'.md5((string) $s->subject_name);

                    return $k === $key;
                });
                if ($sub) {
                    $byTermId[$tid] = [
                        'test' => $this->formatMarkForMatrix($sub->test_average),
                        'exam' => $this->formatMarkForMatrix($sub->exam_mark),
                        'total' => $this->formatMarkForMatrix($sub->total_mark),
                        'obs' => ($sub->grade === 'A' || $sub->grade === 'B' ? 'C' : 'NYC'),
                    ];
                } else {
                    $byTermId[$tid] = [
                        'test' => '—',
                        'exam' => '—',
                        'total' => '—',
                        'obs' => '—',
                    ];
                }
            }
            $rows[] = [
                'module_code' => $meta['module_code'],
                'competence_title' => $meta['competence_title'],
                'credits' => $meta['credits'],
                'by_term_id' => $byTermId,
            ];
        }

        $totalsByTerm = [];
        $positionByTerm = [];
        $averageByTerm = [];
        foreach ($reportCards as $rc) {
            $tid = (int) $rc->term_id;
            $totalsByTerm[$tid] = [
                'credits_sum' => (int) $rc->subjects->sum(fn ($s) => (int) ($s->credits ?? 3)),
                'test_sum' => $this->formatMarkForMatrix($rc->subjects->sum('test_average')),
                'exam_sum' => $this->formatMarkForMatrix($rc->subjects->sum('exam_mark')),
                'total_sum' => $this->formatMarkForMatrix($rc->subjects->sum('total_mark')),
            ];
            $positionByTerm[$tid] = $rc->position_display;
            $averageByTerm[$tid] = $rc->average_marks;
        }

        return [
            'terms' => $terms,
            'rows' => $rows,
            'totals_by_term' => $totalsByTerm,
            'position_by_term' => $positionByTerm,
            'average_by_term' => $averageByTerm,
        ];
    }

    protected function formatMarkForMatrix(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        return number_format((float) $value, 1);
    }

    public function showConsolidated(Request $request)
    {
        $reportCards = $this->consolidatedReportCards($request);

        if ($reportCards->isEmpty()) {
            abort(404);
        }

        if ($reportCards->count() === 1) {
            return redirect()->route('admin.report-cards.show', $reportCards->first());
        }

        $schoolInfo = SchoolInformation::getActive();
        $assessmentMatrix = $this->buildConsolidatedAssessmentMatrix($reportCards);
        $consolidatedRouteParams = $this->consolidatedRouteParams($request);

        return view('admin.report-cards.show-consolidated', compact('reportCards', 'schoolInfo', 'assessmentMatrix', 'consolidatedRouteParams'));
    }

    public function printConsolidated(Request $request)
    {
        $reportCards = $this->consolidatedReportCards($request);

        if ($reportCards->isEmpty()) {
            abort(404);
        }

        if ($reportCards->count() === 1) {
            return $this->printReport($reportCards->first());
        }

        $schoolInfo = SchoolInformation::getActive();
        $assessmentMatrix = $this->buildConsolidatedAssessmentMatrix($reportCards);

        ini_set('memory_limit', '256M');

        $pdf = Pdf::loadView('admin.report-cards.print-consolidated', compact(
            'reportCards',
            'schoolInfo',
            'assessmentMatrix',
        ))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'margin-top' => 8,
                'margin-bottom' => 8,
                'margin-left' => 8,
                'margin-right' => 8,
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);

        $student = $reportCards->first()->student;
        $year = $reportCards->first()->academicYear->year ?? 'year';

        return $pdf->stream('report-card-'.Str::slug($student->full_name).'-'.$year.'-all-terms.pdf');
    }

    public function create()
    {
        $students = Student::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();
        $terms = Term::query()
            ->with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('id')
            ->get();

        return view('admin.report-cards.create', compact('students', 'courses', 'academicYears', 'terms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'course_id' => 'required|exists:courses,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'exists:terms,id',
            'term_id' => 'nullable|exists:terms,id',
        ]);

        $termIds = $this->normalizeTermIds($request);
        if ($termIds === []) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['term_ids' => 'Select at least one term.']);
        }

        $created = 0;
        $skipped = 0;
        $firstId = null;

        foreach ($termIds as $termId) {
            $existing = ReportCard::where('student_id', $request->student_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->where('term_id', $termId)
                ->where('course_id', $request->course_id)
                ->first();

            if ($existing) {
                $skipped++;

                continue;
            }

            $reportCard = ReportCard::create([
                'student_id' => $request->student_id,
                'academic_year_id' => $request->academic_year_id,
                'term_id' => $termId,
                'course_id' => $request->course_id,
            ]);
            $this->generateReportCardData($reportCard);
            ReportCard::recalculatePositionsForCohort(
                (int) $reportCard->course_id,
                (int) $reportCard->academic_year_id,
                (int) $reportCard->term_id
            );
            $created++;
            $firstId = $firstId ?? $reportCard->id;
        }

        if ($created === 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Report cards already exist for all selected terms.');
        }

        $msg = "Created {$created} report card(s)";
        if ($skipped > 0) {
            $msg .= "; skipped {$skipped} (already exist)";
        }
        $msg .= '.';

        if ($created === 1 && $firstId) {
            return redirect()->route('admin.report-cards.show', $firstId)
                ->with('success', $msg);
        }

        return redirect()->route('admin.report-cards.index')
            ->with('success', $msg);
    }

    public function show(ReportCard $reportCard)
    {
        $reportCard->load([
            'student',
            'course',
            'academicYear',
            'term',
            'approvedBy',
            'subjects.lesson',
            'subjects.testMarks.exam',
        ]);

        $schoolInfo = SchoolInformation::getActive();

        $siblingReportCards = ReportCard::query()
            ->where('student_id', $reportCard->student_id)
            ->where('course_id', $reportCard->course_id)
            ->where('academic_year_id', $reportCard->academic_year_id)
            ->where('id', '!=', $reportCard->id)
            ->with('term')
            ->orderBy('term_id')
            ->get();

        return view('admin.report-cards.show', compact('reportCard', 'schoolInfo', 'siblingReportCards'));
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

    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'report_card_ids' => 'required|array|min:1',
            'report_card_ids.*' => 'integer|exists:report_cards,id',
        ]);

        $reportCards = ReportCard::query()
            ->whereIn('id', $validated['report_card_ids'])
            ->get();

        if ($reportCards->isEmpty()) {
            return redirect()->route('admin.report-cards.index')
                ->with('error', 'No report cards selected.');
        }

        $approved = 0;
        $alreadyApproved = 0;
        $cohortsToRecalculate = [];

        foreach ($reportCards as $reportCard) {
            if ($reportCard->status === 'approved') {
                $alreadyApproved++;
                continue;
            }

            $reportCard->status = 'approved';
            $reportCard->approved_by = auth('admin')->id();
            $reportCard->approved_at = now();
            $reportCard->calculateClassPosition();
            $reportCard->save();
            $approved++;

            $cohortKey = $reportCard->course_id.'-'.$reportCard->academic_year_id.'-'.$reportCard->term_id;
            $cohortsToRecalculate[$cohortKey] = [
                'course_id' => (int) $reportCard->course_id,
                'academic_year_id' => (int) $reportCard->academic_year_id,
                'term_id' => (int) $reportCard->term_id,
            ];
        }

        foreach ($cohortsToRecalculate as $cohort) {
            ReportCard::recalculatePositionsForCohort(
                $cohort['course_id'],
                $cohort['academic_year_id'],
                $cohort['term_id']
            );
        }

        $message = "Approved {$approved} report card(s).";
        if ($alreadyApproved > 0) {
            $message .= " {$alreadyApproved} were already approved.";
        }

        return redirect()->route('admin.report-cards.index')->with('success', $message);
    }

    public function generateForClass(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'exists:terms,id',
            'term_id' => 'nullable|exists:terms,id',
        ]);

        $termIds = $this->normalizeTermIds($request);
        if ($termIds === []) {
            return redirect()->back()
                ->withErrors(['term_ids' => 'Select at least one term.']);
        }

        $students = Student::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get();

        $generated = 0;
        $skipped = 0;

        foreach ($termIds as $termId) {
            foreach ($students as $student) {
                $existing = ReportCard::where('student_id', $student->id)
                    ->where('academic_year_id', $request->academic_year_id)
                    ->where('term_id', $termId)
                    ->where('course_id', $request->course_id)
                    ->first();

                if (! $existing) {
                    $reportCard = ReportCard::create([
                        'student_id' => $student->id,
                        'academic_year_id' => $request->academic_year_id,
                        'term_id' => $termId,
                        'course_id' => $request->course_id,
                    ]);
                    $this->generateReportCardData($reportCard);
                    $generated++;
                } else {
                    $skipped++;
                }
            }

            ReportCard::recalculatePositionsForCohort(
                (int) $request->course_id,
                (int) $request->academic_year_id,
                (int) $termId
            );
        }

        $termCount = count($termIds);

        return redirect()->back()
            ->with('success', "Generated {$generated} report card(s) across {$termCount} term(s). Skipped {$skipped} duplicate(s).");
    }

   public function printReport(ReportCard $reportCard)
{
    $reportCard->load([
        'student',
        'course',
        'academicYear',
        'term',
        'approvedBy',
        'subjects.lesson',
        'subjects.testMarks.exam',
    ]);

    $schoolInfo = SchoolInformation::getActive();
    ['base64' => $studentPhotoBase64, 'mime' => $studentPhotoMime] = $this->resolveStudentPhotoData(
        $reportCard->student->passport_photo
    );

    $pdf = Pdf::loadView('admin.report-cards.print', compact(
            'reportCard', 'schoolInfo', 'studentPhotoBase64', 'studentPhotoMime'
        ))
        ->setPaper('a4', 'portrait')
        ->setOption([
            'margin-top'        => 10,
            'margin-bottom'     => 10,
            'isRemoteEnabled'   => true,   // fallback safety
            'isHtml5ParserEnabled' => true,
        ]);

    $termSlug = Str::slug($reportCard->term->name ?? 'term');

    return $pdf->stream(
        'report-card-'.Str::slug($reportCard->student->full_name).'-'.$reportCard->academicYear->year.'-'.$termSlug.'.pdf'
    );
}

    public function printClassReports(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'exists:terms,id',
            'term_id' => 'nullable|exists:terms,id',
        ]);

        $termIds = $this->normalizeTermIds($request);
        if ($termIds === []) {
            return redirect()->back()
                ->withErrors(['term_ids' => 'Select at least one term.']);
        }

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
            ->whereIn('term_id', $termIds)
            ->where('status', 'approved')
            ->join('students', 'report_cards.student_id', '=', 'students.id')
            ->orderBy('report_cards.term_id')
            ->orderBy('students.full_name')
            ->select('report_cards.*')
            ->get();

        if ($reportCards->isEmpty()) {
            return redirect()->back()
                ->with('error', 'No approved report cards found for the selected course, year, and term(s).');
        }

        $schoolInfo = SchoolInformation::getActive();
        $studentPhotosByReportId = [];
        foreach ($reportCards as $reportCard) {
            $studentPhotosByReportId[$reportCard->id] = $this->resolveStudentPhotoData($reportCard->student->passport_photo);
        }

        $pdf = Pdf::loadView('admin.report-cards.print-class', compact('reportCards', 'schoolInfo', 'studentPhotosByReportId'))
            ->setPaper('a4', 'portrait')
            ->setOption(['margin-top' => 10, 'margin-bottom' => 10]);

        $course = Course::find($request->course_id);
        $academicYear = AcademicYear::find($request->academic_year_id);
        $termsSlug = count($termIds) > 1 ? 'terms-'.implode('-', $termIds) : 'term-'.$termIds[0];
        $filename = 'class-reports-'.Str::slug($course->course_name).'-'.$academicYear->year.'-'.$termsSlug.'.pdf';

        if ($request->boolean('download')) {
            return $pdf->download($filename);
        }

        return $pdf->stream($filename);
    }

    /**
     * @return array{base64: ?string, mime: string}
     */
    protected function resolveStudentPhotoData(?string $photoPath): array
    {
        $studentPhotoBase64 = null;
        $studentPhotoMime = 'image/jpeg';

        if (empty($photoPath)) {
            return ['base64' => null, 'mime' => $studentPhotoMime];
        }

        if (filter_var($photoPath, FILTER_VALIDATE_URL)) {
            try {
                $imageData = file_get_contents($photoPath);
                if ($imageData !== false) {
                    $detectedMime = mime_content_type('data://application/octet-stream;base64,'.base64_encode($imageData));
                    $studentPhotoMime = is_string($detectedMime) ? $detectedMime : 'image/jpeg';
                    $studentPhotoBase64 = base64_encode($imageData);
                }
            } catch (\Exception $e) {
                \Log::warning('Could not load remote student photo: '.$e->getMessage());
            }

            return ['base64' => $studentPhotoBase64, 'mime' => $studentPhotoMime];
        }

        $localPath = public_path($photoPath);
        if (! file_exists($localPath)) {
            $localPath = storage_path('app/public/'.ltrim($photoPath, '/'));
        }

        if (! file_exists($localPath)) {
            return ['base64' => null, 'mime' => $studentPhotoMime];
        }

        $imageData = file_get_contents($localPath);
        if ($imageData === false) {
            return ['base64' => null, 'mime' => $studentPhotoMime];
        }

        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $studentPhotoMime = match ($ext) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'image/jpeg',
        };
        $studentPhotoBase64 = base64_encode($imageData);

        return ['base64' => $studentPhotoBase64, 'mime' => $studentPhotoMime];
    }

    /**
     * @return int[]
     */
    protected function normalizeTermIds(Request $request): array
    {
        $raw = $request->input('term_ids');
        if (is_array($raw) && count($raw) > 0) {
            return array_values(array_unique(array_filter(array_map('intval', $raw))));
        }
        if ($request->filled('term_id')) {
            return [(int) $request->term_id];
        }

        return [];
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

            \Log::info('Report card totals updated: ' . json_encode([
                'report_card_id' => $reportCard->id,
                'total_marks' => $totalMarks,
                'average_marks' => $reportCard->average_marks,
                'subject_count' => $subjectCount
            ]));
        }
        // Class position is computed in recalculatePositionsForCohort() after generation (store / bulk).
    }
}
