<?php

// Debug script to check WebRTC live class issue
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

echo "=== WebRTC Live Class Debug ===\n\n";

$roomId = 'class-1'; // Based on logs

// Check cache data
echo "1. Checking WebRTC cache data for room: {$roomId}\n";

$participants = Cache::get("webrtc_participants_{$roomId}", []);
echo "   Participants: " . json_encode($participants, JSON_PRETTY_PRINT) . "\n";

$clients = Cache::get("webrtc_clients_{$roomId}", []);
echo "   Clients: " . json_encode($clients, JSON_PRETTY_PRINT) . "\n";

$hostQueue = Cache::get("webrtc_to_host_{$roomId}", []);
echo "   Host Queue: " . json_encode($hostQueue, JSON_PRETTY_PRINT) . "\n";

foreach ($clients as $sessionId) {
    $clientQueue = Cache::get("webrtc_to_client_{$roomId}_{$sessionId}", []);
    echo "   Client Queue [{$sessionId}]: " . json_encode($clientQueue, JSON_PRETTY_PRINT) . "\n";
}

echo "\n2. Checking recent WebRTC logs:\n";

// Get recent logs (this is a simplified approach)
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = substr($logs, -5000); // Last 5KB
    echo "   Recent logs (last 5KB):\n";
    echo "   " . str_replace("\n", "\n   ", $recentLogs) . "\n";
} else {
    echo "   Log file not found\n";
}

echo "\n3. Testing WebRTC controller methods:\n";

// Test WebRTCController
try {
    $controller = new \App\Http\Controllers\WebRTCController();
    
    // Mock request for testing
    $mockRequest = new \Illuminate\Http\Request();
    $mockRequest->merge([
        'role' => 'client',
        'sessionId' => 'test-session'
    ]);
    
    echo "   WebRTCController instantiated successfully\n";
    
    // Test pollSignaling method
    $result = $controller->pollSignaling($mockRequest, $roomId);
    echo "   pollSignaling result: " . json_encode($result->getContent()) . "\n";
    
} catch (Exception $e) {
    echo "   Error testing WebRTCController: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
