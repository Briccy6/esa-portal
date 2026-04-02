<?php

// Simple test to check if routes are properly defined
echo "=== Testing Route Definitions ===\n\n";

// Check if the routes file exists and is readable
$routesFile = __DIR__ . '/routes/web.php';
if (file_exists($routesFile)) {
    echo "✅ Routes file exists\n";
    
    $routesContent = file_get_contents($routesFile);
    
    // Check for specific route definitions
    if (strpos($routesContent, 'course.assign-fees') !== false) {
        echo "✅ course.assign-fees route found\n";
    } else {
        echo "❌ course.assign-fees route NOT found\n";
    }
    
    if (strpos($routesContent, 'course.store-fees') !== false) {
        echo "✅ course.store-fees route found\n";
    } else {
        echo "❌ course.store-fees route NOT found\n";
    }
    
    // Extract the specific route lines
    $lines = explode("\n", $routesContent);
    foreach ($lines as $lineNum => $line) {
        if (strpos($line, 'assign-fees') !== false || strpos($line, 'store-fees') !== false) {
            echo "Line " . ($lineNum + 1) . ": " . trim($line) . "\n";
        }
    }
} else {
    echo "❌ Routes file not found\n";
}

// Check if controller exists
$controllerFile = __DIR__ . '/app/Http/Controllers/CourseController.php';
if (file_exists($controllerFile)) {
    echo "\n✅ CourseController exists\n";
    
    $controllerContent = file_get_contents($controllerFile);
    
    if (strpos($controllerContent, 'assignFees') !== false) {
        echo "✅ assignFees method found\n";
    } else {
        echo "❌ assignFees method NOT found\n";
    }
    
    if (strpos($controllerContent, 'storeFees') !== false) {
        echo "✅ storeFees method found\n";
    } else {
        echo "❌ storeFees method NOT found\n";
    }
} else {
    echo "\n❌ CourseController not found\n";
}

// Check if view exists
$viewFile = __DIR__ . '/resources/views/admin/course/assign_fees.blade.php';
if (file_exists($viewFile)) {
    echo "\n✅ assign_fees view exists\n";
} else {
    echo "\n❌ assign_fees view NOT found\n";
}

echo "\n=== Route Test Complete ===\n";
