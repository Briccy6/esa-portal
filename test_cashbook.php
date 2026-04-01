<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\AccountingReport;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Cashbook Data...\n\n";

// Test student transactions
$studentTransactions = DB::table('student_transactions')
    ->whereIn('status', ['paid', 'successful'])
    ->count();

echo "Student Transactions (paid/successful): {$studentTransactions}\n";

// Test expenses
$expenses = DB::table('expenses')->count();

echo "Expenses: {$expenses}\n";

// Test the cashbook method
try {
    $data = AccountingReport::getCashBookData();
    
    echo "\nCashbook Data Summary:\n";
    echo "Total Transactions: " . $data['transactions']->count() . "\n";
    echo "Total Receipts: " . number_format($data['total_receipts'], 2) . " RWF\n";
    echo "Total Payments: " . number_format($data['total_payments'], 2) . " RWF\n";
    echo "Net Cash: " . number_format($data['net_cash'], 2) . " RWF\n";
    
    echo "\nFirst 5 Transactions:\n";
    foreach ($data['transactions']->take(5) as $tx) {
        echo "- {$tx->entry_date}: {$tx->entry_type} - {$tx->amount} RWF ({$tx->description})\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
