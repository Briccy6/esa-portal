<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Debugging school information images...\n";

try {
    $schoolInfo = \App\Models\SchoolInformation::getActive();
    
    if (!$schoolInfo) {
        echo "No active school information found. Creating test data...\n";
        
        // Create test school information
        $schoolInfo = new \App\Models\SchoolInformation();
        $schoolInfo->school_name = 'Test School';
        $schoolInfo->address = 'Test Address';
        $schoolInfo->is_active = true;
        $schoolInfo->save();
        
        echo "Created test school information with ID: " . $schoolInfo->id . "\n";
    }
    
    echo "School Information ID: " . $schoolInfo->id . "\n";
    echo "School Name: " . $schoolInfo->school_name . "\n";
    
    // Check raw attributes
    echo "\n--- Raw Attributes ---\n";
    echo "logo: " . (isset($schoolInfo->attributes['logo']) ? 'YES (' . strlen($schoolInfo->attributes['logo']) . ' bytes)' : 'NO') . "\n";
    echo "logo_path: " . ($schoolInfo->logo_path ?? 'NULL') . "\n";
    echo "principal_signature: " . ($schoolInfo->principal_signature ?? 'NULL') . "\n";
    echo "principal_signature_data: " . (isset($schoolInfo->attributes['principal_signature_data']) ? 'YES (' . strlen($schoolInfo->attributes['principal_signature_data']) . ' bytes)' : 'NO') . "\n";
    echo "stamp_signature: " . ($schoolInfo->stamp_signature ?? 'NULL') . "\n";
    echo "stamp_signature_data: " . (isset($schoolInfo->attributes['stamp_signature_data']) ? 'YES (' . strlen($schoolInfo->attributes['stamp_signature_data']) . ' bytes)' : 'NO') . "\n";
    
    // Check URL accessors
    echo "\n--- URL Accessors ---\n";
    $logoUrl = $schoolInfo->logo_url;
    echo "logo_url: " . ($logoUrl ?? 'NULL') . "\n";
    
    $signatureUrl = $schoolInfo->principal_signature_url;
    echo "principal_signature_url: " . ($signatureUrl ?? 'NULL') . "\n";
    
    $stampUrl = $schoolInfo->stamp_signature_url;
    echo "stamp_signature_url: " . ($stampUrl ?? 'NULL') . "\n";
    
    // Test if URLs are valid
    echo "\n--- URL Tests ---\n";
    if ($logoUrl && strpos($logoUrl, 'data:') === 0) {
        echo "logo_url: Valid data URL\n";
    } elseif ($logoUrl) {
        echo "logo_url: File URL - " . $logoUrl . "\n";
    } else {
        echo "logo_url: NULL or empty\n";
    }
    
    if ($signatureUrl && strpos($signatureUrl, 'data:') === 0) {
        echo "principal_signature_url: Valid data URL\n";
    } elseif ($signatureUrl) {
        echo "principal_signature_url: File URL - " . $signatureUrl . "\n";
    } else {
        echo "principal_signature_url: NULL or empty\n";
    }
    
    if ($stampUrl && strpos($stampUrl, 'data:') === 0) {
        echo "stamp_signature_url: Valid data URL\n";
    } elseif ($stampUrl) {
        echo "stamp_signature_url: File URL - " . $stampUrl . "\n";
    } else {
        echo "stamp_signature_url: NULL or empty\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDebug completed.\n";
