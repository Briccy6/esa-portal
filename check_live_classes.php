<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== LIVE CLASSES DEBUG ===\n";

// Check all live classes
$allClasses = \App\Models\LiveClass::all();
echo "Total live classes in database: " . $allClasses->count() . "\n\n";

foreach ($allClasses as $class) {
    echo "ID: {$class->id}\n";
    echo "Title: {$class->title}\n";
    echo "Status: {$class->status}\n";
    echo "Start Time: {$class->start_time}\n";
    echo "End Time: {$class->end_time}\n";
    echo "Created At: {$class->created_at}\n";
    echo "---\n";
}

echo "\n=== QUERY FILTERS TEST ===\n";

// Test the exact query from homepageLiveClasses
$query = \App\Models\LiveClass::with(['branches', 'courses', 'lessons', 'teacher'])
    ->whereIn('status', ['scheduled', 'active'])
    ->where('start_time', '<=', now())
    ->where('end_time', '>=', now())
    ->orderBy('start_time', 'asc');

$filteredClasses = $query->get();
echo "Filtered classes (homepage query): " . $filteredClasses->count() . "\n\n";

foreach ($filteredClasses as $class) {
    echo "ID: {$class->id}, Title: {$class->title}, Status: {$class->status}\n";
}

echo "\n=== TIME COMPARISON ===\n";
echo "Current time: " . now() . "\n";

// Check if there are any classes with different time zones
$futureClasses = \App\Models\LiveClass::where('start_time', '>', now())->get();
echo "Future classes: " . $futureClasses->count() . "\n";

$pastClasses = \App\Models\LiveClass::where('end_time', '<', now())->get();
echo "Past classes: " . $pastClasses->count() . "\n";

$activeClasses = \App\Models\LiveClass::whereIn('status', ['scheduled', 'active'])->get();
echo "Active/Scheduled classes: " . $activeClasses->count() . "\n";

echo "\n=== RECOMMENDATIONS ===\n";

if ($allClasses->count() > 0 && $filteredClasses->count() === 0) {
    echo "ISSUE FOUND: Classes exist but are being filtered out.\n";
    echo "Possible causes:\n";
    echo "1. Status is not 'scheduled' or 'active'\n";
    echo "2. Start time is in the future\n";
    echo "3. End time is in the past\n";
    echo "4. Time zone mismatch\n";
}
