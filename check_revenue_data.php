<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TokenTransaction;
use App\Models\LiveClassPayment;
use App\Models\Resource;
use App\Models\StudentSubscription;
use App\Models\Expense;

echo "=== Revenue Data Check ===\n\n";

// Check Token Transactions
$tokenCount = TokenTransaction::count();
$tokenRevenue = TokenTransaction::where('type', 'credit')->where('source', 'purchase')->sum('amount');
echo "Token Transactions:\n";
echo "  Total Count: $tokenCount\n";
echo "  Revenue: $tokenRevenue\n";

// Check a few sample records
$sampleTokens = TokenTransaction::limit(3)->get(['type', 'source', 'amount']);
echo "  Sample Records:\n";
foreach ($sampleTokens as $token) {
    echo "    Type: {$token->type}, Source: {$token->source}, Amount: {$token->amount}\n";
}
echo "\n";

// Check Live Class Payments
$liveClassCount = LiveClassPayment::count();
$liveClassRevenue = LiveClassPayment::where('status', 'completed')->sum('amount');
echo "Live Class Payments:\n";
echo "  Total Count: $liveClassCount\n";
echo "  Revenue: $liveClassRevenue\n";

$sampleLiveClasses = LiveClassPayment::limit(3)->get(['status', 'amount']);
echo "  Sample Records:\n";
foreach ($sampleLiveClasses as $payment) {
    echo "    Status: {$payment->status}, Amount: {$payment->amount}\n";
}
echo "\n";

// Check Resources
$resourceCount = Resource::count();
$resourceRevenue = Resource::where('is_free', false)->where('price', '>', 0)->sum('price');
echo "Resources:\n";
echo "  Total Count: $resourceCount\n";
echo "  Revenue: $resourceRevenue\n";

$sampleResources = Resource::limit(3)->get(['is_free', 'price']);
echo "  Sample Records:\n";
foreach ($sampleResources as $resource) {
    echo "    Is Free: " . ($resource->is_free ? 'Yes' : 'No') . ", Price: {$resource->price}\n";
}
echo "\n";

// Check Student Subscriptions
$subscriptionCount = StudentSubscription::count();
$subscriptionRevenue = StudentSubscription::where('payment_status', 'paid')->where('is_active', true)->sum('paid_amount');
echo "Student Subscriptions:\n";
echo "  Total Count: $subscriptionCount\n";
echo "  Revenue: $subscriptionRevenue\n";

$sampleSubscriptions = StudentSubscription::limit(3)->get(['payment_status', 'is_active', 'paid_amount']);
echo "  Sample Records:\n";
foreach ($sampleSubscriptions as $subscription) {
    echo "    Payment Status: {$subscription->payment_status}, Is Active: " . ($subscription->is_active ? 'Yes' : 'No') . ", Paid Amount: {$subscription->paid_amount}\n";
}
echo "\n";

// Check Expenses
$expenseCount = Expense::count();
$totalExpenses = Expense::sum('amount');
echo "Expenses:\n";
echo "  Total Count: $expenseCount\n";
echo "  Total Amount: $totalExpenses\n";

$sampleExpenses = Expense::limit(3)->get(['amount']);
echo "  Sample Records:\n";
foreach ($sampleExpenses as $expense) {
    echo "    Amount: {$expense->amount}\n";
}
echo "\n";

echo "=== Summary ===\n";
echo "Total Revenue: " . ($tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue) . "\n";
echo "Total Expenses: $totalExpenses\n";
echo "Net Profit: " . (($tokenRevenue + $liveClassRevenue + $resourceRevenue + $subscriptionRevenue) - $totalExpenses) . "\n";
