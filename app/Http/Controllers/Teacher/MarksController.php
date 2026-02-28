<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lesson;

class MarksController extends Controller
{

public function index()
{
    $teacher = auth('teacher')->user();
    $lessons = Lesson::where('teacher_id', $teacher->id)->get();
    $courses = \App\Models\Course::all(); 
        $branches = \App\Models\Branch::all();
        return view('teacher.marks.index', compact('lessons', 'courses', 'branches'));

}

    public function create(Lesson $lesson)
    {
        // You can fetch students for this lesson here if needed
        return view('teacher.marks.create', compact('lesson'));
    }

    // Add store() and other methods as needed
}
