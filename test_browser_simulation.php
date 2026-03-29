<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing browser request simulation...\n";

try {
    // Test the actual route handling
    $request = \Illuminate\Http\Request::create('/resources/1/download', 'GET');
    
    // Get the router
    $router = app('router');
    
    // Find the route
    $route = $router->getRoutes()->match($request);
    
    echo "Route found: " . $route->getName() . "\n";
    echo "URI: " . $route->uri() . "\n";
    echo "Action: " . $route->getActionName() . "\n";
    
    // Dispatch the request
    $response = $router->dispatch($request);
    
    echo "Response Status: " . $response->getStatusCode() . "\n";
    
    if ($response->getStatusCode() === 200) {
        echo "SUCCESS: Download works!\n";
        echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
        echo "Content-Disposition: " . $response->headers->get('Content-Disposition') . "\n";
        echo "Content Length: " . strlen($response->getContent()) . " bytes\n";
    } else {
        echo "ERROR: Got status " . $response->getStatusCode() . "\n";
        echo "Content: " . $response->getContent() . "\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
