<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "=== Student Subscriptions Table Structure ===\n";

// Get the table structure
$tableStructure = \DB::select("DESCRIBE student_subscriptions");

echo "Current columns in student_subscriptions table:\n";
foreach ($tableStructure as $column) {
    echo "- {$column->Field} ({$column->Type})\n";
}

echo "\n=== Sample Data ===\n";
$sampleData = \DB::table('student_subscriptions')->limit(1)->get();
if ($sampleData->isNotEmpty()) {
    $sample = $sampleData->first();
    echo "Sample record:\n";
    foreach ((array)$sample as $key => $value) {
        echo "- {$key}: {$value}\n";
    }
} else {
    echo "No records found in student_subscriptions table.\n";
}
