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
use App\Models\Branch;
use App\Models\StudentTransaction;
use App\Models\CourseTermFee;
use App\Services\FeePaymentService;
use App\Services\ReportCardService;
use App\Support\AssessmentMarkMath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportCardController extends Controller
{
    public function index()
    {
        $all = ReportCard::query()
            ->with(['student.branch', 'course', 'academicYear', 'term'])
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
     * AJAX endpoint for searching and sorting report cards
     * Returns JSON with paginated and filtered results
     */
    public function searchSort(Request $request)
    {
        $search = trim($request->get('search', ''));
        $sort = $request->get('sort', 'newest');
        $page = max(1, (int) $request->get('page', 1));
        $perPage = 20;

        // Get all report cards with relationships
        $all = ReportCard::query()
            ->with(['student.branch', 'course', 'academicYear', 'term'])
            ->orderByDesc('created_at')
            ->get();

        // Group by student-course-year
        $groups = $all
            ->groupBy(fn (ReportCard $rc) => $rc->student_id.'-'.$rc->course_id.'-'.$rc->academic_year_id)
            ->map(function ($items) {
                return $items->sortBy(function (ReportCard $rc) {
                    return $rc->term?->start_date?->timestamp ?? $rc->term_id;
                })->values();
            });

        // Apply search filter
        if ($search) {
            $searchLower = strtolower($search);
            $groups = $groups->filter(function ($group) use ($searchLower) {
                $first = $group->first();
                $student = $first->student->full_name ?? '';
                $course = $first->course->course_name ?? '';
                $branch = $first->student->branch->name ?? '';
                $year = $first->academicYear->year ?? '';
                $terms = $group->pluck('term.name')->implode(' ');
                
                $haystack = strtolower("$student $course $branch $year $terms");
                return strpos($haystack, $searchLower) !== false;
            })->values();
        }

        // Apply sorting based on sort parameter
        $groups = $groups->sort(function ($a, $b) use ($sort) {
            $aFirst = $a->first();
            $bFirst = $b->first();

            $aStudent = $aFirst->student->full_name ?? '';
            $bStudent = $bFirst->student->full_name ?? '';
            $aCourse = $aFirst->course->course_name ?? '';
            $bCourse = $bFirst->course->course_name ?? '';
            $aBranch = $aFirst->student->branch->name ?? '';
            $bBranch = $bFirst->student->branch->name ?? '';
            $aTs = $a->max('created_at')?->timestamp ?? 0;
            $bTs = $b->max('created_at')?->timestamp ?? 0;
            $aMinTs = $a->min('created_at')?->timestamp ?? 0;
            $bMinTs = $b->min('created_at')?->timestamp ?? 0;

            return match ($sort) {
                'oldest' => $aMinTs <=> $bMinTs,
                'student_asc' => $aStudent <=> $bStudent,
                'student_desc' => $bStudent <=> $aStudent,
                'class_asc' => $aCourse <=> $bCourse,
                'class_desc' => $bCourse <=> $aCourse,
                'branch_asc' => $aBranch <=> $bBranch,
                'branch_desc' => $bBranch <=> $aBranch,
                'outstanding_asc' => $this->getGroupOutstanding($a) <=> $this->getGroupOutstanding($b),
                'outstanding_desc' => $this->getGroupOutstanding($b) <=> $this->getGroupOutstanding($a),
                default => $bTs <=> $aTs, // newest
            };
        })->values();

        // Apply pagination
        $total = $groups->count();
        $slice = $groups->slice(($page - 1) * $perPage, $perPage)->values();

        return response()->json([
            'success' => true,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'pages' => ceil($total / $perPage),
            'results' => $slice->map(function ($group) {
                $first = $group->first();
                $overallAvg = (float) $group->avg('average_marks');
                $overallGrade = match (true) {
                    $overallAvg >= 90 => 'A+',
                    $overallAvg >= 80 => 'A',
                    $overallAvg >= 70 => 'B+',
                    $overallAvg >= 60 => 'B',
                    $overallAvg >= 50 => 'C',
                    $overallAvg >= 40 => 'D',
                    default => 'F',
                };

                $outstanding = 0;
                foreach ($group as $rc) {
                    $status = FeePaymentService::getPaymentStatus($rc->student, [(int) $rc->term_id]);
                    $outstanding += (float) ($status['outstanding'] ?? 0);
                }

                return [
                    'student_id' => $first->student_id,
                    'student_name' => $first->student->full_name,
                    'course_id' => $first->course_id,
                    'course_name' => $first->course->course_name,
                    'branch_name' => $first->student->branch->name ?? 'N/A',
                    'year' => $first->academicYear->year,
                    'overall_avg' => number_format($overallAvg, 1),
                    'overall_grade' => $overallGrade,
                    'outstanding' => number_format($outstanding, 2),
                    'terms' => $group->pluck('term.name')->filter()->values()->all(),
                    'group_data' => [
                        'student' => strtolower($first->student->full_name ?? ''),
                        'course' => strtolower($first->course->course_name ?? ''),
                        'branch' => strtolower($first->student->branch->name ?? ''),
                        'year' => strtolower($first->academicYear->year ?? ''),
                        'terms' => strtolower($group->pluck('term.name')->implode(' ')),
                        'generated_at' => $group->max('created_at')?->timestamp ?? 0,
                    ]
                ];
            })->all(),
        ]);
    }

    /**
     * Calculate total outstanding fees for a group of report cards
     */
    private function getGroupOutstanding($group)
    {
        $outstanding = 0;
        foreach ($group as $rc) {
            $status = FeePaymentService::getPaymentStatus($rc->student, [(int) $rc->term_id]);
            $outstanding += (float) ($status['outstanding'] ?? 0);
        }
        return $outstanding;
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

        $termIds = $this->normalizeTermIds($request);
        
        \Log::info('═══ consolidatedReportCards START ═══');
        \Log::info('Request Input:', [
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'term_ids_raw' => $request->input('term_ids'),
            'term_id_raw' => $request->input('term_id'),
        ]);
        \Log::info('Normalized term IDs:', ['normalized' => $termIds]);

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

        if ($termIds !== []) {
            $query->whereIn('term_id', $termIds);
        }

        $results = $query->get()
            ->sortBy(fn (ReportCard $rc) => $rc->term?->start_date?->timestamp ?? $rc->term_id)
            ->values();
        
        \Log::info('Query Results:', [
            'count' => $results->count(),
            'term_ids_found' => $results->pluck('term_id')->all(),
        ]);
        \Log::info('═══ consolidatedReportCards END ═══');

        return $results;
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
        \Log::info('🔨 buildConsolidatedAssessmentMatrix - Start');
        \Log::info('Input Report Cards:', [
            'count' => $reportCards->count(),
            'terms' => $reportCards->pluck('term_id')->all(),
        ]);
        
        $terms = $reportCards->map(fn (ReportCard $rc) => [
            'id' => (int) $rc->term_id,
            'name' => $rc->term?->name ?? 'Term #'.$rc->term_id,
            'report_card' => $rc,
        ])->values()->all();
        
        \Log::info('Terms for matrix:', $terms);

        $rowMap = [];
        foreach ($reportCards as $rc) {
            \Log::info("Processing ReportCard for Term {$rc->term_id}:", [
                'subjects_count' => $rc->subjects->count(),
                'subject_list' => $rc->subjects->pluck('subject_name')->all(),
            ]);
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
        
        \Log::info('RowMap after sorting:', [
            'count' => count($rowMap),
            'keys' => array_keys($rowMap),
            'subjects' => array_column($rowMap, 'competence_title'),
        ]);

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
                        // Only show test marks - skip exam marks
                        'total' => $this->formatMarkForMatrix($sub->test_average),
                        'obs' => ($sub->grade === 'A' || $sub->grade === 'B' ? 'C' : 'NYC'),
                    ];
                    \Log::debug("Subject {$meta['competence_title']} - Term {$tid}:", [
                        'raw_test_average' => $sub->test_average,
                        'raw_total_mark' => $sub->test_average,
                        'formatted_marks' => $byTermId[$tid],
                    ]);
                } else {
                    $byTermId[$tid] = [
                        'test' => '—',
                        'total' => '—',
                        'obs' => '—',
                    ];
                    \Log::debug("Subject {$meta['competence_title']} - Term {$tid}: NOT FOUND");
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
                // Only show test sum - skip exam sum
                'total_sum' => $this->formatMarkForMatrix($rc->subjects->sum('test_average')),
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

        foreach ($reportCards as $reportCard) {
            if ($this->reportCardHasOutstandingFees($reportCard)) {
                return redirect()->route('admin.report-cards.index')
                    ->with('warning', 'Cannot print/download report card while outstanding fees exist for the selected term.');
            }
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
        \Log::info('printConsolidated - Request data:', [
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'term_ids_raw' => $request->input('term_ids'),
            'term_id_raw' => $request->input('term_id'),
            'all_request' => $request->all()
        ]);
        
        $reportCards = $this->consolidatedReportCards($request);
        
        \Log::info('printConsolidated - Retrieved Report Cards:', [
            'count' => $reportCards->count(),
            'term_ids' => $reportCards->pluck('term_id')->all()
        ]);

        if ($reportCards->isEmpty()) {
            abort(404);
        }

        foreach ($reportCards as $reportCard) {
            if ($this->reportCardHasOutstandingFees($reportCard)) {
                return redirect()->route('admin.report-cards.index')
                    ->with('warning', 'Cannot print/download report card while outstanding fees exist for the selected term.');
            }
        }

        // Get the requested term IDs - if only 1 term was requested, use single report view
        $termIds = $this->normalizeTermIds($request);
        
        \Log::info('═══ printConsolidated START ═══');
        \Log::info('Request params:', [
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'term_ids_requested' => $termIds,
            'report_cards_count' => $reportCards->count(),
        ]);
        
        if ($reportCards->count() === 1 && count($termIds) <= 1) {
            \Log::info('Routing to single report print (only 1 term, 1 report card)');
            return $this->printReport($reportCards->first());
        }
        
        \Log::info('Using consolidated print (multiple terms or partial data)');
        
        // Log detailed subject and marks data
        foreach ($reportCards as $rc) {
            \Log::info("Report Card - Term {$rc->term_id}:", [
                'subjects_count' => $rc->subjects->count(),
                'subject_names' => $rc->subjects->pluck('subject_name')->all(),
            ]);
            foreach ($rc->subjects as $subject) {
                \Log::info("  Subject: {$subject->subject_name}", [
                    'exam_mark' => $subject->exam_mark,
                    'test_average' => $subject->test_average,
                    'total_mark' => $subject->total_mark,
                    'test_marks_count' => $subject->testMarks->count(),
                ]);
            }
        }
        \Log::info('═══ printConsolidated CONTINUE ═══');

        $schoolInfo = SchoolInformation::getActive();
        $assessmentMatrix = $this->buildConsolidatedAssessmentMatrix($reportCards);
        
        \Log::info('Assessment Matrix Compiled:', [
            'terms_count' => count($assessmentMatrix['terms']),
            'terms' => $assessmentMatrix['terms'],
            'rows_count' => count($assessmentMatrix['rows']),
            'first_row_sample' => count($assessmentMatrix['rows']) > 0 ? $assessmentMatrix['rows'][0] : null,
        ]);
        \Log::info('═══ printConsolidated COMPLETE ═══');

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

        // Keep fee checking, but do not block generation.
        $student = Student::find($request->student_id);
        $paymentStatus = FeePaymentService::getPaymentStatus($student, $termIds);

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
        if (($paymentStatus['outstanding'] ?? 0) > 0) {
            $msg .= ' Note: this student still has outstanding fees, so print/download stays blocked until payment is complete.';
        }

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

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'required|array',
            'term_ids.*' => 'exists:terms,id',
        ]);

        try {
            $reportCards = ReportCard::where('student_id', $request->student_id)
                ->where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->whereIn('term_id', $request->term_ids)
                ->get();

            if ($reportCards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report cards found matching the criteria.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($reportCards as $reportCard) {
                $reportCard->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} report card(s)."
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulk delete report cards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report cards: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all report cards for a specific class
     */
    public function deleteByClass(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'exists:terms,id',
            'term_id' => 'nullable|exists:terms,id',
        ]);

        try {
            $termIds = $this->normalizeTermIds($request);
            if ($termIds === []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one term.'
                ], 400);
            }

            $query = ReportCard::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->whereIn('term_id', $termIds);

            $reportCards = $query->get();

            if ($reportCards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report cards found for the selected class.'
                ], 404);
            }

            $course = Course::find($request->course_id);
            $year = AcademicYear::find($request->academic_year_id);
            $deletedCount = 0;

            foreach ($reportCards as $reportCard) {
                $reportCard->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} report card(s) for {$course->course_name} ({$year->year}).",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting report cards by class: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report cards: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete selected report cards by IDs
     */
    public function deleteSelected(Request $request)
    {
        $validated = $request->validate([
            'report_card_ids' => 'required|array|min:1',
            'report_card_ids.*' => 'integer|exists:report_cards,id',
        ]);

        try {
            $reportCards = ReportCard::whereIn('id', $validated['report_card_ids'])->get();

            if ($reportCards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report cards found.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($reportCards as $reportCard) {
                $reportCard->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} report card(s).",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting selected report cards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report cards: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete all report cards at once
     */
    public function deleteAll(Request $request)
    {
        // Require confirmation via status parameter
        if ($request->input('confirm') !== 'DELETE_ALL') {
            return response()->json([
                'success' => false,
                'message' => 'Confirmation not provided. Use confirm=DELETE_ALL parameter.'
            ], 400);
        }

        try {
            $allReportCards = ReportCard::all();

            if ($allReportCards->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report cards found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($allReportCards as $reportCard) {
                $reportCard->delete();
                $deletedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully deleted all {$deletedCount} report card(s).",
                'deleted_count' => $deletedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting all report cards: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting report cards: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send outstanding fees email from report card generation modal
     */
    public function sendFeesEmailFromModal(Student $student)
    {
        try {
            $paymentStatus = FeePaymentService::getPaymentStatus($student);
            
            if ($paymentStatus['outstanding'] <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student has no outstanding fees.'
                ], 400);
            }

            if (!$student->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student does not have a valid email address.'
                ], 400);
            }

            // Send email
            Mail::to($student->email)->send(
                new \App\Mail\StudentOutstandingFeesMail(
                    $student,
                    $paymentStatus['outstanding'],
                    $paymentStatus['required_fees'],
                    $paymentStatus['total_paid']
                )
            );

            return response()->json([
                'success' => true,
                'message' => "Email sent successfully to {$student->full_name}."
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending outstanding fees email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show students with outstanding fees for report card generation
     */
    /**
     * Show all students' fees for class before generating reports
     * This is a REQUIRED step before bulk generating reports
     */
    public function checkClassFeesBeforeGeneration(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_ids' => 'nullable|array',
            'term_ids.*' => 'exists:terms,id',
        ]);

        $termIds = $this->normalizeTermIds($request);
        if ($termIds === []) {
            return redirect()->back()->withErrors(['term_ids' => 'Select at least one term.']);
        }

        // Get course and year details
        $course = Course::findOrFail($request->course_id);
        $academicYear = AcademicYear::findOrFail($request->academic_year_id);
        $terms = Term::whereIn('id', $termIds)->orderBy('start_date')->get();

        // Get all students in this course/year
        $students = Student::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->orderBy('full_name')
            ->get();

        $studentFeeDetails = [];
        $totalOutstanding = 0;
        $studentsWithOutstanding = 0;
        $totalRequired = 0;
        $totalPaid = 0;

        foreach ($students as $student) {
            // Get fees and payments status for selected terms
            $paymentStatus = FeePaymentService::getPaymentStatus($student, $termIds);

            // Get payment history for this student in selected terms (all fee types)
            $payments = StudentTransaction::where('student_id', $student->id)
                ->whereIn('type', ['registration', 'school_fee', 'practical_fee', 'trip_fee', 'student_card'])
                ->whereIn('term_id', $termIds)
                ->orderBy('created_at', 'desc')
                ->get();

            $studentFeeDetails[] = [
                'student' => $student,
                'student_id' => $student->student_id,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'required_fees' => $paymentStatus['required_fees'],
                'total_paid' => $paymentStatus['total_paid'],
                'outstanding' => $paymentStatus['outstanding'],
                'is_fully_paid' => $paymentStatus['is_fully_paid'],
                'payment_percentage' => $paymentStatus['payment_percentage'],
                'payments' => $payments,
                'eligible_for_report' => $paymentStatus['outstanding'] <= 0,
            ];

            if ($paymentStatus['outstanding'] > 0) {
                $studentsWithOutstanding++;
                $totalOutstanding += $paymentStatus['outstanding'];
            }

            $totalRequired += $paymentStatus['required_fees'];
            $totalPaid += $paymentStatus['total_paid'];
        }

        // Sort: Students with outstanding first
        usort($studentFeeDetails, function($a, $b) {
            if ($a['outstanding'] > 0 && $b['outstanding'] == 0) return -1;
            if ($a['outstanding'] == 0 && $b['outstanding'] > 0) return 1;
            return $a['full_name'] <=> $b['full_name'];
        });

        return view('admin.report-cards.check-fees-before-generation', [
            'course' => $course,
            'academicYear' => $academicYear,
            'terms' => $terms,
            'termIds' => $termIds,
            'studentFeeDetails' => $studentFeeDetails,
            'totalStudents' => count($students),
            'studentsWithOutstanding' => $studentsWithOutstanding,
            'totalOutstanding' => $totalOutstanding,
            'totalRequired' => $totalRequired,
            'totalPaid' => $totalPaid,
        ]);
    }

    public function checkOutstandingFeesForGeneration(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'academic_year_id' => 'required|exists:academic_years,id',
                'term_ids' => 'nullable|array',
                'term_ids.*' => 'exists:terms,id',
            ]);

            $termIds = $this->normalizeTermIds($request);
            \Log::info('═══ checkOutstandingFeesForGeneration START ═══');
            \Log::info('Requested term_ids:', ['raw' => $request->input('term_ids'), 'normalized' => $termIds]);
            
            if ($termIds === []) {
                return response()->json([
                    'success' => false,
                    'message' => 'Select at least one term.'
                ], 400);
            }

            // Get students in this course/year
            $students = Student::where('course_id', $request->course_id)
                ->where('academic_year_id', $request->academic_year_id)
                ->get();

            \Log::info('Total students in course:', ['count' => $students->count(), 'course_id' => $request->course_id, 'year_id' => $request->academic_year_id]);

            $studentsWithFees = [];
            foreach ($students as $student) {
                // Pass termIds to check fees ONLY for selected terms
                $paymentStatus = FeePaymentService::getPaymentStatus($student, $termIds);
                
                if ($paymentStatus['outstanding'] > 0) {
                    \Log::debug('Student with outstanding fees:', [
                        'student_id' => $student->id,
                        'name' => $student->full_name,
                        'required_fees' => $paymentStatus['required_fees'],
                        'total_paid' => $paymentStatus['total_paid'],
                        'outstanding' => $paymentStatus['outstanding'],
                    ]);
                    
                    $studentsWithFees[] = [
                        'id' => $student->id,
                        'full_name' => $student->full_name,
                        'email' => $student->email,
                        'student_id' => $student->student_id,
                        'outstanding_amount' => $paymentStatus['outstanding'],
                        'required_fees' => $paymentStatus['required_fees'],
                        'total_paid' => $paymentStatus['total_paid'],
                    ];
                }
            }

            \Log::info('═══ checkOutstandingFeesForGeneration RESULT ═══', [
                'total_students' => $students->count(),
                'students_with_fees' => count($studentsWithFees),
                'term_ids_checked' => $termIds,
            ]);

            return response()->json([
                'success' => true,
                'has_outstanding_fees' => count($studentsWithFees) > 0,
                'students' => $studentsWithFees,
                'count' => count($studentsWithFees),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error checking outstanding fees: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error checking fees: ' . $e->getMessage()
            ], 500);
        }
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
        
        \Log::info('═══ generateForClass START ═══');
        \Log::info('Parameters:', [
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'term_ids_requested' => $termIds,
            'term_count' => count($termIds),
        ]);
        
        if ($termIds === []) {
            \Log::warning('No terms selected');
            return redirect()->back()
                ->withErrors(['term_ids' => 'Select at least one term.']);
        }

        $students = Student::where('course_id', $request->course_id)
            ->where('academic_year_id', $request->academic_year_id)
            ->get();
        
        \Log::info('Students in class:', ['count' => $students->count()]);

        $generated = 0;
        $skipped = 0;
        $skippedFees = 0;

        // Keep outstanding-fees checking for visibility and reminders, but still generate cards.
        foreach ($students as $student) {
            $paymentStatus = FeePaymentService::getPaymentStatus($student, $termIds);
            if (($paymentStatus['outstanding'] ?? 0) > 0) {
                $skippedFees++;
                \Log::info("Student {$student->id} ({$student->full_name}): HAS OUTSTANDING RWF " . number_format((float) $paymentStatus['outstanding'], 2) . " - report will still be generated, output remains blocked.");
            }
        }

        foreach ($termIds as $termId) {
            \Log::info("Processing Term ID: {$termId}");
            $termName = \App\Models\Term::find($termId)?->name ?? "Term #{$termId}";
            \Log::info("  Term Name: {$termName}");
            
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
                    \Log::info("    Student {$student->id} ({$student->full_name}): CREATED report card #{$reportCard->id}");
                } else {
                    $skipped++;
                    \Log::info("    Student {$student->id} ({$student->full_name}): SKIPPED - Already exists");
                }
            }

            ReportCard::recalculatePositionsForCohort(
                (int) $request->course_id,
                (int) $request->academic_year_id,
                (int) $termId
            );
            \Log::info("  Term {$termId} positions recalculated");
        }

        $termCount = count($termIds);
        $message = "Generated {$generated} report card(s) across {$termCount} term(s).";
        if ($skipped > 0) {
            $message .= " Skipped {$skipped} duplicate(s).";
        }
        if ($skippedFees > 0) {
            $message .= " {$skippedFees} student(s) currently have outstanding fees (their print/download is blocked until outstanding is zero).";
        }
        $message .= " You can run this again after payment; only missing students will be generated.";
        
        \Log::info('═══ generateForClass END ═══');
        \Log::info('Summary:', [
            'generated' => $generated,
            'skipped' => $skipped,
            'skipped_fees' => $skippedFees,
            'message' => $message
        ]);

        return redirect()->back()
            ->with('success', $message);
    }

   public function printReport(ReportCard $reportCard)
{
    if ($this->reportCardHasOutstandingFees($reportCard)) {
        return redirect()->route('admin.report-cards.index')
            ->with('warning', 'Cannot print/download report card while outstanding fees exist for this student/term.');
    }

    \Log::info('🖨️ printReport (SINGLE CARD):', [
        'report_card_id' => $reportCard->id,
        'student_id' => $reportCard->student_id,
        'student_name' => $reportCard->student->full_name ?? 'Unknown',
        'term_id' => $reportCard->term_id,
        'term_name' => $reportCard->term->name ?? 'Unknown',
    ]);
    
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
        
        \Log::info('═══ printClassReports START ═══');
        \Log::info('Request parameters:', [
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'term_ids_requested' => $termIds,
            'download' => $request->input('download'),
        ]);
        
        if ($termIds === []) {
            \Log::warning('No terms specified in printClassReports');
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
            ->where('report_cards.course_id', $request->course_id)
            ->where('report_cards.academic_year_id', $request->academic_year_id)
            ->whereIn('report_cards.term_id', $termIds)
            // After generation, report cards are usually created as `pending`.
            // Include pending here so the "Print class PDF" reflects all selected terms.
            ->whereIn('report_cards.status', ['approved', 'pending', 'printed'])
            ->join('students', 'report_cards.student_id', '=', 'students.id')
            ->orderBy('report_cards.term_id')
            ->orderBy('students.full_name')
            ->select('report_cards.*')
            ->get();

        $reportCards = $reportCards->filter(function ($reportCard) {
            return ! $this->reportCardHasOutstandingFees($reportCard);
        })->values();

        $reportCards = $reportCards->filter(function ($reportCard) {
            return ! $this->reportCardHasOutstandingFees($reportCard);
        })->values();
        
        \Log::info('Print query results:', [
            'total_records' => $reportCards->count(),
            'terms_found' => $reportCards->pluck('term_id')->unique()->sort()->values()->all(),
            'students_found' => $reportCards->pluck('student.full_name')->unique()->values()->all(),
        ]);
        
        foreach ($reportCards->groupBy('term_id') as $termId => $cardsForTerm) {
            \Log::info("  Term {$termId}: " . $cardsForTerm->count() . " students");
        }

        if ($reportCards->isEmpty()) {
            \Log::warning('No approved report cards found', ['params' => $request->all()]);
            return redirect()->back()
                ->with('error', 'No approved report cards found for the selected course, year, and term(s).');
        }

        $schoolInfo = SchoolInformation::getActive();
        $studentPhotosByReportId = [];
        foreach ($reportCards as $reportCard) {
            $studentPhotosByReportId[$reportCard->id] = $this->resolveStudentPhotoData($reportCard->student->passport_photo);
        }

        $pdf = Pdf::loadView('admin.report-cards.print-class-unified', compact('reportCards', 'schoolInfo', 'studentPhotosByReportId'))
            ->setPaper('a4', 'portrait')
            ->setOption(['margin-top' => 10, 'margin-bottom' => 10]);

        $course = Course::find($request->course_id);
        $academicYear = AcademicYear::find($request->academic_year_id);
        $termsSlug = count($termIds) > 1 ? 'terms-'.implode('-', $termIds) : 'term-'.$termIds[0];
        $filename = 'class-reports-'.Str::slug($course->course_name).'-'.$academicYear->year.'-'.$termsSlug.'.pdf';
        
        \Log::info('═══ printClassReports COMPLETE ═══');
        \Log::info('PDF generated:', [
            'filename' => $filename,
            'total_pages' => $reportCards->count(),
            'terms_in_pdf' => $reportCards->pluck('term_id')->unique()->sort()->values()->all(),
            'download' => $request->boolean('download') ? 'Yes (download)' : 'No (stream)',
        ]);

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
        
        // Handle case where term_ids is passed as an array
        if (is_array($raw) && count($raw) > 0) {
            return array_values(array_unique(array_filter(array_map('intval', $raw))));
        }
        
        // Handle case where term_ids is passed as a comma-separated string
        if (is_string($raw) && !empty($raw)) {
            $parts = array_map('trim', explode(',', $raw));
            return array_values(array_unique(array_filter(array_map('intval', $parts))));
        }
        
        // Handle single term_id parameter
        if ($request->filled('term_id')) {
            return [(int) $request->term_id];
        }

        return [];
    }

    protected function reportCardHasOutstandingFees(ReportCard $reportCard): bool
    {
        $reportCard->loadMissing('student');
        $paymentStatus = FeePaymentService::getPaymentStatus($reportCard->student, [(int) $reportCard->term_id]);

        return ((float) ($paymentStatus['outstanding'] ?? 0)) > 0;
    }

    public function generateReportCardData(ReportCard $reportCard)
    {
        \Log::info('═══ generateReportCardData START ═══');
        \Log::info('Generating report card data for:', [
            'report_card_id' => $reportCard->id,
            'student_id' => $reportCard->student_id,
            'student_name' => $reportCard->student->full_name ?? 'Unknown',
            'course_id' => $reportCard->course_id,
            'term_id' => $reportCard->term_id,
            'branch_id' => $reportCard->student->branch_id,
        ]);
        
        // Get lessons for this student's branch and course from lesson_branch_course table
        // This ensures we only include lessons assigned to the student's specific branch
        $lessons = \App\Models\LessonBranchCourse::where('course_id', $reportCard->course_id)
            ->where('branch_id', $reportCard->student->branch_id)
            ->with('lesson')
            ->get()
            ->pluck('lesson');
        
        \Log::info('Processing report card: ' . json_encode([
            'report_card_id' => $reportCard->id,
            'course_id' => $reportCard->course_id,
            'branch_id' => $reportCard->student->branch_id,
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

            // Exam: each row is on its own scale (typically /100); convert to % per row then average — avoids false 100% from sum/sum quirks
            $examMark = AssessmentMarkMath::aggregatedExamPercentFromMarks($examMarks);

            if ($examMarks->isNotEmpty()) {
                \Log::info('Exam marks calculated: ' . json_encode([
                    'lesson_id' => $lesson->id,
                    'exam_marks_count' => $examMarks->count(),
                    'aggregated_exam_mark' => $examMark,
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
        \Log::info('═══ generateReportCardData END ═══');
        // Class position is computed in recalculatePositionsForCohort() after generation (store / bulk).
    }

    /**
     * Student-facing methods for report card access
     */
    public function studentIndex()
    {
        $student = auth('student')->user();
        
        if (!$student) {
            return redirect()->route('login')->with('error', 'Please login to access your report cards.');
        }

        // Get overall access status for the student
        $accessStatus = ReportCardService::getAccessStatus($student);

        // Get student's report cards with term-specific access status
        $reportCards = ReportCard::where('student_id', $student->id)
                                ->with(['course', 'academicYear', 'term'])
                                ->orderByDesc('academic_year_id')
                                ->orderBy('term_id')
                                ->get()
                                ->map(function($reportCard) use ($student) {
                                    // Get access status for THIS SPECIFIC term only
                                    $termAccessStatus = ReportCardService::getAccessStatus($student, [$reportCard->term_id]);
                                    
                                    // Attach term-specific access info to the report card
                                    $reportCard->termAccessStatus = $termAccessStatus;
                                    $reportCard->canAccessThisTerm = $termAccessStatus['can_access'];
                                    
                                    return $reportCard;
                                });
        
        return view('students.report-cards.index', compact('reportCards', 'accessStatus'));
    }

    public function studentShow(ReportCard $reportCard)
    {
        $student = auth('student')->user();
        
        if (!$student || $reportCard->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this report card.');
        }

        // Check if student can access this report card
        // Pass the specific term_id for term-specific fee checking
        $accessStatus = ReportCardService::getAccessStatus($student, $reportCard->term_id);
        
        // If student has outstanding fees for this term, show payment page with Flutterwave button
        if (!$accessStatus['payment_status']['is_fully_paid']) {
            $outstanding = $accessStatus['payment_status']['outstanding'];
            return view('students.report-cards.payment', compact('outstanding', 'reportCard'));
        }
        
        // If report card not yet available, show countdown
        if ($accessStatus['countdown_data']) {
            $countdownData = $accessStatus['countdown_data'];
            $availableDate = $accessStatus['available_date'];
            return view('students.report-cards.countdown', compact('reportCard', 'countdownData', 'availableDate'));
        }
        
        if (!$accessStatus['can_access']) {
            return redirect()->route('student.report-cards.index')
                ->with('error', $accessStatus['reason']);
        }

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

        return view('students.report-cards.show', compact('reportCard', 'schoolInfo'));
    }

    public function studentDownload(ReportCard $reportCard)
    {
        $student = auth('student')->user();
        
        if (!$student || $reportCard->student_id !== $student->id) {
            abort(403, 'Unauthorized access to this report card.');
        }

        // Check if student can access this report card
        // Pass the specific term_id for term-specific fee checking
        if (!ReportCardService::canAccessReportCard($student, $reportCard->term_id)) {
            $accessStatus = ReportCardService::getAccessStatus($student, $reportCard->term_id);
            return redirect()->route('student.report-cards.show', $reportCard)
                ->with('error', $accessStatus['reason']);
        }

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

        // Use the professional admin print design
        $pdf = Pdf::loadView('admin.report-cards.print', compact('reportCard', 'schoolInfo'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'margin-top'        => 0,
                'margin-bottom'     => 0,
                'margin-left'       => 0,
                'margin-right'      => 0,
                'isRemoteEnabled'   => true,
                'isHtml5ParserEnabled' => true,
            ]);

        $termSlug = Str::slug($reportCard->term->name ?? 'term');

        return $pdf->download(
            'report-card-'.Str::slug($reportCard->student->full_name).'-'.$reportCard->academicYear->year.'-'.$termSlug.'.pdf'
        );
    }

    /**
     * Show students with outstanding fees for report card generation
     */
    public function outstandingFees()
    {
        // Get all students with outstanding fees
        $allStudents = Student::with(['course', 'academicYear', 'branch'])->get();
        
        $studentsWithFees = [];
        foreach ($allStudents as $student) {
            $paymentStatus = FeePaymentService::getPaymentStatus($student);
            if ($paymentStatus['outstanding'] > 0) {
                $studentsWithFees[] = array_merge($student->toArray(), [
                    'outstanding_amount' => $paymentStatus['outstanding'],
                    'required_fees' => $paymentStatus['required_fees'],
                    'total_paid' => $paymentStatus['total_paid'],
                    'payment_percentage' => $paymentStatus['payment_percentage'],
                ]);
            }
        }

        // Sort by outstanding amount (highest first)
        usort($studentsWithFees, function ($a, $b) {
            return $b['outstanding_amount'] <=> $a['outstanding_amount'];
        });

        $courses = Course::all();
        $branches = Branch::all();

        return view('admin.report-cards.outstanding-fees', compact('studentsWithFees', 'courses', 'branches'));
    }

    /**
     * Send outstanding fees email to a single student
     */
    public function sendOutstandingFeesEmail(Student $student)
    {
        try {
            $paymentStatus = FeePaymentService::getPaymentStatus($student);
            
            if ($paymentStatus['outstanding'] <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student has no outstanding fees.'
                ], 400);
            }

            if (!$student->email) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student does not have a valid email address.'
                ], 400);
            }

            // Send email
            Mail::to($student->email)->send(
                new \App\Mail\StudentOutstandingFeesMail(
                    $student,
                    $paymentStatus['outstanding'],
                    $paymentStatus['required_fees'],
                    $paymentStatus['total_paid']
                )
            );

            return response()->json([
                'success' => true,
                'message' => "Email sent successfully to {$student->full_name}."
            ]);
        } catch (\Exception $e) {
            \Log::error('Error sending outstanding fees email: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending email: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send bulk emails to all students with outstanding fees
     */
    public function bulkSendOutstandingFeesEmails(Request $request)
    {
        try {
            $request->validate([
                'student_ids' => 'nullable|array',
                'student_ids.*' => 'exists:students,id',
            ]);

            // If no students specified, get all with outstanding fees
            if (empty($request->student_ids)) {
                $students = Student::all();
                $studentsToEmail = [];
                foreach ($students as $student) {
                    $paymentStatus = FeePaymentService::getPaymentStatus($student);
                    if ($paymentStatus['outstanding'] > 0 && $student->email) {
                        $studentsToEmail[] = [
                            'student' => $student,
                            'payment_status' => $paymentStatus
                        ];
                    }
                }
            } else {
                $students = Student::whereIn('id', $request->student_ids)->get();
                $studentsToEmail = [];
                foreach ($students as $student) {
                    $paymentStatus = FeePaymentService::getPaymentStatus($student);
                    if ($student->email) {
                        $studentsToEmail[] = [
                            'student' => $student,
                            'payment_status' => $paymentStatus
                        ];
                    }
                }
            }

            if (empty($studentsToEmail)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No students with valid email addresses found.'
                ], 400);
            }

            $sentCount = 0;
            $failedCount = 0;

            foreach ($studentsToEmail as $item) {
                try {
                    Mail::to($item['student']->email)->send(
                        new \App\Mail\StudentOutstandingFeesMail(
                            $item['student'],
                            $item['payment_status']['outstanding'],
                            $item['payment_status']['required_fees'],
                            $item['payment_status']['total_paid']
                        )
                    );
                    $sentCount++;
                } catch (\Exception $e) {
                    \Log::error("Failed to send email to {$item['student']->email}: " . $e->getMessage());
                    $failedCount++;
                }
            }

            $message = "Sent {$sentCount} email(s)";
            if ($failedCount > 0) {
                $message .= ". Failed to send {$failedCount} email(s).";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'sent_count' => $sentCount,
                'failed_count' => $failedCount
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in bulk sending outstanding fees emails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error sending emails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint for countdown timer
     */
    public function getCountdown()
    {
        try {
            $student = auth('student')->user();
            
            // If no student is authenticated, return general availability info
            if (!$student) {
                $countdownData = ReportCardService::getCountdownData();
                $setting = \App\Models\ReportCardSetting::getActive();
                
                if (!$setting) {
                    return response()->json([
                        'can_access' => false,
                        'reason' => 'Report card access not configured by administration',
                        'countdown_data' => null,
                        'available_date' => null,
                        'server_time' => now()->toISOString(),
                        'requires_auth' => true
                    ]);
                }
                
                return response()->json([
                    'can_access' => $setting->isAvailable(),
                    'reason' => $setting->isAvailable() ? 'Report cards available' : 'Report cards not yet available',
                    'countdown_data' => $setting->isAvailable() ? null : $countdownData,
                    'available_date' => $setting->report_card_available_at,
                    'server_time' => now()->toISOString(),
                    'requires_auth' => true
                ]);
            }

            $countdownData = ReportCardService::getCountdownData();
            $accessStatus = ReportCardService::getAccessStatus($student);

            return response()->json([
                'countdown_data' => $countdownData,
                'can_access' => $accessStatus['can_access'],
                'reason' => $accessStatus['reason'],
                'payment_status' => $accessStatus['payment_status'],
                'available_date' => $accessStatus['available_date'],
                'server_time' => now()->toISOString(),
                'requires_auth' => false
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in getCountdown: ' . $e->getMessage());
            return response()->json([
                'can_access' => false,
                'reason' => 'Unable to check report card status. Please try again later.',
                'countdown_data' => null,
                'available_date' => null,
                'server_time' => now()->toISOString(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * AJAX endpoint to get current outstanding fees status for a student
     */
    public function getStudentOutstandingStatus(Student $student)
    {
        try {
            $paymentStatus = FeePaymentService::getPaymentStatus($student);
            
            return response()->json([
                'success' => true,
                'student_id' => $student->id,
                'outstanding_amount' => (float) ($paymentStatus['outstanding'] ?? 0),
                'required_fees' => (float) ($paymentStatus['required_fees'] ?? 0),
                'total_paid' => (float) ($paymentStatus['total_paid'] ?? 0),
                'payment_percentage' => (float) ($paymentStatus['payment_percentage'] ?? 0),
                'has_outstanding' => ((float) ($paymentStatus['outstanding'] ?? 0)) > 0
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting student outstanding status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving student information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
