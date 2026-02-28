<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Branch; // Add this line


class CourseController extends Controller
{
    public function getCourseFee(Request $request)
{
    $course = \App\Models\Course::find($request->course_id);
    return response()->json([
        'registration_fee' => $course ? $course->registration_fee : null,
    ]);
}
    public function index()
    {
        $courses = Course::with('branches')->get(); // eager load branches
        return view('admin.course.index', compact('courses')); // adjust path if needed
    }

    public function create()
    {
        // You will probably want to pass branches here for selection in the form
        $branches = \App\Models\Branch::all();
        return view('admin.course.create', compact('branches'));
    }

    public function search(Request $request)
    {
        $search = $request->q;
        $courses = Course::where('course_name', 'like', '%' . $search . '%')
                         ->select('id', 'course_name as text')
                         ->limit(10)
                         ->get();

        return response()->json($courses);
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'course_name' => 'required|string|max:255',
        'abbreviation' => 'required|string|max:10',
        'course_type' => 'required|string|max:50',
        'branches' => 'nullable|array',
        'branches.*' => 'string',
        'registration_fee' => 'required|numeric|min:0',
        'tuition_fee'      => 'required|numeric|min:0',
        'practical_fee'    => 'nullable|numeric|min:0',
        'trip_fee'         => 'nullable|numeric|min:0',
        'discount_fee'     => 'nullable|numeric|min:0',
    ]);

    // Check for duplicate course in the same branch
    if (!empty($validated['branches']) && !in_array('all', $validated['branches'])) {
        $duplicate = \App\Models\Course::where('course_name', $validated['course_name'])
            ->whereHas('branches', function($q) use ($validated) {
                $q->whereIn('branches.id', array_filter($validated['branches'], fn($b) => is_numeric($b)));
            })
            ->exists();

        if ($duplicate) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['course_name' => 'A course with this name already exists in one of the selected branches.']);
        }
    }

    // 🔥 FIX: fetch ongoing academic year
    $ongoingAcademicYear = AcademicYear::where('is_ongoing', true)->first();

    if (!$ongoingAcademicYear) {
        return redirect()->back()->withErrors(['academic_year' => 'No ongoing academic year is set.']);
    }

    $totalCost = $this->calculateTotalCost(
        $request->tuition_fee,
        $request->practical_fee,
        $request->trip_fee,
        $request->discount_fee
    );

    $course = Course::create([
        'course_name'      => $validated['course_name'],
        'abbreviation'     => $validated['abbreviation'],
        'course_type'      => $validated['course_type'],
        'academic_year_id' => $ongoingAcademicYear->id,
        'registration_fee' => $request->input('registration_fee'),
        'tuition_fee'      => $request->input('tuition_fee'),
        'practical_fee'    => $request->input('practical_fee', null),
        'trip_fee'         => $request->input('trip_fee', null),
        'discount_fee'     => $request->input('discount_fee', null),
        'total_cost'      => $totalCost,
    ]);

    $randomNumber = rand(100, 999);
    $courseCode = strtoupper($course->abbreviation) . $course->id . $randomNumber;

    $course->course_code = $courseCode;
    $course->save();

    if (!empty($validated['branches'])) {
        if (in_array('all', $validated['branches'])) {
            $allBranchIds = \App\Models\Branch::pluck('id')->toArray();
            $course->branches()->sync($allBranchIds);
        } else {
            $branchIds = array_filter($validated['branches'], fn($b) => is_numeric($b));
            $course->branches()->sync($branchIds);
        }
    }

    return redirect()->back()->with('success', 'Course created successfully! Course Code: ' . $courseCode);
}

public function edit($id)
{
    $course = Course::with('branches')->findOrFail($id);
    $branches = \App\Models\Branch::all();
    $academicYears = \App\Models\AcademicYear::all(); // fetch all years
    return view('admin.course.edit', compact('course', 'branches', 'academicYears'));
}
public function update(Request $request, $id)
{
    $validated = $request->validate([
        'course_name' => 'required|string|max:255|unique:courses,course_name,' . $id,
        'abbreviation' => 'required|string|max:10',
        'course_type' => 'required|string|max:50',
        'academic_year_id' => 'required|exists:academic_years,id',
        'branches' => 'nullable|array',
        'branches.*' => 'string',
        'registration_fee' => 'required|numeric|min:0',
        'tuition_fee'      => 'required|numeric|min:0',
        'practical_fee'    => 'nullable|numeric|min:0',
        'trip_fee'         => 'nullable|numeric|min:0',
        'discount_fee'     => 'nullable|numeric|min:0',
    ]);

    $course = Course::findOrFail($id);

    // Check if any fee field changed
    $feeFields = ['tuition_fee', 'practical_fee', 'trip_fee', 'discount_fee'];
    $feeChanged = false;
    foreach ($feeFields as $field) {
        $oldValue = $course->$field ?? 0;
        $newValue = $request->input($field, 0);
        if ($oldValue != $newValue) {
            $feeChanged = true;
            break;
        }
    }

    $totalCost = $course->total_cost;
    if ($feeChanged) {
        $totalCost = $this->calculateTotalCost(
            $request->input('tuition_fee', 0),
            $request->input('practical_fee', 0),
            $request->input('trip_fee', 0),
            $request->input('discount_fee', 0)
        );
    }

    $course->update([
        'course_name'      => $validated['course_name'],
        'abbreviation'     => $validated['abbreviation'],
        'course_type'      => $validated['course_type'],
        'academic_year_id' => $validated['academic_year_id'],
        'registration_fee' => $request->input('registration_fee'),
        'tuition_fee'      => $request->input('tuition_fee'),
        'practical_fee'    => $request->input('practical_fee', null),
        'trip_fee'         => $request->input('trip_fee', null),
        'discount_fee'     => $request->input('discount_fee', null),
        'total_cost' => $totalCost,
    ]);

    // Sync branches
    if (!empty($validated['branches'])) {
        if (in_array('all', $validated['branches'])) {
            $allBranchIds = \App\Models\Branch::pluck('id')->toArray();
            $course->branches()->sync($allBranchIds);
        } else {
            $branchIds = array_filter($validated['branches'], fn($b) => is_numeric($b));
            $course->branches()->sync($branchIds);
        }
    } else {
        $course->branches()->detach();
    }

    return redirect()->route('admin.course.index')->with('success', 'Course updated successfully.');
}
public function destroy($id)
{
    $course = Course::findOrFail($id);

    // Detach related branches before deleting, if relationship exists
    $course->branches()->detach();

    $course->delete();

    return redirect()->route('admin.course.index')->with('success', 'Course deleted successfully.');
}

// Get branches for a given academic year (for AJAX)
public function getBranchesByAcademicYear(Request $request)
{
    $yearId = $request->query('year_id');
    if (!$yearId) {
        return response()->json([]);
    }

    $courseIds = Course::where('academic_year_id', $yearId)->pluck('id');
    if ($courseIds->isEmpty()) {
        return response()->json([]);
    }

    $branchIds = DB::table('branch_course')
        ->whereIn('course_id', $courseIds)
        ->pluck('branch_id')
        ->unique();

    if ($branchIds->isEmpty()) {
        return response()->json([]);
    }

    $branches = \App\Models\Branch::whereIn('id', $branchIds)->get(['id', 'name']);

    return response()->json($branches);
}

// Get courses for selected branches (for AJAX)
public function getCoursesByBranches(Request $request)
{
    $branchIds = $request->query('branch_ids', []);
    $courses = Course::whereHas('branches', function($q) use ($branchIds) {
        $q->whereIn('branches.id', $branchIds);
    })->get(['id', 'course_name as name']);
    return response()->json($courses);
}

public function getAllBranches()
{
    $branches = \App\Models\Branch::all(['id', 'name']);
    return response()->json($branches);
}

public function getBranchesByCourse(Request $request) {
    $courseId = $request->query('course_id');
    if (!$courseId) {
        return response()->json([]);
    }
    $branches = Branch::where('course_id', $courseId)->get(['id', 'name']);
    return response()->json($branches);
}


private function calculateTotalCost($tuition, $practical, $trip, $discount)
{
    $subtotal = floatval($tuition) + floatval($practical) + floatval($trip);
    if ($discount > 0 && $discount <= 100) {
        return round($subtotal - ($subtotal * ($discount / 100)), 2);
    }
    return round($subtotal, 2);
}
}
