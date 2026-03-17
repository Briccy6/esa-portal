<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Testing Term Creation Fix ===\n\n";
    
    // Get the ongoing academic year
    $ongoingYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
    
    if (!$ongoingYear) {
        echo "❌ No ongoing academic year found!\n";
        exit(1);
    }
    
    echo "✅ Found ongoing academic year:\n";
    echo "   ID: {$ongoingYear->id}\n";
    echo "   Name: {$ongoingYear->name}\n";
    echo "   Start: {$ongoingYear->starting_date}\n";
    echo "   End: {$ongoingYear->expiry_date}\n\n";
    
    // Test creating a term with the correct academic year
    $testData = [
        'academic_year_id' => $ongoingYear->id,
        'name' => 'Term 1',
        'start_date' => '2025-09-01',
        'end_date' => '2025-12-15',
    ];
    
    echo "Testing term creation with correct academic year ID ({$ongoingYear->id}):\n";
    echo "Data: " . json_encode($testData) . "\n";
    
    try {
        // Check if term already exists
        $existingTerm = \App\Models\Term::where('academic_year_id', $ongoingYear->id)
                                        ->where('name', 'Term 1')
                                        ->first();
        
        if ($existingTerm) {
            echo "⚠️  Term 1 already exists for this academic year. Deleting it first...\n";
            $existingTerm->delete();
        }
        
        // Create the term
        $term = \App\Models\Term::create($testData);
        echo "✅ Term created successfully! ID: {$term->id}\n";
        
        // Clean up - remove the test term
        $term->delete();
        echo "🧹 Test term cleaned up\n";
        
    } catch (\Exception $e) {
        echo "❌ Term creation failed: " . $e->getMessage() . "\n";
    }
    
    // Test with invalid academic year (should be handled by controller)
    echo "\nTesting with invalid academic year ID (999):\n";
    $invalidData = [
        'academic_year_id' => 999,
        'name' => 'Term 1',
        'start_date' => '2025-09-01',
        'end_date' => '2025-12-15',
    ];
    
    try {
        $term = \App\Models\Term::create($invalidData);
        echo "❌ Unexpected success with invalid academic year!\n";
        $term->delete();
    } catch (\Exception $e) {
        echo "✅ Correctly failed with invalid academic year: " . $e->getMessage() . "\n";
    }
    
    echo "\n=== Test Complete ===\n";
    
} catch (\Exception $e) {
    echo "Global error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
