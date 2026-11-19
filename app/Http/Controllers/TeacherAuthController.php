<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Unit;
use App\Services\AcademicYearContext;
class TeacherAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('teacher.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('teacher')->attempt($credentials)) {
            $teacher = Auth::guard('teacher')->user();

            // Gather login info
            $loginTime = now()->toDayDateTimeString();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $logoutUrl = route('teacher.force-logout');

            // Send welcome back email
            \Mail::to($teacher->email)->send(
                new \App\Mail\TeacherWelcomeBackMail($teacher, $loginTime, $ipAddress, $userAgent, $logoutUrl)
            );

            return redirect()->route('teacher.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::guard('teacher')->logout();
        return redirect()->route('teacher.login');
    }

    public function dashboard()
    {
        $teacher = Auth::guard('teacher')->user();
        $systemName = config('app.name', 'School System');
        $selectedAcademicYear = AcademicYearContext::selected();
        $yearId = $selectedAcademicYear?->id;

        $coursesQuery = $teacher->courses()->with('lessons.units');
        if ($yearId) {
            $coursesQuery->where('courses.academic_year_id', $yearId);
        }
        $courses = $coursesQuery->get();

        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        return view('teacher.dashboard', compact('teacher', 'systemName', 'courses', 'announcements', 'selectedAcademicYear'));
    }
    public function courseDetails()
    {
        $teacher = Auth::guard('teacher')->user();
        $systemName = config('app.name', 'School System');
        $yearId = AcademicYearContext::selectedId();

        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        $coursesQuery = $teacher->courses()->with([
            'students' => function ($query) use ($yearId) {
                if ($yearId) {
                    $query->where('academic_year_id', $yearId);
                }
            },
        ]);

        if ($yearId) {
            $coursesQuery->where('courses.academic_year_id', $yearId);
        }

        $courses = $coursesQuery->get();

        // Load lessons via lesson_branch_course pivot (real teacher assignment mechanism)
        // Grouped by course -> branch so teachers see lessons per branch they teach in
        $pivotRows = \DB::table('lesson_branch_course as lbc')
            ->join('lessons', 'lessons.id', '=', 'lbc.lesson_id')
            ->join('branches', 'branches.id', '=', 'lbc.branch_id')
            ->leftJoin('lesson_units as units', 'units.lesson_id', '=', 'lessons.id')
            ->where('lbc.teacher_id', $teacher->id)
            ->whereIn('lbc.course_id', $courses->pluck('id'))
            ->select(
                'lbc.course_id',
                'lbc.branch_id',
                'branches.name as branch_name',
                'lessons.id as lesson_id',
                'lessons.title as lesson_title',
                'units.id as unit_id',
                'units.unit_name',
                'units.hours'
            )
            ->orderBy('branches.name')
            ->orderBy('lessons.title')
            ->orderBy('units.unit_name')
            ->get();

        // Build structure: course_id => branch_id => [branch_name, lessons => lesson_id => [title, units]]
        $courseBranchLessons = [];
        foreach ($pivotRows as $row) {
            $cid = $row->course_id;
            $bid = $row->branch_id;
            $lid = $row->lesson_id;

            if (!isset($courseBranchLessons[$cid][$bid])) {
                $courseBranchLessons[$cid][$bid] = [
                    'branch_id' => $bid,
                    'branch_name' => $row->branch_name,
                    'lessons' => [],
                ];
            }
            if (!isset($courseBranchLessons[$cid][$bid]['lessons'][$lid])) {
                $courseBranchLessons[$cid][$bid]['lessons'][$lid] = [
                    'id' => $lid,
                    'title' => $row->lesson_title,
                    'units' => [],
                ];
            }
            if ($row->unit_id && !isset($courseBranchLessons[$cid][$bid]['lessons'][$lid]['units'][$row->unit_id])) {
                $courseBranchLessons[$cid][$bid]['lessons'][$lid]['units'][$row->unit_id] = [
                    'id' => $row->unit_id,
                    'unit_name' => $row->unit_name,
                    'hours' => $row->hours,
                ];
            }
        }

        // Attach to courses for the view
        foreach ($courses as $course) {
            $course->branch_lessons = collect($courseBranchLessons[$course->id] ?? [])->map(function ($b) {
                $b['lessons'] = collect($b['lessons'])->map(function ($l) {
                    $l['units'] = collect($l['units'])->values();
                    return $l;
                })->values();
                return $b;
            })->values();
        }

        // Collect all unit IDs for this teacher's courses
        $unitIds = [];
        foreach ($courseBranchLessons as $branches) {
            foreach ($branches as $branch) {
                foreach ($branch['lessons'] as $lesson) {
                    foreach ($lesson['units'] as $unitId => $unit) {
                        $unitIds[] = $unitId;
                    }
                }
            }
        }

        // Get daily hours per unit for this teacher
        $unitDailyHours = \App\Models\TeacherDaily::where('teacher_id', $teacher->id)
            ->whereIn('unit_id', $unitIds)
            ->selectRaw('unit_id, SUM(hours_studied) as total_hours')
            ->groupBy('unit_id')
            ->pluck('total_hours', 'unit_id')
            ->toArray();

        return view('teacher.courses.details', compact('teacher', 'systemName', 'courses', 'unitDailyHours', 'announcements'));
    }

    public function exportCourseStudents($courseId, $branchId = null)
    {
        $teacher = Auth::guard('teacher')->user();
        $yearId = AcademicYearContext::selectedId();

        // Verify the teacher is assigned to this course
        $course = $teacher->courses()->where('courses.id', $courseId)->first();

        if (!$course) {
            return back()->with('error', 'You are not assigned to this course.');
        }

        // If branch specified, verify teacher teaches this course in that branch
        if ($branchId) {
            $assigned = \DB::table('lesson_branch_course')
                ->where('teacher_id', $teacher->id)
                ->where('course_id', $courseId)
                ->where('branch_id', $branchId)
                ->exists();

            if (!$assigned) {
                return back()->with('error', 'You are not assigned to teach this course in the selected branch.');
            }
        }

        // Get students for this course (and branch if specified)
        // Try with academic year filter first
        $studentsQuery = $course->students();
        if ($branchId) {
            $studentsQuery->where('branch_id', $branchId);
        }
        if ($yearId) {
            $studentsQuery->where('academic_year_id', $yearId);
        }
        $students = $studentsQuery->orderBy('full_name')->get(['id', 'full_name', 'reg_number']);

        // If no students found with academic year filter, try without it
        if ($students->isEmpty()) {
            $studentsQuery = $course->students();
            if ($branchId) {
                $studentsQuery->where('branch_id', $branchId);
            }
            $students = $studentsQuery->orderBy('full_name')->get(['id', 'full_name', 'reg_number']);
        }

        $branchName = '';
        if ($branchId) {
            $branchName = \App\Models\Branch::find($branchId)->name ?? '';
        }

        // Generate CSV
        $filename = 'students_' . \Illuminate\Support\Str::slug($course->course_name)
            . ($branchName ? '_' . \Illuminate\Support\Str::slug($branchName) : '')
            . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student Name', 'Registration Number']);

            foreach ($students as $student) {
                fputcsv($file, [
                    $student->full_name,
                    $student->reg_number,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    public function showForceLogout()
    {
        return view('teacher.force-logout');
    }
    public function updateProfile(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'teacher_name' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|confirmed|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.regex' => 'Password must be at least 8 characters with mixed case, letters, numbers, and symbols',
            'current_password.required_with' => 'Current password is required when changing password',
        ]);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $data['photo'] = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        } else {
            unset($data['photo']);
        }

        // Handle password change
        if (!empty($request->password)) {
            // Verify current password
            if (!\Hash::check($request->current_password, $teacher->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'Current password is incorrect']);
            }

            $data['password'] = \Hash::make($request->password);
        } else {
            unset($data['password']);
        }

        // Remove current_password from data (not a database field)
        unset($data['current_password']);

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
