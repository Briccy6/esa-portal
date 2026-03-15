<?php

// Clear WebRTC cache and test fresh join
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Cache;

echo "=== Clearing WebRTC Cache ===\n\n";

$roomId = 'class-1';

// Clear all WebRTC related cache for this room
Cache::forget("webrtc_participants_{$roomId}");
Cache::forget("webrtc_clients_{$roomId}");
Cache::forget("webrtc_to_host_{$roomId}");
Cache::forget("webrtc_to_client_{$roomId}_saVn1EMj0BsX2s72qxX1Hx2flrRYT0oMANNWCXQh");
Cache::forget("webrtc_to_client_{$roomId}_556IiCFvfSKB3LyxsZz4UYmiOgaCz8FHxLjk02Ft");

echo "Cleared cache for room: {$roomId}\n";
echo "Cleared student queue\n";
echo "Cleared host queue\n";
echo "Cleared participants list\n";
echo "Cleared clients list\n\n";

echo "Cache cleared successfully!\n";
echo "Now have the student refresh their page to test fresh connection.\n";
