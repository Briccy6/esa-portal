<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Watermark Investigation ===\n";

$resource = \App\Models\Resource::find(1);
if ($resource) {
    echo "Resource ID: " . $resource->id . "\n";
    echo "Document Name: " . $resource->document_name . "\n";
    echo "Document MIME: " . $resource->document_mime . "\n";
    echo "Is Public: " . ($resource->is_public ? 'Yes' : 'No') . "\n";
    echo "Watermark Applied: " . ($resource->watermark_applied ? 'Yes' : 'No') . "\n";
    echo "Watermark Path: " . ($resource->watermark_path ?? 'None') . "\n";
    echo "Document Blob Size: " . strlen($resource->document_blob ?? '') . " bytes\n";
    
    // Check if ESA logo exists
    $logoPath = public_path('images/esa.png');
    echo "ESA Logo Path: " . $logoPath . "\n";
    echo "ESA Logo Exists: " . (file_exists($logoPath) ? 'Yes' : 'No') . "\n";
    
    if (file_exists($logoPath)) {
        echo "ESA Logo Size: " . filesize($logoPath) . " bytes\n";
    }
    
    // Check if TCPDF is available
    echo "TCPDF Available: " . (class_exists('\TCPDF') ? 'Yes' : 'No') . "\n";
    
    // Check if Intervention Image is available
    echo "Intervention Image Available: " . (class_exists('\Intervention\Image\ImageManager') ? 'Yes' : 'No') . "\n";
    
} else {
    echo "Resource not found.\n";
}

echo "\n=== Investigation Complete ===\n";
