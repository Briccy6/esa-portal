<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Fixing Student Subscriptions ===\n";

// Get all students to test with
$students = \App\Models\Student::all();
if ($students->isEmpty()) {
    echo "No students found. Creating a test student first.\n";
    
    // Create a test student if needed
    $testStudent = \App\Models\Student::create([
        'full_name' => 'Test Student',
        'email' => 'test@student.com',
        'password' => bcrypt('password'),
        'phone' => '1234567890',
        'sex' => 'male',
        'identification_type' => 'national_id',
        'id_number' => '123456789',
    ]);
    
    echo "Created test student with ID: {$testStudent->id}\n";
    $students = collect([$testStudent]);
}

// Get the first subscription (Basic - 12500 RWF)
$subscription = \App\Models\Subscription::first();
if (!$subscription) {
    echo "No subscriptions found!\n";
    exit;
}

echo "Using subscription: {$subscription->name} - {$subscription->price} RWF\n";

// Create a test student subscription
$student = $students->first();
echo "Creating test subscription for student: {$student->full_name} (ID: {$student->id})\n";

$studentSubscription = $student->subscriptions()->create([
    'subscription_id' => $subscription->id,
    'amount' => $subscription->price,
    'paid_amount' => 0, // Not paid yet
    'tokens_included' => $subscription->tokens_included,
    'payment_reference' => null,
    'payment_status' => 'pending',
    'starts_at' => now(),
    'ends_at' => now()->addDays($subscription->duration_days),
    'is_active' => false,
]);

echo "Created student subscription with ID: {$studentSubscription->id}\n";
echo "Amount: {$studentSubscription->amount} RWF\n";
echo "Subscription: {$studentSubscription->subscription->name}\n";

echo "\n=== All Student Subscriptions Now ===\n";
$allStudentSubs = \App\Models\StudentSubscription::all();
foreach ($allStudentSubs as $sub) {
    echo "ID: {$sub->id} - Amount: {$sub->amount} - Paid: {$sub->paid_amount} - Status: {$sub->payment_status}\n";
}

echo "\nDone! You can now test the payment page with student subscription ID: {$studentSubscription->id}\n";
