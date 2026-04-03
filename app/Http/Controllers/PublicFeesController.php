<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Course;
use App\Models\CourseTermFee;
use App\Models\Term;
use Illuminate\Http\Request;
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
        $courses = Course::whereHas('branches', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->orderBy('course_name')->get();

        return response()->json([
            'success' => true,
            'courses' => $courses->map(function($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->course_name,
                    'code' => $course->course_code,
                    'abbreviation' => $course->abbreviation,
                ];
            })
        ]);
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
