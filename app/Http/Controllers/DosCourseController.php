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
}