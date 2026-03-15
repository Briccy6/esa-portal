<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\TeacherDaily;
use App\Models\Expense;
use App\Models\Attendance;
use App\Models\Lesson;
use App\Models\Term;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExpensesExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $adminCount = Admin::count();
        $staffCount = Staff::count();
        $teacherCount = Teacher::count();
        $studentCount = Student::count();
        $paymentTotal = StudentTransaction::sum('amount');

        // Load all terms with academic years for the filter
        $terms = Term::with('academicYear')->orderBy('start_date', 'desc')->get();

        // Student Reports
        $students = Student::with(['course', 'branch'])->latest()->take(10)->get();

        // Teacher Dailies
        $approvedDailies = TeacherDaily::where('approved', true)->count();
        $pendingDailies = TeacherDaily::where('approved', false)->count();

        $from = $request->input('from', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $to = $request->input('to', \Carbon\Carbon::now()->toDateString());

        // Get selected terms
        $selectedTerms = $request->input('terms', []);
        
        // Apply term filtering if terms are selected
        $termDateRange = null;
        if (!empty($selectedTerms)) {
            $termsData = Term::whereIn('id', $selectedTerms)->get();
            if ($termsData->isNotEmpty()) {
                $minStartDate = $termsData->min('start_date');
                $maxEndDate = $termsData->max('end_date');
                $termDateRange = [
                    'from' => $minStartDate,
                    'to' => $maxEndDate
                ];
            }
        }

        // Use term date range if available, otherwise use form dates
        $queryFrom = $termDateRange ? $termDateRange['from'] : $from;
        $queryTo = $termDateRange ? $termDateRange['to'] : $to;

        // Expenses filtered by date range or terms
        $expensesQuery = Expense::with(['paid_by_staff', 'paid_by_staff.branches']);
        if ($termDateRange) {
            $expensesQuery->whereBetween('expense_date', [$queryFrom, $queryTo]);
        } else {
            $expensesQuery->whereBetween('expense_date', [$queryFrom, $queryTo]);
        }
        $expenses = $expensesQuery->latest()->paginate(20);
        $expenseTotal = $expensesQuery->sum('amount');

        // Attendance by day or range
        $attendanceRecords = Attendance::whereBetween('date', [$queryFrom, $queryTo])->get();

        // Number of hours studied by day
        $hoursByDay = TeacherDaily::selectRaw('date, SUM(hours_studied) as total_hours')
            ->whereBetween('date', [$queryFrom, $queryTo])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Lessons and activities studied
        $lessonsStudied = TeacherDaily::with(['lesson:id,title', 'course:id,course_name'])
            ->whereBetween('date', [$queryFrom, $queryTo])
            ->get();

        // Transactions and Dailies for the given date range
        $transactions = StudentTransaction::with(['student'])
            ->whereBetween('created_at', [$queryFrom, $queryTo])
            ->latest()
            ->paginate(20);

        $dailies = TeacherDaily::with(['teacher', 'course'])
            ->whereBetween('date', [$queryFrom, $queryTo])
            ->latest()
            ->paginate(20);

        // Pass all variables to the view
        return view('admin.report', compact(
            'adminCount',
            'staffCount',
            'teacherCount',
            'studentCount',
            'paymentTotal',
            'students',
            'approvedDailies',
            'pendingDailies',
            'expenses',
            'expenseTotal',
            'attendanceRecords',
            'from',
            'to',
            'hoursByDay',
            'lessonsStudied',
            'transactions',
            'dailies',
            'terms'
        ));
    }

    public function exportExpenses(Request $request)
    {
        $from = $request->input('from', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $to = $request->input('to', \Carbon\Carbon::now()->toDateString());
        
        // Get selected terms
        $selectedTerms = $request->input('terms', []);
        
        // Apply term filtering if terms are selected
        if (!empty($selectedTerms)) {
            $termsData = Term::whereIn('id', $selectedTerms)->get();
            if ($termsData->isNotEmpty()) {
                $from = $termsData->min('start_date');
                $to = $termsData->max('end_date');
            }
        }
        
        return Excel::download(new ExpensesExport($from, $to), 'expenses-report.xlsx');
    }
}