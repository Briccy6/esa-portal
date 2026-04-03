<?php

namespace App\Http\Controllers;

use App\Models\Babyeyi;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Course;
use App\Models\SchoolInformation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class BabyeyiController extends Controller
{
    /**
     * Display a listing of babyeyis.
     */
    public function index(Request $request)
    {
        $babyeyis = Babyeyi::with(['academicYear', 'term', 'course', 'creator', 'editor'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('babyeyi.index', compact('babyeyis'));
    }

    /**
     * Show the form for creating a new babyeyi.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderBy('name', 'desc')->get();
        $terms = collect();
        $courses = collect();
        $courseTypes = $this->getCourseTypes();

        return view('babyeyi.create', compact('academicYears', 'terms', 'courses', 'courseTypes'));
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
        ]);

        // Generate initial content based on selections
        $content = $this->generateInitialContent($validated);

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
            ->with('success', 'Babyeyi template created! Accountant can now edit it.');
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
        $content .= "<p><strong>Academic Year:</strong> {$academicYear->name}</p>";
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

        return view('babyeyi.edit', compact('babyeyi'));
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

        return view('babyeyi.preview', compact('babyeyi', 'schoolInfo'));
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

        return view('babyeyi.show', compact('babyeyi', 'schoolInfo'));
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
        $headTeacher = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['head_teacher', 'principal', 'director']);
        })->first();

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
        $headTeacher = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['head_teacher', 'principal', 'director']);
        })->first();

        $pdf = PDF::loadView('babyeyi.public-pdf', compact('babyeyi', 'schoolInfo', 'headTeacher'));
        
        return $pdf->download("babyeyi-{$babyeyi->reference_number}.pdf");
    }

    /**
     * STUDENT: Get student's course-specific babyeyi documents.
     */
    public function studentDashboard()
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        if (!$student || !$student->course_id) {
            return view('babyeyi.student-dashboard', [
                'babyeyis' => collect(),
                'schoolInfo' => SchoolInformation::getActive(),
                'message' => 'No course assigned to your account.'
            ]);
        }

        // Get published babyeyis for this student's course
        $babyeyis = Babyeyi::where('status', Babyeyi::STATUS_PUBLISHED)
            ->where(function($query) use ($student) {
                $query->where('course_id', $student->course_id)
                      ->orWhere('course_type', $student->course->course_type ?? null);
            })
            ->with(['academicYear', 'term', 'course', 'creator'])
            ->orderBy('created_at', 'desc')
            ->get();

        $schoolInfo = SchoolInformation::getActive();

        return view('babyeyi.student-dashboard', compact('babyeyis', 'schoolInfo', 'student'));
    }

    /**
     * STUDENT: Show student's course-specific babyeyi with fee details.
     */
    public function studentShow(Babyeyi $babyeyi)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        // Verify access
        if (!$student || 
            (!($babyeyi->course_id === $student->course_id || 
              $babyeyi->course_type === $student->course->course_type))) {
            abort(403, 'You do not have access to this document.');
        }

        // Get course term fees for this student's course
        $courseTermFees = DB::table('course_term_fees')
            ->where('course_id', $student->course_id)
            ->where('term_id', $babyeyi->term_id)
            ->where('is_active', true)
            ->first();

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        $headTeacher = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['head_teacher', 'principal', 'director']);
        })->first();

        return view('babyeyi.student-show', compact('babyeyi', 'schoolInfo', 'headTeacher', 'student', 'courseTermFees'));
    }

    /**
     * STUDENT: Download student's course-specific babyeyi as PDF with fee details.
     */
    public function studentDownload(Babyeyi $babyeyi)
    {
        $user = Auth::user();
        $student = Student::where('email', $user->email)->first();

        // Verify access
        if (!$student || 
            (!($babyeyi->course_id === $student->course_id || 
              $babyeyi->course_type === $student->course->course_type))) {
            abort(403, 'You do not have access to this document.');
        }

        // Get course term fees for this student's course
        $courseTermFees = DB::table('course_term_fees')
            ->where('course_id', $student->course_id)
            ->where('term_id', $babyeyi->term_id)
            ->where('is_active', true)
            ->first();

        $babyeyi->load(['academicYear', 'term', 'course', 'creator', 'editor']);
        $schoolInfo = SchoolInformation::getActive();
        $headTeacher = User::whereHas('roles', function($q) {
            $q->whereIn('name', ['head_teacher', 'principal', 'director']);
        })->first();

        $pdf = PDF::loadView('babyeyi.student-pdf', compact('babyeyi', 'schoolInfo', 'headTeacher', 'student', 'courseTermFees'));
        
        return $pdf->download("babyeyi-{$babyeyi->reference_number}.pdf");
    }
}
