<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseTermFee;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDF;

class PublicFeesController extends Controller
{
    /**
     * Display fees schedule page
     */
    public function index()
    {
        $branches = Branch::with('courses')->orderBy('name')->get();
        
        return view('public.fees.index', compact('branches'));
    }

    /**
     * Get courses for a specific branch (AJAX)
     */
    public function getCoursesByBranch($branchId)
    {
        try {
            // First try: Query using the pivot table (belongsToMany)
            $courses = Course::whereHas('branches', function($q) use ($branchId) {
                $q->wherePivot('branch_id', $branchId);
            })->orderBy('course_name')->get();

            // Second try: Join the pivot table directly
            if ($courses->isEmpty()) {
                $courses = \DB::table('courses')
                    ->join('branch_course', 'courses.id', '=', 'branch_course.course_id')
                    ->where('branch_course.branch_id', $branchId)
                    ->select('courses.id', 'courses.course_name', 'courses.course_code', 'courses.abbreviation')
                    ->orderBy('course_name')
                    ->distinct()
                    ->get();
            }

            // Third try: Direct foreign key (if course has branch_id column)
            if ($courses->isEmpty()) {
                $courses = Course::where('branch_id', $branchId)
                    ->orderBy('course_name')
                    ->get();
            }

            // Convert to array format
            $formattedCourses = collect($courses)->map(function($course) {
                return [
                    'id' => $course->id ?? $course->course_id,
                    'name' => $course->course_name,
                    'code' => $course->course_code,
                    'abbreviation' => $course->abbreviation,
                ];
            })->values();

            return response()->json([
                'success' => true,
                'courses' => $formattedCourses,
                'count' => $formattedCourses->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Courses loading error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading courses. Please try again.'
            ], 500);
        }
    }

    /**
     * Get fees for a specific course across all terms
     */
    public function getCourseFees($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Get all fees for this course organized by academic year and term
        $fees = CourseTermFee::where('course_id', $courseId)
            ->where('is_active', true)
            ->with(['term' => function($q) {
                $q->with('academicYear')->orderBy('name');
            }])
            ->get()
            ->groupBy(function($fee) {
                return $fee->term->academicYear->name ?? 'Unknown Year';
            })
            ->sortKeys()
            ->reverse();

        if ($fees->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No fees available for this course'
            ], 404);
        }

        $formattedFees = [];
        foreach ($fees as $academicYear => $yearFees) {
            $formattedFees[$academicYear] = $yearFees->map(function($fee) {
                return [
                    'id' => $fee->id,
                    'term_name' => $fee->term->name,
                    'term_id' => $fee->term->id,
                    'registration_fee' => (float) $fee->registration_fee,
                    'tuition_fee' => (float) $fee->tuition_fee,
                    'practical_fee' => (float) $fee->practical_fee,
                    'trip_fee' => (float) $fee->trip_fee,
                    'student_card_fee' => (float) $fee->student_card_fee,
                    'total_fees' => (float) $fee->getTotalFeesAttribute(),
                    'notes' => $fee->notes,
                ];
            })->values();
        }

        return response()->json([
            'success' => true,
            'course' => [
                'id' => $course->id,
                'name' => $course->course_name,
                'code' => $course->course_code,
                'abbreviation' => $course->abbreviation,
            ],
            'fees' => $formattedFees
        ]);
    }

    /**
     * Download fees schedule as PDF
     */
    public function downloadPDF($courseId, $academicYearName = null)
    {
        $course = Course::findOrFail($courseId);
        
        $fees = CourseTermFee::where('course_id', $courseId)
            ->where('is_active', true)
            ->with(['term' => function($q) {
                $q->with('academicYear');
            }])
            ->get()
            ->groupBy(function($fee) {
                return $fee->term->academicYear->name ?? 'Unknown Year';
            })
            ->sortKeys()
            ->reverse();

        // Filter by specific academic year if provided
        if ($academicYearName && isset($fees[$academicYearName])) {
            $fees = collect([$academicYearName => $fees[$academicYearName]]);
        }

        $pdf = PDF::loadView('public.fees.pdf', [
            'course' => $course,
            'fees' => $fees,
            'schoolName' => config('app.school_name', 'Excellent Servant Academy'),
        ]);

        return $pdf->download('fees-schedule-' . slug($course->course_name) . '.pdf');
    }

    /**
     * Debug endpoint - shows database structure and relationships
     */
    public function debugInfo()
    {
        $output = '<h1>Public Fees Debug Info</h1>';
        $output .= '<style>
            body { font-family: Arial; margin: 20px; }
            table { border-collapse: collapse; width: 100%; margin: 20px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; }
            .section { margin: 20px 0; padding: 10px; background: #f9f9f9; }
            .error { color: red; }
            .success { color: green; }
        </style>';

        // Check branches
        $branches = Branch::all();
        $output .= '<div class="section"><h2>Branches (' . $branches->count() . ')</h2>';
        $output .= '<table><tr><th>ID</th><th>Name</th><th>Location</th><th>Courses Count</th></tr>';
        foreach ($branches as $branch) {
            $courseCount = $branch->courses()->count();
            $output .= "<tr>
                <td>{$branch->id}</td>
                <td>{$branch->name}</td>
                <td>{$branch->location}</td>
                <td class='" . ($courseCount > 0 ? 'success' : 'error') . "'>{$courseCount}</td>
            </tr>";
        }
        $output .= '</table></div>';

        // Check courses
        $courses = Course::limit(20)->get();
        $output .= '<div class="section"><h2>Courses (showing first 20 of ' . Course::count() . ')</h2>';
        $output .= '<table><tr><th>ID</th><th>Name</th><th>Code</th><th>Branches Count</th></tr>';
        foreach ($courses as $course) {
            $branchCount = $course->branches()->count();
            $output .= "<tr>
                <td>{$course->id}</td>
                <td>{$course->course_name}</td>
                <td>{$course->course_code}</td>
                <td class='" . ($branchCount > 0 ? 'success' : 'error') . "'>{$branchCount}</td>
            </tr>";
        }
        $output .= '</table></div>';

        // Check branch_course pivot table
        $pivotCount = DB::table('branch_course')->count();
        $output .= '<div class="section"><h2>branch_course Pivot Table</h2>';
        $output .= '<p>Total records: <span class="' . ($pivotCount > 0 ? 'success' : 'error') . '">' . $pivotCount . '</span></p>';
        
        if ($pivotCount > 0) {
            $pivotRecords = DB::table('branch_course')->limit(20)->get();
            $output .= '<table><tr><th>Branch ID</th><th>Course ID</th></tr>';
            foreach ($pivotRecords as $record) {
                $output .= "<tr><td>{$record->branch_id}</td><td>{$record->course_id}</td></tr>";
            }
            $output .= '</table>';
        }
        $output .= '</div>';

        // Test query for each branch
        $output .= '<div class="section"><h2>Query Test (Using First 3 Branches)</h2>';
        foreach ($branches->take(3) as $branch) {
            $output .= '<h3>Branch: ' . $branch->name . ' (ID: ' . $branch->id . ')</h3>';
            
            // Method 1
            $courses1 = Course::whereHas('branches', function($q) use ($branch) {
                $q->wherePivot('branch_id', $branch->id);
            })->count();
            $output .= '<p>Method 1 (wherePivot): <span class="' . ($courses1 > 0 ? 'success' : 'error') . '">' . $courses1 . ' courses</span></p>';
            
            // Method 2
            $courses2 = DB::table('courses')
                ->join('branch_course', 'courses.id', '=', 'branch_course.course_id')
                ->where('branch_course.branch_id', $branch->id)
                ->distinct()
                ->count();
            $output .= '<p>Method 2 (direct join): <span class="' . ($courses2 > 0 ? 'success' : 'error') . '">' . $courses2 . ' courses</span></p>';
            
            // Method 3
            $courses3 = Course::where('branch_id', $branch->id)->count();
            $output .= '<p>Method 3 (direct FK): <span class="' . ($courses3 > 0 ? 'success' : 'error') . '">' . $courses3 . ' courses</span></p>';
        }
        $output .= '</div>';

        return response($output)->header('Content-Type', 'text/html');
    }
}
