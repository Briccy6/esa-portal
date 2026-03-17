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
use App\Models\Client;
use App\Models\ClientTransaction;
use App\Models\ClientSubscription;
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

    public function dashboard(Request $request)
    {
        // Enable query logging for debugging
        DB::enableQueryLog();
        
        try {
            // Basic counts (fast queries)
            $totalStudents = Student::count();
            $pendingTransactions = StudentTransaction::where('status', 'pending')->count();
            $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
            $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;
            
            // Initialize revenue variables to prevent undefined variable errors
            $revenueData = 0;
            $expensesData = 0;
            $tokenRevenue = 0;
            $liveClassRevenue = 0;
            $resourceRevenue = 0;
            $subscriptionRevenue = 0;
            $monthlyTokenRevenue = 0;
            $monthlyLiveClassRevenue = 0;
            $monthlyResourceRevenue = 0;
            $monthlySubscriptionRevenue = 0;
            $totalExpenses = 0;
            
            // Initialize expense variables to prevent undefined variable errors
            $expensesByBranch = collect();
            $expensesByType = collect();
            $expenseTrend = collect(); // Ensure it's always a collection

            // SIMPLIFIED REVENUE CALCULATION - Direct from transaction tables
            // Token Purchase Revenue (from credit transactions)
            $tokenRevenue = TokenTransaction::where('type', 'credit')->where('source', 'purchase')->sum('amount');
            $liveClassRevenue = LiveClassPayment::where('status', 'completed')->sum('amount');
            $resourceRevenue = Resource::where('is_free', false)->where('price', '>', 0)->sum('price');
            $subscriptionRevenue = StudentSubscription::where('payment_status', 'paid')->where('is_active', true)->sum('paid_amount');
            $totalExpenses = Expense::sum('amount') ?? 0;

            // Monthly breakdowns
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

            $revenueData = $tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue;

            // Calculate total revenue and profit metrics
            $totalRevenue = $revenueData;
            $totalExpenses = $totalExpenses; // Use the value from expenses query
            $netProfit = $totalRevenue - $totalExpenses;
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

            // Recent data for dashboard tables
            $recentStudents = Student::with(['branch', 'course'])->latest()->take(10)->get();
            $recentExpenses = Expense::latest()->take(10)->get();

            // Client statistics for dashboard
            $totalClients = Client::count();
            $activeClients = Client::where('is_active', true)->count();
            $inactiveClients = Client::where('is_active', false)->count();
            $totalClientTokens = ClientTransaction::where('type', 'purchase')->sum('total_tokens');
            $totalClientRevenue = ClientTransaction::where('type', 'purchase')->sum('amount');
            $recentClients = Client::with(['activeSubscription'])->latest()->take(5)->get();

            // Chart data variables
            $studentsByGender = Student::select('sex', DB::raw('count(*) as total'))
                ->groupBy('sex')
                ->get();

            $registrationPaidPercent = Student::where('payed_registration_fees', true)->count() > 0 
                ? round((Student::where('payed_registration_fees', true)->count() / Student::count()) * 100, 1)
                : 0;

            $tuitionPaidPercent = 0; // No tuition fee status column exists, set to 0 for now

            // Expenses by branch (from expenses table)
            $expensesByBranch = Expense::select('paid_by as branch', DB::raw('sum(amount) as total'))
                ->groupBy('paid_by')
                ->get();
                
            $expensesByType = Expense::select('expense_type', DB::raw('sum(amount) as total'))
                ->groupBy('expense_type')
                ->get();
            
            $expenseTrend = Expense::select(
                    DB::raw('DATE_FORMAT(expense_date, "%b %Y") as month'),
                    DB::raw('sum(amount) as amount')
                )
                ->where('expense_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('expense_date')
                ->get();

            // Branch Financial Health (using direct table data)
            $branchFinancialHealth = Branch::select('branches.name',
                    DB::raw('(SELECT COALESCE(sum(ss.paid_amount), 0) FROM student_subscriptions ss JOIN students s ON ss.student_id = s.id WHERE s.branch_id = branches.id AND ss.payment_status = "paid" AND ss.is_active = 1) as revenue'),
                    DB::raw('(SELECT COALESCE(sum(e.amount), 0) FROM expenses e WHERE e.paid_by = branches.name) as expenses'))
                ->get();

            // Tuition by branch data
            $tuitionByBranch = Branch::select('name as branch', 
                    DB::raw('(SELECT COUNT(*) FROM students WHERE branch_id = branches.id) * 100000 as expected')) // Assuming 100,000 RWF per student
                ->get();

            $tuitionPaidByBranch = collect(); // Empty collection since we don't have tuition payment tracking

            // Students by course and gender data
            $studentsByCourseGender = Student::select('sex', 'courses.course_name as course_name', 'courses.id as course_id', DB::raw('count(*) as total'))
                ->join('courses', 'students.course_id', '=', 'courses.id')
                ->groupBy('sex', 'courses.id', 'courses.course_name')
                ->get()
                ->map(function($item) {
                    $item->course = (object)['name' => $item->course_name, 'id' => $item->course_id];
                    $item->sex = strtoupper(substr($item->sex, 0, 1)); // Convert to M/F format
                    return $item;
                });

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
                'totalRevenue',
                'totalExpenses',
                'netProfit',
                'profitMargin',
                'recentStudents',
                'recentExpenses',
                'studentsByGender',
                'studentsByCourseGender',
                'registrationPaidPercent',
                'tuitionPaidPercent',
                'expensesByBranch',
                'expensesByType',
                'branchFinancialHealth',
                'expenseTrend',
                'tuitionByBranch',
                'tuitionPaidByBranch',
                'monthlyTokenRevenue',
                'monthlyLiveClassRevenue',
                'monthlyResourceRevenue',
                'monthlySubscriptionRevenue',
                'totalClients',
                'activeClients',
                'inactiveClients',
                'totalClientTokens',
                'totalClientRevenue',
                'recentClients'
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
