<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the dashboard data
try {
    $controller = new \App\Http\Controllers\AdminAuthController();
    
    // Create a mock request with date filters
    $request = new \Illuminate\Http\Request();
    $request->merge([
        'start_date' => '2024-01-01',
        'end_date' => '2024-12-31'
    ]);
    
    echo "Testing admin dashboard with date filters...\n";
    
    // Call the dashboard method
    $response = $controller->dashboard($request);
    
    echo "Dashboard method executed successfully!\n";
    echo "Response type: " . get_class($response) . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
