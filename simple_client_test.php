<?php

// Simple test to verify client management functionality
echo "=== CLIENT MANAGEMENT FUNCTIONALITY TEST ===\n";

// Test 1: Check if ClientManagementController exists
if (class_exists('App\Http\Controllers\ClientManagementController')) {
    echo "✅ ClientManagementController exists\n";
} else {
    echo "❌ ClientManagementController not found\n";
}

// Test 2: Check if Client model exists
if (class_exists('App\Models\Client')) {
    echo "✅ Client model exists\n";
} else {
    echo "❌ Client model not found\n";
}

// Test 3: Check if views exist
$viewPaths = [
    'admin.clients.index' => 'resources/views/admin/clients/index.blade.php',
    'admin.clients.show' => 'resources/views/admin/clients/show.blade.php',
    'admin.clients.add-tokens' => 'resources/views/admin/clients/add-tokens.blade.php',
    'admin.clients.subscription' => 'resources/views/admin/clients/subscription.blade.php',
];

echo "\n=== VIEW FILES CHECK ===\n";
foreach ($viewPaths as $route => $path) {
    if (file_exists($path)) {
        echo "✅ $route view exists\n";
    } else {
        echo "❌ $route view missing: $path\n";
    }
}

// Test 4: Check if route file contains client routes
$routeFile = 'routes/web.php';
if (file_exists($routeFile)) {
    $routeContent = file_get_contents($routeFile);
    if (strpos($routeContent, 'ClientManagementController') !== false) {
        echo "✅ Client management routes found in web.php\n";
    } else {
        echo "❌ Client management routes not found in web.php\n";
    }
} else {
    echo "❌ Routes file not found\n";
}

echo "\n=== TEST COMPLETED ===\n";
