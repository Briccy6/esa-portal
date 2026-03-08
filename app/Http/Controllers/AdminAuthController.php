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
use App\Models\StudentSubscription;
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
        // Enable query logging for debugging
        DB::enableQueryLog();
        
        try {
            // Basic counts (fast queries)
            $totalStudents = Student::count();
            $pendingTransactions = StudentTransaction::where('status', 'pending')->count();
            $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
            $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;
            
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

            // Subscription Revenue (from active student subscriptions)
            $subscriptionRevenue = StudentSubscription::where('payment_status', 'paid')
                ->where('is_active', true)
                ->sum('paid_amount');

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

            $monthlySubscriptionRevenue = StudentSubscription::where('payment_status', 'paid')
                ->where('is_active', true)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('paid_amount');

            $monthlyResourceRevenue = Resource::where('is_free', false)
                ->where('price', '>', 0)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('price');

            // Calculate total revenue from all sources
            $totalRevenue = $tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue;
            
            // Calculate total expenses (if Expense model exists)
            $totalExpenses = class_exists(Expense::class) ? Expense::sum('amount') : 0;
            
            // Get recent expenses for display
            $recentExpenses = class_exists(Expense::class) ? Expense::with('paid_by_staff.branch')->latest('expense_date')->take(10)->get() : collect([]);
            
            // Students by gender data
            $studentsByGender = Student::select('sex', DB::raw('count(*) as total'))
                ->groupBy('sex')
                ->get();
            
            // Recent students for display
            $recentStudents = Student::with(['branch', 'course'])
                ->latest()
                ->take(10)
                ->get();
            
            // Expenses by branch data
            $expensesByBranch = class_exists(Expense::class) ? Expense::with('paid_by_staff.branch')
                ->selectRaw('branches.name as branch, SUM(expenses.amount) as total')
                ->leftJoin('staff', 'expenses.paid_by', '=', 'staff.id')
                ->leftJoin('branches', 'staff.branch_id', '=', 'branches.id')
                ->groupBy('branches.id', 'branches.name')
                ->get() : collect([]);
            
            // Expenses by type data
            $expensesByType = class_exists(Expense::class) ? Expense::selectRaw('expense_type, SUM(amount) as total')
                ->groupBy('expense_type')
                ->get() : collect([]);
            
            // Branch financial health data (revenue vs expenses by branch)
            $branchFinancialHealth = class_exists(Expense::class) ? 
                DB::table('branches as b')
                    ->leftJoin('students as s', 'b.id', '=', 's.branch_id')
                    ->leftJoin('student_transactions as st', 's.id', '=', 'st.student_id')
                    ->leftJoin('staff as staff', 'b.id', '=', 'staff.branch_id')
                    ->leftJoin('expenses as e', 'staff.id', '=', 'e.paid_by')
                    ->selectRaw('
                        b.name,
                        COALESCE(SUM(CASE WHEN st.status = "paid" THEN st.amount ELSE 0 END), 0) as revenue,
                        COALESCE(SUM(e.amount), 0) as expenses
                    ')
                    ->groupBy('b.id', 'b.name')
                    ->get() : collect([]);
            
            // 6-month expense trend data
            $expenseTrend = class_exists(Expense::class) ? Expense::selectRaw('
                    DATE_FORMAT(expense_date, "%M %Y") as month,
                    SUM(amount) as amount
                ')
                ->where('expense_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('expense_date')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month,
                        'amount' => (float) $item->amount
                    ];
                })
                ->toArray() : [];
            
            // Students by course and gender data
            try {
                $studentsByCourseGender = DB::table('students as s')
                    ->leftJoin('courses as c', 's.course_id', '=', 'c.id')
                    ->selectRaw('
                        c.course_name as course_name,
                        c.abbreviation as course_abbreviation,
                        s.sex,
                        COUNT(*) as total
                    ')
                    ->groupBy('c.id', 'c.course_name', 'c.abbreviation', 's.sex')
                    ->get()
                    ->map(function ($item) {
                        return [
                            'course' => [
                                'name' => $item->course_name ?: 'Unknown',
                                'abbreviation' => $item->course_abbreviation ?: 'N/A'
                            ],
                            'sex' => $item->sex,
                            'total' => (int) $item->total
                        ];
                    })
                    ->toArray();
            } catch (\Exception $e) {
                $studentsByCourseGender = [];
            }

            // Tuition fee data by branch
            try {
                $tuitionByBranch = DB::table('branches as b')
                    ->leftJoin('students as s', 'b.id', '=', 's.branch_id')
                    ->leftJoin('courses as c', 's.course_id', '=', 'c.id')
                    ->selectRaw('
                        b.name as branch,
                        COALESCE(SUM(c.tuition_fee), 0) as expected,
                        COUNT(s.id) as student_count
                    ')
                    ->groupBy('b.id', 'b.name')
                    ->get();
            } catch (\Exception $e) {
                $tuitionByBranch = collect([]);
            }
            
            // Paid tuition by branch
            try {
                $tuitionPaidByBranch = DB::table('branches as b')
                    ->leftJoin('students as s', 'b.id', '=', 's.branch_id')
                    ->leftJoin('student_transactions as st', 's.id', '=', 'st.student_id')
                    ->selectRaw('
                        b.name as branch,
                        COALESCE(SUM(CASE WHEN st.status = "paid" THEN st.amount ELSE 0 END), 0) as paid
                    ')
                    ->groupBy('b.id', 'b.name')
                    ->get();
            } catch (\Exception $e) {
                $tuitionPaidByBranch = collect([]);
            }
            
            // Fee payment percentages (based on student transactions)
            $totalStudents = Student::count();
            $registrationPaidPercent = 0; // Default to 0 since we don't have direct registration fee tracking
            $tuitionPaidPercent = $totalStudents > 0 ? round((StudentTransaction::where('status', 'paid')->distinct('student_id')->count() / $totalStudents) * 100, 2) : 0;
            
            // Calculate net profit and profit margin
            $netProfit = $totalRevenue - $totalExpenses;
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

            // Cache the results for 1 hour
            $dashboardData = compact(
                'totalStudents',
                'pendingTransactions',
                'totalTeachers',
                'totalStaff',
                'tokenRevenue',
                'liveClassRevenue',
                'resourceRevenue',
                'subscriptionRevenue',
                'monthlyTokenRevenue',
                'monthlyLiveClassRevenue',
                'monthlyResourceRevenue',
                'monthlySubscriptionRevenue',
                'totalRevenue',
                'totalExpenses',
                'netProfit',
                'profitMargin',
                'recentExpenses',
                'studentsByGender',
                'recentStudents',
                'expensesByBranch',
                'expensesByType',
                'branchFinancialHealth',
                'expenseTrend',
                'tuitionByBranch',
                'tuitionPaidByBranch',
                'studentsByCourseGender',
                'registrationPaidPercent',
                'tuitionPaidPercent'
            );
            
            return view('admin.dashboard', $dashboardData);
            
        } catch (Exception $e) {
            // Get the query log to debug which query failed
            $queries = DB::getQueryLog();
            $errorInfo = [
                'error_message' => $e->getMessage(),
                'query_log' => $queries,
                'trace' => $e->getTraceAsString()
            ];
            
            // Log the error for debugging
            \Log::error('Dashboard Error: ' . $e->getMessage(), [
                'queries' => $queries,
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error view with debug information
            return view('admin.dashboard-error', $errorInfo);
        }
    }
}
