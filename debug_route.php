<?php
// Add a debug route to test_signaling_routes.php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Route;

// Add a simple test route
Route::get('/debug-test', function() {
    return response()->json([
        'message' => 'Debug route works',
        'timestamp' => now(),
        'database_connection' => \DB::connection()->getDatabaseName()
    ]);
});

echo "Debug route added. You can test it at: http://localhost:8000/debug-test\n";
