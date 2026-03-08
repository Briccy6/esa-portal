<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Enable query logging
DB::enableQueryLog();

echo "Testing dashboard method...\n";

// Get the admin auth controller
$controller = new \App\Http\Controllers\AdminAuthController();

// Try to call the dashboard method
try {
    // This should trigger the error
    $controller->dashboard();
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    
    // Print the query log
    $queries = DB::getQueryLog();
    echo "\nQuery Log:\n";
    foreach ($queries as $query) {
        echo "Query: " . $query['query'] . "\n";
        echo "Bindings: " . json_encode($query['bindings']) . "\n";
        echo "Time: " . $query['time'] . "\n\n";
    }
}

echo "Done.\n";
