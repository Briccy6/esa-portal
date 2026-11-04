<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Teacher;

class DosCourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['lessons', 'teachers', 'branches', 'students']);
        
        // Filter by branch
        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }
        
        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', 0);
            }
        }
        
        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('course_name', 'like', "%$search%")
                  ->orWhere('course_code', 'like', "%$search%")
                  ->orWhere('description', 'like', "%$search%");
            });
        }
        
        $courses = $query->paginate(15);
        $branches = \App\Models\Branch::all();
        $teachers = Teacher::all();
        return view('dos.courses', compact('courses', 'branches', 'teachers'));
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
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:50',
            'abbreviation' => 'required|string|max:20',
            'course_type' => 'required|string',
            'description' => 'nullable|string',
            'registration_fee' => 'nullable|numeric|min:0',
            'tuition_fee' => 'nullable|numeric|min:0',
            'practical_fee' => 'nullable|numeric|min:0',
            'trip_fee' => 'nullable|numeric|min:0',
            'discount_fee' => 'nullable|numeric|min:0',
        ]);

        $course = Course::create($request->all());

        return redirect()->route('dos.courses.index')
            ->with('success', 'Course created successfully!');
    }

    public function show(Course $course)
    {
        // Load lessons from lesson_branch_course pivot table
        $lessonIds = \App\Models\LessonBranchCourse::where('course_id', $course->id)
            ->pluck('lesson_id')
            ->unique();

        $lessons = Lesson::whereIn('id', $lessonIds)
            ->with(['teacher', 'terms', 'branchCourses' => function($q) use ($course) {
                $q->where('course_id', $course->id);
            }])
            ->get();

        $course->setRelation('lessons', $lessons);
        $course->load(['teachers']);

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
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:50',
            'abbreviation' => 'required|string|max:20',
            'course_type' => 'required|string',
            'description' => 'nullable|string',
            'registration_fee' => 'nullable|numeric|min:0',
            'tuition_fee' => 'nullable|numeric|min:0',
            'practical_fee' => 'nullable|numeric|min:0',
            'trip_fee' => 'nullable|numeric|min:0',
            'discount_fee' => 'nullable|numeric|min:0',
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