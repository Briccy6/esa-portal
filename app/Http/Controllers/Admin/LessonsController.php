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

        $lessonAssignments = $query->orderBy('branch_id')
                                   ->orderBy('course_id')
                                   ->orderBy('lesson_id')
                                   ->paginate(20);

        // Get filter options
        $branches = Branch::orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();
        $teachers = Teacher::orderBy('teacher_name')->get();

        return view('admin.lessons.index', compact('lessonAssignments', 'branches', 'courses', 'teachers'));
    }

    /**
     * Show the form for creating a new lesson assignment.
     */
    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();
        $teachers = Teacher::orderBy('teacher_name')->get();
        $lessons = Lesson::orderBy('title')->get();

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
            return back()->with('error', 'This lesson is already assigned to this course in this branch. You can edit it to change the teacher.');
        }

        $assignment = LessonBranchCourse::create($validated);
        
        $lesson = Lesson::find($validated['lesson_id']);
        $branch = Branch::find($validated['branch_id']);
        $course = Course::find($validated['course_id']);
        $teacher = Teacher::find($validated['teacher_id']);

        return redirect()->route('admin.lessons.index')->with('success', 
            'Lesson assignment created successfully! ' . $lesson->title . ' assigned to ' . 
            $course->course_name . ' in ' . $branch->name . ' branch with ' . 
            $teacher->teacher_name . ' as teacher.');
    }

    /**
     * Display the specified lesson assignment.
     */
    public function show(LessonBranchCourse $lesson)
    {
        $lesson = $lesson->load(['lesson', 'branch', 'course', 'teacher']);
        return view('admin.lessons.show', compact('lesson'));
    }

    /**
     * Show the form for editing the specified lesson assignment.
     */
    public function edit(LessonBranchCourse $lesson)
    {
        $lesson = $lesson->load(['lesson', 'branch', 'course', 'teacher']);
        $branches = Branch::orderBy('name')->get();
        $courses = Course::orderBy('course_name')->get();
        $teachers = Teacher::orderBy('teacher_name')->get();
        $lessons = Lesson::orderBy('title')->get();

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
            return back()->with('error', 'This lesson is already assigned to this course in this branch with a teacher. Please choose a different combination.');
        }

        $lesson->update($validated);

        return redirect()->route('admin.lessons.index')->with('success', 
            'Lesson assignment updated successfully. ' . 
            $lesson->lesson->title . ' is now taught in ' . $lesson->branch->name . ' under ' . 
            $lesson->course->course_name . ' by ' . $lesson->teacher->teacher_name);
    }

    /**
     * Remove the specified lesson assignment from storage.
     */
    public function destroy(LessonBranchCourse $lesson)
    {
        $lessonTitle = $lesson->lesson->title ?? 'Lesson';
        $courseName = $lesson->course->course_name ?? 'Course';
        $branchName = $lesson->branch->name ?? 'Branch';
        
        $lesson->delete();

        return redirect()->route('admin.lessons.index')->with('success', 
            'Lesson assignment deleted! ' . $lessonTitle . ' has been removed from ' . 
            $courseName . ' in ' . $branchName . ' branch.');
    }

    /**
     * Get lessons for a specific course
     */
    public function getLessonsForCourse(Request $request)
    {
        try {
            $courseId = $request->route('course');
            $lessons = Lesson::where('course_id', $courseId)
                            ->orderBy('title')
                            ->select('id', 'title', 'course_id', 'lesson_weight', 'max_test_marks', 'max_exam_marks')
                            ->get();
            
            return response()->json($lessons);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to fetch lessons'], 500);
        }
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
