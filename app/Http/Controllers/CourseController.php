<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Services\AcademicYearContext;
use App\Models\Branch; // Add this line
use Illuminate\Support\Facades\DB;


class CourseController extends Controller
{
    public function getCourseFee(Request $request)
{
    $course = \App\Models\Course::with(['courseTermFees','courseTermBranchFees'])->find($request->course_id);

    if (!$course) {
        return response()->json(['error' => 'Course not found'], 404);
    }

    $branchId = $request->query('branch_id');
    $termId   = $request->query('term_id');

    // Prefer branch-specific term fee if branch_id and term_id provided
    if ($branchId && $termId) {
        $bf = \App\Models\CourseTermBranchFee::where('course_id', $course->id)
            ->where('term_id', $termId)
            ->where('branch_id', $branchId)
            ->where('is_active', true)
            ->first();

        if ($bf) {
            return response()->json([
                'registration_fee' => $bf->registration_fee,
                'tuition_fee' => $bf->tuition_fee,
                'practical_fee' => $bf->practical_fee,
                'trip_fee' => $bf->trip_fee,
            ]);
        }
    }

    // Next prefer general term fee for the term
    if ($termId) {
        $tf = \App\Models\CourseTermFee::where('course_id', $course->id)
            ->where('term_id', $termId)
            ->where('is_active', true)
            ->first();

        if ($tf) {
            return response()->json([
                'registration_fee' => $tf->registration_fee,
                'tuition_fee' => $tf->tuition_fee,
                'practical_fee' => $tf->practical_fee,
                'trip_fee' => $tf->trip_fee,
            ]);
        }
    }

    // Fallback to legacy course-level fees
    return response()->json([
        'registration_fee' => $course->registration_fee,
        'tuition_fee' => $course->tuition_fee,
        'practical_fee' => $course->practical_fee,
        'trip_fee' => $course->trip_fee,
    ]);
}
    public function index()
    {
        $courses = Course::with('branches')->get(); // eager load branches
        return view('admin.course.index', compact('courses')); // adjust path if needed
    }

    public function create()
    {
        $branches = \App\Models\Branch::all();
        $academicYears = \App\Models\AcademicYear::all();
        return view('admin.course.create', compact('branches', 'academicYears'));
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
    $rules = [
        'course_name' => 'required|string|max:255',
        'abbreviation' => 'required|string|max:10',
        'course_type' => 'required|string|max:50',
        'academic_year_id' => 'required|exists:academic_years,id',
        'branches' => 'nullable|array',
        'branches.*' => 'string',
    ];
    
    // Only validate term fees if setup_fees_now is checked
    if ($request->input('setup_fees_now') == 'true' || $request->boolean('setup_fees_now')) {
        $rules['term_fees'] = 'nullable|array';
        $rules['term_fees.*.term_id'] = 'required|exists:terms,id';
        $rules['term_fees.*.registration_fee'] = 'required|numeric|min:0';
        $rules['term_fees.*.tuition_fee'] = 'required|numeric|min:0';
        $rules['term_fees.*.practical_fee'] = 'required|numeric|min:0';
        $rules['term_fees.*.trip_fee'] = 'required|numeric|min:0';
    }
    
    $validated = $request->validate($rules);

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

    DB::beginTransaction();

    try {
        $course = Course::create([
            'course_name'      => $validated['course_name'],
            'abbreviation'     => $validated['abbreviation'],
            'course_type'      => $validated['course_type'],
            'academic_year_id' => $validated['academic_year_id'],
            // Keep legacy fees for backward compatibility
            'registration_fee' => $request->input('registration_fee', 0),
            'tuition_fee'      => $request->input('tuition_fee', 0),
            'practical_fee'    => $request->input('practical_fee', 0),
            'trip_fee'         => $request->input('trip_fee', 0),
            'discount_fee'     => $request->input('discount_fee', 0),
        ]);

        $randomNumber = rand(100, 999);
        $courseCode = strtoupper($course->abbreviation) . $course->id . $randomNumber;

        $course->course_code = $courseCode;
        $course->save();

        // Create term-based fees only if they are provided
        if (!empty($validated['term_fees'])) {
            foreach ($validated['term_fees'] as $termFeeData) {
                \App\Models\CourseTermFee::create([
                    'course_id' => $course->id,
                    'term_id' => $termFeeData['term_id'],
                    'registration_fee' => $termFeeData['registration_fee'],
                    'tuition_fee' => $termFeeData['tuition_fee'],
                    'practical_fee' => $termFeeData['practical_fee'],
                    'trip_fee' => $termFeeData['trip_fee'],
                    'student_card_fee' => 2000, // Fixed fee per term
                    'is_active' => true,
                ]);
            }
        }

        if (!empty($validated['branches'])) {
            if (in_array('all', $validated['branches'], true)) {
                $allBranchIds = \App\Models\Branch::pluck('id')->toArray();
                $course->branches()->sync($allBranchIds);
            } else {
                $branchIds = array_unique(array_filter($validated['branches'], fn($b) => is_numeric($b)));
                $course->branches()->sync($branchIds);
            }
        }

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create course: ' . $e->getMessage()]);
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
    ]);

    // Sync branches
    if (!empty($validated['branches'])) {
        if (in_array('all', $validated['branches'], true)) {
            $allBranchIds = \App\Models\Branch::pluck('id')->toArray();
            $course->branches()->sync($allBranchIds);
        } else {
            $branchIds = array_unique(array_filter($validated['branches'], fn($b) => is_numeric($b)));
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

    try {
        // Delete in the correct order to avoid foreign key constraints
        
        // 1. Delete report card subjects that reference lessons of this course
        $lessonIds = $course->lessons()->pluck('id');
        if ($lessonIds->isNotEmpty()) {
            \App\Models\ReportCardSubject::whereIn('lesson_id', $lessonIds)->delete();
            
            // 2. Delete lesson units for these lessons (if table exists)
            try {
                \App\Models\LessonUnit::whereIn('lesson_id', $lessonIds)->delete();
            } catch (\Exception $e) {
                // LessonUnit table might not exist, continue
            }
            
            // 3. Delete lesson-exam relationships for these lessons (if table exists)
            try {
                \DB::table('lesson_exam')->whereIn('lesson_id', $lessonIds)->delete();
            } catch (\Exception $e) {
                // lesson_exam table might not exist, continue
            }
            
            // 4. Delete lesson-term relationships for these lessons (if table exists)
            try {
                \DB::table('lesson_term')->whereIn('lesson_id', $lessonIds)->delete();
            } catch (\Exception $e) {
                // lesson_term table might not exist, continue
            }
            
            // 5. Delete the lessons themselves
            $course->lessons()->delete();
        }
        
        // 6. Delete other related records (only if tables exist)
        $course->marks()->delete();
        
        // 7. Try to delete transcripts (if table exists)
        try {
            $course->transcripts()->delete();
        } catch (\Exception $e) {
            // transcripts table might not exist, continue
        }
        
        // 8. Detach relationships (many-to-many)
        $course->branches()->detach();
        $course->exams()->detach();
        
        // 9. Finally delete the course
        $course->delete();

        return redirect()->route('admin.course.index')->with('success', 'Course and all related data deleted successfully.');
        
    } catch (\Illuminate\Database\QueryException $e) {
        // If there's still a foreign key constraint, provide a more helpful error
        if ($e->getCode() == '23000') {
            return redirect()->route('admin.course.index')
                ->with('error', 'Cannot delete this course because it is referenced by other records. Please contact administrator.');
        }
        
        // Re-throw the exception if it's something else
        throw $e;
    }
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

public function getCourseStudents($courseId)
{
    try {
        $course = Course::find($courseId);
        
        if (!$course) {
            return response()->json(['error' => 'Course not found'], 404);
        }
        
        $students = $course->students()
            ->with(['branch', 'academicYear'])
            ->get(['id', 'full_name', 'reg_number', 'branch_id', 'academic_year_id'])
            ->map(function ($student) use ($course) {
                return [
                    'id' => $student->id,
                    'full_name' => $student->full_name,
                    'reg_number' => $student->reg_number,
                    'class' => $course->course_name,
                    'branch_name' => $student->branch->name ?? 'N/A',
                    'academic_year' => $student->academicYear->year ?? 'N/A',
                ];
            });

        return response()->json($students);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Failed to load students: ' . $e->getMessage()], 500);
    }
}

public function getBranchesByCourse(Request $request) {
    $courseId = $request->query('course_id');
    if (!$courseId) {
        return response()->json([]);
    }

    // Use the Course -> branches relationship (many-to-many pivot) instead of expecting a course_id FK on branches
    $course = Course::with('branches')->find($courseId);
    if (!$course) {
        return response()->json([]);
    }

    $branches = $course->branches()->get(['id', 'name']);
    return response()->json($branches);
}

// Course Fee Assignment Methods
public function assignFees($courseId)
{
    $course = Course::with(['branches', 'courseTermFees.term', 'courseTermFees.term.academicYear'])->findOrFail($courseId);
    $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
    
    return view('admin.course.assign_fees', compact('course', 'terms'));
}

public function testAssignFees($courseId)
{
    $course = Course::with(['branches', 'courseTermFees.term', 'courseTermFees.term.academicYear'])->findOrFail($courseId);
    $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
    
    return view('admin.course.test_assign_fees', compact('course', 'terms'));
}

public function storeFees(Request $request, $courseId)
{
    $course = Course::findOrFail($courseId);
    
    // Filter out empty term fee rows where term_id is not selected
    $termFees = collect($request->input('term_fees', []))->filter(function($termFee) {
        return !empty($termFee['term_id']);
    })->toArray();
    
    // Update request with filtered data
    $request->merge(['term_fees' => $termFees]);
    
    $validated = $request->validate([
        'term_fees' => 'required|array|min:1',
        'term_fees.*.term_id' => 'required|exists:terms,id',
        'term_fees.*.registration_fee' => 'required|numeric|min:0',
        'term_fees.*.tuition_fee' => 'required|numeric|min:0',
        'term_fees.*.practical_fee' => 'required|numeric|min:0',
        'term_fees.*.trip_fee' => 'required|numeric|min:0',
        // Optional branch-specific fees per term: term_fees.*.branch_fees.{branchId} => array with fees
        'term_fees.*.branch_fees' => 'nullable|array',
        'term_fees.*.branch_fees.*.registration_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.tuition_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.practical_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.trip_fee' => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // Deactivate existing general term fees for this course
        \App\Models\CourseTermFee::where('course_id', $courseId)->update(['is_active' => false]);
        // Deactivate existing branch-specific term fees
        \App\Models\CourseTermBranchFee::where('course_id', $courseId)->update(['is_active' => false]);

        // Create or update term fees (general or branch-specific)
        foreach ($validated['term_fees'] as $termFeeData) {
            $termId = $termFeeData['term_id'];

            // If branch_fees provided for this term, create branch-specific entries
            if (!empty($termFeeData['branch_fees']) && is_array($termFeeData['branch_fees'])) {
                foreach ($termFeeData['branch_fees'] as $branchId => $bf) {
                    $registration = $bf['registration_fee'] ?? ($termFeeData['registration_fee'] ?? 0);
                    $tuition      = $bf['tuition_fee'] ?? ($termFeeData['tuition_fee'] ?? 0);
                    $practical    = $bf['practical_fee'] ?? ($termFeeData['practical_fee'] ?? 0);
                    $trip         = $bf['trip_fee'] ?? ($termFeeData['trip_fee'] ?? 0);

                    \App\Models\CourseTermBranchFee::updateOrCreate(
                        [
                            'course_id' => $courseId,
                            'term_id'   => $termId,
                            'branch_id' => $branchId,
                        ],
                        [
                            'registration_fee' => $registration,
                            'tuition_fee' => $tuition,
                            'practical_fee' => $practical,
                            'trip_fee' => $trip,
                            'student_card_fee' => 2000,
                            'is_active' => true,
                        ]
                    );
                }
            } else {
                // No branch-specific fees -> create general term fee
                \App\Models\CourseTermFee::updateOrCreate(
                    [
                        'course_id' => $courseId,
                        'term_id' => $termId
                    ],
                    [
                        'registration_fee' => $termFeeData['registration_fee'],
                        'tuition_fee' => $termFeeData['tuition_fee'],
                        'practical_fee' => $termFeeData['practical_fee'],
                        'trip_fee' => $termFeeData['trip_fee'],
                        'student_card_fee' => 2000,
                        'is_active' => true,
                    ]
                );
            }
        }

        DB::commit();
        return redirect()->route('admin.course.index')->with('success', 'Course fees assigned successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors(['error' => 'Failed to assign fees: ' . $e->getMessage()]);
    }
}

public function editFees($courseId)
{
    $course = Course::with(['branches', 'courseTermFees.term', 'courseTermFees.term.academicYear'])->findOrFail($courseId);
    $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
    
    return view('admin.course.edit_fees', compact('course', 'terms'));
}

public function updateFees(Request $request, $courseId)
{
    $course = Course::findOrFail($courseId);
    
    $validated = $request->validate([
        'term_fees' => 'required|array|min:1',
        'term_fees.*.term_id' => 'required|exists:terms,id',
        'term_fees.*.registration_fee' => 'required|numeric|min:0',
        'term_fees.*.tuition_fee' => 'required|numeric|min:0',
        'term_fees.*.practical_fee' => 'required|numeric|min:0',
        'term_fees.*.trip_fee' => 'required|numeric|min:0',
        'term_fees.*.branch_fees' => 'nullable|array',
        'term_fees.*.branch_fees.*.registration_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.tuition_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.practical_fee' => 'nullable|numeric|min:0',
        'term_fees.*.branch_fees.*.trip_fee' => 'nullable|numeric|min:0',
    ]);

    DB::beginTransaction();

    try {
        // Deactivate existing general term fees for this course
        \App\Models\CourseTermFee::where('course_id', $courseId)->update(['is_active' => false]);
        // Deactivate existing branch-specific term fees
        \App\Models\CourseTermBranchFee::where('course_id', $courseId)->update(['is_active' => false]);

        // Create or update term fees (general or branch-specific)
        foreach ($validated['term_fees'] as $termFeeData) {
            $termId = $termFeeData['term_id'];

            if (!empty($termFeeData['branch_fees']) && is_array($termFeeData['branch_fees'])) {
                foreach ($termFeeData['branch_fees'] as $branchId => $bf) {
                    $registration = $bf['registration_fee'] ?? ($termFeeData['registration_fee'] ?? 0);
                    $tuition      = $bf['tuition_fee'] ?? ($termFeeData['tuition_fee'] ?? 0);
                    $practical    = $bf['practical_fee'] ?? ($termFeeData['practical_fee'] ?? 0);
                    $trip         = $bf['trip_fee'] ?? ($termFeeData['trip_fee'] ?? 0);

                    \App\Models\CourseTermBranchFee::updateOrCreate(
                        [
                            'course_id' => $courseId,
                            'term_id'   => $termId,
                            'branch_id' => $branchId,
                        ],
                        [
                            'registration_fee' => $registration,
                            'tuition_fee' => $tuition,
                            'practical_fee' => $practical,
                            'trip_fee' => $trip,
                            'student_card_fee' => 2000,
                            'is_active' => true,
                        ]
                    );
                }
            } else {
                \App\Models\CourseTermFee::updateOrCreate(
                    [
                        'course_id' => $courseId,
                        'term_id' => $termId
                    ],
                    [
                        'registration_fee' => $termFeeData['registration_fee'],
                        'tuition_fee' => $termFeeData['tuition_fee'],
                        'practical_fee' => $termFeeData['practical_fee'],
                        'trip_fee' => $termFeeData['trip_fee'],
                        'student_card_fee' => 2000,
                        'is_active' => true,
                    ]
                );
            }
        }

        DB::commit();
        return redirect()->route('admin.course.index')->with('success', 'Course fees updated successfully!');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->withInput()->withErrors(['error' => 'Failed to update fees: ' . $e->getMessage()]);
    }
}


}
