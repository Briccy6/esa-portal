<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
            $request->session()->regenerate();
            
            // Redirect to intended URL or dashboard
            $intendedUrl = session()->pull('admin_intended', route('admin.dashboard'));
            return redirect()->to($intendedUrl);
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
        // Use query caching and optimize expensive operations
        $cacheKey = 'admin_dashboard_' . date('Y-m-d-H'); // Cache per hour
        
        // Try to get cached data first
        $cachedData = Cache::get($cacheKey);
        if ($cachedData) {
            return view('admin.dashboard', $cachedData);
        }
        
        // Basic counts (fast queries)
        $totalStudents = Student::count();
        $pendingTransactions = StudentTransaction::where('status', 'pending')->count();
        $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
        $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;
        
        // Use raw queries for expensive aggregations
        $totalFees = DB::table('courses')->sum('tuition_fee');
        
        // Monthly calculations (single query)
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $monthlyData = DB::table('students')
            ->select(
                DB::raw('COUNT(*) as monthly_registrations'),
                DB::raw('COALESCE(SUM(courses.registration_fee), 0) as monthly_revenue')
            )
            ->leftJoin('courses', 'students.course_id', '=', 'courses.id')
            ->whereMonth('students.created_at', $currentMonth)
            ->whereYear('students.created_at', $currentYear)
            ->first();
            
        $monthlyRegistrations = $monthlyData->monthly_registrations;
        $monthlyRevenue = $monthlyData->monthly_revenue;
        
        // Progress calculations
        $registrationTarget = 1000;
        $registrationProgress = $totalStudents > 0 ? round(($totalStudents / $registrationTarget) * 100, 1) : 0;

        // Simplified aggregations using raw queries
        $financialData = DB::table('students')
            ->select(
                DB::raw('COUNT(*) as total_students'),
                DB::raw('COALESCE(SUM(c.registration_fee), 0) as expected_registration'),
                DB::raw('COALESCE(SUM(c.tuition_fee), 0) as expected_tuition')
            )
            ->leftJoin('courses as c', 'students.course_id', '=', 'c.id')
            ->first();
            
        $expectedRegistration = $financialData->expected_registration;
        $expectedTuition = $financialData->expected_tuition;
        
        // Transaction data
        $transactionData = DB::table('student_transactions')
            ->select(
                'type',
                DB::raw('SUM(CASE WHEN status IN ("paid", "successful") THEN amount ELSE 0 END) as paid_amount'),
                DB::raw('COUNT(DISTINCT CASE WHEN type = "school_fee" AND status IN ("paid", "successful") THEN student_id END) as paid_tuition_students')
            )
            ->whereIn('type', ['registration', 'school_fee'])
            ->groupBy('type')
            ->get()
            ->keyBy('type');
            
        $registrationPaidAmount = $transactionData->get('registration', (object)['paid_amount' => 0])->paid_amount;
        $studentsPaidTuition = $transactionData->get('school_fee', (object)['paid_tuition_students' => 0])->paid_tuition_students;
        
        $registrationPaidPercent = $expectedRegistration > 0 ? round(($registrationPaidAmount / $expectedRegistration) * 100, 1) : 0;
        $registrationRemaining = $expectedRegistration - $registrationPaidAmount;
        $tuitionPaidPercent = $totalStudents > 0 ? round(($studentsPaidTuition / $totalStudents) * 100, 1) : 0;

        // Simplified branch and course data
        $studentsByBranch = DB::table('students')
            ->select('branch_id', DB::raw('count(*) as total'))
            ->groupBy('branch_id')
            ->get()
            ->map(function($item) {
                $branch = Branch::find($item->branch_id);
                $item->branch = $branch;
                return $item;
            });
            
        $studentsByCourse = DB::table('students')
            ->select('course_id', DB::raw('count(*) as total'))
            ->groupBy('course_id')
            ->get()
            ->map(function($item) {
                $course = Course::find($item->course_id);
                $item->course = $course;
                return $item;
            });

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

        // Simplified data for missing variables
        $tuitionByBranch = collect([]);
        $tuitionPaidByBranch = collect([]);

        // Cache the results for 1 hour
        $dashboardData = compact(
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
        );
        
        Cache::put($cacheKey, $dashboardData, 3600); // Cache for 1 hour
        
        return view('admin.dashboard', $dashboardData);
    }
}