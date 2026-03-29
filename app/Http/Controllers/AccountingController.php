<?php

namespace App\Http\Controllers;

use App\Models\AccountingReport;
use App\Models\Account;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AccountingController extends Controller
{
    public function __construct()
    {
        // Middleware is applied via routes, not in constructor
    }

    public function index()
    {
        // NESA Registration Statistics
        $nesaRegistered = Student::where('nesa_registered', true)->count();
        $nesaNotRegistered = Student::where('nesa_registered', false)->count();
        
        // Student Approval Statistics (using registration fee payment as approval indicator)
        $approvedStudents = Student::where('payed_registration_fees', true)->count();
        $notApprovedStudents = Student::where('payed_registration_fees', false)->count();

        return view('admin.accounting.index', compact(
            'nesaRegistered',
            'nesaNotRegistered', 
            'approvedStudents',
            'notApprovedStudents'
        ));
    }

    public function cashbook(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;
        $data = AccountingReport::getCashBookData($startDate, $endDate);

        return view('admin.accounting.cashbook', compact('data', 'startDate', 'endDate'));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date') ? Carbon::parse($request->get('as_of_date')) : now();

        $data = AccountingReport::getBalanceSheetData($asOfDate);

        return view('admin.accounting.balance-sheet', compact('data', 'asOfDate'));
    }

    public function trialBalance(Request $request)
    {
        $asOfDate = $request->get('as_of_date') ? Carbon::parse($request->get('as_of_date')) : now();

        $data = AccountingReport::getTrialBalanceData($asOfDate);

        return view('admin.accounting.trial-balance', compact('data', 'asOfDate'));
    }

    public function exportCashbook(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $data = AccountingReport::getCashBookData($startDate, $endDate);

        $filename = 'cashbook_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Date', 'Reference', 'Type', 'Description', 'Amount', 'Currency', 'Running Balance']);
            
            foreach ($data['transactions'] as $transaction) {
                fputcsv($file, [
                    $transaction->entry_date,
                    $transaction->tx_ref,
                    $transaction->entry_type,
                    $transaction->description,
                    $transaction->direction . $transaction->amount,
                    $transaction->currency,
                    $transaction->running_balance
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Opening Balance', $data['opening_balance']]);
            fputcsv($file, ['Total Receipts', $data['total_receipts']]);
            fputcsv($file, ['Total Payments', $data['total_payments']]);
            fputcsv($file, ['Net Cash Flow', $data['net_cash']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportBalanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date') ? Carbon::parse($request->get('as_of_date')) : now();
        $data = AccountingReport::getBalanceSheetData($asOfDate);

        $filename = 'balance_sheet_' . $asOfDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $asOfDate) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Balance Sheet as of ' . $asOfDate->format('M d, Y')]);
            fputcsv($file, []);
            
            fputcsv($file, ['ASSETS']);
            foreach ($data['assets']['details'] as $asset) {
                fputcsv($file, [$asset['name'], $asset['balance']]);
            }
            fputcsv($file, ['Total Assets', $data['assets']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['LIABILITIES']);
            foreach ($data['liabilities']['details'] as $liability) {
                fputcsv($file, [$liability['name'], $liability['balance']]);
            }
            fputcsv($file, ['Total Liabilities', $data['liabilities']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['EQUITY']);
            foreach ($data['equity']['details'] as $equity) {
                fputcsv($file, [$equity['name'], $equity['balance']]);
            }
            fputcsv($file, ['Total Equity', $data['equity']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Total Liabilities & Equity', $data['liabilities']['total'] + $data['equity']['total']]);
            fputcsv($file, ['Balanced', $data['is_balanced'] ? 'YES' : 'NO']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportTrialBalance(Request $request)
    {
        $asOfDate = $request->get('as_of_date') ? Carbon::parse($request->get('as_of_date')) : now();
        $data = AccountingReport::getTrialBalanceData($asOfDate);

        $filename = 'trial_balance_' . $asOfDate->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data, $asOfDate) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Trial Balance as of ' . $asOfDate->format('M d, Y')]);
            fputcsv($file, []);
            fputcsv($file, ['Account Code', 'Account Name', 'Account Type', 'Debit Balance', 'Credit Balance']);
            
            foreach ($data['accounts'] as $account) {
                fputcsv($file, [
                    $account['account_code'],
                    $account['account_name'],
                    $account['account_type'],
                    $account['debit_balance'],
                    $account['credit_balance']
                ]);
            }
            
            fputcsv($file, []);
            fputcsv($file, ['Totals', '', '', $data['total_debits'], $data['total_credits']]);
            fputcsv($file, ['Difference', '', '', $data['difference']]);
            fputcsv($file, ['Balanced', '', '', $data['is_balanced'] ? 'YES' : 'NO']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function cashFlowStatement(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $data = AccountingReport::getCashFlowStatementData($startDate, $endDate);

        return view('admin.accounting.cash-flow', compact('data', 'startDate', 'endDate'));
    }

    public function exportCashFlowStatement(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $data = AccountingReport::getCashFlowStatementData($startDate, $endDate);

        $filename = 'cash_flow_' . now()->format('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Cash Flow Statement']);
            fputcsv($file, ['Period', $data['period_start']->format('M d, Y'), 'to', $data['period_end']->format('M d, Y')]);
            fputcsv($file, []);
            
            fputcsv($file, ['Cash Flows from Operating Activities', $data['cash_flows']['operating_activities']]);
            fputcsv($file, ['Cash Flows from Investing Activities', $data['cash_flows']['investing_activities']]);
            fputcsv($file, ['Cash Flows from Financing Activities', $data['cash_flows']['financing_activities']]);
            fputcsv($file, ['Net Cash Flow', $data['cash_flows']['net_cash_flow']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Cash Balances']);
            fputcsv($file, ['Opening Balance', $data['cash_balances']['opening_balance']]);
            fputcsv($file, ['Closing Balance', $data['cash_balances']['closing_balance']]);
            fputcsv($file, ['Net Change', $data['cash_balances']['net_change']]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function incomeStatement(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $data = AccountingReport::getIncomeStatementData($startDate, $endDate);

        return view('admin.accounting.income-statement', compact('data', 'startDate', 'endDate'));
    }

    public function generalLedger(Request $request)
    {
        $accounts = Account::where('is_active', true)
            ->orderBy('account_code')
            ->get();

        $selectedAccountId = $request->get('account_id');
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : null;
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date')) : null;

        $ledgerData = null;

        if ($selectedAccountId) {
            $ledgerData = AccountingReport::getGeneralLedgerData($selectedAccountId, $startDate, $endDate);
        }

        return view('admin.accounting.general-ledger', compact(
            'accounts',
            'selectedAccountId',
            'startDate',
            'endDate',
            'ledgerData'
        ));
    }
}
