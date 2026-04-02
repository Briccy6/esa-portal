<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Fixing public resource access...\n";

// Get the first resource
$resource = App\Models\Resource::first();

if (!$resource) {
    echo "No resources found in database.\n";
    exit;
}

echo "Current resource details:\n";
echo "ID: {$resource->id}\n";
echo "Name: " . ($resource->document_name ?: $resource->video_name ?: 'No name') . "\n";
echo "Is Public: " . ($resource->is_public ? 'Yes' : 'No') . "\n";
echo "Is Free: " . ($resource->is_free ? 'Yes' : 'No') . "\n";
echo "Price: {$resource->price}\n";

// Make the resource public
$resource->is_public = true;
$resource->save();

echo "\nResource has been made public!\n";
echo "New status: " . ($resource->is_public ? 'Public' : 'Private') . "\n";

// Verify the change
$publicResources = App\Models\Resource::where('is_public', true)->count();
echo "\nTotal public resources now: {$publicResources}\n";

echo "\nYou can now access the resource at: /resources/{$resource->id}\n";
