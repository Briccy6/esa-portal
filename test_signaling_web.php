<?php
// Test signaling through a simulated web environment
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Bootstrap the application for web requests
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Signaling in Web Context ===\n";

// Start a session to simulate web environment
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use Illuminate\Http\Request;
use App\Http\Controllers\LiveClassSignalingController;

try {
    // Create request exactly like the browser sends
    $request = new Request();
    $request->merge([
        'from_peer' => 'test_student_web',
        'to_peer' => 'teacher',
        'type' => 'join',
        'payload' => [
            'sessionId' => 'test_session_web',
            'name' => 'Test Student Web'
        ]
    ]);
    
    // Set headers like browser
    $request->headers->set('Content-Type', 'application/json');
    $request->headers->set('Accept', 'application/json');
    $request->headers->set('X-Requested-With', 'XMLHttpRequest');
    
    echo "Request data prepared\n";
    echo "From: " . $request->input('from_peer') . "\n";
    echo "To: " . $request->input('to_peer') . "\n";
    echo "Type: " . $request->input('type') . "\n";
    
    // Test the controller
    $controller = new LiveClassSignalingController();
    $response = $controller->send($request, 3);
    
    echo "✅ Controller response: " . $response->getContent() . "\n";
    echo "Status: " . $response->getStatusCode() . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== End Test ===\n";
