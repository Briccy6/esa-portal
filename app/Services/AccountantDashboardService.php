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

            $totalStudents = $studentQuery->count();
            
            // Get total expected fees
            $totalExpected = $this->calculateTotalExpectedFees($studentQuery);
            
            // Get total collected
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
            
            // Payments today
            $paymentsToday = $transactionQuery
                ->whereDate('created_at', Carbon::today())
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->sum('amount');
            
            // Students with arrears
            $studentsWithArrears = $this->getStudentsWithArrears($studentQuery);

            // Previous period data for trends
            $previousPeriodData = $this->getPreviousPeriodData();

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
                'previous_period' => $previousPeriodData,
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
                ->with(['student', 'student.course', 'student.branch'])
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'student_name' => $transaction->student->full_name ?? 'Unknown',
                        'student_reg_number' => $transaction->student->reg_number ?? 'N/A',
                        'course' => $transaction->student->course->course_name ?? 'N/A',
                        'amount' => $transaction->amount,
                        'date' => $transaction->created_at->format('M d, Y H:i'),
                        'method' => $this->getPaymentMethod($transaction),
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
            
            return $studentQuery
                ->with(['course', 'branch'])
                ->get()
                ->map(function ($student) {
                    $paid = StudentTransaction::where('student_id', $student->id)
                        ->where(function($q) {
                            $q->where('status', 'successful')->orWhere('status', 'paid');
                        })
                        ->sum('amount');
                    
                    $expected = $this->getStudentExpectedFees($student);
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
                ->with(['paid_by_staff', 'term'])
                ->orderBy('expense_date', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($expense) {
                    return [
                        'id' => $expense->id,
                        'date' => $expense->expense_date->format('M d, Y'),
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
     * Calculate total expected fees for students
     */
    protected function calculateTotalExpectedFees($studentQuery)
    {
        $total = 0;
        $students = $studentQuery->with(['course'])->get();
        
        foreach ($students as $student) {
            $total += $this->getStudentExpectedFees($student);
        }
        
        return $total;
    }

    /**
     * Get expected fees for a single student
     */
    protected function getStudentExpectedFees($student)
    {
        $expected = 0;
        
        if ($student->course) {
            $expected += $student->course->registration_fee ?? 0;
            $expected += $student->course->tuition_fee ?? 0;
            $expected += $student->course->practical_fee ?? 0;
            $expected += $student->course->trip_fee ?? 0;
            $expected += $student->course->student_card_fee ?? 0;
        }
        
        return $expected;
    }

    /**
     * Get students with arrears
     */
    protected function getStudentsWithArrears($studentQuery)
    {
        $count = 0;
        $students = $studentQuery->get();
        
        foreach ($students as $student) {
            $paid = StudentTransaction::where('student_id', $student->id)
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->sum('amount');
            
            $expected = $this->getStudentExpectedFees($student);
            
            if ($paid < $expected) {
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get previous period data for trend calculation
     */
    protected function getPreviousPeriodData()
    {
        $previousStart = $this->startDate ? $this->startDate->copy()->subMonth() : Carbon::now()->subMonth();
        $previousEnd = $this->endDate ? $this->endDate->copy()->subMonth() : Carbon::now();
        
        $previousCollected = StudentTransaction::whereBetween('created_at', [$previousStart, $previousEnd])
            ->where(function($q) {
                $q->where('status', 'successful')->orWhere('status', 'paid');
            })
            ->sum('amount');
        
        $previousExpenses = Expense::whereBetween('expense_date', [$previousStart, $previousEnd])
            ->sum('amount');
        
        return [
            'fees_collected' => $previousCollected,
            'expenses' => $previousExpenses,
        ];
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
            
            $fees = StudentTransaction::whereBetween('created_at', [$monthStart, $monthEnd])
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->sum('amount');
            
            $expenses = Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
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
        $students = $studentQuery->get();
        
        $paid = 0;
        $partial = 0;
        $unpaid = 0;
        
        foreach ($students as $student) {
            $paidAmount = StudentTransaction::where('student_id', $student->id)
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->sum('amount');
            
            $expected = $this->getStudentExpectedFees($student);
            
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
        $branches = Branch::all();
        $data = [];
        
        foreach ($branches as $branch) {
            $query = StudentTransaction::whereHas('student', function($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            });
            
            if ($this->academicYearId) {
                $query->whereHas('student', function($q) {
                    $q->where('academic_year_id', $this->academicYearId);
                });
            }
            
            if ($this->startDate) {
                $query->where('created_at', '>=', $this->startDate);
            }
            
            if ($this->endDate) {
                $query->where('created_at', '<=', $this->endDate);
            }
            
            $total = $query->where(function($q) {
                $q->where('status', 'successful')->orWhere('status', 'paid');
            })->sum('amount');
            
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
        
        for ($i = 0; $i < 30; $i++) {
            $dayStart = $startDate->copy()->addDays($i);
            $dayEnd = $dayStart->copy()->endOfDay();
            
            $total = StudentTransaction::whereBetween('created_at', [$dayStart, $dayEnd])
                ->where(function($q) {
                    $q->where('status', 'successful')->orWhere('status', 'paid');
                })
                ->sum('amount');
            
            $days[] = [
                'date' => $dayStart->format('M d'),
                'amount' => $total,
            ];
        }
        
        return $days;
    }

    /**
     * Get payment method from transaction
     */
    protected function getPaymentMethod($transaction)
    {
        $meta = is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true);
        return $meta['method'] ?? 'Unknown';
    }
}
