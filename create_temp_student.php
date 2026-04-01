<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Creating Temporary Student for Testing ===\n";

// Check if we need to create a branch first
$branch = \App\Models\Branch::first();
if (!$branch) {
    echo "No branch found. Creating a default branch...\n";
    $branch = \App\Models\Branch::create([
        'name' => 'Default Branch',
        'location' => 'Kigali',
        'phone' => '1234567890',
        'email' => 'branch@example.com',
        'is_active' => true,
    ]);
    echo "Created branch: {$branch->name} (ID: {$branch->id})\n";
}

// Check if we need to create a course first
$course = \App\Models\Course::first();
if (!$course) {
    echo "No course found. Creating a default course...\n";
    $course = \App\Models\Course::create([
        'name' => 'General Course',
        'code' => 'GEN001',
        'branch_id' => $branch->id,
        'description' => 'General course for testing',
        'is_active' => true,
    ]);
    echo "Created course: {$course->name} (ID: {$course->id})\n";
}

// Check if we need to create an academic year first
$academicYear = \App\Models\AcademicYear::first();
if (!$academicYear) {
    echo "No academic year found. Creating a default academic year...\n";
    $academicYear = \App\Models\AcademicYear::create([
        'name' => '2025-2026',
        'start_date' => '2025-09-01',
        'end_date' => '2026-07-31',
        'is_active' => true,
    ]);
    echo "Created academic year: {$academicYear->name} (ID: {$academicYear->id})\n";
}

// Create the student
$student = \App\Models\Student::create([
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
]);

echo "Created student: {$student->full_name} (ID: {$student->id})\n";

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
echo "Student: {$studentSubscription->student->full_name}\n";
echo "Subscription: {$studentSubscription->subscription->name}\n";
echo "Amount: {$studentSubscription->amount} RWF\n";
echo "Tokens Included: {$studentSubscription->tokens_included}\n";
echo "Status: {$studentSubscription->payment_status}\n";
echo "Duration: {$studentSubscription->subscription->duration_days} days\n";

echo "\n=== Login Details ===\n";
echo "Email: teststudent@example.com\n";
echo "Password: password123\n";

echo "\nYou can now:\n";
echo "1. Login as the student at /student/login\n";
echo "2. Access the payment page at /subscriptions/payment/3\n";
echo "3. The price should now display correctly as: {$studentSubscription->amount} RWF\n";
