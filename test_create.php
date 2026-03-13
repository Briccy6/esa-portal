<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Simulate the TermController create() method
use App\Http\Controllers\TermController;

$controller = new TermController();

// Create a mock request
$request = new Illuminate\Http\Request();

try {
    $response = $controller->create();
    echo "Create method executed successfully.\n";
    echo "Response type: " . get_class($response) . "\n";
    
    if (method_exists($response, 'getData')) {
        $data = $response->getData();
        echo "Response data:\n";
        print_r($data);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
