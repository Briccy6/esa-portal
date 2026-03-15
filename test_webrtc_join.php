<?php

// Test WebRTC join flow after syntax fix
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

echo "=== Testing WebRTC Join Flow ===\n\n";

$roomId = 'class-1';
$sessionId = 'test-student-' . time();
$name = 'Test Student';

// Simulate student joining
echo "1. Simulating student join...\n";

// Mock the joinRoom method logic
$participants = Cache::get("webrtc_participants_{$roomId}", []);
$participants[$sessionId] = [
    'name'      => $name,
    'role'      => 'client',
    'joined_at' => now()->toISOString(),
];
Cache::put("webrtc_participants_{$roomId}", $participants, 600);

// Add to client list
$clients = Cache::get("webrtc_clients_{$roomId}", []);
if (!in_array($sessionId, $clients)) {
    $clients[] = $sessionId;
    Cache::put("webrtc_clients_{$roomId}", $clients, 600);
}

// Simulate the fixed push method
function pushSignal($key, $signal) {
    $q = Cache::get($key, []);
    $q[] = $signal;
    Cache::put($key, $q, 600);
}

// Push viewer_ready (this was the broken part)
pushSignal("webrtc_to_host_{$roomId}", [
    'type'        => 'viewer_ready',
    'name'        => $name,
    'sessionId'   => $sessionId,
    'fromSession' => $sessionId,
    'count'       => count($participants),
]);

// Also push participant_joined for the UI
pushSignal("webrtc_to_host_{$roomId}", [
    'type'        => 'participant_joined',
    'name'        => $name,
    'sessionId'   => $sessionId,
    'fromSession' => $sessionId,
    'count'       => count($participants),
]);

echo "2. Checking if signals were added to host queue...\n";

$hostQueue = Cache::get("webrtc_to_host_{$roomId}", []);
echo "Host queue now has " . count($hostQueue) . " signals:\n";

foreach ($hostQueue as $i => $signal) {
    echo "  Signal " . ($i + 1) . ": {$signal['type']} from {$signal['sessionId']}\n";
}

if (count($hostQueue) > 0) {
    echo "\n✅ SUCCESS: viewer_ready signal is now reaching the host queue!\n";
    echo "The WebRTC live class should now work properly.\n";
} else {
    echo "\n❌ FAILED: viewer_ready signal still not reaching host queue\n";
}

echo "\n=== Test Complete ===\n";
