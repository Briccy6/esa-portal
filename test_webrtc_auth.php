<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Test WebRTC authentication
echo "=== WebRTC Authentication Test ===\n";

// Check if teacher is authenticated
if (auth()->guard('teacher')->check()) {
    echo "✓ Teacher is authenticated: " . auth()->guard('teacher')->user()->name . "\n";
    echo "✓ Teacher ID: " . auth()->guard('teacher')->id() . "\n";
    echo "✓ Session ID: " . session()->getId() . "\n";
} else {
    echo "✗ Teacher is NOT authenticated\n";
}

// Check if WebRTC routes are accessible
echo "\n=== WebRTC Route Test ===\n";

try {
    // Test signaling route
    $url = '/webrtc/signaling/test-room';
    echo "Testing route: $url\n";
    
    $route = $app['router']->getRoutes()->get($request->create($url, 'GET'));
    if ($route) {
        echo "✓ Route found: " . $route->getName() . "\n";
        echo "✓ Middleware: " . implode(', ', $route->middleware()) . "\n";
    } else {
        echo "✗ Route NOT found\n";
    }
} catch (Exception $e) {
    echo "✗ Error testing route: " . $e->getMessage() . "\n";
}

echo "\n=== Session Info ===\n";
echo "Session ID: " . session()->getId() . "\n";
echo "Session driver: " . config('session.driver') . "\n";
echo "Auth guard: " . auth()->getDefaultDriver() . "\n";

$kernel->terminate($request, $response);
