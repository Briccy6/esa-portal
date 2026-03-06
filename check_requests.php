<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check for live classes and requests
$liveClass = \App\Models\LiveClass::find(5);
if ($liveClass) {
    echo "Live Class ID: " . $liveClass->id . "\n";
    
    // Get a student to create test request
    $student = \App\Models\Student::first();
    echo "Student ID: " . ($student ? $student->id : 'None') . "\n";
    echo "Student Name: " . ($student ? $student->full_name : 'None') . "\n";
    
    if ($student) {
        // Check if request already exists
        $existingRequest = $liveClass->requests()
            ->where('user_id', $student->id)
            ->where('user_type', 'student')
            ->where('status', 'pending')
            ->first();
            
        if (!$existingRequest) {
            echo "Creating test pending request...\n";
            $testRequest = $liveClass->requests()->create([
                'user_id' => $student->id,
                'user_type' => 'student',
                'reason' => 'Test pending request for debugging',
                'status' => 'pending'
            ]);
            echo "Created test request ID: " . $testRequest->id . "\n";
        } else {
            echo "Pending request already exists: " . $existingRequest->id . "\n";
        }
    }
    
    // Test the API endpoint
    $requests = $liveClass->requests()
        ->with(['user' => function($query) {
            $query->select('id', 'full_name', 'course_id');
        }])
        ->where('user_type', 'student')
        ->where('status', 'pending')
        ->latest()
        ->get();

    echo "\nPending requests for API: " . $requests->count() . "\n";
    foreach ($requests as $req) {
        echo "Request ID: " . $req->id . ", User ID: " . $req->user_id . ", User: " . ($req->user ? $req->user->full_name : 'N/A') . "\n";
    }
} else {
    echo "Live class not found\n";
}
