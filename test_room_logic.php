<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate visiting the room page
$liveClass = \App\Models\LiveClass::find(5);
if ($liveClass) {
    echo "Live Class ID: " . $liveClass->id . "\n";
    echo "Title: " . $liveClass->title . "\n";
    
    // Simulate the room method logic
    $studentRequests = [];
    
    // This is the condition from the controller
    $shouldLoadRequests = true; // Simulating admin/teacher/staff access
    
    if ($shouldLoadRequests) {
        $studentRequests = $liveClass->requests()
            ->with(['user' => function($query) {
                $query->select('id', 'full_name', 'course_id');
            }])
            ->where('user_type', 'student')
            ->where('status', 'pending')
            ->latest()
            ->get();
    }
    
    echo "Student Requests Count: " . $studentRequests->count() . "\n";
    
    // Check if requests would be visible in the view
    if ($studentRequests->count() > 0) {
        echo "✓ Requests should be visible in the room\n";
        foreach ($studentRequests as $req) {
            echo "  - Request " . $req->id . " by " . ($req->user ? $req->user->full_name : 'Unknown') . "\n";
        }
    } else {
        echo "✗ No pending requests to display\n";
    }
} else {
    echo "Live class not found\n";
}
