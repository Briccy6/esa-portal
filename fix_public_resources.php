<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing Public Resources Visibility...\n\n";

// Find resources that should be public but aren't
$nonPublicResources = App\Models\Resource::where('is_public', false)->get();

if ($nonPublicResources->count() > 0) {
    echo "Found {$nonPublicResources->count()} non-public resources:\n";
    foreach ($nonPublicResources as $resource) {
        echo "  - ID: {$resource->id}, Name: " . ($resource->document_name ?: 'No name') . "\n";
    }
    
    echo "\nWould you like to make all resources public? (This will make them visible on the public page)\n";
    echo "Run this command to make them public:\n";
    echo "php artisan tinker --execute=\"App\Models\Resource::where('is_public', false)->update(['is_public' => true]);\"\n";
} else {
    echo "All resources are already marked as public.\n";
}

// Check if resources have content but no pricing set
$noPriceResources = App\Models\Resource::where('is_public', true)
    ->where('is_free', false)
    ->whereNull('price')
    ->get();

if ($noPriceResources->count() > 0) {
    echo "\nFound {$noPriceResources->count()} public resources marked as paid but with no price:\n";
    foreach ($noPriceResources as $resource) {
        echo "  - ID: {$resource->id}, Name: " . ($resource->document_name ?: 'No name') . "\n";
    }
    echo "These resources need a price set to be visible.\n";
}

echo "\n=== CURRENT PUBLIC RESOURCES ===\n";
$publicResources = App\Models\Resource::where('is_public', true)->get();
echo "Public resources that should be visible:\n";
foreach ($publicResources as $resource) {
    $status = $resource->is_free ? 'FREE' : 'PAID (' . $resource->price . ' RWF)';
    $content = [];
    if ($resource->document_blob) $content[] = 'Document';
    if ($resource->video_blob) $content[] = 'Video';
    if ($resource->youtube_link) $content[] = 'YouTube';
    
    echo "  ✅ ID: {$resource->id} - {$resource->document_name} [{$status}] [" . implode(', ', $content) . "]\n";
}
