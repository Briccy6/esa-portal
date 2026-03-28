<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LiveClass;
use App\Models\LiveClassSignal;

echo "=== Live Class Signaling Debug ===\n";

// Check if live class exists
$liveClassId = 3; // From the logs: class-3
echo "Checking LiveClass ID: $liveClassId\n";

$liveClass = LiveClass::find($liveClassId);
if ($liveClass) {
    echo "✅ LiveClass found: " . $liveClass->title . "\n";
} else {
    echo "❌ LiveClass not found\n";
    // List available classes
    $classes = LiveClass::all(['id', 'title']);
    echo "Available classes:\n";
    foreach ($classes as $class) {
        echo "  - ID: {$class->id}, Title: {$class->title}\n";
    }
}

// Check recent signals
echo "\nRecent signals:\n";
$signals = LiveClassSignal::where('live_class_id', $liveClassId)
    ->orderByDesc('id')
    ->limit(10)
    ->get(['id', 'from_peer', 'to_peer', 'type', 'created_at']);

foreach ($signals as $signal) {
    echo "  - ID: {$signal->id}, From: {$signal->from_peer}, To: {$signal->to_peer}, Type: {$signal->type}, Time: {$signal->created_at}\n";
}

// Check table structure
echo "\nTable info:\n";
$totalSignals = LiveClassSignal::count();
echo "Total signals in database: $totalSignals\n";

$classSignals = LiveClassSignal::where('live_class_id', $liveClassId)->count();
echo "Signals for class $liveClassId: $classSignals\n";

echo "\n=== End Debug ===\n";
