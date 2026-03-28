<?php

// Detailed WebRTC debugging
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

echo "=== Detailed WebRTC Debug ===\n\n";

$roomId = 'class-1';

// Test 1: Check current cache state
echo "1. Current cache state:\n";
$participants = Cache::get("webrtc_participants_{$roomId}", []);
$clients = Cache::get("webrtc_clients_{$roomId}", []);
$hostQueue = Cache::get("webrtc_to_host_{$roomId}", []);

echo "   Participants: " . count($participants) . "\n";
echo "   Clients: " . count($clients) . "\n";  
echo "   Host queue signals: " . count($hostQueue) . "\n\n";

// Test 2: Manually add a signal to host queue
echo "2. Manually adding test signal to host queue...\n";
$testSignal = [
    'type' => 'viewer_ready',
    'name' => 'Test Student',
    'sessionId' => 'test-session-123',
    'fromSession' => 'test-session-123',
    'count' => 1,
];

// Use the exact same push method as the controller
$q = Cache::get("webrtc_to_host_{$roomId}", []);
$q[] = $testSignal;
Cache::put("webrtc_to_host_{$roomId}", $q, 600);

echo "   Added test signal: {$testSignal['type']}\n";

// Test 3: Immediately check if signal is there
echo "3. Checking if signal was saved...\n";
$hostQueueAfter = Cache::get("webrtc_to_host_{$roomId}", []);
echo "   Host queue now has: " . count($hostQueueAfter) . " signals\n";

if (count($hostQueueAfter) > 0) {
    echo "   ✅ Signal successfully saved!\n";
    foreach ($hostQueueAfter as $signal) {
        echo "      - {$signal['type']} from {$signal['sessionId']}\n";
    }
} else {
    echo "   ❌ Signal was NOT saved!\n";
}

// Test 4: Simulate teacher polling
echo "\n4. Simulating teacher poll...\n";
$teacherSignals = Cache::get("webrtc_to_host_{$roomId}", []);
echo "   Teacher would receive: " . count($teacherSignals) . " signals\n";

// Test 5: Check if cache is working properly
echo "\n5. Cache system test:\n";
$testKey = "test-cache-key";
Cache::put($testKey, ['test' => 'value'], 60);
$cachedValue = Cache::get($testKey, []);
echo "   Cache put/get test: " . (count($cachedValue) > 0 ? "WORKING" : "BROKEN") . "\n";

echo "\n=== Debug Complete ===\n";
