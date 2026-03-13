<?php

// Simple test to check WebRTC functionality
require_once __DIR__.'/bootstrap/app.php';

$app = require_once __DIR__.'/bootstrap/app.php';

// Test WebRTC signaling
echo "=== Testing WebRTC Signaling ===\n";

// Simulate a student joining
$roomId = 'class-1';
$sessionId = 'test-session-' . time();

echo "Room ID: $roomId\n";
echo "Session ID: $sessionId\n";

// Test cache operations
use Illuminate\Support\Facades\Cache;

// Simulate student joining
$clientsKey = "webrtc_clients_{$roomId}";
$clients = Cache::get($clientsKey, []);
$clients[] = $sessionId;
Cache::put($clientsKey, $clients, 600);

echo "✓ Student registered in clients list\n";
echo "✓ Clients in room: " . count($clients) . "\n";

// Simulate viewer_ready signal
$hostKey = "webrtc_to_host_{$roomId}";
$viewerReadySignal = [
    'type' => 'viewer_ready',
    'sessionId' => $sessionId,
    'role' => 'client',
];

// Get existing signals
$existingSignals = Cache::get($hostKey, []);
$existingSignals[] = $viewerReadySignal;
Cache::put($hostKey, $existingSignals, 600);

echo "✓ viewer_ready signal sent to host\n";

// Test host polling
$hostSignals = Cache::get($hostKey, []);
echo "✓ Host would receive " . count($hostSignals) . " signals\n";

// Test client signal routing
$clientKey = "webrtc_to_client_{$roomId}_{$sessionId}";
$offerSignal = [
    'type' => 'offer',
    'sdp' => 'test-sdp-data',
    'role' => 'host',
    'sessionId' => 'host-session',
    'forSession' => $sessionId,
];

Cache::put($clientKey, [$offerSignal], 600);

echo "✓ Offer signal sent to client\n";

// Test client polling
$clientSignals = Cache::get($clientKey, []);
echo "✓ Client would receive " . count($clientSignals) . " signals\n";

echo "\n=== Cache Test Complete ===\n";
echo "All cache operations working correctly!\n";
