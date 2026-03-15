<?php

// Check if teacher has started broadcasting
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

echo "=== Teacher Broadcast Status ===\n\n";

$roomId = 'class-1';

// Check if teacher (host) is in participants
$participants = Cache::get("webrtc_participants_{$roomId}", []);
echo "Participants in room:\n";
foreach ($participants as $sessionId => $data) {
    echo "  Session: {$sessionId}\n";
    echo "  Name: {$data['name']}\n";
    echo "  Role: {$data['role']}\n";
    echo "  Joined: {$data['joined_at']}\n\n";
}

// Check host queue for viewer_ready signals
$hostQueue = Cache::get("webrtc_to_host_{$roomId}", []);
echo "Host queue signals:\n";
foreach ($hostQueue as $signal) {
    echo "  Type: {$signal['type']}\n";
    echo "  From: " . (isset($signal['fromSession']) ? $signal['fromSession'] : 'unknown') . "\n";
    echo "  Session: " . (isset($signal['sessionId']) ? $signal['sessionId'] : 'unknown') . "\n\n";
}

echo "=== Diagnosis ===\n";
$teacherSessionId = '556IiCFvfSKB3LyxsZz4UYmiOgaCz8FHxLjk02Ft'; // From logs
$studentSessionId = 'saVn1EMj0BsX2s72qxX1Hx2flrRYT0oMANNWCXQh'; // From logs

$teacherConnected = isset($participants[$teacherSessionId]);
$studentConnected = isset($participants[$studentSessionId]);

echo "Teacher connected: " . ($teacherConnected ? "YES" : "NO") . "\n";
echo "Student connected: " . ($studentConnected ? "YES" : "NO") . "\n";

$viewerReadyFound = false;
foreach ($hostQueue as $signal) {
    if ($signal['type'] === 'viewer_ready' && $signal['fromSession'] === $studentSessionId) {
        $viewerReadyFound = true;
        break;
    }
}

echo "Student viewer_ready signal found: " . ($viewerReadyFound ? "YES" : "NO") . "\n";

if ($teacherConnected && $studentConnected && !$viewerReadyFound) {
    echo "\n🔧 SOLUTION: The student needs to refresh their page to send the viewer_ready signal again.\n";
    echo "   The teacher is connected but the student's viewer_ready signal was processed before teacher joined.\n";
} elseif ($teacherConnected && $studentConnected && $viewerReadyFound) {
    echo "\n🔧 SOLUTION: The teacher needs to click 'Start Camera' to begin broadcasting.\n";
    echo "   Both are connected but teacher hasn't started the media stream yet.\n";
} else {
    echo "\n🔧 SOLUTION: Check connection status above.\n";
}

echo "\n=== End ===\n";
