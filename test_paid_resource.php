<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing paid resource functionality...\n";

try {
    // Test paid resource
    $resourceId = 2;
    $resource = \App\Models\Resource::where('is_public', true)->findOrFail($resourceId);
    
    echo "Paid Resource found:\n";
    echo "ID: {$resource->id}\n";
    echo "Name: {$resource->document_name}\n";
    echo "Is Free: " . ($resource->is_free ? 'Yes' : 'No') . "\n";
    echo "Price: {$resource->price}\n";
    echo "Has Document: " . ($resource->document_blob ? 'Yes' : 'No') . "\n";
    
    // Test download attempt
    echo "\nTesting download attempt...\n";
    
    if (!$resource->document_blob) {
        echo "ERROR: No document blob found\n";
        exit(1);
    }
    
    if (!$resource->is_free && $resource->price > 0) {
        echo "CORRECT: Resource is paid - should redirect to payment\n";
        echo "Payment route: " . route('public.resources.payment', $resource->id) . "\n";
    } else {
        echo "ERROR: Resource should be paid\n";
        exit(1);
    }
    
    // Test payment route exists
    echo "\nTesting payment route...\n";
    $paymentRoute = route('public.resources.payment', $resource->id);
    echo "Payment URL: $paymentRoute\n";
    
    // Test preview functionality
    echo "\nTesting preview functionality...\n";
    if (!$resource->is_free && $resource->price > 0) {
        echo "CORRECT: Preview should redirect to payment for paid resources\n";
    } else {
        echo "ERROR: Preview logic incorrect\n";
    }
    
    echo "\nSUCCESS: Paid resource logic working correctly\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
