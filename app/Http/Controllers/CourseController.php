<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{

  public function index()
    {
        $courses = Course::all();  // Get all courses from database
        return view('admin.course.index', compact('courses'));  // Pass $courses to the view
    }

    public function create()
    {
        return view('admin.course.create');
    }
public function store(Request $request)
{
    $validated = $request->validate([
        'course_name' => 'required|string|max:255|unique:courses,course_name',
        'abbreviation' => 'required|string|max:10',
        'course_type' => 'required|string|max:50',
    ]);

    // Create course without course_code
    $course = Course::create([
        'course_name' => $validated['course_name'],
        'abbreviation' => $validated['abbreviation'],
        'course_type' => $validated['course_type'],
    ]);

    // Generate course_code starting with abbreviation + course ID + random number
    $randomNumber = rand(100, 999);
    $courseCode = strtoupper($course->abbreviation) . $randomNumber;

    // Update course_code
    $course->course_code = $courseCode;
    $course->save();

    return redirect()->back()->with('success', 'Course created successfully! Course Code: ' . $courseCode);
}
}