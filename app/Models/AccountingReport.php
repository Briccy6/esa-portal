<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AccountingReport extends Model
{
    public static function getCashBookData($startDate = null, $endDate = null)
    {
        $query = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->select([
                'je.id',
                'je.reference_number as tx_ref',
                'je.entry_date',
                DB::raw('CASE WHEN ji.debit_amount > 0 THEN ji.debit_amount ELSE ji.credit_amount END as amount'),
                DB::raw("'RWF' as currency"),
                'je.status',
                'je.entry_date as paid_at',
                'a.account_name as transaction_type',
                DB::raw("CASE WHEN ji.debit_amount > 0 THEN 'Cash Receipt' ELSE 'Cash Payment' END as entry_type"),
                'je.description',
                DB::raw("CASE WHEN ji.debit_amount > 0 THEN '+' ELSE '-' END as direction")
            ])
            ->where('je.status', 'Posted')
            ->where('a.is_cash_account', true)
            ->orderBy('je.entry_date');

        if ($startDate) {
            $query->whereDate('je.entry_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('je.entry_date', '<=', $endDate);
        }

        $transactions = $query->get();

        $runningBalance = 0;
        $transactionsWithBalance = collect();

        foreach ($transactions as $transaction) {
            if ($transaction->direction === '+') {
                $runningBalance += $transaction->amount;
            } else {
                $runningBalance -= $transaction->amount;
            }
            $transaction->running_balance = $runningBalance;
            $transactionsWithBalance->push($transaction);
        }

        $totalReceipts = $transactions->where('direction', '+')->sum('amount');
        $totalPayments = $transactions->where('direction', '-')->sum('amount');

        return [
            'transactions' => $transactionsWithBalance,
            'total_receipts' => $totalReceipts,
            'total_payments' => $totalPayments,
            'net_cash' => $runningBalance,
            'opening_balance' => self::getOpeningCashBalance($startDate)
        ];
    }

    private static function getOpeningCashBalance($startDate)
    {
        if (!$startDate) return 0;

        return DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->where('a.is_cash_account', true)
            ->whereDate('je.entry_date', '<', $startDate)
            ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as balance')
            ->value('balance') ?? 0;
    }

    public static function getBalanceSheetData($asOfDate = null)
    {
        $asOfDate = $asOfDate ?: now()->endOfDay();

        // Get account balances as of date
        $accountBalances = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereDate('je.entry_date', '<=', $asOfDate)
            ->where('a.is_active', true)
            ->select(
                'a.account_name',
                'a.account_type',
                'a.normal_balance',
                DB::raw('SUM(ji.debit_amount - ji.credit_amount) as raw_balance')
            )
            ->groupBy('a.id', 'a.account_name', 'a.account_type', 'a.normal_balance')
            ->get();

        // Calculate final balances based on normal balance
        $assets = 0;
        $liabilities = 0;
        $equity = 0;
        $detailedAssets = [];
        $detailedLiabilities = [];
        $detailedEquity = [];

        foreach ($accountBalances as $balance) {
            $finalBalance = $balance->normal_balance === 'Debit' 
                ? $balance->raw_balance 
                : -$balance->raw_balance;

            if ($finalBalance == 0) continue;

            switch ($balance->account_type) {
                case 'Asset':
                    $assets += $finalBalance;
                    $detailedAssets[] = [
                        'name' => $balance->account_name,
                        'balance' => $finalBalance
                    ];
                    break;
                case 'Liability':
                    $liabilities += $finalBalance;
                    $detailedLiabilities[] = [
                        'name' => $balance->account_name,
                        'balance' => $finalBalance
                    ];
                    break;
                case 'Equity':
                    $equity += $finalBalance;
                    $detailedEquity[] = [
                        'name' => $balance->account_name,
                        'balance' => $finalBalance
                    ];
                    break;
            }
        }

        // Calculate retained earnings (Revenue - Expenses)
        $retainedEarnings = self::calculateRetainedEarnings($asOfDate);
        $equity += $retainedEarnings;

        $detailedEquity[] = [
            'name' => 'Retained Earnings',
            'balance' => $retainedEarnings
        ];

        return [
            'as_of_date' => $asOfDate,
            'assets' => [
                'total' => $assets,
                'details' => $detailedAssets
            ],
            'liabilities' => [
                'total' => $liabilities,
                'details' => $detailedLiabilities
            ],
            'equity' => [
                'total' => $equity,
                'details' => $detailedEquity
            ],
            'is_balanced' => abs($assets - ($liabilities + $equity)) < 0.01
        ];
    }

    private static function calculateRetainedEarnings($asOfDate)
    {
        $revenue = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereDate('je.entry_date', '<=', $asOfDate)
            ->where('a.account_type', 'Revenue')
            ->selectRaw('SUM(ji.credit_amount - ji.debit_amount) as amount')
            ->value('amount') ?? 0;

        $expenses = DB::table('journal_items as ji')
            ->join('journal_entries as je', 'ji.journal_entry_id', '=', 'je.id')
            ->join('accounts as a', 'ji.account_id', '=', 'a.id')
            ->where('je.status', 'Posted')
            ->whereDate('je.entry_date', '<=', $asOfDate)
            ->where('a.account_type', 'Expense')
            ->selectRaw('SUM(ji.debit_amount - ji.credit_amount) as amount')
            ->value('amount') ?? 0;

        return $revenue - $expenses;
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
}
