<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking public resources...\n";
echo "Total resources: " . App\Models\Resource::count() . "\n";
echo "Public resources: " . App\Models\Resource::where('is_public', true)->count() . "\n\n";

$resources = App\Models\Resource::where('is_public', true)->get(['id', 'document_name', 'is_free', 'price', 'document_blob', 'video_blob', 'youtube_link']);

foreach ($resources as $resource) {
    echo "ID: {$resource->id}\n";
    echo "Name: " . ($resource->document_name ?: 'null') . "\n";
    echo "Free: " . ($resource->is_free ? 'yes' : 'no') . "\n";
    echo "Price: {$resource->price}\n";
    echo "Has document: " . ($resource->document_blob ? 'yes' : 'no') . "\n";
    echo "Has video: " . ($resource->video_blob ? 'yes' : 'no') . "\n";
    echo "Has YouTube: " . ($resource->youtube_link ? 'yes' : 'no') . "\n";
    echo "---\n";
}
