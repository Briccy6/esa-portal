<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StudentTransaction;
use App\Models\Expense;

echo "=== Student Transactions Revenue Data Check ===\n\n";

// Check Student Transactions
$transactionCount = StudentTransaction::count();
echo "Student Transactions:\n";
echo "  Total Count: $transactionCount\n";

// Check revenue by type
$tokenRevenue = StudentTransaction::where('type', 'token_purchase')->where('status', 'successful')->sum('amount');
$liveClassRevenue = StudentTransaction::where('type', 'live_class')->where('status', 'successful')->sum('amount');
$resourceRevenue = StudentTransaction::where('type', 'resource')->where('status', 'successful')->sum('amount');
$subscriptionRevenue = StudentTransaction::where('type', 'subscription')->where('status', 'successful')->sum('amount');

echo "  Token Revenue (token_purchase): $tokenRevenue\n";
echo "  Live Class Revenue (live_class): $liveClassRevenue\n";
echo "  Resource Revenue (resource): $resourceRevenue\n";
echo "  Subscription Revenue (subscription): $subscriptionRevenue\n";

// Show all transaction types
$transactionTypes = StudentTransaction::select('type', 'status', \DB::raw('count(*) as count'), \DB::raw('sum(amount) as total'))
    ->groupBy('type', 'status')
    ->get();

echo "\n  Transaction Types:\n";
foreach ($transactionTypes as $type) {
    echo "    Type: {$type->type}, Status: {$type->status}, Count: {$type->count}, Total: {$type->total}\n";
}

// Show sample records
$sampleTransactions = StudentTransaction::limit(5)->get(['type', 'status', 'amount', 'created_at']);
echo "\n  Sample Records:\n";
foreach ($sampleTransactions as $transaction) {
    echo "    Type: {$transaction->type}, Status: {$transaction->status}, Amount: {$transaction->amount}, Date: {$transaction->created_at}\n";
}

// Check Expenses
$expenseCount = Expense::count();
$totalExpenses = Expense::sum('amount');
echo "\nExpenses:\n";
echo "  Total Count: $expenseCount\n";
echo "  Total Amount: $totalExpenses\n";

$sampleExpenses = Expense::limit(3)->get(['amount', 'expense_date']);
echo "  Sample Records:\n";
foreach ($sampleExpenses as $expense) {
    echo "    Amount: {$expense->amount}, Date: {$expense->expense_date}\n";
}

echo "\n=== Summary ===\n";
$totalRevenue = $tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue;
echo "Total Revenue: $totalRevenue\n";
echo "Total Expenses: $totalExpenses\n";
echo "Net Profit: " . ($totalRevenue - $totalExpenses) . "\n";
