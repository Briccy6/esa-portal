<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Branch; // Add Branch model import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TeachersExport;
use App\Mail\TeacherWelcomeMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class TeacherController extends Controller
{
    public function index()
    {
        // Eager load courses and branch to avoid N+1 problem
        $teachers = Teacher::with(['courses', 'branch'])->latest()->paginate(10);
        $branches = Branch::all(); // Get all branches for filter dropdown
        return view('admin.teachers.index', compact('teachers', 'branches'));
    }
    public function list()
{
    $teachers = Teacher::with('course')->get();
    return response()->json($teachers->map(function ($teacher) {
        return [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'course' => $teacher->course->course_name ?? 'N/A',
        ];
    }));
}


    public function create()
    {
        $branches = Branch::all();
        $generated_teacher_id = 'TCH-' . strtoupper(\Illuminate\Support\Str::random(8));
        return view('admin.teachers.form', compact('branches', 'generated_teacher_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_name' => 'required|string|max:255',
            'teacher_id_number' => 'required|string|unique:teachers,teacher_id_number',
            'id_number' => 'required|string|max:50',
            'telephone' => 'required|string|max:20',
            'email' => 'required|email|unique:teachers,email',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:100', // 100KB max
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
            'department' => 'required|string',
            'employment_type' => 'required|string',
            'level_of_education' => 'required|string',
            'salary' => 'required|numeric|min:0',
        ]);

        // Convert photo to base64
        $photoBase64 = null;
        if ($request->hasFile('photo')) {
            $photoBase64 = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        }

        $randomPassword = \Str::random(10); // or use any password policy you want

        $teacher = Teacher::create([
            'teacher_name' => $request->teacher_name,
            'teacher_id_number' => $request->teacher_id_number,
            'id_number' => $request->id_number,
            'telephone' => $request->telephone,
            'email' => $request->email,
            'photo' => $photoBase64,
            'department' => $request->department,
            'employment_type' => $request->employment_type,
            'level_of_education' => $request->level_of_education,
            'salary' => $request->salary,
            'password' => Hash::make($randomPassword),
        ]);

        // Assign branches
        $teacher->branches()->sync($request->input('branch_ids', []));

        Mail::to($teacher->email)->send(new \App\Mail\TeacherWelcomeMail($teacher, $randomPassword));

        return redirect()
            ->route('teachers.index')
            ->with('success', 'Teacher created successfully!')
            ->with('temp_password', $randomPassword);
    }

    public function show(Teacher $teacher)
    {
        $teacher->load(['courses', 'branch']);
        return view('admin.teachers.show', compact('teacher'));
    }

    public function edit(Teacher $teacher)
    {
        $courses = Course::all();
        $branches = Branch::all(); // Get all branches for dropdown
        $assignedCourses = $teacher->courses->pluck('id')->toArray();
        $assignedBranches = $teacher->branches->pluck('id')->toArray();

        return view('admin.teachers.edit', compact('teacher', 'courses', 'branches', 'assignedCourses', 'assignedBranches'));
    }

    public function update(Request $request, Teacher $teacher)
    {
        $data = $request->validate([
            // Personal Information
            'teacher_name' => 'required|string|max:255',
            'teacher_id_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('teachers', 'teacher_id_number')->ignore($teacher->id)
            ],
            'id_number' => [
                'required',
                'string',
                'max:20',
                Rule::unique('teachers', 'id_number')->ignore($teacher->id)
            ],
            'telephone' => 'required|string|max:20',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('teachers', 'email')->ignore($teacher->id)
            ],
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:100', // Max 100KB, nullable for updates

            // Professional Information
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'exists:branches,id',
            'department' => ['required', Rule::in(['All', 'REB Combinations', 'RTB Trades', 'Training', 'Internship'])],
            'employment_type' => ['required', Rule::in(['Permanent', 'Short Term', 'Casual'])],
            'level_of_education' => ['required', Rule::in(['Bachelor\'s Degree', 'Master\'s Degree', 'PhD', 'Diploma', 'Certificate', 'Other'])],

            // Financial Information
            'salary' => 'required|numeric|min:0|max:99999999.99',

            // Course Assignment
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
        ]);

        // Handle photo upload as base64
        if ($request->hasFile('photo')) {
            $data['photo'] = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        } else {
            unset($data['photo']);
        }

        $teacher->update($data);

        // Sync courses and branches
        $teacher->courses()->sync($request->input('courses', []));
        $teacher->branches()->sync($request->input('branch_ids', []));

        return redirect()->route('teachers.index')->with('success', 'Teacher updated successfully.');
    }

    public function destroy(Teacher $teacher)
    {
        // Delete photo if exists
        if ($teacher->photo && Storage::disk('public')->exists('teachers/' . $teacher->photo)) {
            Storage::disk('public')->delete('teachers/' . $teacher->photo);
        }

        // Delete teacher record
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Teacher deleted successfully.');
    }

    public function assignCourse(Teacher $teacher)
    {
        $courses = Course::all();
        return view('admin.teachers.assign-course', compact('teacher', 'courses'));
    }

    public function storeCourseAssignment(Request $request, Teacher $teacher)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        $teacher->courses()->sync($request->input('course_ids', []));

        return redirect()->route('admin.teachers.index')->with('success', 'Courses assigned successfully!');
    }

    public function removeCourseAssignment(Teacher $teacher, Course $course)
    {
        DB::table('teacher_course')
            ->where('teacher_id', $teacher->id)
            ->where('course_id', $course->id)
            ->delete();

        return redirect()->back()->with('success', 'Course removed from teacher successfully.');
    }

    /**
     * Filter teachers by department
     */
    public function filterByDepartment(Request $request)
    {
        $department = $request->get('department');
        $teachers = Teacher::byDepartment($department)->with(['courses', 'branch'])->latest()->paginate(10);
        $branches = Branch::all(); // Add branches for view

        return view('admin.teachers.index', compact('teachers', 'department', 'branches'));
    }

    /**
     * Filter teachers by employment type
     */
    public function filterByEmploymentType(Request $request)
    {
        $employmentType = $request->get('employment_type');
        $teachers = Teacher::byEmploymentType($employmentType)->with(['courses', 'branch'])->latest()->paginate(10);
        $branches = Branch::all(); // Add branches for view

        return view('admin.teachers.index', compact('teachers', 'employmentType', 'branches'));
    }

    /**
     * Filter teachers by branch
     */
    public function filterByBranch(Request $request)
    {
        $branchId = $request->get('branch_id');
        $teachers = Teacher::where('branch_id', $branchId)->with(['courses', 'branch'])->latest()->paginate(10);
        $branches = Branch::all();
        $selectedBranch = Branch::find($branchId);

        return view('admin.teachers.index', compact('teachers', 'branches', 'selectedBranch'));
    }

    /**
     * Search teachers
     */
    public function search(Request $request)
    {
        $query = $request->get('query');

        $teachers = Teacher::where(function ($q) use ($query) {
            $q->where('teacher_name', 'LIKE', "%{$query}%")
                ->orWhere('teacher_id_number', 'LIKE', "%{$query}%")
                ->orWhere('email', 'LIKE', "%{$query}%")
                ->orWhere('telephone', 'LIKE', "%{$query}%");
        })
            ->with(['courses', 'branch'])
            ->latest()
            ->paginate(10);

        $branches = Branch::all(); // Add branches for view

        return view('admin.teachers.index', compact('teachers', 'query', 'branches'));
    }

    /**
     * Export teachers data to Excel
     */
    public function export()
    {
        $fileName = 'teachers_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new TeachersExport, $fileName);
    }

    public function updateProfile(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'teacher_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:100',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        } else {
            unset($data['photo']);
        }

        if (!empty($data['password'])) {
            $data['password'] = \Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function photo($id)
    {
        $teacher = Teacher::findOrFail($id);
        if ($teacher->photo) {
            // If photo is base64 string
            if (str_starts_with($teacher->photo, 'data:image')) {
                $base64 = explode(',', $teacher->photo, 2)[1];
                return response(base64_decode($base64))->header('Content-Type', 'image/png');
            }
            // If photo is raw BLOB
            return response($teacher->photo)->header('Content-Type', 'image/png');
        }
        // Fallback to default image
        return response()->file(public_path('images/default-teacher.png'));
    }
    public function getByLesson(Request $request)
{
    $lessonId = $request->lesson_id;
    $lesson = \App\Models\Lesson::find($lessonId);
    if ($lesson && $lesson->teacher) {
        return response()->json(['id' => $lesson->teacher->id, 'name' => $lesson->teacher->teacher_name]);
    }
    return response()->json(['id' => null, 'name' => '']);
}

public function resetPassword(\App\Models\Teacher $teacher)
{
    $newPassword = \Illuminate\Support\Str::random(8);
    $teacher->password = bcrypt($newPassword);
    $teacher->save();

    // Send email
    \Mail::to($teacher->email)->send(new \App\Mail\TeacherPasswordResetMail($teacher, $newPassword));

    return redirect()->back()->with('success', 'Password reset and sent to teacher email.');
}

}