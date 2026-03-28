<?php
// Test signaling endpoint directly
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\LiveClassSignalingController;
use Illuminate\Http\Request;

echo "=== Testing Signaling Controller ===\n";

$controller = new LiveClassSignalingController();

// Test send method
echo "Testing send method...\n";
$request = new Request([
    'from_peer' => 'test_student',
    'to_peer' => 'teacher',
    'type' => 'join',
    'payload' => json_encode([
        'sessionId' => 'test_session',
        'name' => 'Test Student'
    ])
]);

try {
    $response = $controller->send($request, 3);
    echo "✅ Send method response: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "❌ Send method error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

// Test poll method
echo "\nTesting poll method...\n";
$pollRequest = new Request();

try {
    $pollResponse = $controller->poll($pollRequest, 3);
    echo "✅ Poll method response: " . $pollResponse->getContent() . "\n";
} catch (Exception $e) {
    echo "❌ Poll method error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== End Test ===\n";
