<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check authentication status
echo "=== Admin Authentication Debug ===\n";
echo "Session ID: " . session()->getId() . "\n";

// Check if admin guard is working
$adminGuard = auth()->guard('admin');
echo "Admin Guard Driver: " . get_class($adminGuard) . "\n";
echo "Admin Check Result: " . ($adminGuard->check() ? 'TRUE' : 'FALSE') . "\n";

if ($adminGuard->check()) {
    $admin = $adminGuard->user();
    echo "Logged in Admin ID: " . $admin->id . "\n";
    echo "Logged in Admin Email: " . $admin->email . "\n";
} else {
    echo "No admin logged in\n";
}

// Check session data
echo "\n=== Session Data ===\n";
echo "Session Data: " . print_r(session()->all(), true) . "\n";

// Check if admin login route exists
echo "\n=== Route Check ===\n";
try {
    $route = \Illuminate\Support\Facades\Route::has('admin.blogs.create');
    echo "admin.blogs.create route exists: " . ($route ? 'TRUE' : 'FALSE') . "\n";
} catch (Exception $e) {
    echo "Route check error: " . $e->getMessage() . "\n";
}

// Check middleware registration
echo "\n=== Middleware Check ===\n";
$middleware = \Illuminate\Foundation\Application::getInstance()->make('middleware');
echo "Auth middleware registered: " . (class_exists('Illuminate\Auth\Middleware\Authenticate') ? 'TRUE' : 'FALSE') . "\n";

?>
