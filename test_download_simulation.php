<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing download route simulation...\n";

try {
    // Create a mock request
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['HTTP_HOST'] = 'localhost:8000';
    $_SERVER['REQUEST_URI'] = '/resources/1/download';
    
    // Get the controller
    $controller = new \App\Http\Controllers\PublicResourceController();
    
    echo "Calling downloadDocument method...\n";
    
    // This should trigger the download
    $response = $controller->downloadDocument(1);
    
    echo "Response type: " . get_class($response) . "\n";
    
    if ($response instanceof \Illuminate\Http\Response) {
        echo "Status: " . $response->getStatusCode() . "\n";
        echo "Headers:\n";
        foreach ($response->headers as $key => $values) {
            echo "  $key: " . implode(', ', $values) . "\n";
        }
        echo "Content size: " . strlen($response->getContent()) . " bytes\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
