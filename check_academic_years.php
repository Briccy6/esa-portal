<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Academic Years Check ===\n\n";
    
    // Get all academic years
    $academicYears = \App\Models\AcademicYear::all();
    
    echo "Available academic years:\n";
    foreach ($academicYears as $year) {
        echo "   - ID: {$year->id}, Name: {$year->name}, Ongoing: " . ($year->is_ongoing ? 'YES' : 'NO') . "\n";
        echo "     Start: {$year->starting_date}, End: {$year->expiry_date}\n";
    }
    
    if ($academicYears->isEmpty()) {
        echo "   No academic years found!\n";
    }
    
    // Check specifically for ID 4
    echo "\nChecking for academic year ID 4:\n";
    $year4 = \App\Models\AcademicYear::find(4);
    if ($year4) {
        echo "   ✅ Academic year 4 exists: {$year4->name}\n";
    } else {
        echo "   ❌ Academic year 4 does NOT exist!\n";
        
        // Find the ongoing academic year
        $ongoingYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
        if ($ongoingYear) {
            echo "   💡 Suggestion: Use academic year ID {$ongoingYear->id} ({$ongoingYear->name}) instead\n";
        } else {
            echo "   ⚠️  No ongoing academic year found!\n";
        }
    }
    
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
