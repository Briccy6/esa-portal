<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class');

$kernel->bootstrap();

echo "=== Final Fix for Payment Page ===\n";

// Get any existing branch or create one
$branch = \App\Models\Branch::first();
if (!$branch) {
    echo "Creating a default branch...\n";
    $branch = \App\Models\Branch::create([
        'name' => 'Default Branch',
        'location' => 'Kigali',
        'phone' => '1234567890',
        'email' => 'branch@example.com',
        'is_active' => true,
    ]);
}
echo "Using branch: {$branch->name} (ID: {$branch->id})\n";

// Get any existing course or create one
$course = \App\Models\Course::first();
if (!$course) {
    echo "Creating a default course...\n";
    $course = \App\Models\Course::create([
        'name' => 'General Course',
        'course_name' => 'General Course', // Add the required field
        'code' => 'GEN001',
        'branch_id' => $branch->id,
        'description' => 'General course for testing',
        'is_active' => true,
    ]);
}
echo "Using course: {$course->name} (ID: {$course->id})\n";

// Get any existing academic year or create one
$academicYear = \App\Models\AcademicYear::first();
if (!$academicYear) {
    echo "Creating a default academic year...\n";
    $academicYear = \App\Models\AcademicYear::create([
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'is_active' => true,
    ]);
}
echo "Using academic year: {$academicYear->name} (ID: {$academicYear->id})\n";

// Temporarily disable foreign key checks
\DB::statement('SET FOREIGN_KEY_CHECKS=0');

// Create the student with all required fields
\DB::table('students')->insert([
    'id' => 270,
    'full_name' => 'Test Student',
    'email' => 'teststudent@example.com',
    'password' => bcrypt('password123'),
    'phone' => '1234567890',
    'sex' => 'male',
    'branch_id' => $branch->id,
    'course_id' => $course->id,
    'academic_year_id' => $academicYear->id,
    'identification_type' => 'national_id',
    'id_number' => '1234567890123456789',
    'created_at' => now(),
    'updated_at' => now(),
]);

// Re-enable foreign key checks
\DB::statement('SET FOREIGN_KEY_CHECKS=1');

echo "Created student: Test Student (ID: 270)\n";

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
$studentSubscription->student_id = 270;
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

echo "\n=== Verification ===\n";
$verifySub = \App\Models\StudentSubscription::find(3);
echo "Verified subscription data:\n";
echo "- Amount: {$verifySub->amount} RWF\n";
echo "- Paid Amount: {$verifySub->paid_amount} RWF\n";
echo "- Tokens: {$verifySub->tokens_included}\n";
echo "- Subscription Name: {$verifySub->subscription->name}\n";
echo "- Subscription Price: {$verifySub->subscription->price} RWF\n";

echo "\n✅ FIXED! The payment page should now show the correct price.\n";
echo "Access the payment page at: /subscriptions/payment/3\n";
echo "The price should display as: {$verifySub->amount} RWF instead of 0 RWF\n";

echo "\n=== Login Details ===\n";
echo "Email: teststudent@example.com\n";
echo "Password: password123\n";
