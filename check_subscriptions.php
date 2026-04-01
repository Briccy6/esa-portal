<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Subscriptions ===\n";
$subscriptions = \App\Models\Subscription::all();
foreach ($subscriptions as $subscription) {
    echo "ID: {$subscription->id} - {$subscription->name} - {$subscription->price} RWF\n";
}

echo "\n=== Student Subscriptions ===\n";
$studentSubscriptions = \App\Models\StudentSubscription::all();
foreach ($studentSubscriptions as $studentSub) {
    echo "ID: {$studentSub->id} - Student: {$studentSub->student_id} - Amount: {$studentSub->amount} - Paid: {$studentSub->paid_amount} - Status: {$studentSub->payment_status}\n";
}
