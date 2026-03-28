<?php
// Test the web route with proper request simulation
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Web Signaling Route ===\n";

use Illuminate\Http\Request;

// Create a proper POST request like the browser would send
$request = Request::create('/live-signaling/3/send', 'POST', [
    'from_peer' => 'AeSNEuQi0agCkCdJPn19KFSRaPtRjm4YR5CxIIzm',
    'to_peer' => 'teacher',
    'type' => 'join',
    'payload' => [
        'sessionId' => 'AeSNEuQi0agCkCdJPn19KFSRaPtRjm4YR5CxIIzm',
        'name' => 'Test Student'
    ]
], [], [], [
    'HTTP_CONTENT_TYPE' => 'application/json',
    'HTTP_ACCEPT' => 'application/json',
]);

// Add JSON payload
$request->headers->set('Content-Type', 'application/json');
$request->headers->set('Accept', 'application/json');

try {
    $response = $kernel->handle($request);
    echo "Response status: " . $response->getStatusCode() . "\n";
    echo "Response content: " . $response->getContent() . "\n";
    
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== End Test ===\n";
