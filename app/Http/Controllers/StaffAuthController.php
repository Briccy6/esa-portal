<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

            // Log the authentication attempt for debugging
            \Log::info('Staff login attempt', [
                'email' => $request->email,
                'role' => $role,
                'staff_id' => $staff->id
            ]);

            if ($role === 'DOS') {
                return redirect()->route('staff.dashboard.dos')->with('success', 'Welcome, DOS!');
            } elseif ($role === 'Accountant') {
                return redirect()->route('staff.dashboard.accountant')->with('success', 'Welcome, Accountant!');
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

  // Replace the dashboard() method in App\Http\Controllers\StaffAuthController.php
// Replace the dashboard() method in App\Http\Controllers\StaffAuthController.php

public function dashboard()
{
    $staff = Auth::guard('staff')->user();
    $role = $staff->role;

    \Log::info('Staff dashboard access', [
        'staff_id' => $staff->id,
        'role' => $role,
        'email' => $staff->email
    ]);

    if ($role === 'DOS') {
        $attendanceStats = \App\Models\Attendance::selectRaw('date, COUNT(*) as total, SUM(status = "P") as present, SUM(status != "P") as absent')
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $studiedHours = \App\Models\TeacherDaily::selectRaw('date, SUM(hours_studied) as hours')
            ->where('date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('staff.dashboard_dos', compact('attendanceStats', 'studiedHours'));
    } 
    elseif ($role === 'Accountant') {
        $branches = $staff->branches; // Eloquent relationship
        $branchStats = [];
        
        // Get all expenses for this staff
        $allExpenses = \App\Models\Expense::where('paid_by', $staff->id)->get();
        
        foreach ($branches as $branch) {
            // Get all students in this branch
            $studentIds = \App\Models\Student::where('branch_id', $branch->id)->pluck('id');

            // Sum income for all students in this branch
            $totalIncome = \App\Models\StudentTransaction::whereIn('status', ['successful', 'paid'])
                ->whereIn('student_id', $studentIds)
                ->sum('amount');

            // Expenses: cannot filter by branch, so show all for this staff
            $branchExpenses = $allExpenses; // or filter by date/type if needed
            $totalExpenses = $branchExpenses->sum('amount');
            $expensesCount = $branchExpenses->count();

            $studentsCount = $studentIds->count();

            // Get courses and their student counts for this branch
            $courseData = \App\Models\Course::whereHas('branches', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })
            ->withCount(['students' => function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            }])
            ->get()
            ->map(function($course) {
                return [
                    'id' => $course->id, // NEW: Add course ID
                    'name' => $course->abbreviation ?? $course->course_name,
                    'count' => $course->students_count
                ];
            })
            ->filter(function($course) {
                return $course['count'] > 0; // Only show courses with students
            })
            ->values()
            ->toArray();

            $branchStats[] = [
                'branch' => $branch,
                'totalIncome' => $totalIncome,
                'totalExpenses' => $totalExpenses,
                'studentsCount' => $studentsCount,
                'expensesCount' => $expensesCount,
                'courseData' => $courseData // NEW: Course data for charts
            ];
        }
        
        return view('staff.dashboard_accountant', compact('branchStats'));
    } 
    else {
        abort(403, 'Unauthorized action. Role: ' . $role);
    }
}

/**
 * Get students by course and branch for modal display
 */
public function getStudentsByCourseAndBranch($courseId, $branchId)
{
    $staff = Auth::guard('staff')->user();
    
    // Verify staff has access to this branch
    $hasAccess = $staff->branches()->where('branches.id', $branchId)->exists();
    
    if (!$hasAccess) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }
    
    // Get students for this course and branch
    $students = \App\Models\Student::where('course_id', $courseId)
        ->where('branch_id', $branchId)
        ->select('id', 'full_name', 'reg_number', 'email', 'phone', 'passport_photo')
        ->orderBy('full_name')
        ->get();
    
    return response()->json([
        'students' => $students,
        'count' => $students->count()
    ]);
}
}
