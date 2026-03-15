<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Reset and Reapply Watermark ===\n";

$resource = \App\Models\Resource::find(1);
if ($resource) {
    echo "Resetting watermark status...\n";
    
    // Reset watermark status
    $resource->update([
        'watermark_applied' => false,
        'watermark_path' => null,
    ]);
    
    echo "Watermark status reset.\n";
    
    // Now reapply watermark properly
    echo "Reapplying watermark with TCPDF...\n";
    
    $watermarkService = app(\App\Services\WatermarkService::class);
    $result = $watermarkService->applyWatermark($resource);
    
    echo "Watermark application result: " . ($result ? 'Success' : 'Failed') . "\n";
    
    // Check final status
    $resource->refresh();
    echo "Final Watermark Applied: " . ($resource->watermark_applied ? 'Yes' : 'No') . "\n";
    echo "Final Watermark Path: " . ($resource->watermark_path ?? 'None') . "\n";
    echo "Final Document Size: " . strlen($resource->document_blob ?? '') . " bytes\n";
    
} else {
    echo "Resource not found.\n";
}

echo "\n=== Complete ===\n";
