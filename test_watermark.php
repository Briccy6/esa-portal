<?php

require_once __DIR__ . '/bootstrap/app.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\WatermarkService;
use App\Models\Resource;

// Test the watermark service
echo "Testing ESA Watermark Service...\n\n";

// Check if ESA logo exists
$logoPath = public_path('images/esa.png');
echo "ESA Logo Path: " . $logoPath . "\n";
echo "ESA Logo Exists: " . (file_exists($logoPath) ? "YES" : "NO") . "\n\n";

if (!file_exists($logoPath)) {
    echo "❌ ESA logo not found at: " . $logoPath . "\n";
    echo "   Please ensure the ESA logo exists at public/images/esa.png\n\n";
} else {
    echo "✅ ESA logo found!\n\n";
}

// Test watermark service instantiation
try {
    $watermarkService = new WatermarkService();
    echo "✅ WatermarkService instantiated successfully\n\n";
} catch (Exception $e) {
    echo "❌ Failed to instantiate WatermarkService: " . $e->getMessage() . "\n\n";
}

// Check for existing resources
try {
    $resources = Resource::whereNotNull('document_blob')
        ->where('watermark_applied', false)
        ->limit(3)
        ->get();
    
    echo "Found " . $resources->count() . " resources without watermarks:\n";
    
    foreach ($resources as $resource) {
        echo "- Resource ID: " . $resource->id . 
             " | Name: " . $resource->document_name . 
             " | Type: " . $resource->document_mime . "\n";
    }
    
    if ($resources->count() > 0 && isset($watermarkService)) {
        echo "\nTesting watermark application on first resource...\n";
        $testResource = $resources->first();
        
        $result = $watermarkService->applyWatermark($testResource);
        
        if ($result) {
            echo "✅ Watermark applied successfully to resource " . $testResource->id . "\n";
            echo "   Watermark applied: " . ($testResource->fresh()->watermark_applied ? "YES" : "NO") . "\n";
            echo "   Watermark path: " . $testResource->fresh()->watermark_path . "\n";
        } else {
            echo "❌ Failed to apply watermark to resource " . $testResource->id . "\n";
        }
    } else {
        echo "\nNo resources available for testing or service not instantiated.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error testing resources: " . $e->getMessage() . "\n";
}

echo "\nWatermark testing completed.\n";
