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
            $recentExpenses = Expense::with(['paid_by_staff.branch'])->latest()->get();

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
