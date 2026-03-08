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
use App\Models\TokenTransaction;
use App\Models\LiveClassPayment;
use App\Models\Resource;
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

        // Expenses by branch (simplified - just show total expenses without branch breakdown)
        $expensesByBranch = Branch::select(
                'id as branch_id',
                'name as branch',
                DB::raw('0 as total'),
                DB::raw('0 as count')
            )
            ->orderBy('name')
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

        // Expense by branch and type (simplified - just show types without branch breakdown)
        $expenseByBranchType = Expense::select(
                'expense_type',
                DB::raw('sum(amount) as total')
            )
            ->groupBy('expense_type')
            ->orderBy('total', 'desc')
            ->get();

        // Top spenders (simplified - just show expense totals by staff name)
        $topSpenders = Expense::select(
                'paid_by as name',
                DB::raw('sum(amount) as total_spent'),
                DB::raw('count(*) as expense_count')
            )
            ->groupBy('paid_by')
            ->orderBy('total_spent', 'desc')
            ->take(5)
            ->get();

        // Revenue vs Expenses comparison
        $totalRevenue = $registrationPaidAmount + StudentTransaction::where('type', 'school_fee')
            ->where('status', 'successful')
            ->sum('amount');
        
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // REVENUE SOURCE ANALYTICS
        // Token Purchase Revenue (from credit transactions)
        $tokenRevenue = TokenTransaction::where('type', 'credit')
            ->where('source', 'purchase')
            ->sum('amount');

        // Live Class Revenue (from completed payments)
        $liveClassRevenue = LiveClassPayment::where('status', 'completed')
            ->sum('amount');

        // Resource Upload Revenue (from paid resources)
        $resourceRevenue = Resource::where('is_free', false)
            ->where('price', '>', 0)
            ->sum('price');

        // Monthly breakdown for each revenue source
        $monthlyTokenRevenue = TokenTransaction::where('type', 'credit')
            ->where('source', 'purchase')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');

        $monthlyLiveClassRevenue = LiveClassPayment::where('status', 'completed')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');

        $monthlyResourceRevenue = Resource::where('is_free', false)
            ->where('price', '>', 0)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('price');

        // Update total revenue to include all sources
        $totalRevenue = $registrationPaidAmount + 
            StudentTransaction::where('type', 'school_fee')
                ->where('status', 'successful')
                ->sum('amount') +
            $tokenRevenue + 
            $liveClassRevenue + 
            $resourceRevenue;

        // Recalculate profit and margin
        $netProfit = $totalRevenue - $totalExpenses;
        $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 1) : 0;

        // Recent expenses (last 10) - simplified without relationship
        $recentExpenses = Expense::orderBy('expense_date', 'desc')
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

        // Branch financial health (simplified - revenue only, expenses shown separately)
        $branchFinancialHealth = DB::table('branches')
            ->select(
                'branches.id',
                'branches.name',
                DB::raw('COALESCE(SUM(CASE WHEN student_transactions.status = "successful" THEN student_transactions.amount ELSE 0 END), 0) as revenue'),
                DB::raw('0 as expenses')
            )
            ->leftJoin('students', 'branches.id', '=', 'students.branch_id')
            ->leftJoin('student_transactions', function($join) {
                $join->on('students.id', '=', 'student_transactions.student_id')
                     ->whereIn('student_transactions.type', ['registration', 'school_fee'])
                     ->where('student_transactions.status', 'successful');
            })
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
            ->get()
            ->map(function($item) {
                $item->sex = 'Male'; // Default since sex column doesn't exist
                return $item;
            });

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
            'recentStudents',
            'tokenRevenue',
            'liveClassRevenue',
            'resourceRevenue',
            'monthlyTokenRevenue',
            'monthlyLiveClassRevenue',
            'monthlyResourceRevenue'
        ));
    }
}