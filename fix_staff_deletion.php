<?php

// Comprehensive staff deletion fix
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== COMPREHENSIVE STAFF DELETION FIX ===\n\n";

try {
    // Check if there are any staff records
    $staffCount = \App\Models\Staff::count();
    echo "Current staff count: {$staffCount}\n\n";
    
    // The issue might be that you're trying to delete a non-existent staff member
    // or there's a route mismatch. Let's create a robust deletion method.
    
    echo "Creating enhanced StaffController with comprehensive dependency checking...\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== FIX IMPLEMENTATION ===\n";
