<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Checking Student Subscription ID 3 ===\n";
$studentSub = \App\Models\StudentSubscription::find(3);

if ($studentSub) {
    echo "Found Student Subscription:\n";
    echo "ID: {$studentSub->id}\n";
    echo "Student ID: {$studentSub->student_id}\n";
    echo "Subscription ID: {$studentSub->subscription_id}\n";
    echo "Amount: {$studentSub->amount}\n";
    echo "Paid Amount: {$studentSub->paid_amount}\n";
    echo "Payment Status: {$studentSub->payment_status}\n";
    
    if ($studentSub->subscription) {
        echo "Subscription Name: {$studentSub->subscription->name}\n";
        echo "Subscription Price: {$studentSub->subscription->price}\n";
    }
} else {
    echo "Student Subscription ID 3 not found\n";
}

echo "\n=== All Student Subscriptions ===\n";
$allStudentSubs = \App\Models\StudentSubscription::all();
if ($allStudentSubs->isEmpty()) {
    echo "No student subscriptions found\n";
} else {
    foreach ($allStudentSubs as $sub) {
        echo "ID: {$sub->id} - Amount: {$sub->amount} - Paid: {$sub->paid_amount} - Sub ID: {$sub->subscription_id}\n";
    }
}
