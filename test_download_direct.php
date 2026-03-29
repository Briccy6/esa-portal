<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing direct download functionality...\n";

try {
    // Simulate the download request
    $resourceId = 1;
    $resource = \App\Models\Resource::where('is_public', true)->findOrFail($resourceId);
    
    echo "Resource found:\n";
    echo "ID: {$resource->id}\n";
    echo "Name: {$resource->document_name}\n";
    echo "Is Free: " . ($resource->is_free ? 'Yes' : 'No') . "\n";
    echo "Has Document: " . ($resource->document_blob ? 'Yes' : 'No') . "\n";
    echo "Document MIME: {$resource->document_mime}\n";
    echo "Document Size: " . strlen($resource->document_blob) . " bytes\n";
    
    if (!$resource->document_blob) {
        echo "ERROR: No document blob found\n";
        exit(1);
    }
    
    if (!$resource->is_free && $resource->price > 0) {
        echo "ERROR: Resource is not free - should redirect to payment\n";
        exit(1);
    }
    
    echo "SUCCESS: Download should work\n";
    echo "Headers that would be sent:\n";
    echo "Content-Type: {$resource->document_mime}\n";
    echo "Content-Disposition: attachment; filename=\"{$resource->document_name}\"\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
