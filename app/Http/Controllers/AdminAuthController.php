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
    DB::enableQueryLog();

    try {
        // Date filters
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        // Basic counts
        $totalStudents = Student::count();
        $pendingTransactions = StudentTransaction::where('status', 'pending')->count();
        $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
        $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;

        // Check if accounting system has data
        $hasAccountingData = DB::table('journal_entries')->count() > 0;

        if ($hasAccountingData) {
            // Revenue and expenses via accounting
            $revenueData = DB::table('journal_items as ji')
                ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                ->where('je.status', 'Posted')
                ->where('a.account_type', 'Revenue')
                ->when($startDate, fn($q) => $q->whereDate('je.entry_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('je.entry_date', '<=', $endDate))
                ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as total_revenue')
                ->value('total_revenue') ?? 0;

            $totalExpenses = DB::table('journal_items as ji')
                ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
                ->join('accounts as a', 'ji.account_id', '=', 'a.id')
                ->where('je.status', 'Posted')
                ->where('a.account_type', 'Expense')
                ->when($startDate, fn($q) => $q->whereDate('je.entry_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('je.entry_date', '<=', $endDate))
                ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as total_expenses')
                ->value('total_expenses') ?? 0;

            // Branch financial health via accounting
            $branchFinancialHealth = Branch::select('branches.name',
                    DB::raw('(SELECT COALESCE(SUM(ji.credit_amount - ji.debit_amount), 0) 
                             FROM journal_items ji 
                             JOIN journal_entries je ON ji.journal_entry_id = je.id 
                             JOIN accounts a ON ji.account_id = a.id 
                             WHERE a.account_type = "Revenue" AND je.status = "Posted" 
                             AND je.description LIKE CONCAT("%", branches.name, "%")) as revenue'),
                    DB::raw('(SELECT COALESCE(SUM(ji.debit_amount - ji.credit_amount), 0) 
                             FROM journal_items ji 
                             JOIN journal_entries je ON ji.journal_entry_id = je.id 
                             JOIN accounts a ON ji.account_id = a.id 
                             WHERE a.account_type = "Expense" AND je.status = "Posted" 
                             AND je.description LIKE CONCAT("%", branches.name, "%")) as expenses'))
                ->get();
        } else {
            // Fallback: use tables without "esa." prefix
            $tokenRevenue = TokenTransaction::where('type', 'credit')->where('source', 'purchase')
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->sum('amount');

            $liveClassRevenue = LiveClassPayment::where('status', 'completed')
                ->when($startDate, fn($q) => $q->whereDate('paid_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('paid_at', '<=', $endDate))
                ->sum('amount');

            $resourceRevenue = Resource::where('is_free', false)->where('price', '>', 0)
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->sum('price');

            $subscriptionRevenue = StudentSubscription::where('payment_status', 'paid')->where('is_active', true)
                ->when($startDate, fn($q) => $q->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('created_at', '<=', $endDate))
                ->sum('paid_amount');

            $totalExpenses = Expense::when($startDate, fn($q) => $q->whereDate('expense_date', '>=', $startDate))
                ->when($endDate, fn($q) => $q->whereDate('expense_date', '<=', $endDate))
                ->sum('amount') ?? 0;

            $branchFinancialHealth = Branch::select('branches.name',
                    DB::raw('(SELECT COALESCE(sum(ss.paid_amount), 0) 
                             FROM student_subscriptions ss 
                             JOIN students s ON ss.student_id = s.id 
                             WHERE s.branch_id = branches.id AND ss.payment_status = "paid" AND ss.is_active = 1) as revenue'),
                    DB::raw('(SELECT COALESCE(sum(e.amount), 0) 
                             FROM expenses e WHERE e.paid_by = branches.name) as expenses'))
                ->get();
        }

        // Tuition by branch
        $tuitionByBranch = Branch::select('name as branch', 
                DB::raw('(SELECT COUNT(*) FROM students WHERE branch_id = branches.id) * 100000 as expected'))
            ->get();

        $tuitionPaidByBranch = collect();

        // Students by course and gender
        $studentsByCourseGender = Student::select('sex', 'courses.course_name as course_name', 'courses.id as course_id', DB::raw('count(*) as total'))
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->groupBy('sex', 'courses.id', 'courses.course_name')
            ->get()
            ->map(fn($item) => (object)[
                'course' => (object)['name' => $item->course_name, 'id' => $item->course_id],
                'sex' => strtoupper(substr($item->sex, 0, 1)),
                'total' => $item->total
            ]);

        // Dashboard data
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
            'branchFinancialHealth',
            'tuitionByBranch',
            'tuitionPaidByBranch',
            'studentsByCourseGender'
        );

        return view('admin.dashboard', $dashboardData);

    } catch (\Exception $e) {
        \Log::error('Dashboard Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString(), 'queries' => DB::getQueryLog()]);
        return view('admin.dashboard-error', ['error_message' => $e->getMessage()]);
    }
}
}
