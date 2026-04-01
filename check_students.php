<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Checking Students ===\n";
$students = \App\Models\Student::all();

if ($students->isEmpty()) {
    echo "No students found in the database.\n";
    echo "You need to register a student first through the student registration system.\n";
} else {
    echo "Found {$students->count()} students:\n";
    foreach ($students as $student) {
        echo "ID: {$student->id} - {$student->full_name} - {$student->email}\n";
    }
    
    // Use the first student to create subscription
    $firstStudent = $students->first();
    echo "\nUsing student: {$firstStudent->full_name} (ID: {$firstStudent->id})\n";
    
    // Get the first subscription
    $subscription = \App\Models\Subscription::find(1);
    if (!$subscription) {
        echo "Subscription with ID 1 not found.\n";
        exit;
    }
    
    // Delete any existing subscription with ID 3
    \App\Models\StudentSubscription::where('id', 3)->delete();
    
    // Create the student subscription with ID 3
    $studentSubscription = new \App\Models\StudentSubscription();
    $studentSubscription->id = 3; // Force ID 3 to match the URL
    $studentSubscription->student_id = $firstStudent->id;
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
    echo "Student: {$studentSubscription->student->full_name}\n";
    echo "Subscription: {$studentSubscription->subscription->name}\n";
    echo "Amount: {$studentSubscription->amount} RWF\n";
    echo "Tokens Included: {$studentSubscription->tokens_included}\n";
    echo "Status: {$studentSubscription->payment_status}\n";
    
    echo "\nYou can now access the payment page at: /subscriptions/payment/3\n";
    echo "The price should now display correctly as: {$studentSubscription->amount} RWF\n";
}
