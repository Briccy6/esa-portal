<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseTermFee;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            Log::info("getCoursesByBranch called with branchId: {$branchId}");
            
            // Verify branch exists
            $branch = Branch::find($branchId);
            if (!$branch) {
                Log::error("Branch not found: {$branchId}");
                return response()->json([
                    'success' => false,
                    'message' => "Branch with ID {$branchId} not found in database",
                    'debug' => [
                        'branchId' => $branchId,
                        'totalBranches' => Branch::count(),
                        'totalCourses' => Course::count(),
                        'totalPivotRecords' => DB::table('branch_course')->count(),
                    ]
                ], 404);
            }

            // First try: Join the pivot table directly (this is the reliable method)
            $courses = DB::table('courses')
                ->join('branch_course', 'courses.id', '=', 'branch_course.course_id')
                ->where('branch_course.branch_id', $branchId)
                ->select('courses.id', 'courses.course_name', 'courses.course_code', 'courses.abbreviation')
                ->orderBy('courses.course_name')
                ->distinct()
                ->get();
            
            Log::info("Direct JOIN query returned: " . $courses->count() . " courses");

            // Second try: Direct foreign key (if course has branch_id column)
            if ($courses->isEmpty()) {
                $courses = Course::where('branch_id', $branchId)
                    ->orderBy('course_name')
                    ->get();
                
                Log::info("Direct FK query returned: " . $courses->count() . " courses");
            }

            // Convert to array format - handle both Model and stdClass objects
            $formattedCourses = collect($courses)->map(function($course) {
                return [
                    'id' => $course->id ?? $course->course_id,
                    'name' => $course->course_name ?? $course->name ?? 'Unnamed',
                    'code' => $course->course_code ?? $course->code ?? '',
                    'abbreviation' => $course->abbreviation ?? '',
                ];
            })->values();

            Log::info("Formatted " . $formattedCourses->count() . " courses for response");

            return response()->json([
                'success' => true,
                'courses' => $formattedCourses,
                'count' => $formattedCourses->count(),
                'branch' => [
                    'id' => $branch->id,
                    'name' => $branch->name,
                    'location' => $branch->location
                ]
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $errorCode = $e->getCode();
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
            $errorTrace = $e->getTraceAsString();
            
            Log::error("Courses loading error: {$errorMessage}", [
                'code' => $errorCode,
                'file' => $errorFile,
                'line' => $errorLine,
                'trace' => $errorTrace
            ]);
            
            // Return detailed error info for debugging
            return response()->json([
                'success' => false,
                'message' => "Error loading courses: {$errorMessage}",
                'debug' => [
                    'errorType' => class_basename($e),
                    'errorMessage' => $errorMessage,
                    'errorFile' => $errorFile,
                    'errorLine' => $errorLine,
                    'branchId' => $branchId,
                    'databaseStatus' => 'Connected',
                    'totalBranches' => Branch::count(),
                    'totalCourses' => Course::count(),
                    'totalPivotRecords' => DB::table('branch_course')->count(),
                    'trace' => explode("\n", $errorTrace)
                ]
            ], 500);
        }
    }

    /**
     * Get fees for a specific course across all terms
     */
    public function getCourseFees($courseId)
    {
        try {
            Log::info("getCourseFees called with courseId: {$courseId}");
            
            // Verify course exists
            $course = Course::findOrFail($courseId);
        
            // Get all fees for this course organized by academic year and term
            $fees = CourseTermFee::where('course_id', $courseId)
                ->where('is_active', true)
                ->with(['term' => function($q) {
                    $q->with('academicYear')->orderBy('name');
                }])
                ->get();
            
            // Ensure academic year data is available
            $fees = $fees->map(function($fee) {
                // Make sure academicYear relationship is loaded
                if (!$fee->term || !$fee->term->academicYear) {
                    Log::warning("Fee without complete relationship data: {$fee->id}");
                }
                return $fee;
            });
            
            // Group by academic year with better error handling
            $fees = $fees->groupBy(function($fee) {
                if ($fee->term && $fee->term->academicYear) {
                    return $fee->term->academicYear->name;
                }
                // Fallback: use current academic year
                return date('Y') . ' - ' . (date('Y') + 1);
            })
            ->sortKeys()
            ->reverse();

            if ($fees->isEmpty()) {
                Log::warning("No active fees found for course {$courseId}");
                return response()->json([
                    'success' => false,
                    'message' => "No fees have been configured for this course. Please contact the administration.",
                    'debug' => [
                        'courseId' => $courseId,
                        'courseName' => $course->course_name,
                        'totalFeesRecords' => CourseTermFee::where('course_id', $courseId)->count(),
                        'activeFeesRecords' => CourseTermFee::where('course_id', $courseId)->where('is_active', true)->count(),
                    ]
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

            Log::info("Formatted fees for course {$courseId}: " . count($formattedFees) . " academic years");

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
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error("Error loading fees: {$errorMessage}", [
                'courseId' => $courseId,
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => "Error loading fees: {$errorMessage}",
                'debug' => [
                    'errorType' => class_basename($e),
                    'errorMessage' => $errorMessage,
                    'courseId' => $courseId,
                    'courseFeeRecords' => CourseTermFee::where('course_id', $courseId)->count(),
                ]
            ], 500);
        }
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

        return $pdf->download('fees-schedule-' . Str::slug($course->course_name) . '.pdf');
    }

    /**
     * Debug endpoint - shows database structure and relationships
     */
    public function debugInfo()
    {
        try {
            $output = '<h1>Public Fees Debug Info</h1>';
            $output .= '<style>
                body { font-family: Arial; margin: 20px; background: #f5f5f5; }
                table { border-collapse: collapse; width: 100%; margin: 20px 0; background: white; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #2c3e50; color: white; font-weight: bold; }
                tr:nth-child(even) { background-color: #f9f9f9; }
                .section { margin: 20px 0; padding: 15px; background: white; border-left: 4px solid #3498db; }
                .error { color: #e74c3c; font-weight: bold; }
                .success { color: #27ae60; font-weight: bold; }
                .warning { color: #f39c12; font-weight: bold; }
                h2 { color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 10px; }
                .code { background: #ecf0f1; padding: 10px; border-radius: 5px; font-family: monospace; overflow-x: auto; }
            </style>';

            // Check branch
            $branchId = 4;
            $branch = Branch::find($branchId);
            
            $output .= '<div class="section">';
            $output .= '<h2>Selected Branch (ID: ' . $branchId . ')</h2>';
            if ($branch) {
                $output .= '<p><strong>Name:</strong> ' . $branch->name . '</p>';
                $output .= '<p><strong>Location:</strong> ' . $branch->location . '</p>';
            } else {
                $output .= '<p class="error">❌ Branch with ID ' . $branchId . ' not found!</p>';
            }
            $output .= '</div>';

            // Check all branches
            $branches = Branch::all();
            $output .= '<div class="section">';
            $output .= '<h2>All Branches (' . $branches->count() . ')</h2>';
            $output .= '<table><tr><th>ID</th><th>Name</th><th>Location</th></tr>';
            foreach ($branches as $b) {
                $output .= "<tr><td>{$b->id}</td><td>{$b->name}</td><td>{$b->location}</td></tr>";
            }
            $output .= '</table></div>';

            // Test branch_course pivot for this branch
            $output .= '<div class="section">';
            $output .= '<h2>Branch-Course Relationships</h2>';
            $pivotRecords = DB::table('branch_course')->where('branch_id', $branchId)->get();
            $output .= '<h3>Records in branch_course table for Branch ' . $branchId . ': ' . $pivotRecords->count() . '</h3>';
            
            if ($pivotRecords->count() > 0) {
                $output .= '<table><tr><th>Branch ID</th><th>Course ID</th></tr>';
                foreach ($pivotRecords as $record) {
                    $output .= "<tr><td>{$record->branch_id}</td><td>{$record->course_id}</td></tr>";
                }
                $output .= '</table>';
            } else {
                $output .= '<p class="error">❌ No branch_course records found for Branch ' . $branchId . '</p>';
            }
            $output .= '</div>';

            // Check courses for this branch using each method
            $output .= '<div class="section">';
            $output .= '<h2>Query Test Results for Branch ' . $branchId . '</h2>';
            
            // Method 1
            $courses1 = Course::whereHas('branches', function($q) use ($branchId) {
                $q->wherePivot('branch_id', $branchId);
            })->get();
            $output .= '<h3>Method 1: whereHas with wherePivot</h3>';
            $output .= '<p class="' . ($courses1->count() > 0 ? 'success' : 'error') . '">' . $courses1->count() . ' courses found</p>';
            if ($courses1->count() > 0) {
                $output .= '<table><tr><th>ID</th><th>Name</th><th>Code</th></tr>';
                foreach ($courses1 as $c) {
                    $output .= "<tr><td>{$c->id}</td><td>{$c->course_name}</td><td>{$c->course_code}</td></tr>";
                }
                $output .= '</table>';
            }
            
            // Method 2
            $courses2 = DB::table('courses')
                ->join('branch_course', 'courses.id', '=', 'branch_course.course_id')
                ->where('branch_course.branch_id', $branchId)
                ->select('courses.id', 'courses.course_name', 'courses.course_code', 'courses.abbreviation')
                ->distinct()
                ->get();
            $output .= '<h3>Method 2: Direct JOIN on branch_course</h3>';
            $output .= '<p class="' . ($courses2->count() > 0 ? 'success' : 'error') . '">' . $courses2->count() . ' courses found</p>';
            if ($courses2->count() > 0) {
                $output .= '<table><tr><th>ID</th><th>Name</th><th>Code</th></tr>';
                foreach ($courses2 as $c) {
                    $output .= "<tr><td>{$c->id}</td><td>{$c->course_name}</td><td>{$c->course_code}</td></tr>";
                }
                $output .= '</table>';
            }
            
            // Method 3
            $courses3 = Course::where('branch_id', $branchId)->get();
            $output .= '<h3>Method 3: Direct branch_id FK</h3>';
            $output .= '<p class="' . ($courses3->count() > 0 ? 'success' : 'error') . '">' . $courses3->count() . ' courses found</p>';
            if ($courses3->count() > 0) {
                $output .= '<table><tr><th>ID</th><th>Name</th><th>Code</th></tr>';
                foreach ($courses3 as $c) {
                    $output .= "<tr><td>{$c->id}</td><td>{$c->course_name}</td><td>{$c->course_code}</td></tr>";
                }
                $output .= '</table>';
            }
            
            $output .= '</div>';

            // Show total courses in database
            $totalCourses = Course::count();
            $output .= '<div class="section">';
            $output .= '<h2>Database Summary</h2>';
            $output .= '<table>';
            $output .= '<tr><td><strong>Total Branches:</strong></td><td>' . Branch::count() . '</td></tr>';
            $output .= '<tr><td><strong>Total Courses:</strong></td><td>' . $totalCourses . '</td></tr>';
            $output .= '<tr><td><strong>Total branch_course records:</strong></td><td>' . DB::table('branch_course')->count() . '</td></tr>';
            $output .= '</table></div>';

            return response($output)->header('Content-Type', 'text/html');
        } catch (\Exception $e) {
            return response('<h1>Error</h1><p>' . $e->getMessage() . '</p><pre>' . $e->getTraceAsString() . '</pre>')
                ->header('Content-Type', 'text/html');
        }
    }
}
