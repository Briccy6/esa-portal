<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the API endpoint
$liveClass = \App\Models\LiveClass::find(5);
if ($liveClass) {
    // Simulate the API call
    $requests = $liveClass->requests()
        ->with(['user' => function($query) {
            $query->select('id', 'full_name', 'course_id');
        }])
        ->where('user_type', 'student')
        ->where('status', 'pending')
        ->latest()
        ->get();

    echo "API Response Structure:\n";
    echo json_encode([
        'requests' => $requests->map(function($req) {
            return [
                'id' => $req->id,
                'user_id' => $req->user_id,
                'user' => $req->user ? [
                    'id' => $req->user->id,
                    'full_name' => $req->user->full_name,
                    'course_id' => $req->user->course_id
                ] : null,
                'reason' => $req->reason,
                'status' => $req->status,
                'created_at' => $req->created_at
            ];
        })
    ], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Live class not found\n";
}
