<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Lesson;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;
use App\Models\TimetableSession;
use App\Models\ReportCardSubject;

class LessonController extends Controller
{
    public function index()
    {
        $lessonDetails = Lesson::with(['course', 'teacher', 'units'])->get();

        // Check if user is staff (DOS) and return appropriate view
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
        $courses = Course::all();
        $terms = Term::all();

        $teachers = collect();

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

        // Redirect to lesson details creation page with lesson ID, course ID, and term IDs
        return redirect()->route('staff.lesson.details.create', [
            'lesson' => $lesson->id,
            'course_id' => $lesson->course_id,
            'terms' => $validated['terms'],
        ]);
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
        $lesson = Lesson::with(['terms', 'units', 'course', 'teacher'])->findOrFail($lessonId);

        // Load any other relations or data as needed

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
            
            // Delete in proper order to avoid foreign key constraint issues
            
            // 1. Delete lesson-term relationships (pivot table)
            $lesson->terms()->detach();
            
            // 2. Delete lesson-exam relationships (pivot table)
            $lesson->exams()->detach();
            
            // 3. Delete lesson units (they have cascade foreign key to lesson)
            \App\Models\LessonUnit::where('lesson_id', $lessonId)->delete();
            
            // 4. Delete timetable sessions (they have cascade foreign key to lesson)
            TimetableSession::where('lesson_id', $lessonId)->delete();
            
            // 5. Delete report card subjects (they have foreign key to lesson)
            ReportCardSubject::where('lesson_id', $lessonId)->delete();
            
            // 6. Delete any marks related to this lesson
            \App\Models\Marks::where('lesson_id', $lessonId)->delete();
            
            // 7. Delete any attendance records related to this lesson
            \App\Models\Attendance::where('lesson_id', $lessonId)->delete();
            
            // 8. Delete any teacher dailies related to this lesson
            \App\Models\TeacherDaily::where('lesson_id', $lessonId)->delete();
            
            // 9. Finally delete the lesson
            $lesson->delete();
            
            DB::commit();
            
            return redirect()->route('staff.lessons.index')
                             ->with('success', 'Lesson and all related data deleted successfully.');
            
        } catch (\Exception $e) {
            DB::rollback();
            
            \Log::error('Error deleting lesson: ' . $e->getMessage());
            
            return redirect()->route('staff.lessons.index')
                             ->with('error', 'Error deleting lesson: ' . $e->getMessage());
        }
    }
}
