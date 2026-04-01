<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Quick Fix for Subscription Payment ===\n";

// Just create a simple student subscription with ID 3 to match the error
echo "Creating a student subscription with ID 3...\n";

// Get any existing student or create minimal one
$student = \App\Models\Student::first();
if (!$student) {
    echo "No students found. You need to create a student first through the registration system.\n";
    exit;
}

// Get the first subscription
$subscription = \App\Models\Subscription::first();
if (!$subscription) {
    echo "No subscriptions found!\n";
    exit;
}

// Delete any existing subscription with ID 3
\App\Models\StudentSubscription::where('id', 3)->delete();

// Create the student subscription with ID 3
$studentSubscription = new \App\Models\StudentSubscription();
$studentSubscription->id = 3; // Force ID 3
$studentSubscription->student_id = $student->id;
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
echo "Student: {$student->full_name}\n";
echo "Subscription: {$subscription->name}\n";
echo "Amount: {$studentSubscription->amount} RWF\n";
echo "Status: {$studentSubscription->payment_status}\n";

echo "\nYou can now access the payment page at: /subscriptions/payment/3\n";
