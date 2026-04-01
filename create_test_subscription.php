<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Creating Test Student Subscription ===\n";

// Get the first subscription (Basic - 12500 RWF)
$subscription = \App\Models\Subscription::find(1);
if (!$subscription) {
    echo "Subscription with ID 1 not found.\n";
    exit;
}

echo "Using subscription: {$subscription->name} - {$subscription->price} RWF\n";

// Delete any existing subscription with ID 3
\App\Models\StudentSubscription::where('id', 3)->delete();

// Create the student subscription with ID 3
$studentSubscription = new \App\Models\StudentSubscription();
$studentSubscription->id = 3; // Force ID 3 to match the URL
$studentSubscription->student_id = 270; // From the error logs
$studentSubscription->subscription_id = $subscription->id;
$studentSubscription->amount = $subscription->price;
$studentSubscription->paid_amount = 0;
$studentSubscription->tokens_included = $subscription->tokens_included;
$studentSubscription->payment_reference = null;
$studentSubscription->payment_status = 'pending';
$studentSubscription->starts_at = now();
$studentSubscription->ends_at = now()->addDays($subscription->duration_days);
$studentSubscription->is_active = false;
$studentSubscription->save();

echo "Created student subscription:\n";
echo "ID: {$studentSubscription->id}\n";
echo "Student ID: {$studentSubscription->student_id}\n";
echo "Subscription: {$studentSubscription->subscription->name}\n";
echo "Amount: {$studentSubscription->amount} RWF\n";
echo "Tokens Included: {$studentSubscription->tokens_included}\n";
echo "Status: {$studentSubscription->payment_status}\n";
echo "Duration: {$studentSubscription->subscription->duration_days} days\n";

echo "\nYou can now access the payment page at: /subscriptions/payment/3\n";
echo "The price should now display correctly as: {$studentSubscription->amount} RWF\n";
