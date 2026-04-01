<?php

namespace App\Http\Controllers;

use App\Models\AccountingReport;
use App\Models\Account;
use App\Models\Student;
use App\Models\SchoolInformation;
use App\Support\StudentPaymentTranscript;
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
        
        // Get school information
        $school = SchoolInformation::getActive();

        return view('admin.accounting.cashbook', compact('data', 'startDate', 'endDate', 'school'));
    }

    public function balanceSheet(Request $request)
    {
        $asOfDate = $request->get('as_of_date') ? Carbon::parse($request->get('as_of_date')) : now();

        $data = AccountingReport::getBalanceSheetData($asOfDate);
        $school = SchoolInformation::getActive();

        return view('admin.accounting.balance-sheet', compact('data', 'asOfDate', 'school'));
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
            fputcsv($file, ['Account Code', 'Description', 'Amount (RWF)']);
            fputcsv($file, []);
            
            fputcsv($file, ['ASSETS']);
            foreach ($data['assets']['details'] as $asset) {
                fputcsv($file, [$asset['account_code'] ?? '', $asset['name'], $asset['balance']]);
            }
            fputcsv($file, ['Total Assets', $data['assets']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['LIABILITIES']);
            foreach ($data['liabilities']['details'] as $liability) {
                fputcsv($file, [$liability['account_code'] ?? '', $liability['name'], $liability['balance']]);
            }
            fputcsv($file, ['Total Liabilities', $data['liabilities']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['EQUITY']);
            foreach ($data['equity']['details'] as $equity) {
                fputcsv($file, [$equity['account_code'] ?? '', $equity['name'], $equity['balance']]);
            }
            fputcsv($file, ['Total Equity', $data['equity']['total']]);
            fputcsv($file, []);
            
            fputcsv($file, ['Total Liabilities & Equity', $data['liabilities']['total'] + $data['equity']['total']]);
            fputcsv($file, ['Balanced', $data['is_balanced'] ? 'YES' : 'NO']);
            if (!empty($data['meta'])) {
                fputcsv($file, []);
                fputcsv($file, ['Total student receipts (basis)', $data['meta']['total_student_receipts']]);
                fputcsv($file, ['Total expenses (basis)', $data['meta']['total_expenses']]);
            }
            
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

    public function cashFlowForecast(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $year = max(2000, min(2100, $year));

        $data = AccountingReport::getCashFlowForecastData($year);
        $school = SchoolInformation::getActive();

        return view('admin.accounting.cash-flow-forecast', compact('data', 'year', 'school'));
    }

    public function exportCashFlowForecast(Request $request)
    {
        $year = (int) $request->get('year', now()->year);
        $year = max(2000, min(2100, $year));

        $data = AccountingReport::getCashFlowForecastData($year);

        $filename = 'cash_flow_forecast_' . $year . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($data, $year) {
            $file = fopen('php://output', 'w');

            fputcsv($file, ['Cash flow forecast', $year]);
            fputcsv($file, ['Source: student_transactions (paid / successful) and expenses']);
            fputcsv($file, []);

            $monthLabels = array_map(fn ($m) => $m['label'], $data['months']);
            fputcsv($file, array_merge(['Line'], $monthLabels, ['Year total']));

            $sumRow = function (array $values) {
                return array_sum($values);
            };

            $opening = array_map(fn ($m) => $m['opening'], $data['months']);
            fputcsv($file, array_merge(['Opening balance (start of each month)'], $opening, ['']));

            $cashIn = array_map(fn ($m) => $m['cash_in'], $data['months']);
            fputcsv($file, array_merge(['Cash in — student receipts (sum of amount)'], $cashIn, [$sumRow($cashIn)]));

            foreach ($data['in_types'] as $type) {
                $row = [];
                foreach ($data['months'] as $m) {
                    $row[] = $m['cash_in_by_type'][$type] ?? 0;
                }
                fputcsv($file, array_merge(['  └ by type: ' . $type], $row, [$sumRow($row)]));
            }

            $cashOut = array_map(fn ($m) => $m['cash_out'], $data['months']);
            fputcsv($file, array_merge(['Cash out — expenses (sum of amount)'], $cashOut, [$sumRow($cashOut)]));

            foreach ($data['expense_types'] as $expType) {
                $row = [];
                foreach ($data['months'] as $m) {
                    $row[] = $m['cash_out_by_expense_type'][$expType] ?? 0;
                }
                fputcsv($file, array_merge(['  └ by expense_type: ' . $expType], $row, [$sumRow($row)]));
            }

            $net = array_map(fn ($m) => $m['net'], $data['months']);
            fputcsv($file, array_merge(['Net cash movement'], $net, [$sumRow($net)]));

            $closing = array_map(fn ($m) => $m['closing'], $data['months']);
            fputcsv($file, array_merge(['Closing balance (end of month)'], $closing, [$data['year_end_closing']]));

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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

    /**
     * Printable fee / payment transcript for a student (school header, transactions, signatures).
     */
    public function printPaymentTranscript(Student $student)
    {
        $data = StudentPaymentTranscript::build($student);
        $printedBy = auth('admin')->user();
        $printedByName = $printedBy
            ? (trim(($printedBy->first_name ?? '') . ' ' . ($printedBy->last_name ?? '')) ?: $printedBy->email)
            : null;

        return view('admin.accounting.print-payment-transcript', [
            'student'            => $student,
            'schoolInfo'         => $data['school'],
            'feeRows'            => $data['feeRows'],
            'totalExpected'      => $data['totalExpected'],
            'totalPaid'          => $data['totalPaid'],
            'totalPaidAllTypes'  => $data['totalPaidAllTypes'],
            'totalOutstanding'   => $data['totalOutstanding'],
            'discountPct'        => $data['discountPct'],
            'discountAmount'     => $data['discountAmount'],
            'transactions'       => $data['transactions'],
            'printedByName'      => $printedByName,
            'printedAt'          => now(),
        ]);
    }
}
