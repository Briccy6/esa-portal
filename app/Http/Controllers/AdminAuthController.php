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
use App\Services\AcademicYearContext;
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
            $selectedAcademicYear = AcademicYearContext::selected();
            $academicYearId = $selectedAcademicYear?->id;

            // Basic counts (fast queries)
            $totalStudents = AcademicYearContext::filterStudents(Student::query())->count();
            $pendingTransactions = AcademicYearContext::filterTransactions(
                StudentTransaction::where('status', 'pending')
            )->count();
            $totalTeachers = class_exists(Teacher::class) ? Teacher::count() : 0;
            $totalStaff = class_exists(Staff::class) ? Staff::count() : 0;
            
            // Calculate total revenue from successful student transactions for selected year
            $totalRevenue = AcademicYearContext::filterTransactions(
                StudentTransaction::where('status', 'successful')
            )->sum('amount') ?? 0;
            
            // Revenue breakdown by type for detailed analysis
            $revenueByType = AcademicYearContext::filterTransactions(
                StudentTransaction::where('status', 'successful')
            )
                ->select('type', DB::raw('sum(amount) as total'))
                ->groupBy('type')
                ->pluck('total', 'type')
                ->toArray();
            
            // Individual revenue types for dashboard cards
            $tokenRevenue = $revenueByType['token_purchase'] ?? 0;
            $liveClassRevenue = $revenueByType['live_class'] ?? 0;
            $resourceRevenue = $revenueByType['resource'] ?? 0;
            $subscriptionRevenue = $revenueByType['subscription'] ?? 0;
            $registrationRevenue = ($revenueByType['registration'] ?? 0) + ($revenueByType['registration_fee'] ?? 0);
            $schoolFeeRevenue = $revenueByType['school_fee'] ?? 0;
            $studentCardRevenue = $revenueByType['student_card'] ?? 0;
            $examRevenue = $revenueByType['exam_purchase'] ?? 0;
            
            // Calculate total expenses for selected academic year
            $totalExpenses = AcademicYearContext::filterExpenses(Expense::query())->sum('amount') ?? 0;
            
            $netProfit = $totalRevenue - $totalExpenses;
            $profitMargin = $totalRevenue > 0 ? round(($netProfit / $totalRevenue) * 100, 2) : 0;
            
            // Monthly breakdowns for transaction types in selected year
            $monthlyRevenueByType = AcademicYearContext::filterTransactions(
                StudentTransaction::where('status', 'successful')
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
            )
                ->select('type', DB::raw('sum(amount) as total'))
                ->groupBy('type')
                ->pluck('total', 'type')
                ->toArray();
            
            $monthlyTokenRevenue = $monthlyRevenueByType['token_purchase'] ?? 0;
            $monthlyLiveClassRevenue = $monthlyRevenueByType['live_class'] ?? 0;
            $monthlyResourceRevenue = $monthlyRevenueByType['resource'] ?? 0;
            $monthlySubscriptionRevenue = $monthlyRevenueByType['subscription'] ?? 0;
            $monthlyRegistrationRevenue = ($monthlyRevenueByType['registration'] ?? 0) + ($monthlyRevenueByType['registration_fee'] ?? 0);
            $monthlySchoolFeeRevenue = $monthlyRevenueByType['school_fee'] ?? 0;
            $monthlyStudentCardRevenue = $monthlyRevenueByType['student_card'] ?? 0;
            $monthlyExamRevenue = $monthlyRevenueByType['exam_purchase'] ?? 0;

            // Recent data for dashboard tables
            $recentStudents = AcademicYearContext::filterStudents(
                Student::with(['branch', 'course'])
            )->latest()->take(10)->get();
            $recentExpenses = AcademicYearContext::filterExpenses(
                Expense::with(['branch'])
            )->latest()->take(10)->get();

            // Client statistics
            $totalClients = Client::count();
            $activeClients = Client::where('is_active', true)->count();
            $inactiveClients = Client::where('is_active', false)->count();
            $totalClientTokens = ClientTransaction::where('type', 'purchase')->sum('total_tokens') ?? 0;
            $totalClientRevenue = ClientTransaction::where('type', 'purchase')->sum('amount') ?? 0;
            $recentClients = Client::with(['activeSubscription'])->latest()->take(5)->get();

            // NESA Registration Statistics
            $nesaRegistered = AcademicYearContext::filterStudents(
                Student::where('nesa_registered', true)
            )->count();
            $nesaNotRegistered = AcademicYearContext::filterStudents(
                Student::where('nesa_registered', false)
            )->count();
            
            // Student Approval Statistics (using registration fee payment as approval indicator)
            $approvedStudents = AcademicYearContext::filterStudents(
                Student::where('payed_registration_fees', true)
            )->count();
            $notApprovedStudents = AcademicYearContext::filterStudents(
                Student::where('payed_registration_fees', false)
            )->count();

            // Student demographics
            $studentsByGender = AcademicYearContext::filterStudents(Student::query())
                ->select('sex', DB::raw('count(*) as total'))
                ->groupBy('sex')
                ->get();

            $registrationPaidPercent = $totalStudents > 0
                ? round(($approvedStudents / $totalStudents) * 100, 1)
                : 0;

            $tuitionPaidPercent = 0; // No tuition fee tracking

            // Comprehensive fee payment data from student_transactions
            $feePaymentData = [
                'registration_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->whereIn('type', ['registration', 'registration_fee'])
                )->sum('amount'),
                'school_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'school_fee')
                )->sum('amount'),
                'subscription_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'subscription')
                )->sum('amount'),
                'student_card_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'student_card')
                )->sum('amount'),
                'exam_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'exam_purchase')
                )->sum('amount'),
                'token_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'token_purchase')
                )->sum('amount'),
                'live_class_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'live_class')
                )->sum('amount'),
                'resource_fees' => AcademicYearContext::filterTransactions(
                    StudentTransaction::where('status', 'successful')->where('type', 'resource')
                )->sum('amount'),
            ];

            // Expenses by branch and type
            $expensesByBranch = AcademicYearContext::filterExpenses(
                Expense::select('branches.name as branch', DB::raw('sum(expenses.amount) as total'))
                    ->leftJoin('branches', 'expenses.paid_by', '=', 'branches.id')
            )->groupBy('branches.name', 'expenses.paid_by')->get();
                
            $expensesByType = AcademicYearContext::filterExpenses(
                Expense::select('expense_type', DB::raw('sum(amount) as total'))
            )->groupBy('expense_type')->get();
            
            $expenseTrend = AcademicYearContext::filterExpenses(
                Expense::select(
                    DB::raw('DATE_FORMAT(expense_date, "%b %Y") as month'),
                    DB::raw('sum(amount) as amount'),
                    DB::raw('YEAR(expense_date) as year'),
                    DB::raw('MONTH(expense_date) as month_num')
                )->where('expense_date', '>=', Carbon::now()->subMonths(6))
            )->groupBy('year', 'month_num', 'month')
                ->orderBy('year')
                ->orderBy('month_num')
                ->get();

            $yearFilterSql = $academicYearId ? ' AND s.academic_year_id = ' . (int) $academicYearId : '';

            // Branch financial health with actual revenue from student transactions
            $branchFinancialHealth = Branch::select('branches.name', 
                    DB::raw('(SELECT COALESCE(sum(st.amount), 0) 
                              FROM student_transactions st 
                              JOIN students s ON st.student_id = s.id 
                              WHERE s.branch_id = branches.id AND st.status = "successful"' . $yearFilterSql . ') as revenue'),
                    DB::raw('(SELECT COALESCE(sum(expenses.amount), 0) FROM expenses WHERE expenses.paid_by = branches.id) as expenses'))
                ->get();

            // Tuition by branch with actual collected amounts
            $tuitionByBranch = Branch::select('name as branch', 
                    DB::raw('(SELECT COALESCE(sum(st.amount), 0) 
                              FROM student_transactions st 
                              JOIN students s ON st.student_id = s.id 
                              WHERE s.branch_id = branches.id AND st.status = "successful" 
                              AND st.type IN ("school_fee", "registration", "registration_fee")' . $yearFilterSql . ') as expected'))
                ->get();

            $tuitionPaidByBranch = Branch::select('name as branch',
                    DB::raw('(SELECT COALESCE(sum(st.amount), 0) 
                              FROM student_transactions st 
                              JOIN students s ON st.student_id = s.id 
                              WHERE s.branch_id = branches.id AND st.status = "successful"' . $yearFilterSql . ') as paid'))
                ->pluck('paid', 'branch');

            // Students by course and gender (with error handling)
            $studentsByCourseGender = collect();
            try {
                $studentsByCourseGender = AcademicYearContext::filterStudents(
                    Student::select('sex', 'courses.course_name as course_name', 'courses.id as course_id', DB::raw('count(*) as total'))
                        ->join('courses', 'students.course_id', '=', 'courses.id')
                )->groupBy('sex', 'courses.id', 'courses.course_name')
                    ->get()
                    ->map(function($item) {
                        $item->course = (object)['name' => $item->course_name, 'id' => $item->course_id];
                        $item->sex = strtoupper(substr($item->sex, 0, 1));
                        return $item;
                    });
            } catch (\Exception $e) {
                $studentsByCourseGender = collect();
            }

            // Students by branch
            $studentsByBranch = AcademicYearContext::filterStudents(
                Student::select('branches.name as branch_name', DB::raw('count(*) as total'))
                    ->join('branches', 'students.branch_id', '=', 'branches.id')
            )->groupBy('branches.id', 'branches.name')
                ->orderBy('total', 'desc')
                ->get();

            // Students by course in each branch
            $studentsByCourseInBranch = AcademicYearContext::filterStudents(
                Student::select('branches.name as branch_name', 'courses.course_name as course_name', DB::raw('count(*) as total'))
                    ->join('branches', 'students.branch_id', '=', 'branches.id')
                    ->join('courses', 'students.course_id', '=', 'courses.id')
            )->groupBy('branches.id', 'branches.name', 'courses.id', 'courses.course_name')
                ->orderBy('branches.name')
                ->orderBy('total', 'desc')
                ->get();

            // Cache the results for 1 hour
            $dashboardData = compact(
                'selectedAcademicYear',
                'totalStudents',
                'pendingTransactions',
                'totalTeachers',
                'totalStaff',
                'tokenRevenue',
                'liveClassRevenue',
                'resourceRevenue',
                'subscriptionRevenue',
                'registrationRevenue',
                'schoolFeeRevenue',
                'studentCardRevenue',
                'examRevenue',
                'totalRevenue',
                'totalExpenses',
                'netProfit',
                'profitMargin',
                'recentStudents',
                'recentExpenses',
                'studentsByGender',
                'studentsByCourseGender',
                'studentsByBranch',
                'studentsByCourseInBranch',
                'registrationPaidPercent',
                'tuitionPaidPercent',
                'feePaymentData',
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
                'monthlyRegistrationRevenue',
                'monthlySchoolFeeRevenue',
                'monthlyStudentCardRevenue',
                'monthlyExamRevenue',
                'totalClients',
                'activeClients',
                'inactiveClients',
                'totalClientTokens',
                'totalClientRevenue',
                'recentClients',
                'nesaRegistered',
                'nesaNotRegistered',
                'approvedStudents',
                'notApprovedStudents'
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
