<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Testing Public Resources Access...\n\n";

// Test 1: Check if resources are properly marked as public
echo "=== PUBLIC RESOURCE CHECK ===\n";
$allResources = App\Models\Resource::all();
foreach ($allResources as $resource) {
    echo "Resource ID: {$resource->id}\n";
    echo "  Name: " . ($resource->document_name ?: 'No document name') . "\n";
    echo "  Is Public: " . ($resource->is_public ? 'YES' : 'NO') . "\n";
    echo "  Is Free: " . ($resource->is_free ? 'YES' : 'NO') . "\n";
    echo "  Price: {$resource->price}\n";
    echo "  Has Document: " . ($resource->document_blob ? 'YES' : 'NO') . "\n";
    echo "  Has Video: " . ($resource->video_blob ? 'YES' : 'NO') . "\n";
    echo "  Has YouTube: " . ($resource->youtube_link ? 'YES' : 'NO') . "\n";
    echo "---\n";
}

// Test 2: Check what the public query returns
echo "\n=== PUBLIC QUERY RESULTS ===\n";
$publicResources = App\Models\Resource::with(['teacher', 'course', 'branch', 'lesson'])
    ->where('is_public', true)
    ->where(function($q) {
        $q->where('is_free', true)
          ->orWhereNotNull('price');
    })
    ->where(function($q) {
        $q->whereNotNull('document_blob')
          ->orWhereNotNull('video_blob')
          ->orWhereNotNull('youtube_link');
    })
    ->get();

echo "Found {$publicResources->count()} public resources that meet criteria:\n";
foreach ($publicResources as $resource) {
    echo "  - ID: {$resource->id}, Name: " . ($resource->document_name ?: 'No name') . "\n";
}

// Test 3: Check specific issues
echo "\n=== POTENTIAL ISSUES ===\n";

// Check if any resources have null blobs but should be visible
$nullBlobs = App\Models\Resource::where('is_public', true)
    ->whereNull('document_blob')
    ->whereNull('video_blob')
    ->whereNull('youtube_link')
    ->get();

if ($nullBlobs->count() > 0) {
    echo "Found {$nullBlobs->count()} public resources with no content:\n";
    foreach ($nullBlobs as $resource) {
        echo "  - ID: {$resource->id}, Name: " . ($resource->document_name ?: 'No name') . "\n";
    }
} else {
    echo "All public resources have at least one type of content.\n";
}

// Check if any resources have invalid price data
$invalidPrice = App\Models\Resource::where('is_public', true)
    ->where('is_free', false)
    ->where(function($q) {
        $q->whereNull('price')
          ->orWhere('price', '<', 0);
    })
    ->get();

if ($invalidPrice->count() > 0) {
    echo "Found {$invalidPrice->count()} paid resources with invalid pricing:\n";
    foreach ($invalidPrice as $resource) {
        echo "  - ID: {$resource->id}, Price: {$resource->price}\n";
    }
} else {
    echo "All paid resources have valid pricing.\n";
}

echo "\n=== SUMMARY ===\n";
echo "Total resources in database: " . App\Models\Resource::count() . "\n";
echo "Resources marked as public: " . App\Models\Resource::where('is_public', true)->count() . "\n";
echo "Public resources visible on website: {$publicResources->count()}\n";

if ($publicResources->count() > 0) {
    echo "✅ Public resources are working correctly!\n";
} else {
    echo "❌ No public resources are visible - there might be an issue.\n";
}
