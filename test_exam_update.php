<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

// Initialize Laravel
$app->make('Illuminate\Contracts\Console\Kernel');

$exam = \App\Models\Exam::find(28);
echo 'Current exam public status: ' . ($exam->is_public ? 'true' : 'false') . PHP_EOL;
echo 'Current exam public price: ' . $exam->public_price . PHP_EOL;

// Test update
$exam->update([
    'is_public' => true,
    'public_price' => 100.50
]);

$exam = \App\Models\Exam::find(28);
echo 'After update - public status: ' . ($exam->is_public ? 'true' : 'false') . PHP_EOL;
echo 'After update - public price: ' . $exam->public_price . PHP_EOL;
