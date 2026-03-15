<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;

class DosCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['lessons', 'teachers'])->get();
        $teachers = Teacher::all();
        return view('staff.courses.index', compact('courses', 'teachers'));
    }

    public function assignLesson(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer|exists:teachers,id',
            'lesson_id' => 'required|integer|exists:lessons,id',
        ]);
        $lesson = Lesson::findOrFail($request->lesson_id);
        $lesson->teacher_id = $request->teacher_id;
        $lesson->save();

        return back()->with('success', 'Lesson assigned to teacher successfully!');
    }

    public function create()
    {
        $teachers = Teacher::all();
        return view('staff.courses.create', compact('teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $course = Course::create($request->all());

        return redirect()->route('dos.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        $course->load(['lessons', 'teachers']);
        return view('staff.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $teachers = Teacher::all();
        return view('staff.courses.edit', compact('course', 'teachers'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teacher_id' => 'nullable|exists:teachers,id',
        ]);

        $course->update($request->all());

        return redirect()->route('dos.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('dos.courses.index')
            ->with('success', 'Course deleted successfully!');
    }
}