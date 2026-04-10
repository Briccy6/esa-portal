<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LessonBranchCourse;
use App\Models\Lesson;
use App\Models\Branch;
use App\Models\Course;
use App\Models\Teacher;

class LessonsController extends Controller
{
    /**
     * Display a listing of lessons.
     */
    public function index(Request $request)
    {
        $query = LessonBranchCourse::with(['lesson', 'branch', 'course', 'teacher']);

        // Filter by branch
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by course
        if ($request->course_id) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by teacher
        if ($request->teacher_id) {
            $query->where('teacher_id', $request->teacher_id);
        }

        // Search by lesson title
        if ($request->search) {
            $query->whereHas('lesson', function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%');
            });
        }

        $lessonAssignments = $query->orderBy('branch_id')->orderBy('course_id')->paginate(15);

        // Get filter options
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();

        return view('admin.lessons.index', compact('lessonAssignments', 'branches', 'courses', 'teachers'));
    }

    /**
     * Show the form for creating a new lesson assignment.
     */
    public function create()
    {
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();
        $lessons = Lesson::all();

        return view('admin.lessons.create', compact('branches', 'courses', 'teachers', 'lessons'));
    }

    /**
     * Store a newly created lesson assignment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Check if assignment already exists
        $existing = LessonBranchCourse::where([
            'lesson_id' => $validated['lesson_id'],
            'branch_id' => $validated['branch_id'],
            'course_id' => $validated['course_id'],
        ])->first();

        if ($existing) {
            return back()->with('error', 'This lesson is already assigned to this course in this branch.');
        }

        LessonBranchCourse::create($validated);

        return redirect()->route('admin.lessons.index')->with('success', 'Lesson assignment created successfully.');
    }

    /**
     * Display the specified lesson assignment.
     */
    public function show(LessonBranchCourse $lesson)
    {
        return view('admin.lessons.show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified lesson assignment.
     */
    public function edit(LessonBranchCourse $lesson)
    {
        $branches = Branch::all();
        $courses = Course::all();
        $teachers = Teacher::all();
        $lessons = Lesson::all();

        return view('admin.lessons.edit', compact('lesson', 'branches', 'courses', 'teachers', 'lessons'));
    }

    /**
     * Update the specified lesson assignment in storage.
     */
    public function update(Request $request, LessonBranchCourse $lesson)
    {
        $validated = $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'branch_id' => 'required|exists:branches,id',
            'course_id' => 'required|exists:courses,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        // Check if assignment already exists with different id
        $existing = LessonBranchCourse::where([
            'lesson_id' => $validated['lesson_id'],
            'branch_id' => $validated['branch_id'],
            'course_id' => $validated['course_id'],
        ])->where('id', '!=', $lesson->id)->first();

        if ($existing) {
            return back()->with('error', 'This lesson is already assigned to this course in this branch.');
        }

        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')->with('success', 'Lesson assignment updated successfully.');
    }

    /**
     * Remove the specified lesson assignment from storage.
     */
    public function destroy(LessonBranchCourse $lesson)
    {
        $lesson->delete();

        return redirect()->route('admin.lessons.index')->with('success', 'Lesson assignment deleted successfully.');
    }

    /**
     * Get lessons for a specific course
     */
    public function getLessonsForCourse(Request $request)
    {
        $lessons = Lesson::where('course_id', $request->course_id)->get();
        return response()->json($lessons);
    }

    /**
     * Get assigned teachers for a lesson
     */
    public function getTeachersForLesson(Request $request)
    {
        $teachers = Teacher::all();
        return response()->json($teachers);
    }
}
