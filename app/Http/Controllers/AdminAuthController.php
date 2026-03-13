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

            // Calculate total revenue and profit metrics
            $totalRevenue = $tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue;
            $totalExpenses = Expense::sum('amount') ?? 0;
            $netProfit = $totalRevenue - $totalExpenses;
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;

            // Recent data for dashboard tables
            $recentStudents = Student::with(['branch', 'course'])->latest()->take(10)->get();
            $recentExpenses = Expense::latest()->take(10)->get();

            // Chart data variables
            $studentsByGender = Student::select('sex', DB::raw('count(*) as total'))
                ->groupBy('sex')
                ->get();

            $registrationPaidPercent = Student::where('payed_registration_fees', true)->count() > 0 
                ? round((Student::where('payed_registration_fees', true)->count() / Student::count()) * 100, 1)
                : 0;

            $tuitionPaidPercent = 0; // No tuition fee status column exists, set to 0 for now

            $expensesByBranch = Expense::select('paid_by as branch', DB::raw('sum(amount) as total'))
                ->groupBy('paid_by')
                ->get();

            $expensesByType = Expense::select('expense_type', DB::raw('sum(amount) as total'))
                ->groupBy('expense_type')
                ->get();

            $branchFinancialHealth = Branch::select('branches.name',
                    DB::raw('(SELECT COALESCE(sum(ss.paid_amount), 0) FROM student_subscriptions ss JOIN students s ON ss.student_id = s.id WHERE s.branch_id = branches.id AND ss.payment_status = "paid" AND ss.is_active = 1) as revenue'),
                    DB::raw('(SELECT COALESCE(sum(e.amount), 0) FROM expenses e WHERE e.paid_by = branches.name) as expenses'))
                ->get();

            $expenseTrend = Expense::select(
                    DB::raw('DATE_FORMAT(expense_date, "%b %Y") as month'),
                    DB::raw('sum(amount) as amount')
                )
                ->where('expense_date', '>=', Carbon::now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('expense_date')
                ->get()
                ->toArray();

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
                'monthlySubscriptionRevenue'
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
