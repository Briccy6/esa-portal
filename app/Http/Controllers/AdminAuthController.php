<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\Course;
use App\Models\StudentTransaction;
use App\Models\Branch;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (auth()->guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ])->withInput();
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login.form');
    }

    public function showForceLogout()
    {
        return view('admin.force-logout');
    }

    public function dashboard()
    {
        $totalStudents = Student::count();
        $totalFees = Course::sum('tuition_fee');
        $registrationTarget = 1000;
        $registrationProgress = $totalStudents > 0 ? round(($totalStudents / $registrationTarget) * 100, 1) : 0;

        // Pending transactions
        $pendingTransactions = StudentTransaction::where('status', 'pending')->count();

        // Monthly registrations
        $monthlyRegistrations = Student::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // Monthly revenue (registration fees)
        $monthlyRevenue = Student::whereMonth('students.created_at', Carbon::now()->month)
            ->whereYear('students.created_at', Carbon::now()->year)
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->sum('courses.registration_fee');

        // Students by branch
        $studentsByBranch = Student::select('branch_id', DB::raw('count(*) as total'))
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        // Students by course
        $studentsByCourse = Student::select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->with('course')
            ->get();

        // Percentage of students who paid tuition fee
        $studentsPaidTuition = StudentTransaction::where('type', 'school_fee')
            ->where('status', ['paid', 'successful'])
            ->distinct('student_id')
            ->count('student_id');
        $tuitionPaidPercent = $totalStudents > 0 ? round(($studentsPaidTuition / $totalStudents) * 100, 1) : 0;

        // Total teachers and staff
        $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
        $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;

        // Expected registration and tuition money
        $expectedRegistration = Student::join('courses', 'students.course_id', '=', 'courses.id')
            ->sum('courses.registration_fee');
        $expectedTuition = Student::join('courses', 'students.course_id', '=', 'courses.id')
            ->sum('courses.tuition_fee');

        // Registration fee paid and progress
       $registrationPaidAmount = StudentTransaction::where('type', 'registration')
    ->whereIn('status', ['paid', 'successful'])
    ->sum('amount');

        $registrationPaidPercent = $expectedRegistration > 0 ? round(($registrationPaidAmount / $expectedRegistration) * 100, 1) : 0;
        $registrationRemaining = $expectedRegistration - $registrationPaidAmount;

        // Tuition fee by branch (expected and paid)
        $tuitionByBranch = Student::join('branches', 'students.branch_id', '=', 'branches.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->select('branches.name as branch', DB::raw('sum(courses.tuition_fee) as expected'))
            ->groupBy('branches.id', 'branches.name')
            ->get();

        $tuitionPaidByBranch = StudentTransaction::where('type', 'school_fee')
            ->where('status', 'successful')
            ->join('students', 'student_transactions.student_id', '=', 'students.id')
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->select('branches.name as branch', DB::raw('sum(student_transactions.amount) as paid'))
            ->groupBy('branches.id', 'branches.name')
            ->get();

        // ENHANCED EXPENSE ANALYTICS
        // Total expenses
        $totalExpenses = Expense::sum('amount');
        
        // Monthly expenses
        $monthlyExpenses = Expense::whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');

        // Expenses by branch (via staff_branch pivot table - no duplicates)
        $expensesByBranch = DB::table('branches')
            ->leftJoin('staff_branch', 'branches.id', '=', 'staff_branch.branch_id')
            ->leftJoin('expenses', 'staff_branch.staff_id', '=', 'expenses.paid_by')
            ->select(
                'branches.id as branch_id',
                'branches.name as branch',
                DB::raw('COALESCE(sum(DISTINCT expenses.amount), 0) as total'),
                DB::raw('count(DISTINCT expenses.id) as count')
            )
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get();

        // Expenses by type with detailed breakdown
        $expensesByType = Expense::select(
                'expense_type',
                DB::raw('sum(amount) as total'),
                DB::raw('count(*) as count'),
                DB::raw('avg(amount) as average')
            )
            ->groupBy('expense_type')
            ->orderBy('total', 'desc')
            ->get();

        // Monthly expense trend (last 6 months)
        $expenseTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $expenseTrend[] = [
                'month' => $date->format('M Y'),
                'amount' => Expense::whereMonth('expense_date', $date->month)
                    ->whereYear('expense_date', $date->year)
                    ->sum('amount')
            ];
        }

        // Expense by branch and type (via staff_branch pivot - no duplicates)
        $expenseByBranchType = DB::table('branches')
            ->leftJoin('staff_branch', 'branches.id', '=', 'staff_branch.branch_id')
            ->leftJoin('expenses', 'staff_branch.staff_id', '=', 'expenses.paid_by')
            ->select(
                'branches.name as branch',
                'expenses.expense_type',
                DB::raw('COALESCE(sum(DISTINCT expenses.amount), 0) as total')
            )
            ->whereNotNull('expenses.expense_type')
            ->groupBy('branches.name', 'expenses.expense_type')
            ->orderBy('branches.name')
            ->get();

        // Top spenders (staff via their branch assignments - no duplicates)
        $topSpenders = DB::table('expenses')
            ->join('staff', 'expenses.paid_by', '=', 'staff.id')
            ->join('staff_branch', 'staff.id', '=', 'staff_branch.staff_id')
            ->join('branches', 'staff_branch.branch_id', '=', 'branches.id')
            ->select(
                'staff.id',
                'staff.name',
                DB::raw('GROUP_CONCAT(DISTINCT branches.name SEPARATOR ", ") as branch_names'),
                DB::raw('sum(DISTINCT expenses.amount) as total_spent'),
                DB::raw('count(DISTINCT expenses.id) as expense_count')
            )
            ->groupBy('staff.id', 'staff.name')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();

        // Revenue vs Expenses comparison
        $totalRevenue = $registrationPaidAmount + StudentTransaction::where('type', 'school_fee')
            ->where('status', 'successful')
            ->sum('amount');
        
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // Recent expenses (last 10)
        $recentExpenses = Expense::with(['paid_by_staff.branch'])
            ->orderBy('expense_date', 'desc')
            ->take(10)
            ->get();

        // Recent students with NESA status for dashboard
        $recentStudents = Student::with(['branch', 'course'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        // Expense categories summary
        $expenseCategories = Expense::select('expense_type', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('expense_type')
            ->get();

        // Branch financial health shows revenue vs expenses to help admin see which branches are profitable
        // Revenue: money coming in from students (registration + tuition fees)
        // Expenses: money going out through staff expenses in that branch
        // This helps identify branches that need attention (high expenses, low revenue)
        $branchFinancialHealth = DB::table('branches')
            ->select(
                'branches.id',
                'branches.name',
                DB::raw('COALESCE(SUM(DISTINCT CASE WHEN student_transactions.status = "successful" THEN student_transactions.amount ELSE 0 END), 0) as revenue'),
                DB::raw('COALESCE(SUM(DISTINCT expenses.amount), 0) as expenses')
            )
            ->leftJoin('students', 'branches.id', '=', 'students.branch_id')
            ->leftJoin('student_transactions', function($join) {
                $join->on('students.id', '=', 'student_transactions.student_id')
                     ->whereIn('student_transactions.type', ['registration', 'school_fee'])
                     ->where('student_transactions.status', 'successful');
            })
            ->leftJoin('staff_branch', 'branches.id', '=', 'staff_branch.branch_id')
            ->leftJoin('expenses', 'staff_branch.staff_id', '=', 'expenses.paid_by')
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('branches.name')
            ->get();

        // Students by gender (for demographic analysis)
        $studentsByGender = Student::select('sex', DB::raw('count(*) as total'))
            ->groupBy('sex')
            ->get();

        // Students by course and gender (cross-analysis)
        $studentsByCourseGender = Student::select('course_id', 'sex', DB::raw('count(*) as total'))
            ->groupBy('course_id', 'sex')
            ->with('course')
            ->get();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalFees',
            'registrationProgress',
            'pendingTransactions',
            'monthlyRegistrations',
            'monthlyRevenue',
            'registrationTarget',
            'studentsByBranch',
            'studentsByCourse',
            'tuitionPaidPercent',
            'totalTeachers',
            'totalStaff',
            'expectedRegistration',
            'expectedTuition',
            'registrationPaidAmount',
            'registrationPaidPercent',
            'registrationRemaining',
            'tuitionByBranch',
            'tuitionPaidByBranch',
            'totalExpenses',
            'monthlyExpenses',
            'expensesByBranch',
            'expensesByType',
            'expenseTrend',
            'totalRevenue',
            'netProfit',
            'profitMargin',
            'recentExpenses',
            'expenseCategories',
            'expenseByBranchType',
            'topSpenders',
            'branchFinancialHealth',
            'studentsByGender',
            'studentsByCourseGender',
            'recentStudents'
        ));
    }
}