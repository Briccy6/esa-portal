<?php
session_start();

// Simulate a logged in staff user
$_SESSION['staff_user'] = [
    'id' => 1,
    'name' => 'Test User',
    'role' => 'DOS'
];

// Test the actual controller method
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$request = Illuminate\Http\Request::create('/staff/lessons/debug-courses', 'POST', [
    'branch_ids' => [4]
]);

$request->headers->set('Content-Type', 'application/json');
$request->headers->set('X-CSRF-TOKEN', 'test-token');

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
