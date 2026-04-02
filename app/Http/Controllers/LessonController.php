<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Lesson;
use App\Models\Term;
use App\Models\LessonUnit;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;
use App\Models\TimetableSession;
use App\Models\ReportCardSubject;
use App\Models\Mark;
use App\Models\Attendance;
use App\Models\TeacherDaily;

class LessonController extends Controller
{
    public function index()
    {
        $lessonDetails = Lesson::with(['course', 'teacher', 'units', 'branches', 'terms', 'branchCourses' => function($query) {
            $query->with(['branch', 'course', 'teacher']);
        }])->get();

        // Check if user is DOS and return DOS view
        if (auth('staff')->check() && auth('staff')->user()->role === 'DOS') {
            return view('dos.lessons.index', compact('lessonDetails'));
        }

        // Check if user is staff (non-DOS) and return appropriate view
        if (auth('staff')->check()) {
            return view('staff.lessons.index', compact('lessonDetails'));
        }

        // If admin view doesn't exist, fallback to staff view or show error
        if (view()->exists('admin.lessons.index')) {
            return view('admin.lessons.index', compact('lessonDetails'));
        } else {
            // Fallback to staff view if admin view doesn't exist
            return view('staff.lessons.index', compact('lessonDetails'));
        }
    }

    public function create(Request $request)
    {
        $courses = Course::with('branches')->get();
        $branches = \App\Models\Branch::all();
        $terms = Term::all();
        $teachers = collect();

        // For DOS dashboard, we need multi-branch, multi-course support
        if (auth('staff')->check() && auth('staff')->user()->role === 'DOS') {
            $ongoingAcademicYear = AcademicYear::where('is_ongoing', true)->first();
            
            return view('dos.lessons.create', compact(
                'courses', 
                'branches', 
                'teachers', 
                'ongoingAcademicYear', 
                'terms'
            ));
        }

        // Original logic for other users
        if ($request->has('course_id') && $request->course_id) {
            // Get teachers assigned to selected course from teacher_course pivot table
            $teachers = DB::table('teacher_course')
                ->join('teachers', 'teacher_course.teacher_id', '=', 'teachers.id')
                ->where('teacher_course.course_id', $request->course_id)
                ->select('teachers.id', 'teachers.teacher_name')
                ->get();
        }

        $ongoingAcademicYear = AcademicYear::where('is_ongoing', true)->first();

        return view('staff.lessons.create', compact('courses', 'teachers', 'ongoingAcademicYear', 'terms'));
    }

    public function store(Request $request)
    {
        // Check if this is a multi-branch, multi-course creation from DOS
        if ($request->has('is_multi_branch') && $request->is_multi_branch) {
            return $this->storeMultiBranchLesson($request);
        }

        // Original single lesson creation logic
        $validated = $request->validate([
            'lesson_title' => 'required|string|max:255',
            'teacher_id' => 'nullable|exists:teachers,id',
            'course_id' => 'required|exists:courses,id',
            'max_test_marks' => 'nullable|integer|min:0',
            'max_exam_marks' => 'nullable|integer|min:0',
            'lesson_weight' => 'required|integer|min:1|max:100', // <-- add this line
            'terms' => 'required|array|min:1|max:2',
            'terms.*' => 'exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $lesson = new Lesson();
        $lesson->title = $validated['lesson_title'];
        $lesson->teacher_id = $validated['teacher_id'] ?? null;
        $lesson->course_id = $validated['course_id'];
        $lesson->max_test_marks = $validated['max_test_marks'] ?? 100;
        $lesson->max_exam_marks = $validated['max_exam_marks'] ?? 100;
        $lesson->lesson_weight = $validated['lesson_weight']; // <-- add this line
        $lesson->academic_year_id = $validated['academic_year_id'];
        $lesson->save();

        // Attach terms
        $lesson->terms()->attach($validated['terms']);

        // Check if user is staff/DOS and redirect appropriately
        if (auth('staff')->check()) {
            return redirect()->route('staff.lessons.index')
                             ->with('success', 'Lesson created successfully.');
        }
        
        // For admin users, redirect to lesson details creation
        return redirect()->route('staff.lesson.details.create', [
            'lesson' => $lesson->id,
            'course_id' => $lesson->course_id,
            'terms' => $validated['terms'],
        ]);
    }

    private function storeMultiBranchLesson(Request $request)
    {
        $validated = $request->validate([
            'lesson_title' => 'required|string|max:255',
            'selected_courses' => 'required|array|min:1',
            'selected_courses.*.course_id' => 'required|exists:courses,id',
            'selected_courses.*.branch_id' => 'required|exists:branches,id',
            'selected_courses.*.teacher_id' => 'nullable|exists:teachers,id',
            'max_test_marks' => 'nullable|integer|min:0',
            'max_exam_marks' => 'nullable|integer|min:0',
            'lesson_weight' => 'required|integer|min:1|max:100',
            'terms' => 'required|array|min:1|max:2',
            'terms.*' => 'exists:terms,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        DB::beginTransaction();
        try {
            // Create the main lesson record
            $lesson = new Lesson();
            $lesson->title = $validated['lesson_title'];
            $lesson->teacher_id = null; // Will be set per branch
            $lesson->course_id = $validated['selected_courses'][0]['course_id']; // Primary course
            $lesson->max_test_marks = $validated['max_test_marks'] ?? 100;
            $lesson->max_exam_marks = $validated['max_exam_marks'] ?? 100;
            $lesson->lesson_weight = $validated['lesson_weight'];
            $lesson->academic_year_id = $validated['academic_year_id'];
            $lesson->save();

            // Attach terms to the lesson
            $lesson->terms()->attach($validated['terms']);

            // Create branch-course-teacher assignments
            foreach ($validated['selected_courses'] as $assignment) {
                \App\Models\LessonBranchCourse::create([
                    'lesson_id' => $lesson->id,
                    'branch_id' => $assignment['branch_id'],
                    'course_id' => $assignment['course_id'],
                    'teacher_id' => $assignment['teacher_id'] ?? null,
                ]);
            }

            DB::commit();

            // Redirect to units creation page so user can create units for all selected branches/courses at once
            return redirect()->route('lesson.details.create', $lesson->id)
                             ->with('success', 'Multi-branch lesson created successfully! Now create the units for this lesson. Units created here will automatically apply to all ' . count($validated['selected_courses']) . ' selected branch-course combinations.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error creating multi-branch lesson: ' . $e->getMessage());
            
            return redirect()->back()
                             ->with('error', 'Error creating lesson: ' . $e->getMessage())
                             ->withInput();
        }
    }
public function createDetails(Request $request, $lessonId)
{
    // Load the lesson with relations if needed
    $lesson = Lesson::with('course')->findOrFail($lessonId);

    // Get term IDs from the request (passed as query string or form data)
    $termIds = $request->input('terms', []);

        // Get term IDs from the request (passed as query string or form data)
        $termIds = $request->input('terms', []);

        // Fetch Term models from those IDs
        $terms = Term::whereIn('id', $termIds)->get();

        // Pass lesson and terms to your view
        return view('admin.lessons.details.create', compact('lesson', 'terms'));
    }

    public function show($lessonId)
    {
        $lesson = Lesson::with([
            'terms',
            'units',
            'course',
            'teacher',
            'branchCourses.branch',
            'branchCourses.course',
            'branchCourses.teacher'
        ])->findOrFail($lessonId);

        return view('staff.lessons.show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit($lessonId)
    {
        $lesson = Lesson::with(['terms', 'course', 'teacher'])->findOrFail($lessonId);
        
        // Get all available data for the form
        $courses = Course::all();
        $teachers = DB::table('teacher_course')
            ->join('teachers', 'teacher_course.teacher_id', '=', 'teachers.id')
            ->where('teacher_course.course_id', $lesson->course_id)
            ->select('teachers.id', 'teachers.teacher_name')
            ->get();
        $terms = Term::with('academicYear')->get();
        $academicYears = \App\Models\AcademicYear::all();
        
        return view('staff.lessons.edit', compact('lesson', 'courses', 'teachers', 'terms', 'academicYears'));
    }

    /**
     * Get teachers for a specific course (AJAX)
     */
    public function getTeachers($courseId)
    {
        $teachers = DB::table('teacher_course')
            ->join('teachers', 'teacher_course.teacher_id', '=', 'teachers.id')
            ->where('teacher_course.course_id', $courseId)
            ->select('teachers.id', 'teachers.teacher_name')
            ->get();
        
        return response()->json($teachers);
    }

    /**
     * Get courses for selected branches (AJAX)
     */
    public function getCoursesForBranches(Request $request)
    {
        try {
            $branchIds = $request->input('branch_ids', []);
            
            if (empty($branchIds)) {
                return response()->json(['courses' => []]);
            }
            
            $courses = Course::with('branches')
                ->whereHas('branches', function($query) use ($branchIds) {
                    $query->whereIn('branches.id', $branchIds);
                })
                ->get()
                ->flatMap(function($course) use ($branchIds) {
                    return $course->branches
                        ->whereIn('id', $branchIds)
                        ->map(function($branch) use ($course) {
                            return [
                                'id' => $course->id,
                                'course_name' => $course->course_name,
                                'abbreviation' => $course->abbreviation,
                                'branch_id' => $branch->id,
                                'branch_name' => $branch->name
                            ];
                        });
                })
                ->values();
            
            return response()->json(['courses' => $courses]);
            
        } catch (\Exception $e) {
            \Log::error('Error in getCoursesForBranches: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'error' => 'An error occurred while fetching courses',
                'message' => $e->getMessage(),
                'courses' => []
            ], 500);
        }
    }

    /**
     * Show form to create lesson details (outcomes)
     */
    public function getByCourses(Request $request)
    {
        $courseIds = $request->course_ids;
        $lessons = \App\Models\Lesson::whereIn('course_id', $courseIds)->get(['id', 'title']);
        return response()->json($lessons);
    }

    /**
     * Update the specified lesson in storage.
     */
    public function update(Request $request, $lessonId)
    {
        $request->validate([
            'lesson_title' => 'required|string|max:255',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'max_test_marks' => 'required|integer|min:1',
            'max_exam_marks' => 'required|integer|min:1',
            'lesson_weight' => 'required|integer|min:1|max:100',
            'terms' => 'required|array',
            'terms.*' => 'exists:terms,id',
            'units' => 'nullable|array',
            'units.*.name' => 'required|string|max:255',
            'units.*.hours' => 'required|integer|min:1',
            'units.*.term_id' => 'required|exists:terms,id',
            'units.*.course_level' => 'required|string|in:beginner,intermediate,advanced',
        ]);

        $lesson = Lesson::findOrFail($lessonId);
        
        // Update lesson basic information
        $lesson->title = $request->lesson_title;
        $lesson->course_id = $request->course_id;
        $lesson->teacher_id = $request->teacher_id;
        $lesson->academic_year_id = $request->academic_year_id;
        $lesson->max_test_marks = $request->max_test_marks;
        $lesson->max_exam_marks = $request->max_exam_marks;
        $lesson->lesson_weight = $request->lesson_weight;
        $lesson->save();

        // Update lesson terms (sync the relationship)
        $lesson->terms()->sync($request->terms);

        // Handle units
        if ($request->has('units')) {
            // Delete units marked for deletion
            if ($request->has('delete_units')) {
                \App\Models\LessonUnit::destroy($request->delete_units);
            }

            // Update or create units
            foreach ($request->units as $unitData) {
                if (isset($unitData['id']) && !empty($unitData['id'])) {
                    // Update existing unit
                    $unit = \App\Models\LessonUnit::find($unitData['id']);
                    if ($unit) {
                        $unit->update([
                            'unit_name' => $unitData['name'],
                            'hours' => $unitData['hours'],
                            'term_id' => $unitData['term_id'],
                            'course_level' => $unitData['course_level'],
                        ]);
                    }
                } else {
                    // Create new unit
                    \App\Models\LessonUnit::create([
                        'lesson_id' => $lessonId,
                        'unit_name' => $unitData['name'],
                        'hours' => $unitData['hours'],
                        'term_id' => $unitData['term_id'],
                        'course_level' => $unitData['course_level'],
                    ]);
                }
            }
        }

        return redirect()->route('staff.lessons.show', $lessonId)
                         ->with('success', 'Lesson updated successfully.');
    }

    /**
     * Remove the specified lesson from storage with cascade deletion.
     */
    public function destroy($lessonId)
    {
        try {
            DB::beginTransaction();
            
            $lesson = Lesson::findOrFail($lessonId);
            
            \Log::info('Attempting to delete lesson: ' . $lesson->title . ' (ID: ' . $lessonId . ')');
            
            // 1. Delete lesson-term relationships (pivot table)
            try {
                $lesson->terms()->detach();
                \Log::info('Detached terms from lesson');
            } catch (\Exception $e) {
                \Log::warning('Could not detach terms: ' . $e->getMessage());
            }
            
            // 2. Delete lesson-exam relationships (pivot table)
            try {
                $lesson->exams()->detach();
                \Log::info('Detached exams from lesson');
            } catch (\Exception $e) {
                \Log::warning('Could not detach exams: ' . $e->getMessage());
            }
            
            // 3. Delete lesson units
            try {
                $unitsDeleted = LessonUnit::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $unitsDeleted . ' lesson units');
            } catch (\Exception $e) {
                \Log::warning('Could not delete lesson units: ' . $e->getMessage());
            }
            
            // 4. Delete timetable sessions
            try {
                $sessionsDeleted = TimetableSession::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $sessionsDeleted . ' timetable sessions');
            } catch (\Exception $e) {
                \Log::warning('Could not delete timetable sessions: ' . $e->getMessage());
            }
            
            // 5. Delete report card subjects
            try {
                $subjectsDeleted = ReportCardSubject::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $subjectsDeleted . ' report card subjects');
            } catch (\Exception $e) {
                \Log::warning('Could not delete report card subjects: ' . $e->getMessage());
            }
            
            // 6. Delete marks
            try {
                $marksDeleted = Mark::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $marksDeleted . ' marks');
            } catch (\Exception $e) {
                \Log::warning('Could not delete marks: ' . $e->getMessage());
            }
            
            // 7. Delete attendance
            try {
                $attendanceDeleted = Attendance::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $attendanceDeleted . ' attendance records');
            } catch (\Exception $e) {
                \Log::warning('Could not delete attendance: ' . $e->getMessage());
            }
            
            // 8. Delete teacher dailies
            try {
                $dailiesDeleted = TeacherDaily::where('lesson_id', $lessonId)->delete();
                \Log::info('Deleted ' . $dailiesDeleted . ' teacher dailies');
            } catch (\Exception $e) {
                \Log::warning('Could not delete teacher dailies: ' . $e->getMessage());
            }
            
            // 9. Finally delete the lesson
            $lesson->delete();
            \Log::info('Deleted lesson successfully');
            
            DB::commit();
            
            return redirect()->route('staff.lessons.index')
                             ->with('success', 'Lesson and all related data deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error deleting lesson: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->route('staff.lessons.index')
                             ->with('error', 'Error deleting lesson: ' . $e->getMessage());
        }
    }

    /**
     * Assign lesson to branches and courses (replaces all existing assignments)
     */
    public function assignToBranches(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'assignments' => 'required|array|min:1',
            'assignments.*.branch_id' => 'required|exists:branches,id',
            'assignments.*.course_id' => 'required|exists:courses,id',
            'assignments.*.teacher_id' => 'nullable|exists:teachers,id',
        ]);

        DB::beginTransaction();
        try {
            $lesson = Lesson::findOrFail($validated['lesson_id']);
            
            // DELETE all existing assignments for this lesson first
            \App\Models\LessonBranchCourse::where('lesson_id', $lesson->id)->delete();
            
            // CREATE new assignments
            foreach ($validated['assignments'] as $assignment) {
                \App\Models\LessonBranchCourse::create([
                    'lesson_id' => $lesson->id,
                    'branch_id' => $assignment['branch_id'],
                    'course_id' => $assignment['course_id'],
                    'teacher_id' => $assignment['teacher_id'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('dos.lessons.index')
                             ->with('success', 'Lesson assignments updated successfully. ' . count($validated['assignments']) . ' branch-course combinations assigned.');

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error assigning lesson: ' . $e->getMessage());
            
            return redirect()->back()
                             ->with('error', 'Error assigning lesson: ' . $e->getMessage())
                             ->withInput();
        }
    }

    /**
     * Get lesson assignments for AJAX
     */
    public function getAssignments($lessonId)
    {
        $lesson = Lesson::with(['branches', 'courses', 'teachers'])->findOrFail($lessonId);
        
        $assignments = [];
        
        // Get assignments from lesson_branch_course table
        $lessonAssignments = \App\Models\LessonBranchCourse::where('lesson_id', $lessonId)
            ->with(['branch', 'course', 'teacher'])
            ->get();
        
        foreach ($lessonAssignments as $assignment) {
            $assignments[] = [
                'branch_name' => $assignment->branch ? $assignment->branch->name : 'Unknown',
                'course_name' => $assignment->course ? $assignment->course->course_name : 'Unknown',
                'teacher_name' => $assignment->teacher ? $assignment->teacher->teacher_name : null,
            ];
        }

        return response()->json(['assignments' => $assignments]);
    }
}
