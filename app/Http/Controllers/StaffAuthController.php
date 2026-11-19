<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AcademicYearContext;

class StaffAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('staff.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('staff')->attempt($credentials)) {
            $request->session()->regenerate();

            $staff = Auth::guard('staff')->user();
            $role = $staff->role;

            \Log::info('Staff login attempt', [
                'email' => $request->email,
                'role' => $role,
                'staff_id' => $staff->id
            ]);

            if ($role === 'DOS') {
                return redirect()->route('staff.dashboard.dos')->with('success', 'Welcome, DOS!');
            } elseif ($role === 'Accountant') {
                return redirect()->route('staff.accountant.dashboard')->with('success', 'Welcome, Accountant!');
            } elseif ($role === 'SEC' || $role === 'Secretary') {
                return redirect()->route('secretary.dashboard')->with('success', 'Welcome, Secretary!');
            } else {
                Auth::guard('staff')->logout();
                return back()->withErrors(['email' => 'Unauthorized role: ' . $role]);
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::guard('staff')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('staff.login');
    }

    public function dashboard()
    {
        $staff = Auth::guard('staff')->user();
        $role = $staff->role;
        $selectedAcademicYear = AcademicYearContext::selected();
        $academicYearId = $selectedAcademicYear?->id;

        \Log::info('Staff dashboard access', [
            'staff_id' => $staff->id,
            'role' => $role,
            'email' => $staff->email
        ]);

        if ($role === 'DOS') {
            $branches = $staff->branches()->orderBy('name')->get();

            $branchAnalytics = $branches->map(function ($branch) use ($academicYearId) {
                $students = \App\Models\Student::query()
                    ->where('branch_id', $branch->id)
                    ->when($academicYearId, fn ($query) => $query->where('academic_year_id', $academicYearId));

                $totalStudents = (clone $students)->count();
                $profileCompleted = (clone $students)
                    ->whereNotNull('id_number')
                    ->whereNotNull('identification_type')
                    ->whereNotNull('id_photo')
                    ->whereNotNull('passport_photo')
                    ->count();
                $verifiedStudents = (clone $students)
                    ->whereIn('email', function ($query) {
                        $query->select('email')
                            ->from('email_verifications')
                            ->where('verified', true);
                    })
                    ->count();

                $genderCounts = (clone $students)
                    ->selectRaw("COALESCE(NULLIF(TRIM(sex), ''), 'Unknown') as gender, COUNT(*) as total")
                    ->groupBy('gender')
                    ->get()
                    ->mapWithKeys(fn ($row) => [ucfirst(strtolower($row->gender)) => (int) $row->total])
                    ->all();

                $courses = $branch->courses()
                    ->withCount(['students as student_count' => function ($query) use ($branch, $academicYearId) {
                        $query->where('branch_id', $branch->id)
                            ->when($academicYearId, fn ($studentQuery) => $studentQuery->where('academic_year_id', $academicYearId));
                    }])
                    ->orderBy('course_name')
                    ->get(['courses.id', 'courses.course_name']);

                $teachers = $branch->teachers()
                    ->orderBy('teacher_name')
                    ->get(['teachers.id', 'teachers.teacher_name']);

                return [
                    'branch' => $branch,
                    'total_students' => $totalStudents,
                    'profile_completed' => $profileCompleted,
                    'profile_completed_rate' => $totalStudents > 0
                        ? round(($profileCompleted / $totalStudents) * 100, 1)
                        : null,
                    'verified_students' => $verifiedStudents,
                    'verified_rate' => $totalStudents > 0
                        ? round(($verifiedStudents / $totalStudents) * 100, 1)
                        : null,
                    'gender_counts' => $genderCounts,
                    'courses' => $courses,
                    'teachers' => $teachers,
                ];
            });

            $attendanceQuery = \App\Models\Attendance::query()
                ->where('date', '>=', now()->subDays(7)->toDateString());

            if ($academicYearId) {
                $attendanceQuery->whereHas('student', fn ($q) => $q->where('academic_year_id', $academicYearId));
            }

            $attendanceStats = $attendanceQuery
                ->selectRaw('date, COUNT(*) as total, SUM(status = "P") as present, SUM(status != "P") as absent')
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            $studiedHours = \App\Models\TeacherDaily::selectRaw('date, SUM(hours_studied) as hours')
                ->where('date', '>=', now()->subDays(7)->toDateString())
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            return view('staff.dashboard_dos', compact(
                'attendanceStats',
                'studiedHours',
                'selectedAcademicYear',
                'branchAnalytics'
            ));
        }

        if ($role === 'Accountant') {
            // Redirect to the new accountant dashboard
            return redirect()->route('accountant.dashboard');
        }

        if ($role === 'SEC' || $role === 'Secretary') {
            // Redirect to secretary dashboard route
            return redirect()->route('secretary.dashboard');
        }

        abort(403, 'Unauthorized action. Role: ' . $role);
    }

    public function getStudentsByCourseAndBranch($courseId, $branchId)
    {
        $staff = Auth::guard('staff')->user();

        $hasAccess = $staff->branches()->where('branches.id', $branchId)->exists();

        if (! $hasAccess) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $studentsQuery = \App\Models\Student::where('course_id', $courseId)
            ->where('branch_id', $branchId);

        if ($yearId = AcademicYearContext::selectedId()) {
            $studentsQuery->where('academic_year_id', $yearId);
        }

        $students = $studentsQuery
            ->select('id', 'full_name', 'reg_number', 'email', 'phone', 'passport_photo')
            ->orderBy('full_name')
            ->get();

        return response()->json([
            'students' => $students,
            'count' => $students->count(),
        ]);
    }
}
