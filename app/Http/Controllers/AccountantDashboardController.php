<?php

namespace App\Http\Controllers;

use App\Services\AccountantDashboardService;
use Illuminate\Http\Request;
use App\Services\AcademicYearContext;

class AccountantDashboardController extends Controller
{
    protected $service;

    /**
     * Display the accountant dashboard
     */
    public function index(Request $request)
    {
        $filters = [
            'academic_year_id' => $request->get('academic_year_id') ?? AcademicYearContext::selectedId(),
            'branch_id' => $request->get('branch_id'),
            'term_id' => $request->get('term_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'staff_id' => auth()->guard('staff')->id(),
        ];

        $this->service = new AccountantDashboardService($filters);

        $kpiData = $this->service->getKpiData();
        $chartData = $this->service->getChartData();
        $recentPayments = $this->service->getRecentPayments(10);
        $topOutstanding = $this->service->getTopOutstandingBalances(10);
        $recentExpenses = $this->service->getRecentExpenses(10);

        // Get filter options
        $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
        $branches = auth()->guard('staff')->user()->branches;
        $terms = \App\Models\Term::with('academicYear')->orderBy('start_date', 'desc')->get();
        $selectedAcademicYear = AcademicYearContext::selected();

        // Student statistics data
        $selectedAcademicYear = AcademicYearContext::selected();
        $academicYearId = $selectedAcademicYear?->id;

        // Students by shift in each branch
        $studentsByShiftInBranch = \App\Models\Student::select('branches.name as branch_name', 'shift', \DB::raw('count(*) as total'))
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->groupBy('branches.id', 'branches.name', 'shift')
            ->orderBy('branches.name')
            ->orderBy('shift')
            ->get();

        // Students with completed profiles for each branch
        $studentsWithCompletedProfileByBranch = \App\Models\Student::select('branches.name as branch_name', \DB::raw('count(*) as total'))
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->whereNotNull('email')
            ->whereNotNull('phone')
            ->whereNotNull('sex')
            ->whereNotNull('identification_type')
            ->whereNotNull('id_number')
            ->where('email', '!=', '')
            ->where('phone', '!=', '')
            ->where('sex', '!=', '')
            ->where('identification_type', '!=', '')
            ->where('id_number', '!=', '')
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get();

        // Students by branch for calculations
        $studentsByBranch = \App\Models\Student::select('branches.name as branch_name', \DB::raw('count(*) as total'))
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get();

        // Profile completion by course and shift
        $profileCompletionByCourseShift = \App\Models\Student::select(
                'courses.course_name as course_name',
                'shift',
                \DB::raw('count(*) as total'),
                \DB::raw('SUM(CASE WHEN email IS NOT NULL AND email != "" AND phone IS NOT NULL AND phone != "" AND sex IS NOT NULL AND sex != "" AND identification_type IS NOT NULL AND identification_type != "" AND id_number IS NOT NULL AND id_number != "" THEN 1 ELSE 0 END) as completed')
            )
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->when($academicYearId, fn($q) => $q->where('students.academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('students.branch_id', $filters['branch_id']))
            ->groupBy('courses.id', 'courses.course_name', 'shift')
            ->orderBy('courses.course_name')
            ->orderBy('shift')
            ->get();

        // Students who fully paid their fees
        $fullyPaidStudents = \App\Models\Student::whereHas('transactions', function($query) use ($academicYearId) {
            $query->where('status', 'successful');
        })
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->count();

        // Students who did not pay anything
        $studentsWithNoPayment = \App\Models\Student::whereDoesntHave('transactions', function($query) use ($academicYearId) {
            $query->where('status', 'successful');
        })
            ->when($academicYearId, fn($q) => $q->where('academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('branch_id', $filters['branch_id']))
            ->count();

        // Students by Course in Branch
        $studentsByCourseInBranch = \App\Models\Student::select(
                'branches.name as branch_name',
                'courses.course_name as course_name',
                \DB::raw('count(*) as total')
            )
            ->join('branches', 'students.branch_id', '=', 'branches.id')
            ->join('courses', 'students.course_id', '=', 'courses.id')
            ->when($academicYearId, fn($q) => $q->where('students.academic_year_id', $academicYearId))
            ->when($filters['branch_id'], fn($q) => $q->where('students.branch_id', $filters['branch_id']))
            ->groupBy('branches.id', 'branches.name', 'courses.id', 'courses.course_name')
            ->orderBy('branches.name')
            ->orderBy('total', 'desc')
            ->get();

        // Expenses by Branch
        $expensesByBranch = \App\Models\Expense::select('branches.name as branch_name', \DB::raw('sum(expenses.amount) as total'))
            ->leftJoin('branches', 'expenses.paid_by', '=', 'branches.id')
            ->when($academicYearId, function($q) use ($academicYearId) {
                $q->where(function($query) use ($academicYearId) {
                    $query->whereHas('term', function($termQuery) use ($academicYearId) {
                        $termQuery->where('academic_year_id', $academicYearId);
                    })->orWhereBetween('expense_date', [
                        \Carbon\Carbon::createFromDate(intval($academicYearId) - 1, 8, 1)->format('Y-m-d'),
                        \Carbon\Carbon::createFromDate(intval($academicYearId) + 1, 7, 31)->format('Y-m-d')
                    ]);
                });
            })
            ->when($filters['branch_id'], fn($q) => $q->where('paid_by', $filters['branch_id']))
            ->groupBy('branches.id', 'branches.name')
            ->orderBy('total', 'desc')
            ->get();

        return view('accountant.dashboard', compact(
            'kpiData',
            'chartData',
            'recentPayments',
            'topOutstanding',
            'recentExpenses',
            'academicYears',
            'branches',
            'terms',
            'selectedAcademicYear',
            'filters',
            'studentsByShiftInBranch',
            'studentsWithCompletedProfileByBranch',
            'studentsByBranch',
            'profileCompletionByCourseShift',
            'fullyPaidStudents',
            'studentsWithNoPayment',
            'studentsByCourseInBranch',
            'expensesByBranch'
        ));
    }

    /**
     * JSON endpoint for chart data (AJAX updates)
     */
    public function chartData(Request $request)
    {
        $filters = [
            'academic_year_id' => $request->get('academic_year_id'),
            'branch_id' => $request->get('branch_id'),
            'term_id' => $request->get('term_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'staff_id' => auth()->guard('staff')->id(),
        ];

        $service = new AccountantDashboardService($filters);
        $data = $service->getChartData();

        return response()->json($data);
    }

    /**
     * JSON endpoint for KPI data (AJAX updates)
     */
    public function kpiData(Request $request)
    {
        $filters = [
            'academic_year_id' => $request->get('academic_year_id'),
            'branch_id' => $request->get('branch_id'),
            'term_id' => $request->get('term_id'),
            'start_date' => $request->get('start_date'),
            'end_date' => $request->get('end_date'),
            'staff_id' => auth()->guard('staff')->id(),
        ];

        $service = new AccountantDashboardService($filters);
        $data = $service->getKpiData();

        return response()->json($data);
    }

    /**
     * Display receipts list
     */
    public function receipts(Request $request)
    {
        $query = \App\Models\StudentTransaction::with(['student', 'student.course', 'student.branch']);

        // Filter by student
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('accountant.receipts.index', compact('transactions'));
    }

    /**
     * Show receipt details
     */
    public function receiptShow($transactionId)
    {
        $transaction = \App\Models\StudentTransaction::with(['student', 'student.course', 'student.branch', 'term'])
            ->findOrFail($transactionId);

        return view('accountant.receipts.show', compact('transaction'));
    }

    /**
     * Print receipt as PDF
     */
    public function receiptPrint($transactionId)
    {
        $transaction = \App\Models\StudentTransaction::with(['student', 'student.course', 'student.branch', 'term'])
            ->findOrFail($transactionId);

        $pdf = \PDF::loadView('accountant.receipts.print', compact('transaction'))
            ->setPaper('a4')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm');

        return $pdf->download('receipt_' . $transaction->tx_ref . '.pdf');
    }

    /**
     * Filter students by payment percentage
     */
    public function filterByPaymentPercentage(Request $request)
    {
        $percentage = $request->input('percentage', 50);
        $branchId = $request->input('branch_id');
        $courseId = $request->input('course_id');

        $query = \App\Models\Student::with(['branch', 'course', 'transactions']);

        // Apply academic year filter
        $selectedAcademicYear = AcademicYearContext::selected();
        if ($selectedAcademicYear) {
            $query->where('academic_year_id', $selectedAcademicYear->id);
        }

        // Apply branch filter
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Apply course filter
        if ($courseId) {
            $query->where('course_id', $courseId);
        }

        $students = $query->get();

        $filteredStudents = collect();

        foreach ($students as $student) {
            // Get total paid amount for this academic year
            $paidAmount = $student->transactions()
                ->where('status', 'successful')
                ->whereHas('term', fn ($q) => $q->where('academic_year_id', $student->academic_year_id))
                ->sum('amount');

            // Get the student's total fees (from course term fees)
            $totalFees = 0;
            if ($student->course) {
                $currentTerm = \App\Models\Term::where('academic_year_id', $student->academic_year_id)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->first();

                if ($currentTerm) {
                    $courseTermFee = \App\Models\CourseTermFee::where('course_id', $student->course_id)
                        ->where('term_id', $currentTerm->id)
                        ->where('is_active', true)
                        ->first();

                    if ($courseTermFee) {
                        $totalFees = $courseTermFee->total_fees;
                    }
                }
            }

            // Calculate payment percentage
            // If totalFees is 0 but student has made payments, consider it as 100% (no expected fee set)
            // If totalFees is 0 and no payments, percentage is 0
            if ($totalFees > 0) {
                $paymentPercentage = ($paidAmount / $totalFees) * 100;
            } elseif ($paidAmount > 0) {
                // Student has made payments but no expected fee is set - assume they've paid what they owe
                $paymentPercentage = 100;
                $totalFees = $paidAmount; // Set total fees to paid amount for display
            } else {
                $paymentPercentage = 0;
            }

            // Check if student meets the threshold (greater than or equal to the specified percentage)
            if ($paymentPercentage >= $percentage) {
                $filteredStudents->push([
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'reg_number' => $student->reg_number,
                    'branch' => $student->branch->name ?? 'N/A',
                    'course' => $student->course->course_name ?? 'N/A',
                    'paid_amount' => $paidAmount,
                    'total_fees' => $totalFees,
                    'payment_percentage' => round($paymentPercentage, 2),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'count' => $filteredStudents->count(),
            'students' => $filteredStudents,
        ]);
    }
}
