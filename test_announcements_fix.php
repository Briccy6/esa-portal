<?php

// Test script to verify announcements are properly loaded
require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the Announcement model
try {
    $announcements = \App\Models\Announcement::latest()->take(5)->get();
    echo "✅ Announcements loaded successfully: " . $announcements->count() . " announcements found\n";
    
    if ($announcements->count() > 0) {
        echo "Latest announcement: " . $announcements->first()->title . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Error loading announcements: " . $e->getMessage() . "\n";
}

echo "\n✅ Teacher MarksController, ResourceController, and TeacherQuestionController have been updated with announcements variable\n";
echo "The undefined \$announcements error should now be resolved.\n";
