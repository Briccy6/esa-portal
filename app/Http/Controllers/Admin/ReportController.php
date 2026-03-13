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

        // Student Reports
        $students = Student::with(['course', 'branch'])->latest()->take(10)->get();

        // Teacher Dailies
        $approvedDailies = TeacherDaily::where('approved', true)->count();
        $pendingDailies = TeacherDaily::where('approved', false)->count();

        $from = $request->input('from', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $to = $request->input('to', \Carbon\Carbon::now()->toDateString());

        // Expenses filtered by date range
        $expenses = Expense::with(['paid_by_staff', 'paid_by_staff.branches'])
            ->whereBetween('expense_date', [$from, $to])
            ->latest()
            ->paginate(20); // Use paginate, not get()
        $expenseTotal = Expense::whereBetween('expense_date', [$from, $to])->sum('amount');

        // Attendance by day or range
        $attendanceRecords = Attendance::whereBetween('date', [$from, $to])->get();

        // Number of hours studied by day
        $hoursByDay = TeacherDaily::selectRaw('date, SUM(hours_studied) as total_hours')
            ->whereBetween('date', [$from, $to])
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Lessons and activities studied
        $lessonsStudied = TeacherDaily::with(['lesson:id,title', 'course:id,course_name'])
            ->whereBetween('date', [$from, $to])
            ->get();

        // Transactions and Dailies for the given date range
        $transactions = StudentTransaction::with(['student'])
            ->whereBetween('created_at', [$from, $to])
            ->latest()
            ->paginate(20);

        $dailies = TeacherDaily::with(['teacher', 'course'])
            ->whereBetween('date', [$from, $to])
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
            'dailies'
        ));
    }

    public function exportExpenses(Request $request)
    {
        $from = $request->input('from', \Carbon\Carbon::now()->startOfMonth()->toDateString());
        $to = $request->input('to', \Carbon\Carbon::now()->toDateString());
        return Excel::download(new ExpensesExport($from, $to), 'expenses-report.xlsx');
    }
}