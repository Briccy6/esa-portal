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
}
