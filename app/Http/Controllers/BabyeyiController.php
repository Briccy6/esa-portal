<?php

namespace App\Http\Controllers;

use App\Models\Babyeyi;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Course;
use App\Models\CourseTermFee;
use App\Models\SchoolInformation;
use App\Models\Student;
use App\Models\User;
use App\Services\BabyeyiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BabyeyiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of babyeyis.
     */
    public function index(Request $request)
    {
        $babyeyis = Babyeyi::with(['academicYear', 'term', 'course', 'creator', 'editor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Route to appropriate view based on user role
        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.index';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.index';
        } else {
            $view = 'babyeyi.public-index';
        }
        
        return view($view, compact('babyeyis'));
    }

    /**
     * Show the form for creating a new babyeyi.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $terms = collect();
        $courses = Course::orderBy('course_name')->get();
        $courseTypes = $this->getCourseTypes();

        // Route to appropriate view based on user role
        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.create';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.create';
        } else {
            $view = 'babyeyi.create';
        }

        return view($view, compact('academicYears', 'terms', 'courses', 'courseTypes'));
    }

    /**
     * Get course types from database.
     */
    private function getCourseTypes()
    {
        return DB::table('courses')
            ->select('course_type')
            ->distinct()
            ->whereNotNull('course_type')
            ->pluck('course_type')
            ->toArray();
    }

    /**
     * Store a newly created babyeyi (initial template).
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'selection_type' => 'required|in:single,multiple,by_type',
            'course_id' => 'nullable|exists:courses,id',
            'course_type' => 'nullable|string',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        // Create empty content - secretary will fill it in the editor
        $content = '';

        $babyeyi = Babyeyi::create([
            'academic_year_id' => $validated['academic_year_id'],
            'term_id' => $validated['term_id'],
            'course_id' => $validated['course_id'] ?? null,
            'course_type' => $validated['selection_type'] === 'by_type' ? $validated['course_type'] : null,
            'created_by' => Auth::id(),
            'content' => $content,
            'reference_number' => Babyeyi::generateReferenceNumber(),
            'status' => Babyeyi::STATUS_DRAFT,
        ]);

        return redirect()->route('babyeyi.edit', $babyeyi)
            ->with('success', 'Babyeyi template created! Now add your content using the editor below.');
    }

    /**
     * Generate initial content based on selection.
     */
    private function generateInitialContent($data)
    {
        $term = Term::find($data['term_id']);
        $academicYear = AcademicYear::find($data['academic_year_id']);
        
        $termStart = $term->start_date->format('F j, Y');
        $termEnd = $term->end_date->format('F j, Y');

        $content = "<h2>Babyeyi - Term Registration Document</h2>";
        $content .= "<p><strong>Academic Year:</strong> {$academicYear->year}</p>";
        $content .= "<p><strong>Term:</strong> {$term->name}</p>";
        $content .= "<p><strong>Term Duration:</strong> {$termStart} to {$termEnd}</p>";

        // Add course information based on selection type
        if ($data['selection_type'] === 'single' && !empty($data['course_id'])) {
            $course = Course::find($data['course_id']);
            $content .= "<p><strong>Course:</strong> {$course->name}</p>";
            $content .= $this->generateFeeTable($course->id, $data['term_id']);
        } elseif ($data['selection_type'] === 'by_type') {
            $courseType = $data['course_type'];
            $content .= "<p><strong>Course Type:</strong> {$courseType}</p>";
            $content .= "<p><em>Multiple courses of this type will be included.</em></p>";
        } else {
            $content .= "<p><em>Multiple courses included in this babyeyi.</em></p>";
        }

        // Add trip section
        $content .= "<h3>Trip Information</h3>";
        $content .= "<p><strong>Place to Visit:</strong> [To be updated]</p>";
        $content .= "<p><strong>Trip Fees:</strong> RWF [To be updated]</p>";

        // Add class entry conditions
        $content .= "<h3>Conditions to Enter Class</h3>";
        $content .= "<ul>";
        $content .= "<li>All fees must be paid before the start of the term</li>";
        $content .= "<li>Student must be registered in the system</li>";
        $content .= "<li>Student must have valid identification</li>";
        $content .= "</ul>";

        return $content;
    }

    /**
     * Generate fee table for a specific course and term.
     */
    private function generateFeeTable($courseId, $termId)
    {
        $courseFees = DB::table('course_term_fees')
            ->where('course_id', $courseId)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->first();

        if (!$courseFees) {
            return "<p><em>Fee schedule not found for this course and term.</em></p>";
        }

        $table = "<h3>Fee Structure</h3>";
        $table .= "<table border='1' cellpadding='8' style='width: 100%; border-collapse: collapse;'>";
        $table .= "<tr style='background-color: #f0f0f0;'>";
        $table .= "<th>Fee Type</th>";
        $table .= "<th>Amount (RWF)</th>";
        $table .= "</tr>";
        $table .= "<tr><td>Registration Fee</td><td>" . number_format($courseFees->registration_fee, 2) . "</td></tr>";
        $table .= "<tr><td>Tuition Fee</td><td>" . number_format($courseFees->tuition_fee, 2) . "</td></tr>";
        $table .= "<tr><td>Practical Fee</td><td>" . number_format($courseFees->practical_fee, 2) . "</td></tr>";
        $table .= "<tr><td>Trip Fee</td><td>" . number_format($courseFees->trip_fee, 2) . "</td></tr>";
        $table .= "<tr><td>Student Card Fee</td><td>" . number_format($courseFees->student_card_fee, 2) . "</td></tr>";
        
        $total = $courseFees->registration_fee + $courseFees->tuition_fee + $courseFees->practical_fee + $courseFees->trip_fee + $courseFees->student_card_fee;
        $table .= "<tr style='background-color: #e8e8e8; font-weight: bold;'>";
        $table .= "<td>Total</td>";
        $table .= "<td>" . number_format($total, 2) . "</td>";
        $table .= "</tr>";
        $table .= "</table>";

        return $table;
    }

    /**
     * Show the form for editing babyeyi (Rich text editor for accountant).
     */
    public function edit(Babyeyi $babyeyi)
    {
        $babyeyi->load(['academicYear', 'term', 'course', 'creator']);
        // Route to appropriate view based on user role
        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.edit';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.edit';
        } else {
            $view = 'babyeyi.edit';
        }

        return view($view, compact('babyeyi'));
    }

    /**
     * Update the babyeyi (Save edited content).
     */
    public function update(Request $request, Babyeyi $babyeyi)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'content' => 'required|string',
            'trip_place' => 'nullable|string',
            'trip_fees' => 'nullable|numeric|min:0',
            'trip_known' => 'boolean',
        ]);

        $babyeyi->update([
            'title' => $validated['title'],
            'content' => $validated['content'],
            'trip_place' => $validated['trip_place'],
            'trip_fees' => $validated['trip_fees'],
            'trip_known' => $request->has('trip_known'),
            'edited_by' => Auth::id(),
            'status' => Babyeyi::STATUS_DRAFT,
        ]);

        return redirect()->route('babyeyi.preview', $babyeyi)
            ->with('success', 'Babyeyi updated! Please preview before publishing.');
    }

    /**
     * Show preview of the babyeyi.
     */
    public function preview(Babyeyi $babyeyi)
    {
        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.preview';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'babyeyi.preview';
        } else {
            $view = 'babyeyi.preview';
        }

        return view($view, compact('babyeyi', 'schoolInfo'));
    }

    /**
     * Publish the babyeyi.
     */
    public function publish(Request $request, Babyeyi $babyeyi)
    {
        $babyeyi->update([
            'status' => Babyeyi::STATUS_PUBLISHED,
        ]);

        return redirect()->route('babyeyi.show', $babyeyi)
            ->with('success', 'Babyeyi published successfully!');
    }

    /**
     * Show the published babyeyi.
     */
    public function show(Babyeyi $babyeyi)
    {
        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.show';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.show';
        } else {
            $view = 'babyeyi.public-show';
        }

        return view($view, compact('babyeyi', 'schoolInfo'));
    }

    /**
     * Generate and download babyeyi as PDF.
     */
    public function download(Babyeyi $babyeyi)
    {
        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        $headTeacher = User::role('head_teacher')->first();

        $pdf = PDF::loadView('babyeyi.pdf', compact('babyeyi', 'schoolInfo', 'headTeacher'));
        
        return $pdf->download("babyeyi-{$babyeyi->reference_number}.pdf");
    }

    /**
     * Get terms by academic year (AJAX).
     */
    public function getTerms($academicYearId)
    {
        $terms = Term::where('academic_year_id', $academicYearId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($terms);
    }

    /**
     * Get courses by filter type (AJAX).
     */
    public function getCourses(Request $request)
    {
        $query = Course::query();

        if ($request->has('course_type') && !empty($request->course_type)) {
            $query->where('course_type', $request->course_type);
        }

        $courses = $query->orderBy('name')->get(['id', 'name', 'course_type']);

        return response()->json($courses);
    }

    /**
     * PUBLIC: List all published babyeyis.
     */
    public function publicIndex()
    {
        $babyeyis = Babyeyi::where('status', Babyeyi::STATUS_PUBLISHED)
            ->with(['academicYear', 'term', 'course'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $schoolInfo = SchoolInformation::getActive();

        return view('babyeyi.public-index', compact('babyeyis', 'schoolInfo'));
    }

    /**
     * PUBLIC: Show a published babyeyi document.
     */
    public function publicShow(Babyeyi $babyeyi)
    {
        // Only allow viewing published documents
        if ($babyeyi->status !== Babyeyi::STATUS_PUBLISHED) {
            abort(404, 'Document not found');
        }

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        
        // Try to find a head teacher, principal, or admin user
        // First check for staff with DOS role, then admin
        $headTeacher = User::whereIn('role', ['DOS', 'admin'])
            ->orderBy('role')
            ->first();

        return view('babyeyi.public-show', compact('babyeyi', 'schoolInfo', 'headTeacher'));
    }

    /**
     * PUBLIC: Download babyeyi as PDF (public access).
     */
    public function publicDownload(Babyeyi $babyeyi)
    {
        // Only allow downloading published documents
        if ($babyeyi->status !== Babyeyi::STATUS_PUBLISHED) {
            abort(404, 'Document not found');
        }

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        
        // Try to find a head teacher, principal, or admin user
        // First check for staff with DOS role, then admin
        $headTeacher = User::whereIn('role', ['DOS', 'admin'])
            ->orderBy('role')
            ->first();

        $pdf = PDF::loadView('babyeyi.public-pdf', compact('babyeyi', 'schoolInfo', 'headTeacher'));
        
        return $pdf->download("babyeyi-{$babyeyi->reference_number}.pdf");
    }

    /**
     * STUDENT: Get student's course-specific babyeyi documents.
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        $student = Student::with('course')->where('email', $user->email)->first();

        // Debug info
        $debug = [];
        $debug['user_email'] = $user->email;
        $debug['student_found'] = !is_null($student);
        $debug['student_id'] = $student?->id;
        $debug['student_course_id'] = $student?->course_id;

        if (!$student || !$student->course_id) {
            $debug['message'] = 'No course assigned to student';
            
            return view('babyeyi.student-dashboard', [
                'babyeyis' => collect(),
                'schoolInfo' => SchoolInformation::getActive(),
                'message' => 'No course assigned to your account.',
                'student' => $student,
                'courseTermFees' => collect(),
                'debug' => $debug
            ]);
        }

        // Get the student's course
        $studentCourse = $student->course;
        $debug['course_name'] = $studentCourse?->name;
        $debug['course_type'] = $studentCourse?->course_type;

        // Query 1: Published babyeyis for this exact course
        $exactCourseQuery = Babyeyi::where('status', Babyeyi::STATUS_PUBLISHED)
            ->where('course_id', $student->course_id);
        
        $debug['exact_course_count'] = $exactCourseQuery->count();

        $babyeyis = $exactCourseQuery
            ->with(['academicYear', 'term', 'course', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Query 2: If no exact course match, try by course_type
        if ($babyeyis->isEmpty() && $studentCourse && $studentCourse->course_type) {
            $courseTypeQuery = Babyeyi::where('status', Babyeyi::STATUS_PUBLISHED)
                ->where('course_type', $studentCourse->course_type);
            
            $debug['course_type_count'] = $courseTypeQuery->count();
            
            $babyeyis = $courseTypeQuery
                ->with(['academicYear', 'term', 'course', 'creator'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $debug['course_type_count'] = 0;
        }

        // Query 3: ALWAYS include babyeyis for ALL COURSES (course_id = NULL AND course_type = NULL)
        $allCoursesQuery = Babyeyi::where('status', Babyeyi::STATUS_PUBLISHED)
            ->whereNull('course_id')
            ->whereNull('course_type');
        
        $debug['all_courses_count'] = $allCoursesQuery->count();
        
        $allCoursesBabyeyis = $allCoursesQuery
            ->with(['academicYear', 'term', 'course', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Merge all babyeyis (remove duplicates by ID)
        $babyeyis = $babyeyis->merge($allCoursesBabyeyis)->unique('id')->sortByDesc('created_at');

        $debug['total_babyeyis_found'] = $babyeyis->count();

        // Build fee data for each babyeyi
        $babyeyisWithFees = $babyeyis->map(function($babyeyi) use ($student) {
            $courseTermFees = CourseTermFee::where('course_id', $student->course_id)
                ->where('term_id', $babyeyi->term_id)
                ->where('is_active', true)
                ->first();
            
            $babyeyi->courseTermFees = $courseTermFees;
            return $babyeyi;
        });

        $schoolInfo = SchoolInformation::getActive();

        return view('babyeyi.student-dashboard', [
            'babyeyis' => $babyeyisWithFees,
            'schoolInfo' => $schoolInfo,
            'student' => $student,
            'debug' => $debug
        ]);
    }

    /**
     * STUDENT: Show student's course-specific babyeyi with fee details.
     */
    public function studentShow(Babyeyi $babyeyi)
    {
        $user = Auth::user();
        $student = Student::with('course')->where('email', $user->email)->first();

        if (!$student) {
            abort(403, 'You do not have access to this document.');
        }

        // Verify access - student can view babyeyis if:
        // 1. Exact course match: babyeyi->course_id === student->course_id
        // 2. Course type match: babyeyi->course_type === student->course->course_type
        // 3. For all courses: babyeyi->course_id IS NULL AND babyeyi->course_type IS NULL
        
        $hasAccess = false;

        // Check 1: Exact course match
        if ($babyeyi->course_id && $babyeyi->course_id === $student->course_id) {
            $hasAccess = true;
        }
        // Check 2: Course type match
        elseif ($babyeyi->course_type && $student->course && $babyeyi->course_type === $student->course->course_type) {
            $hasAccess = true;
        }
        // Check 3: For all courses (no specific course_id or course_type)
        elseif (!$babyeyi->course_id && !$babyeyi->course_type) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'You do not have access to this document.');
        }

        // Get course term fees for this student's course
        $courseTermFees = CourseTermFee::where('course_id', $student->course_id)
            ->where('term_id', $babyeyi->term_id)
            ->where('is_active', true)
            ->first();

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        
        // Try to find a head teacher, principal, or admin user
        // First check for staff with DOS role, then admin
        $headTeacher = User::whereIn('role', ['DOS', 'admin'])
            ->orderBy('role')
            ->first();

        return view('babyeyi.student-show', compact('babyeyi', 'schoolInfo', 'headTeacher', 'student', 'courseTermFees'));
    }

    /**
     * STUDENT: Download student's course-specific babyeyi as PDF with fee details.
     */
    public function studentDownload(Babyeyi $babyeyi)
    {
        $user = Auth::user();
        $student = Student::with('course')->where('email', $user->email)->first();

        if (!$student) {
            abort(403, 'You do not have access to this document.');
        }

        // Verify access - student can download babyeyis if:
        // 1. Exact course match: babyeyi->course_id === student->course_id
        // 2. Course type match: babyeyi->course_type === student->course->course_type
        // 3. For all courses: babyeyi->course_id IS NULL AND babyeyi->course_type IS NULL
        
        $hasAccess = false;

        // Check 1: Exact course match
        if ($babyeyi->course_id && $babyeyi->course_id === $student->course_id) {
            $hasAccess = true;
        }
        // Check 2: Course type match
        elseif ($babyeyi->course_type && $student->course && $babyeyi->course_type === $student->course->course_type) {
            $hasAccess = true;
        }
        // Check 3: For all courses (no specific course_id or course_type)
        elseif (!$babyeyi->course_id && !$babyeyi->course_type) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'You do not have access to this document.');
        }

        // Get course term fees for this student's course
        $courseTermFees = CourseTermFee::where('course_id', $student->course_id)
            ->where('term_id', $babyeyi->term_id)
            ->where('is_active', true)
            ->first();

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        
        // Try to find a head teacher, principal, or admin user
        // First check for staff with DOS role, then admin
        $headTeacher = User::whereIn('role', ['DOS', 'admin'])
            ->orderBy('role')
            ->first();

        $pdf = PDF::loadView('babyeyi.student-pdf', compact('babyeyi', 'schoolInfo', 'headTeacher', 'student', 'courseTermFees'));
        
        return $pdf->download("babyeyi-{$babyeyi->reference_number}.pdf");
    }

    /**
     * Delete a babyeyi document.
     */
    public function destroy(Babyeyi $babyeyi)
    {
        // Authorize deletion through policy
        $this->authorize('delete', $babyeyi);

        // Store reference before deletion
        $reference = $babyeyi->reference_number;
        
        // Delete the babyeyi
        $deleted = $babyeyi->delete();

        if ($deleted) {
            return redirect()->route('babyeyi.index')
                ->with('success', "Babyeyi document ({$reference}) has been deleted successfully.");
        }

        return redirect()->back()
            ->with('error', 'Failed to delete the document. Please try again.');
    }

    /**
     * Show form for bulk generation of babyeyis for all courses in a term.
     */
    public function bulkCreate()
    {
        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $terms = collect();

        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.bulk-create';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.bulk-create';
        } else {
            $view = 'babyeyi.bulk-create';
        }

        return view($view, compact('academicYears', 'terms'));
    }

    /**
     * Process bulk generation of babyeyis for a specific term and all its courses.
     */
    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'auto_publish' => 'boolean',
        ]);

        $service = new BabyeyiService();
        $term = Term::findOrFail($validated['term_id']);

        try {
            // Generate babyeyis for all courses with term fees
            $result = $service->generateBabyeyisForTerm(
                $validated['term_id'],
                $validated['academic_year_id'],
                Auth::id() ?? Auth::guard('admin')->id() ?? Auth::guard('staff')->id()
            );

            if ($validated['auto_publish'] ?? false) {
                $publishCount = $service->publishBabyeyisForTerm($validated['term_id']);
                $status = 'success';
                $message = "Successfully created and published {$result['created']} babyeyis for {$term->name}!";
                if ($publishCount > 0) {
                    $message .= " {$publishCount} documents published.";
                }
            } else {
                $status = 'success';
                $message = "Successfully created {$result['created']} babyeyis for {$term->name}! Please review and publish them.";
            }

            if (!empty($result['errors'])) {
                $message .= "\n\nWarnings: " . implode("; ", $result['errors']);
                $status = 'warning';
            }

            return redirect()->route('babyeyi.index')
                ->with(['status' => $status, 'message' => $message]);

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating babyeyis: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the babyeyi bulk operations page.
     */
    public function bulkOperations()
    {
        $terms = Term::with('academicYear')->orderBy('created_at', 'desc')->get();
        $statistics = [];

        foreach ($terms as $term) {
            $service = new BabyeyiService();
            $statistics[$term->id] = $service->getTermStatistics($term->id);
        }

        if (Auth::guard('admin')->check()) {
            $view = 'admin.babyeyi.bulk-operations';
        } elseif (Auth::guard('staff')->check()) {
            $view = 'staff.babyeyi.bulk-operations';
        } else {
            $view = 'babyeyi.bulk-operations';
        }

        return view($view, compact('terms', 'statistics'));
    }

    /**
     * Publish all draft babyeyis for a specific term.
     */
    public function bulkPublish(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
        ]);

        $service = new BabyeyiService();
        $publishCount = $service->publishBabyeyisForTerm($validated['term_id']);
        $term = Term::findOrFail($validated['term_id']);

        return redirect()->route('babyeyi.index')
            ->with('success', "Published {$publishCount} babyeyis for {$term->name}!");
    }

    /**
     * Regenerate all babyeyis for a term (useful after fee updates).
     */
    public function regenerateForTerm(Request $request)
    {
        $validated = $request->validate([
            'term_id' => 'required|exists:terms,id',
        ]);

        $babyeyis = Babyeyi::where('term_id', $validated['term_id'])->get();
        $service = new BabyeyiService();
        $count = 0;

        foreach ($babyeyis as $babyeyi) {
            try {
                $service->regenerateBabyeyi($babyeyi);
                $count++;
            } catch (\Exception $e) {
                \Log::error("Error regenerating babyeyi {$babyeyi->id}: " . $e->getMessage());
            }
        }

        $term = Term::findOrFail($validated['term_id']);

        return redirect()->route('babyeyi.index')
            ->with('success', "Regenerated content for {$count} babyeyis for {$term->name}!");
    }

    /**
     * Quick action: Generate and publish babyeyis for a term in one step.
     */
    public function quickGenerate(Request $request, $termId)
    {
        $term = Term::findOrFail($termId);
        $service = new BabyeyiService();

        try {
            $result = $service->generateBabyeyisForTerm(
                $termId,
                $term->academic_year_id,
                Auth::id() ?? Auth::guard('admin')->id() ?? Auth::guard('staff')->id()
            );

            // Auto-publish all generated babyeyis
            $publishCount = $service->publishBabyeyisForTerm($termId);

            return redirect()->route('babyeyi.index')
                ->with('success', 
                    "Generated and published {$result['created']} babyeyis for {$term->name}! " .
                    ($publishCount > 0 ? "All {$publishCount} documents are now live for students." : "")
                );

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
