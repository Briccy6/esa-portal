<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Teacher;
use App\Models\Lesson;
use App\Models\Term;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;

class LessonController extends Controller
{
    public function index()
    {
        $lessonDetails = Lesson::with(['course', 'teacher', 'units'])->get();

        return view('admin.lessons.index', compact('lessonDetails'));
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

return view('admin.lessons.create', compact('courses', 'teachers', 'ongoingAcademicYear', 'terms'));
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
        return redirect()->route('lesson.details.create', [
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

    // Fetch Term models from those IDs
    $terms = Term::whereIn('id', $termIds)->get();

    // Pass lesson and terms to your view
    return view('admin.lessons.details.create', compact('lesson', 'terms'));
}
 public function show($lessonId)
    {
        $lesson = Lesson::with('terms')->findOrFail($lessonId);

        // Load any other relations or data as needed

        return view('admin.lessons.show', compact('lesson'));
    }
    /**
     * Show the form to create lesson details (outcomes)
     */
    public function getByCourses(Request $request)
{
    $courseIds = $request->course_ids;
    $lessons = \App\Models\Lesson::whereIn('course_id', $courseIds)->get(['id', 'title']);
    return response()->json($lessons);
}
}
