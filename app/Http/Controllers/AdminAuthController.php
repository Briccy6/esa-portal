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
            // Get date filters from request
            $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
            $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;
            
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

            // REVENUE SOURCE ANALYTICS - Using Accounting System with Fallback
            // Check if accounting system has actual revenue/expense data
            $hasAccountingData = DB::table('journal_items as ji')
                ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                ->where('je.status', 'Posted')
                ->whereIn('a.account_type', ['Revenue', 'Expense'])
                ->count() > 0;
            
            // Debug: Log accounting system detection
            \Log::info('Accounting system has data: ' . ($hasAccountingData ? 'YES' : 'NO'));
            
            if ($hasAccountingData) {
                // Get revenue from journal entries (Revenue accounts)
                $revenueData = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->when($startDate, function($query, $startDate) {
                        return $query->whereDate('je.entry_date', '>=', $startDate);
                    })
                    ->when($endDate, function($query, $endDate) {
                        return $query->whereDate('je.entry_date', '<=', $endDate);
                    })
                    ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as total_revenue')
                    ->value('total_revenue') ?? 0;

                // Get expenses from journal entries (Expense accounts)
                $expensesData = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Expense')
                    ->when($startDate, function($query, $startDate) {
                        return $query->whereDate('je.entry_date', '>=', $startDate);
                    })
                    ->when($endDate, function($query, $endDate) {
                        return $query->whereDate('je.entry_date', '<=', $endDate);
                    })
                    ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as total_expenses')
                    ->value('total_expenses') ?? 0;

                // Revenue breakdown by source (using account names)
                $revenueBySource = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->when($startDate, function($query, $startDate) {
                        return $query->whereDate('je.entry_date', '>=', $startDate);
                    })
                    ->when($endDate, function($query, $endDate) {
                        return $query->whereDate('je.entry_date', '<=', $endDate);
                    })
                    ->select('a.account_name as source', DB::raw('SUM(ji.credit_amount - ji.debit_amount) as amount'))
                    ->groupBy('a.id', 'a.account_name')
                    ->get();

                // Extract revenue by source for dashboard
                $tokenRevenue = $revenueBySource->where('source', 'LIKE', '%token%')->sum('amount');
                $liveClassRevenue = $revenueBySource->where('source', 'LIKE', '%class%')->sum('amount');
                $resourceRevenue = $revenueBySource->where('source', 'LIKE', '%resource%')->sum('amount');
                $subscriptionRevenue = $revenueBySource->where('source', 'LIKE', '%subscription%')->sum('amount');

                // Monthly breakdowns by source
                $monthlyTokenRevenue = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->where('a.account_name', 'LIKE', '%token%')
                    ->whereMonth('je.entry_date', Carbon::now()->month)
                    ->whereYear('je.entry_date', Carbon::now()->year)
                    ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as amount')
                    ->value('amount') ?? 0;

                $monthlyLiveClassRevenue = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->where('a.account_name', 'LIKE', '%class%')
                    ->whereMonth('je.entry_date', Carbon::now()->month)
                    ->whereYear('je.entry_date', Carbon::now()->year)
                    ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as amount')
                    ->value('amount') ?? 0;

                $monthlySubscriptionRevenue = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->where('a.account_name', 'LIKE', '%subscription%')
                    ->whereMonth('je.entry_date', Carbon::now()->month)
                    ->whereYear('je.entry_date', Carbon::now()->year)
                    ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as amount')
                    ->value('amount') ?? 0;

                $monthlyResourceRevenue = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Revenue')
                    ->where('a.account_name', 'LIKE', '%resource%')
                    ->whereMonth('je.entry_date', Carbon::now()->month)
                    ->whereYear('je.entry_date', Carbon::now()->year)
                    ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as amount')
                    ->value('amount') ?? 0;
            } else {
                // Fallback to existing data models when accounting system is empty
                // Token Purchase Revenue (from credit transactions)
                $tokenRevenueQuery = TokenTransaction::where('type', 'credit')->where('source', 'purchase');
                $liveClassRevenueQuery = LiveClassPayment::where('status', 'completed');
                $resourceRevenueQuery = Resource::where('is_free', false)->where('price', '>', 0);
                $subscriptionRevenueQuery = StudentSubscription::where('payment_status', 'paid')->where('is_active', true);
                $expensesQuery = Expense::query();

                // Apply date filters if provided
                if ($startDate) {
                    $tokenRevenueQuery->whereDate('created_at', '>=', $startDate);
                    $liveClassRevenueQuery->whereDate('paid_at', '>=', $startDate);
                    $resourceRevenueQuery->whereDate('created_at', '>=', $startDate);
                    $subscriptionRevenueQuery->whereDate('created_at', '>=', $startDate);
                    $expensesQuery->whereDate('expense_date', '>=', $startDate);
                }
                if ($endDate) {
                    $tokenRevenueQuery->whereDate('created_at', '<=', $endDate);
                    $liveClassRevenueQuery->whereDate('paid_at', '<=', $endDate);
                    $resourceRevenueQuery->whereDate('created_at', '<=', $endDate);
                    $subscriptionRevenueQuery->whereDate('created_at', '<=', $endDate);
                    $expensesQuery->whereDate('expense_date', '<=', $endDate);
                }

                $tokenRevenue = $tokenRevenueQuery->sum('amount');
                $liveClassRevenue = $liveClassRevenueQuery->sum('amount');
                $resourceRevenue = $resourceRevenueQuery->sum('price');
                $subscriptionRevenue = $subscriptionRevenueQuery->sum('paid_amount');
                $totalExpenses = $expensesQuery->sum('amount') ?? 0;

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
                
                // Debug: Log fallback revenue data
                \Log::info('Fallback Revenue - Token: ' . $tokenRevenue . ', LiveClass: ' . $liveClassRevenue . ', Resource: ' . $resourceRevenue . ', Subscription: ' . $subscriptionRevenue . ', Total: ' . $revenueData);
            } else {
                \Log::info('Using accounting system for revenue data');
            }

            // Calculate total revenue and profit metrics
            $totalRevenue = $revenueData;
            $totalExpenses = $expensesData ?? $totalExpenses;
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

            // Expenses by branch (from accounting system with fallback)
            if ($hasAccountingData) {
                $expensesByBranch = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Expense')
                    ->when($startDate, function($query, $startDate) {
                        return $query->whereDate('je.entry_date', '>=', $startDate);
                    })
                    ->when($endDate, function($query, $endDate) {
                        return $query->whereDate('je.entry_date', '<=', $endDate);
                    })
                    ->select('je.description as branch', DB::raw('SUM(ji.debit_amount - ji.credit_amount) as total'))
                    ->groupBy('je.description')
                    ->get();

                $expensesByType = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Expense')
                    ->when($startDate, function($query, $startDate) {
                        return $query->whereDate('je.entry_date', '>=', $startDate);
                    })
                    ->when($endDate, function($query, $endDate) {
                        return $query->whereDate('je.entry_date', '<=', $endDate);
                    })
                    ->select('a.account_name as expense_type', DB::raw('SUM(ji.debit_amount - ji.credit_amount) as total'))
                    ->groupBy('a.id', 'a.account_name')
                    ->get();

                $expenseTrend = DB::table('journal_items as ji')
                    ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                    ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                    ->where('je.status', 'Posted')
                    ->where('a.account_type', 'Expense')
                    ->where('je.entry_date', '>=', Carbon::now()->subMonths(6))
                    ->select(
                        DB::raw('DATE_FORMAT(je.entry_date, "%b %Y") as month'),
                        DB::raw('SUM(ji.debit_amount - ji.credit_amount) as amount')
                    )
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
            } else {
                // Fallback to expenses table
                $expensesByBranchQuery = Expense::select('paid_by as branch', DB::raw('sum(amount) as total'));
                $expensesByTypeQuery = Expense::select('expense_type', DB::raw('sum(amount) as total'));
                $expenseTrendQuery = Expense::select(
                    DB::raw('DATE_FORMAT(expense_date, "%b %Y") as month'),
                    DB::raw('sum(amount) as amount')
                );

                // Apply date filters
                if ($startDate) {
                    $expensesByBranchQuery->whereDate('expense_date', '>=', $startDate);
                    $expensesByTypeQuery->whereDate('expense_date', '>=', $startDate);
                    $expenseTrendQuery->whereDate('expense_date', '>=', $startDate);
                }
                if ($endDate) {
                    $expensesByBranchQuery->whereDate('expense_date', '<=', $endDate);
                    $expensesByTypeQuery->whereDate('expense_date', '<=', $endDate);
                    $expenseTrendQuery->whereDate('expense_date', '<=', $endDate);
                }

                $expensesByBranch = $expensesByBranchQuery->groupBy('paid_by')->get();
                $expensesByType = $expensesByTypeQuery->groupBy('expense_type')->get();
                
                $expenseTrend = $expenseTrendQuery
                    ->where('expense_date', '>=', Carbon::now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('expense_date')
                    ->get();
            }

            // Branch Financial Health (using accounting data with fallback)
            if ($hasAccountingData) {
                $branchFinancialHealth = Branch::select('branches.name',
                        DB::raw('(SELECT COALESCE(SUM(ji.credit_amount - ji.debit_amount), 0) 
                                 FROM u244769603_esa.journal_items ji 
                                 JOIN u244769603_esa.journal_entries je ON ji.journal_entry_id = je.id 
                                 JOIN u244769603_esa.accounts a ON ji.account_id = a.id 
                                 WHERE a.account_type = "Revenue" AND je.status = "Posted" 
                                 AND je.description LIKE CONCAT("%", branches.name, "%")) as revenue'),
                        DB::raw('(SELECT COALESCE(SUM(ji.debit_amount - ji.credit_amount), 0) 
                                 FROM u244769603_esa.journal_items ji 
                                 JOIN u244769603_esa.journal_entries je ON ji.journal_entry_id = je.id 
                                 JOIN u244769603_esa.accounts a ON ji.account_id = a.id 
                                 WHERE a.account_type = "Expense" AND je.status = "Posted" 
                                 AND je.description LIKE CONCAT("%", branches.name, "%")) as expenses'))
                    ->get();
            } else {
                // Fallback to existing data models
                $branchFinancialHealth = Branch::select('branches.name',
                        DB::raw('(SELECT COALESCE(sum(ss.paid_amount), 0) FROM u244769603_esa.student_subscriptions ss JOIN u244769603_esa.students s ON ss.student_id = s.id WHERE s.branch_id = branches.id AND ss.payment_status = "paid" AND ss.is_active = 1) as revenue'),
                        DB::raw('(SELECT COALESCE(sum(e.amount), 0) FROM u244769603_esa.expenses e WHERE e.paid_by = branches.name) as expenses'))
                    ->get();
            }

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
            // Debug: Check expenseTrend data structure
            if ($expenseTrend && $expenseTrend->count() > 0) {
                \Log::info('expenseTrend type: ' . gettype($expenseTrend));
                \Log::info('expenseTrend first item: ' . print_r($expenseTrend->first(), true));
            } else {
                \Log::info('expenseTrend is empty or null');
            }
            
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
                'recentClients',
                'startDate',
                'endDate'
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
