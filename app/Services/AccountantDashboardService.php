<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentTransaction;
use App\Models\Expense;
use App\Models\Branch;
use App\Models\Term;
use App\Models\AcademicYear;
use App\Services\AcademicYearContext;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class AccountantDashboardService
{
    protected $academicYearId;
    protected $branchId;
    protected $termId;
    protected $startDate;
    protected $endDate;
    protected $staffId;

    public function __construct($filters = [])
    {
        $this->academicYearId = $filters['academic_year_id'] ?? AcademicYearContext::selectedId();
        $this->branchId = $filters['branch_id'] ?? null;
        $this->termId = $filters['term_id'] ?? null;
        $this->startDate = $filters['start_date'] ? Carbon::parse($filters['start_date']) : null;
        $this->endDate = $filters['end_date'] ? Carbon::parse($filters['end_date']) : null;
        $this->staffId = $filters['staff_id'] ?? auth()->guard('staff')?->id();
    }

    /**
     * Get cache key for dashboard data
     */
    protected function getCacheKey($suffix)
    {
        return 'accountant_dashboard_' . $suffix . '_' . md5(json_encode([
            'academic_year_id' => $this->academicYearId,
            'branch_id' => $this->branchId,
            'term_id' => $this->termId,
            'start_date' => $this->startDate?->toDateString(),
            'end_date' => $this->endDate?->toDateString(),
            'staff_id' => $this->staffId,
        ]));
    }

    /**
     * Get all dashboard KPI data
     */
    public function getKpiData()
    {
        return Cache::remember($this->getCacheKey('kpi'), 300, function () {
            $studentQuery = $this->getStudentQuery();
            $transactionQuery = $this->getTransactionQuery();
            $expenseQuery = $this->getExpenseQuery();

            // Get students with their courses in a single query
            $students = $studentQuery->with(['course:id,course_name,registration_fee,tuition_fee,practical_fee,trip_fee,discount_fee'])
                ->get();

            $totalStudents = $students->count();

            // Calculate total expected fees from loaded students (no additional queries)
            $totalExpected = $students->sum(function ($student) {
                return ($student->course->registration_fee ?? 0)
                    + ($student->course->tuition_fee ?? 0)
                    + ($student->course->practical_fee ?? 0)
                    + ($student->course->trip_fee ?? 0)
                    + ($student->course->discount_fee ?? 0);
            });
            
            // Get total collected - use single query
            $totalCollected = $transactionQuery->where('status', 'successful')
                ->orWhere('status', 'paid')
                ->sum('amount');
            
            // Get outstanding balance
            $outstandingBalance = max(0, $totalExpected - $totalCollected);
            
            // Collection rate
            $collectionRate = $totalExpected > 0 ? ($totalCollected / $totalExpected) * 100 : 0;
            
            // Total expenses
            $totalExpenses = $expenseQuery->sum('amount');
            
            // Net balance
            $netBalance = $totalCollected - $totalExpenses;
            
            // Payments today - use single query
            $paymentsToday = StudentTransaction::whereIn('status', ['successful', 'paid'])
                ->whereDate('created_at', Carbon::today())
                ->when($this->academicYearId, fn($q) => $q->whereHas('student', fn($s) => $s->where('academic_year_id', $this->academicYearId)))
                ->when($this->branchId, fn($q) => $q->whereHas('student', fn($s) => $s->where('branch_id', $this->branchId)))
                ->sum('amount');
            
            // Students with arrears - use single aggregation query
            $studentIds = $students->pluck('id');
            $paidAmounts = StudentTransaction::whereIn('student_id', $studentIds)
                ->whereIn('status', ['successful', 'paid'])
                ->selectRaw('student_id, SUM(amount) as total_paid')
                ->groupBy('student_id')
                ->pluck('total_paid', 'student_id')
                ->toArray();

            $studentsWithArrears = 0;
            foreach ($students as $student) {
                $expected = ($student->course->registration_fee ?? 0)
                    + ($student->course->tuition_fee ?? 0)
                    + ($student->course->practical_fee ?? 0)
                    + ($student->course->trip_fee ?? 0)
                    + ($student->course->discount_fee ?? 0);
                $paid = $paidAmounts[$student->id] ?? 0;
                if ($paid < $expected) {
                    $studentsWithArrears++;
                }
            }

            // Previous period data for trends - use single query
            $previousStart = $this->startDate ? $this->startDate->copy()->subMonth() : Carbon::now()->subMonth();
            $previousEnd = $this->endDate ? $this->endDate->copy()->subMonth() : Carbon::now();
            
            $previousCollected = StudentTransaction::whereIn('status', ['successful', 'paid'])
                ->whereBetween('created_at', [$previousStart, $previousEnd])
                ->when($this->academicYearId, fn($q) => $q->whereHas('student', fn($s) => $s->where('academic_year_id', $this->academicYearId)))
                ->when($this->branchId, fn($q) => $q->whereHas('student', fn($s) => $s->where('branch_id', $this->branchId)))
                ->sum('amount');
            
            $previousExpenses = Expense::whereBetween('expense_date', [$previousStart, $previousEnd])
                ->when($this->staffId, fn($q) => $q->where('paid_by', $this->staffId))
                ->when($this->termId, fn($q) => $q->where('term_id', $this->termId))
                ->sum('amount');

            return [
                'total_fees_expected' => $totalExpected,
                'fees_collected' => $totalCollected,
                'outstanding_balance' => $outstandingBalance,
                'collection_rate' => $collectionRate,
                'total_expenses' => $totalExpenses,
                'net_balance' => $netBalance,
                'payments_today' => $paymentsToday,
                'students_with_arrears' => $studentsWithArrears,
                'total_students' => $totalStudents,
                'previous_period' => [
                    'fees_collected' => $previousCollected,
                    'expenses' => $previousExpenses,
                ],
            ];
        });
    }

    /**
     * Get chart data
     */
    public function getChartData()
    {
        return Cache::remember($this->getCacheKey('charts'), 300, function () {
            return [
                'fees_vs_expenses_by_month' => $this->getFeesVsExpensesByMonth(),
                'fee_status' => $this->getFeeStatusDistribution(),
                'collections_by_branch' => $this->getCollectionsByBranch(),
                'expenses_by_category' => $this->getExpensesByCategory(),
                'daily_collections' => $this->getDailyCollections(),
            ];
        });
    }

    /**
     * Get recent payments
     */
    public function getRecentPayments($limit = 10)
    {
        return Cache::remember($this->getCacheKey('recent_payments'), 300, function () use ($limit) {
            return $this->getTransactionQuery()
                ->with(['student:id,full_name,reg_number', 'student.course:id,course_name', 'student.branch:id,name'])
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($transaction) {
                    $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
                    return [
                        'id' => $transaction->id,
                        'student_name' => $transaction->student->full_name ?? 'Unknown',
                        'student_reg_number' => $transaction->student->reg_number ?? 'N/A',
                        'course' => $transaction->student->course->course_name ?? 'N/A',
                        'amount' => $transaction->amount,
                        'date' => $transaction->created_at->format('M d, Y H:i'),
                        'method' => $meta['method'] ?? 'Unknown',
                        'status' => $transaction->status,
                    ];
                });
        });
    }

    /**
     * Get top outstanding balances
     */
    public function getTopOutstandingBalances($limit = 10)
    {
        return Cache::remember($this->getCacheKey('outstanding'), 300, function () use ($limit) {
            $studentQuery = $this->getStudentQuery();
            
            // Get students with courses in single query
            $students = $studentQuery->with(['course:id,course_name,registration_fee,tuition_fee,practical_fee,trip_fee,discount_fee', 'branch:id,name'])
                ->get();
            
            // Get all payments in single query with aggregation
            $studentIds = $students->pluck('id');
            $paidAmounts = StudentTransaction::whereIn('student_id', $studentIds)
                ->whereIn('status', ['successful', 'paid'])
                ->selectRaw('student_id, SUM(amount) as total_paid')
                ->groupBy('student_id')
                ->pluck('total_paid', 'student_id')
                ->toArray();
            
            return $students->map(function ($student) use ($paidAmounts) {
                $paid = $paidAmounts[$student->id] ?? 0;
                $expected = ($student->course->registration_fee ?? 0)
                    + ($student->course->tuition_fee ?? 0)
                    + ($student->course->practical_fee ?? 0)
                    + ($student->course->trip_fee ?? 0)
                    + ($student->course->discount_fee ?? 0);
                $outstanding = max(0, $expected - $paid);
                
                return [
                    'student' => $student,
                    'paid' => $paid,
                    'expected' => $expected,
                    'outstanding' => $outstanding,
                ];
            })
            ->filter(fn($item) => $item['outstanding'] > 0)
            ->sortByDesc('outstanding')
            ->take($limit)
            ->values();
        });
    }

    /**
     * Get recent expenses
     */
    public function getRecentExpenses($limit = 10)
    {
        return Cache::remember($this->getCacheKey('recent_expenses'), 300, function () use ($limit) {
            return $this->getExpenseQuery()
                ->with(['paid_by_staff:id,name', 'term:id,name,academicYear:id,year'])
                ->orderBy('expense_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($expense) {
                    $expenseDate = is_string($expense->expense_date) 
                        ? Carbon::parse($expense->expense_date) 
                        : $expense->expense_date;
                    return [
                        'id' => $expense->id,
                        'date' => $expenseDate->format('M d, Y'),
                        'type' => $expense->expense_type,
                        'amount' => $expense->amount,
                        'description' => $expense->description,
                        'paid_to' => $expense->paid_to,
                        'paid_by' => $expense->paid_by_staff->name ?? 'Unknown',
                        'payment_method' => $expense->payment_method,
                    ];
                });
        });
    }

    /**
     * Get base student query with filters
     */
    protected function getStudentQuery()
    {
        $query = Student::query();

        if ($this->academicYearId) {
            $query->where('academic_year_id', $this->academicYearId);
        }

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        return $query;
    }

    /**
     * Get base transaction query with filters
     */
    protected function getTransactionQuery()
    {
        $query = StudentTransaction::query();

        if ($this->academicYearId) {
            $query->whereHas('student', function($q) {
                $q->where('academic_year_id', $this->academicYearId);
            });
        }

        if ($this->branchId) {
            $query->whereHas('student', function($q) {
                $q->where('branch_id', $this->branchId);
            });
        }

        if ($this->termId) {
            $query->where('term_id', $this->termId);
        }

        if ($this->startDate) {
            $query->where('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('created_at', '<=', $this->endDate);
        }

        return $query;
    }

    /**
     * Get base expense query with filters
     */
    protected function getExpenseQuery()
    {
        $query = Expense::query();

        if ($this->staffId) {
            $query->where('paid_by', $this->staffId);
        }

        if ($this->termId) {
            $query->where('term_id', $this->termId);
        }

        if ($this->startDate) {
            $query->where('expense_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('expense_date', '<=', $this->endDate);
        }

        return $query;
    }

    /**
     * Get fees vs expenses by month
     */
    protected function getFeesVsExpensesByMonth()
    {
        $months = [];
        $startDate = $this->startDate ?? Carbon::now()->subMonths(11);
        $endDate = $this->endDate ?? Carbon::now();

        for ($i = 0; $i < 12; $i++) {
            $monthStart = $startDate->copy()->addMonths($i)->startOfMonth();
            $monthEnd = $startDate->copy()->addMonths($i)->endOfMonth();

            if ($monthStart > $endDate) break;

            // Single query for fees
            $fees = StudentTransaction::whereIn('status', ['successful', 'paid'])
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->when($this->academicYearId, fn($q) => $q->whereHas('student', fn($s) => $s->where('academic_year_id', $this->academicYearId)))
                ->when($this->branchId, fn($q) => $q->whereHas('student', fn($s) => $s->where('branch_id', $this->branchId)))
                ->sum('amount');

            // Single query for expenses
            $expenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->when($this->staffId, fn($q) => $q->where('paid_by', $this->staffId))
                ->when($this->termId, fn($q) => $q->where('term_id', $this->termId))
                ->sum('amount');

            $months[] = [
                'month' => $monthStart->format('M Y'),
                'fees' => $fees,
                'expenses' => $expenses,
            ];
        }

        return $months;
    }

    /**
     * Get fee status distribution
     */
    protected function getFeeStatusDistribution()
    {
        $studentQuery = $this->getStudentQuery();

        // Get students with courses in single query
        $students = $studentQuery->with(['course:id,registration_fee,tuition_fee,practical_fee,trip_fee,discount_fee'])
            ->get();

        // Get all payments in single aggregation query
        $studentIds = $students->pluck('id');
        $paidAmounts = StudentTransaction::whereIn('student_id', $studentIds)
            ->whereIn('status', ['successful', 'paid'])
            ->selectRaw('student_id, SUM(amount) as total_paid')
            ->groupBy('student_id')
            ->pluck('total_paid', 'student_id')
            ->toArray();

        $paid = 0;
        $partial = 0;
        $unpaid = 0;

        foreach ($students as $student) {
            $paidAmount = $paidAmounts[$student->id] ?? 0;
            $expected = ($student->course->registration_fee ?? 0)
                + ($student->course->tuition_fee ?? 0)
                + ($student->course->practical_fee ?? 0)
                + ($student->course->trip_fee ?? 0)
                + ($student->course->discount_fee ?? 0);

            if ($expected <= 0) {
                continue;
            }

            if ($paidAmount >= $expected) {
                $paid++;
            } elseif ($paidAmount > 0) {
                $partial++;
            } else {
                $unpaid++;
            }
        }

        return [
            'paid' => $paid,
            'partial' => $partial,
            'unpaid' => $unpaid,
        ];
    }

    /**
     * Get collections by branch
     */
    protected function getCollectionsByBranch()
    {
        // Get branches with student counts in single query
        $branches = Branch::withCount(['students' => function($q) {
            $q->when($this->academicYearId, fn($s) => $s->where('academic_year_id', $this->academicYearId));
        }])->get();

        $data = [];

        foreach ($branches as $branch) {
            // Single query per branch for collections
            $total = StudentTransaction::whereHas('student', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
                if ($this->academicYearId) {
                    $q->where('academic_year_id', $this->academicYearId);
                }
            })
            ->whereBetween('created_at', [$this->startDate ?? Carbon::now()->subMonths(11), $this->endDate ?? Carbon::now()])
            ->where(function($q) {
                $q->where('status', 'successful')->orWhere('status', 'paid');
            })
            ->sum('amount');

            $data[] = [
                'branch' => $branch->name,
                'amount' => $total,
            ];
        }

        return $data;
    }

    /**
     * Get expenses by category
     */
    protected function getExpensesByCategory()
    {
        $expenses = $this->getExpenseQuery()
            ->selectRaw('expense_type, SUM(amount) as total')
            ->groupBy('expense_type')
            ->orderByDesc('total')
            ->get();

        return $expenses->map(function ($expense) {
            return [
                'category' => $expense->expense_type,
                'amount' => $expense->total,
            ];
        });
    }

    /**
     * Get daily collections for last 30 days
     */
    protected function getDailyCollections()
    {
        $days = [];
        $startDate = Carbon::now()->subDays(29)->startOfDay();

        // Get all data in single query with date grouping
        $collections = StudentTransaction::whereIn('status', ['successful', 'paid'])
            ->where('created_at', '>=', $startDate)
            ->when($this->academicYearId, fn($q) => $q->whereHas('student', fn($s) => $s->where('academic_year_id', $this->academicYearId)))
            ->when($this->branchId, fn($q) => $q->whereHas('student', fn($s) => $s->where('branch_id', $this->branchId)))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        for ($i = 0; $i < 30; $i++) {
            $dayStart = $startDate->copy()->addDays($i);
            $dateStr = $dayStart->toDateString();

            $days[] = [
                'date' => $dayStart->format('M d'),
                'amount' => $collections[$dateStr] ?? 0,
            ];
        }

        return $days;
    }
}
