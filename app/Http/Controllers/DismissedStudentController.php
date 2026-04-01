<?php

namespace App\Http\Controllers;

use App\Models\DismissedStudent;
use App\Models\Branch;
use App\Models\Course;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class DismissedStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = DismissedStudent::with(['branch', 'course', 'academicYear', 'dismissedBy']);

        // Filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                  ->orWhere('reg_number', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('dismissal_date_from')) {
            $query->whereDate('dismissal_date', '>=', $request->dismissal_date_from);
        }

        if ($request->filled('dismissal_date_to')) {
            $query->whereDate('dismissal_date', '<=', $request->dismissal_date_to);
        }

        $dismissedStudents = $query->orderBy('dismissal_date', 'desc')->paginate(20);

        // Get filter options
        $branches = Branch::all();
        $courses = Course::all();
        $academicYears = AcademicYear::all();

        // Statistics
        $totalDismissed = DismissedStudent::count();
        $dismissedThisMonth = DismissedStudent::whereMonth('dismissal_date', now()->month)
            ->whereYear('dismissal_date', now()->year)->count();
        $dismissedThisYear = DismissedStudent::whereYear('dismissal_date', now()->year)->count();

        return view('admin.dismissed-students.index', compact(
            'dismissedStudents',
            'branches',
            'courses',
            'academicYears',
            'totalDismissed',
            'dismissedThisMonth',
            'dismissedThisYear'
        ));
    }

    public function show(DismissedStudent $dismissedStudent)
    {
        $dismissedStudent->load(['branch', 'course', 'academicYear', 'dismissedBy']);

        return view('admin.dismissed-students.show', compact('dismissedStudent'));
    }

    public function restore(Request $request, DismissedStudent $dismissedStudent)
    {
        try {
            // Create new student from dismissed student
            $student = new \App\Models\Student();
            
            // Copy all attributes except dismissal-specific ones
            foreach ($dismissedStudent->getAttributes() as $key => $value) {
                if (!in_array($key, ['id', 'dismissal_reason', 'dismissal_notes', 'dismissed_by', 'dismissal_date', 'original_student_id', 'created_at', 'updated_at'])) {
                    $student->$key = $value;
                }
            }
            
            $student->save();

            // Log the restoration
            \Log::info("Student restored from dismissed records", [
                'dismissed_student_id' => $dismissedStudent->id,
                'new_student_id' => $student->id,
                'student_name' => $student->full_name,
                'restored_by' => auth('admin')->id(),
            ]);

            // Optionally delete the dismissed record
            $dismissalNotes = $request->input('restoration_notes', 'Student restored to active records');
            $dismissedStudent->delete();

            return redirect()->route('admin.dismissed-students.index')
                ->with('success', "Student '{$student->full_name}' has been successfully restored to active records.");

        } catch (\Exception $e) {
            \Log::error("Error restoring student from dismissed records", [
                'dismissed_student_id' => $dismissedStudent->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'An error occurred while restoring the student: ' . $e->getMessage());
        }
    }
}
