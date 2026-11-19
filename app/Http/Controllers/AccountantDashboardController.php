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
            'filters'
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
            $query->where('created_at', '<=', $request->end_date);
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
        $transaction = \App\Models\StudentTransaction::with(['student', 'student.course', 'student.branch', 'courseTermFee', 'courseTermFee.term'])
            ->findOrFail($transactionId);

        return view('accountant.receipts.show', compact('transaction'));
    }

    /**
     * Print receipt as PDF
     */
    public function receiptPrint($transactionId)
    {
        $transaction = \App\Models\StudentTransaction::with(['student', 'student.course', 'student.branch', 'courseTermFee', 'courseTermFee.term'])
            ->findOrFail($transactionId);

        $pdf = \PDF::loadView('accountant.receipts.print', compact('transaction'))
            ->setPaper('a4')
            ->setOption('margin-top', '10mm')
            ->setOption('margin-bottom', '10mm')
            ->setOption('margin-left', '10mm')
            ->setOption('margin-right', '10mm');

        return $pdf->download('receipt_' . $transaction->tx_ref . '.pdf');
    }
}
