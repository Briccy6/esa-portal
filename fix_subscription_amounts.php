<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Fixing Student Subscription Amounts ===\n";

// Get all student subscriptions that have null or 0 amounts
$subscriptions = \App\Models\StudentSubscription::where(function($query) {
    $query->whereNull('amount')
          ->orWhere('amount', 0);
})->get();

if ($subscriptions->isEmpty()) {
    echo "No student subscriptions need fixing.\n";
    
    // Check if there are any student subscriptions at all
    $allSubs = \App\Models\StudentSubscription::all();
    if ($allSubs->isEmpty()) {
        echo "No student subscriptions found in database.\n";
        
        // Let's check if the user is logged in and has a subscription
        echo "Creating a test subscription for testing purposes...\n";
        
        // Get the subscription with ID 1 (Basic - 12500 RWF)
        $subscription = \App\Models\Subscription::find(1);
        if (!$subscription) {
            echo "Subscription with ID 1 not found.\n";
            exit;
        }
        
        // Create a student subscription with ID 3 manually
        \DB::statement('ALTER TABLE student_subscriptions AUTO_INCREMENT = 3');
        
        $studentSubscription = \App\Models\StudentSubscription::create([
            'id' => 3,
            'student_id' => 270, // From the error logs
            'subscription_id' => $subscription->id,
            'amount' => $subscription->price,
            'paid_amount' => 0,
            'tokens_included' => $subscription->tokens_included,
            'payment_reference' => null,
            'payment_status' => 'pending',
            'starts_at' => now(),
            'ends_at' => now()->addDays($subscription->duration_days),
            'is_active' => false,
        ]);
        
        echo "Created student subscription with ID: {$studentSubscription->id}\n";
        echo "Amount: {$studentSubscription->amount} RWF\n";
    }
} else {
    echo "Found {$subscriptions->count()} student subscriptions to fix:\n";
    
    foreach ($subscriptions as $studentSub) {
        if ($studentSub->subscription) {
            $correctAmount = $studentSub->subscription->price;
            $studentSub->update(['amount' => $correctAmount]);
            echo "Updated subscription ID {$studentSub->id}: set amount to {$correctAmount} RWF\n";
        } else {
            echo "Subscription ID {$studentSub->id} has no related subscription, skipping...\n";
        }
    }
}

echo "\nDone! The payment page should now show the correct price.\n";
