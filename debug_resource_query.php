<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Resource queries...\n";

// Test the correct query
try {
    echo "Testing correct query (price):\n";
    $result = \App\Models\Resource::where('is_free', false)->sum('price');
    echo "Result: $result\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Test the wrong query to reproduce error
try {
    echo "Testing wrong query (amount):\n";
    $result = \App\Models\Resource::where('is_free', false)->sum('amount');
    echo "Result: $result\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "Done.\n";
