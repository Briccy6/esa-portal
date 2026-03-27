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
        try {
            // Basic counts (fast queries)
            $totalStudents = Student::count();
            $pendingTransactions = StudentTransaction::where('status', 'pending')->count();
            $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
            $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;
            
            // Initialize revenue variables with actual transaction types from your database
            $tokenRevenue = 0; // No token_purchase transactions exist
            $liveClassRevenue = 0; // No live_class transactions exist
            $resourceRevenue = 0; // No resource transactions exist
            $subscriptionRevenue = StudentTransaction::where('type', 'subscription')->where('status', 'successful')->sum('amount');
            $registrationRevenue = StudentTransaction::where('type', 'registration')->where('status', 'successful')->sum('amount') + 
                                 StudentTransaction::where('type', 'registration_fee')->where('status', 'successful')->sum('amount');
            $examRevenue = 0; // No exam_purchase transactions exist
            
            // Calculate total expenses from expenses table
            $totalExpenses = Expense::sum('amount') ?? 0;
            
            // Calculate total revenue
            $totalRevenue = $subscriptionRevenue + $registrationRevenue;
            $netProfit = $totalRevenue - $totalExpenses;
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;
            
            // Monthly breakdowns
            $monthlyTokenRevenue = 0;
            $monthlyLiveClassRevenue = 0;
            $monthlyResourceRevenue = 0;
            $monthlySubscriptionRevenue = StudentTransaction::where('type', 'subscription')
                ->where('status', 'successful')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amount');

            // Recent data for dashboard tables
            $recentStudents = Student::with(['branch', 'course'])->latest()->take(10)->get();
            $recentExpenses = Expense::latest()->take(10)->get();

            // Client statistics
            $totalClients = Client::count();
            $activeClients = Client::where('is_active', true)->count();
            $inactiveClients = Client::where('is_active', false)->count();
            $totalClientTokens = ClientTransaction::where('type', 'purchase')->sum('total_tokens') ?? 0;
            $totalClientRevenue = ClientTransaction::where('type', 'purchase')->sum('amount') ?? 0;
            $recentClients = Client::with(['activeSubscription'])->latest()->take(5)->get();

            // Student demographics
            $studentsByGender = Student::select('sex', DB::raw('count(*) as total'))
                ->groupBy('sex')
                ->get();

            $registrationPaidPercent = Student::where('payed_registration_fees', true)->count() > 0 
                ? round((Student::where('payed_registration_fees', true)->count() / Student::count()) * 100, 1)
                : 0;

            $tuitionPaidPercent = 0; // No tuition fee tracking

            // Expenses by branch and type
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

            // Simplified branch financial health
            $branchFinancialHealth = Branch::select('name', 
                    DB::raw('0 as revenue'), // No subscription revenue tracking
                    DB::raw('(SELECT COALESCE(sum(amount), 0) FROM expenses WHERE paid_by = name) as expenses'))
                ->get();

            // Tuition by branch (simplified)
            $tuitionByBranch = Branch::select('name as branch', 
                    DB::raw('(SELECT COUNT(*) FROM students WHERE branch_id = branches.id) * 100000 as expected'))
                ->get();

            $tuitionPaidByBranch = collect();

            // Students by course and gender (with error handling)
            $studentsByCourseGender = collect();
            try {
                $studentsByCourseGender = Student::select('sex', 'courses.course_name as course_name', 'courses.id as course_id', DB::raw('count(*) as total'))
                    ->join('courses', 'students.course_id', '=', 'courses.id')
                    ->groupBy('sex', 'courses.id', 'courses.course_name')
                    ->get()
                    ->map(function($item) {
                        $item->course = (object)['name' => $item->course_name, 'id' => $item->course_id];
                        $item->sex = strtoupper(substr($item->sex, 0, 1));
                        return $item;
                    });
            } catch (\Exception $e) {
                // If courses table doesn't exist or has issues, return empty collection
                $studentsByCourseGender = collect();
            }

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
                'registrationRevenue',
                'examRevenue',
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
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Dashboard Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error view with debug information
            return view('admin.dashboard-error', [
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
