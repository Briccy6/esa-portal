<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Database\Query\Builder;

class AccountingReport extends Model
{
    public static function getCashBookData($startDate = null, $endDate = null, $termId = null)
    {
        // Get student transactions (receipts)
        $studentTransactionsQuery = DB::table('student_transactions as st')
            ->join('students as s', 'st.student_id', '=', 's.id')
            ->leftJoin('terms as t', 'st.term_id', '=', 't.id')
            ->select([
                'st.id',
                'st.tx_ref',
                'st.paid_at as entry_date',
                'st.amount',
                DB::raw("'RWF' as currency"),
                'st.status',
                'st.paid_at',
                'st.term_id',
                't.name as term_name',
                DB::raw("CONCAT('Registration - ', s.full_name) as transaction_type"),
                DB::raw("'Cash Receipt' as entry_type"),
                DB::raw("CONCAT('Payment from student: ', s.full_name, ' (', st.tx_ref, ')') as description"),
                DB::raw("'+' as direction")
            ])
            ->whereIn('st.status', ['paid', 'successful'])
            ->orderBy('st.paid_at');

        // Get expenses (payments)
        $expensesQuery = DB::table('expenses as e')
            ->leftJoin('terms as t', 'e.term_id', '=', 't.id')
            ->select([
                'e.id',
                DB::raw("'EXP-' || e.id as tx_ref"),
                'e.expense_date as entry_date',
                'e.amount',
                DB::raw("'RWF' as currency"),
                DB::raw("'paid' as status"),
                'e.expense_date as paid_at',
                'e.term_id',
                't.name as term_name',
                'e.expense_type as transaction_type',
                DB::raw("'Cash Payment' as entry_type"),
                DB::raw("CONCAT('Payment to: ', e.paid_to, ' - ', e.description) as description"),
                DB::raw("'-' as direction")
            ])
            ->orderBy('e.expense_date');

        // Apply term filter
        if ($termId) {
            $studentTransactionsQuery->where('st.term_id', $termId);
            $expensesQuery->where('e.term_id', $termId);
        }

        // Apply date filters to student transactions
        if ($startDate) {
            $studentTransactionsQuery->whereDate('st.paid_at', '>=', $startDate);
        }
        if ($endDate) {
            $studentTransactionsQuery->whereDate('st.paid_at', '<=', $endDate);
        }

        // Apply date filters to expenses
        if ($startDate) {
            $expensesQuery->whereDate('e.expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $expensesQuery->whereDate('e.expense_date', '<=', $endDate);
        }

        // Get both sets of transactions
        $studentTransactions = $studentTransactionsQuery->get();
        $expenses = $expensesQuery->get();

        // Combine and sort all transactions by date
        $allTransactions = $studentTransactions->concat($expenses)->sortBy('entry_date');

        $runningBalance = 0;
        $transactionsWithBalance = collect();

        foreach ($allTransactions as $transaction) {
            if ($transaction->direction === '+') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            $transaction->running_balance = $runningBalance;
            $transactionsWithBalance->push($transaction);
        }

        $totalReceipts = $transactionsWithBalance->where('direction', '+')->sum('amount');
        $totalPayments = $transactionsWithBalance->where('direction', '-')->sum('amount');

        return [
            'transactions' => $transactionsWithBalance,
            'total_receipts' => $totalReceipts,
            'total_payments' => $totalPayments,
            'net_cash' => $runningBalance,
            'opening_balance' => self::getOpeningCashBalanceFromTransactions($startDate),
            'term_id' => $termId
        ];
    }

    private static function getOpeningCashBalanceFromTransactions($startDate)
    {
        if (!$startDate) return 0;

        // Get sum of student transactions before start date
        $studentTotal = DB::table('student_transactions')
            ->whereIn('status', ['paid', 'successful'])
            ->whereDate('paid_at', '<', $startDate)
            ->sum('amount') ?? 0;

        // Get sum of expenses before start date
        $expenseTotal = DB::table('expenses')
            ->whereDate('expense_date', '<', $startDate)
            ->sum('amount') ?? 0;

        return $studentTotal - $expenseTotal;
    }

    /**
     * Balance sheet from operational tables: student_transactions, expenses, students, courses
     * Uses course_term_fees for more accurate receivables calculation.
     */
    public static function getBalanceSheetData($asOfDate = null, $termId = null, $branchId = null)
    {
        $asOfDate = $asOfDate ?: now()->endOfDay();

        $totalReceipts = self::sumStudentReceiptsThrough($asOfDate, $termId, $branchId);
        $totalExpenses = self::sumExpensesThrough($asOfDate, $termId, $branchId);
        $cash = $totalReceipts - $totalExpenses;

        $feePosition = self::aggregateStudentFeePositionThrough($asOfDate, $termId, $branchId);
        $receivable = $feePosition['receivable'];
        $deferred = $feePosition['deferred'];

        $assets = $cash + $receivable;
        $liabilities = $deferred;
        $equity = $assets - $liabilities;

        // Build detailed asset lines
        $detailedAssets = [
            [
                'account_code' => 'CASH',
                'name' => 'Cash (Student receipts less expenses)',
                'balance' => (float) $cash,
            ],
        ];

        // Add student receivables
        if ($receivable > 0.005) {
            $detailedAssets[] = [
                'account_code' => 'AR-STU',
                'name' => 'Accounts Receivable - Student Fees',
                'balance' => (float) $receivable,
            ];
        }

        // Build detailed liability lines
        $detailedLiabilities = [];
        if ($deferred > 0.005) {
            $detailedLiabilities[] = [
                'account_code' => 'DEF',
                'name' => 'Student Fee Advances (Deferred Income)',
                'balance' => (float) $deferred,
            ];
        }

        // Build detailed equity lines
        $detailedEquity = [
            [
                'account_code' => 'EQUITY',
                'name' => 'Net Assets (Unrestricted)',
                'balance' => (float) $equity,
            ],
        ];

        $difference = (float) ($assets - ($liabilities + $equity));

        return [
            'as_of_date' => $asOfDate,
            'source' => 'student_transactions_expenses_course_term_fees',
            'assets' => [
                'total' => (float) $assets,
                'details' => $detailedAssets,
            ],
            'liabilities' => [
                'total' => (float) $liabilities,
                'details' => $detailedLiabilities,
            ],
            'equity' => [
                'total' => (float) $equity,
                'details' => $detailedEquity,
            ],
            'is_balanced' => abs($difference) < 0.02,
            'difference' => $difference,
            'meta' => [
                'total_student_receipts' => (float) $totalReceipts,
                'total_expenses' => (float) $totalExpenses,
                'source_description' => 'Built from: student_transactions (paid/successful), expenses, and course_term_fees for accurate fee positions.',
            ],
        ];
    }

    private static function applyStudentTransactionThroughDate(Builder $query, Carbon $asOfDate): void
    {
        $query->where(function ($q) use ($asOfDate) {
            $q->where(function ($q2) use ($asOfDate) {
                $q2->whereNotNull('paid_at')->whereDate('paid_at', '<=', $asOfDate);
            })->orWhere(function ($q2) use ($asOfDate) {
                $q2->whereNull('paid_at')->whereDate('created_at', '<=', $asOfDate);
            });
        });
    }

    private static function sumStudentReceiptsThrough(Carbon $asOfDate, $termId = null, $branchId = null): float
    {
        $q = DB::table('student_transactions')
            ->whereIn('status', ['paid', 'successful']);

        if ($termId) {
            $q->where('term_id', $termId);
        }

        if ($branchId) {
            $q->whereExists(function ($query) use ($branchId) {
                $query->select(DB::raw(1))
                    ->from('students')
                    ->whereColumn('student_transactions.student_id', 'students.id')
                    ->where('branch_id', $branchId);
            });
        }

        self::applyStudentTransactionThroughDate($q, $asOfDate);

        return (float) ($q->sum('amount') ?? 0);
    }

    private static function sumExpensesThrough(Carbon $asOfDate, $termId = null, $branchId = null): float
    {
        $q = DB::table('expenses')
            ->whereDate('expense_date', '<=', $asOfDate);

        if ($termId) {
            $q->where('term_id', $termId);
        }

        if ($branchId) {
            $q->where('branch_id', $branchId);
        }

        return (float) ($q->sum('amount') ?? 0);
    }

    /**
     * Get expected fees for student from course_term_fees table (most accurate source).
     * Falls back to course table if course_term_fees not set.
     */
    private static function expectedTotalFeesForStudent(Student $student, $termId = null): float
    {
        $course = $student->course;
        if (!$course) {
            return 0.0;
        }

        // Try to get fees from course_term_fees table first
        if ($termId) {
            $courseTermFee = DB::table('course_term_fees')
                ->where('course_id', $course->id)
                ->where('term_id', $termId)
                ->where('is_active', true)
                ->first();

            if ($courseTermFee) {
                $registrationFee = (float) ($courseTermFee->registration_fee ?? 0);
                $tripFee = (float) ($courseTermFee->trip_fee ?? 0);
                $tuitionFee = (float) ($courseTermFee->tuition_fee ?? 0);
                $practicalFee = (float) ($courseTermFee->practical_fee ?? 0);
                $studentCardFee = (float) ($courseTermFee->student_card_fee ?? 2000.0);
                
                $discount = (float) ($student->discount ?? 0);
                $discountableFees = $tripFee + $tuitionFee + $practicalFee;
                $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0.0;
                
                return $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);
            }
        }

        // Fallback to course table defaults
        $registrationFee = (float) ($course->registration_fee ?? 0);
        $tripFee = (float) ($course->trip_fee ?? 0);
        $tuitionFee = (float) ($course->tuition_fee ?? 0);
        $practicalFee = (float) ($course->practical_fee ?? $course->pratical_fee ?? 0);
        $studentCardFee = 2000.0;
        $discount = (float) ($student->discount ?? 0);
        $discountableFees = $tripFee + $tuitionFee + $practicalFee;
        $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0.0;

        return $registrationFee + $studentCardFee + ($discountableFees - $discountAmount);
    }

    private static function studentPaidThrough(Student $student, Carbon $asOfDate, $termId = null): float
    {
        $q = DB::table('student_transactions')
            ->where('student_id', $student->id)
            ->whereIn('status', ['paid', 'successful']);
            
        if ($termId) {
            $q->where('term_id', $termId);
        }
            
        self::applyStudentTransactionThroughDate($q, $asOfDate);

        return (float) ($q->sum('amount') ?? 0);
    }

    /**
     * @return array{receivable: float, deferred: float}
     */
    private static function aggregateStudentFeePositionThrough(Carbon $asOfDate, $termId = null, $branchId = null): array
    {
        $receivable = 0.0;
        $deferred = 0.0;

        $query = Student::query()->with('course');

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $query->chunkById(200, function ($students) use ($asOfDate, &$receivable, &$deferred, $termId) {
            foreach ($students as $student) {
                $expected = self::expectedTotalFeesForStudent($student, $termId);
                if ($expected <= 0) {
                    continue;
                }
                $paid = self::studentPaidThrough($student, $asOfDate, $termId);
                $diff = $expected - $paid;
                if ($diff > 0) {
                        $receivable += $diff;
                    } elseif ($diff < 0) {
                        $deferred += abs($diff);
                    }
                }
            });

        return ['receivable' => $receivable, 'deferred' => $deferred];
    }

    public static function getTrialBalanceData($asOfDate = null)
    {
        $asOfDate = $asOfDate ?: now()->endOfDay();

        // Get all account balances from journal entries
        $accounts = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereDate('je.entry_date', '<=', $asOfDate)
            ->where('a.is_active', true)
            ->select(
                'a.account_name',
                'a.account_type',
                'a.normal_balance',
                'a.account_code',
                DB::raw('SUM(ji.debit_amount) as total_debits'),
                DB::raw('SUM(ji.credit_amount) as total_credits'),
                DB::raw('SUM(ji.debit_amount - ji.credit_amount) as raw_balance')
            )
            ->groupBy('a.id', 'a.account_name', 'a.account_type', 'a.normal_balance', 'a.account_code')
            ->orderBy('a.account_code')
            ->get();

        $trialBalanceAccounts = [];
        $totalDebits = 0;
        $totalCredits = 0;

        foreach ($accounts as $account) {
            $debitBalance = 0;
            $creditBalance = 0;

            // Calculate final balance based on normal balance
            $finalBalance = $account->normal_balance === 'Debit' 
                ? $account->raw_balance 
                : -$account->raw_balance;

            if ($finalBalance > 0) {
                $debitBalance = $finalBalance;
            } elseif ($finalBalance < 0) {
                $creditBalance = abs($finalBalance);
            }

            // Skip zero balances
            if ($debitBalance == 0 && $creditBalance == 0) {
                continue;
            }

            $trialBalanceAccounts[] = [
                'account_code' => $account->account_code,
                'account_name' => $account->account_name,
                'account_type' => $account->account_type,
                'debit_balance' => $debitBalance,
                'credit_balance' => $creditBalance
            ];

            $totalDebits += $debitBalance;
            $totalCredits += $creditBalance;
        }

        return [
            'as_of_date' => $asOfDate,
            'accounts' => $trialBalanceAccounts,
            'total_debits' => $totalDebits,
            'total_credits' => $totalCredits,
            'difference' => abs($totalDebits - $totalCredits),
            'is_balanced' => abs($totalDebits - $totalCredits) < 0.01
        ];
    }

    /**
     * Monthly cash flow forecast-style grid from operational tables:
     * student_transactions (receipts) and expenses (payments).
     * Uses the same opening-cash logic as the cash book.
     */
    public static function getCashFlowForecastData(?int $year = null): array
    {
        $year = $year ?? (int) now()->year;

        $startOfYear = Carbon::create($year, 1, 1)->startOfDay();
        $openingBeforeYear = self::getOpeningCashBalanceFromTransactions($startOfYear);

        $txRows = DB::table('student_transactions')
            ->whereIn('status', ['paid', 'successful'])
            ->whereNotNull('paid_at')
            ->whereYear('paid_at', $year)
            ->select('paid_at', 'amount', 'type', 'currency')
            ->get();

        $expRows = DB::table('expenses')
            ->whereYear('expense_date', $year)
            ->select('expense_date', 'amount', 'expense_type', 'payment_method')
            ->get();

        $inTypes = $txRows->pluck('type')->unique()->filter()->sort()->values()->all();
        $expTypes = $expRows->pluck('expense_type')->unique()->filter()->sort()->values()->all();

        $months = [];
        $running = $openingBeforeYear;

        for ($m = 1; $m <= 12; $m++) {
            $cashIn = 0.0;
            $cashOut = 0.0;
            $inDetail = array_fill_keys($inTypes, 0.0);
            $outDetail = array_fill_keys($expTypes, 0.0);

            foreach ($txRows as $r) {
                if ((int) Carbon::parse($r->paid_at)->month === $m) {
                    $amt = (float) $r->amount;
                    $cashIn += $amt;
                    if (array_key_exists($r->type, $inDetail)) {
                        $inDetail[$r->type] += $amt;
                    }
                }
            }

            foreach ($expRows as $r) {
                if ((int) Carbon::parse($r->expense_date)->month === $m) {
                    $amt = (float) $r->amount;
                    $cashOut += $amt;
                    if (array_key_exists($r->expense_type, $outDetail)) {
                        $outDetail[$r->expense_type] += $amt;
                    }
                }
            }

            $opening = $running;
            $net = $cashIn - $cashOut;
            $closing = $opening + $net;
            $running = $closing;

            $months[] = [
                'month' => $m,
                'label' => Carbon::create($year, $m, 1)->format('M Y'),
                'opening' => $opening,
                'cash_in' => $cashIn,
                'cash_out' => $cashOut,
                'net' => $net,
                'closing' => $closing,
                'cash_in_by_type' => $inDetail,
                'cash_out_by_expense_type' => $outDetail,
            ];
        }

        $yearEndClosing = $running;

        return [
            'year' => $year,
            'opening_before_year' => $openingBeforeYear,
            'year_end_closing' => $yearEndClosing,
            'months' => $months,
            'in_types' => $inTypes,
            'expense_types' => $expTypes,
            'totals' => [
                'cash_in' => collect($months)->sum('cash_in'),
                'cash_out' => collect($months)->sum('cash_out'),
                'net' => collect($months)->sum('net'),
            ],
            'currency' => 'RWF',
        ];
    }

    public static function getCashFlowStatementData($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->startOfYear();
        $endDate = $endDate ?: now()->endOfDay();

        // Get cash account movements
        $cashMovements = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->where('a.is_cash_account', true)
            ->whereDate('je.entry_date', '>=', $startDate)
            ->whereDate('je.entry_date', '<=', $endDate)
            ->select(
                'je.entry_date',
                'je.description',
                'ji.debit_amount',
                'ji.credit_amount',
                'a.cash_flow_category'
            )
            ->orderBy('je.entry_date')
            ->get();

        // Categorize cash flows
        $operatingActivities = 0;
        $investingActivities = 0;
        $financingActivities = 0;
        $nonCashActivities = [];

        foreach ($cashMovements as $movement) {
            $netAmount = $movement->debit_amount - $movement->credit_amount;
            
            if ($movement->cash_flow_category === 'Operating') {
                $operatingActivities += $netAmount;
            } elseif ($movement->cash_flow_category === 'Investing') {
                $investingActivities += $netAmount;
            } elseif ($movement->cash_flow_category === 'Financing') {
                $financingActivities += $netAmount;
            }
        }

        // Get opening cash balance
        $openingBalance = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->where('a.is_cash_account', true)
            ->whereDate('je.entry_date', '<', $startDate)
            ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as balance')
            ->value('balance') ?? 0;

        $netCashFlow = $operatingActivities + $investingActivities + $financingActivities;
        $closingBalance = $openingBalance + $netCashFlow;

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'cash_flows' => [
                'operating_activities' => $operatingActivities,
                'investing_activities' => $investingActivities,
                'financing_activities' => $financingActivities,
                'net_cash_flow' => $netCashFlow
            ],
            'cash_balances' => [
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'net_change' => $closingBalance - $openingBalance
            ],
            'detailed_movements' => $cashMovements
        ];
    }

    public static function getIncomeStatementData($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: now()->startOfYear();
        $endDate = $endDate ?: now()->endOfDay();

        // Revenue accounts
        $revenues = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereBetween('je.entry_date', [$startDate, $endDate])
            ->where('a.account_type', 'Revenue')
            ->select(
                'a.account_name',
                'a.account_code',
                DB::raw('SUM(ji.credit_amount - ji.debit_amount) as amount')
            )
            ->groupBy('a.id', 'a.account_name', 'a.account_code')
            ->get();

        // Expense accounts
        $expenses = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereBetween('je.entry_date', [$startDate, $endDate])
            ->where('a.account_type', 'Expense')
            ->select(
                'a.account_name',
                'a.account_code',
                DB::raw('SUM(ji.debit_amount - ji.credit_amount) as amount')
            )
            ->groupBy('a.id', 'a.account_name', 'a.account_code')
            ->get();

        $totalRevenue = $revenues->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netIncome = $totalRevenue - $totalExpenses;

        return [
            'period_start' => $startDate,
            'period_end' => $endDate,
            'revenues' => $revenues,
            'expenses' => $expenses,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_income' => $netIncome,
        ];
    }

    public static function getGeneralLedgerData($accountId, $startDate = null, $endDate = null)
    {
        if (!$accountId) {
            return null;
        }

        $account = DB::table('accounts')->where('id', $accountId)->first();

        if (!$account) {
            return null;
        }

        $startDate = $startDate ?: now()->startOfYear();
        $endDate = $endDate ?: now()->endOfDay();

        // Opening balance before period
        $rawOpening = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.status', 'Posted')
            ->where('ji.account_id', $accountId)
            ->whereDate('je.entry_date', '<', $startDate)
            ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as balance')
            ->value('balance') ?? 0;

        $openingBalance = $account->normal_balance === 'Credit'
            ? -$rawOpening
            : $rawOpening;

        // Movements within period
        $transactions = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->where('je.status', 'Posted')
            ->where('ji.account_id', $accountId)
            ->whereBetween('je.entry_date', [$startDate, $endDate])
            ->select(
                'je.entry_date',
                'je.reference_number',
                'je.description as entry_description',
                'ji.debit_amount',
                'ji.credit_amount'
            )
            ->orderBy('je.entry_date')
            ->orderBy('je.id')
            ->get();

        $runningBalance = $openingBalance;
        $transactionsWithBalance = collect();

        foreach ($transactions as $transaction) {
            $netMovement = $transaction->debit_amount - $transaction->credit_amount;

            if ($account->normal_balance === 'Credit') {
                $runningBalance -= $netMovement;
            } else {
                $runningBalance += $netMovement;
            }

            $transaction->running_balance = $runningBalance;
            $transactionsWithBalance->push($transaction);
        }

        return [
            'account' => $account,
            'period_start' => $startDate,
            'period_end' => $endDate,
            'opening_balance' => $openingBalance,
            'closing_balance' => $runningBalance,
            'transactions' => $transactionsWithBalance,
        ];
    }
}
