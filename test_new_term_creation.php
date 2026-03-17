<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Testing New Term Creation ===\n\n";
    
    // Get the ongoing academic year
    $ongoingYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
    
    echo "Using academic year ID: {$ongoingYear->id}\n\n";
    
    // Check existing terms
    $existingTerms = \App\Models\Term::where('academic_year_id', $ongoingYear->id)->get();
    echo "Existing terms:\n";
    foreach ($existingTerms as $term) {
        echo "   - {$term->name} (ID: {$term->id})\n";
    }
    
    // Find next available term
    $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
    $createdTerms = $existingTerms->pluck('name')->toArray();
    
    $nextTerm = null;
    foreach ($termOrder as $term => $order) {
        if (!in_array($term, $createdTerms)) {
            $nextTerm = $term;
            break;
        }
    }
    
    if (!$nextTerm) {
        echo "\n❌ All terms already exist!\n";
        exit(0);
    }
    
    echo "\nNext available term: {$nextTerm}\n";
    
    // Test creating the next term
    $testData = [
        'academic_year_id' => $ongoingYear->id,
        'name' => $nextTerm,
        'start_date' => '2025-09-01',
        'end_date' => '2025-12-15',
    ];
    
    echo "\nTesting term creation:\n";
    echo "Data: " . json_encode($testData) . "\n";
    
    try {
        $term = \App\Models\Term::create($testData);
        echo "✅ Term created successfully! ID: {$term->id}\n";
        echo "🧹 Cleaning up test term...\n";
        $term->delete();
        echo "✅ Test completed successfully!\n";
        
    } catch (\Exception $e) {
        echo "❌ Term creation failed: " . $e->getMessage() . "\n";
    }
    
} catch (\Exception $e) {
    echo "Global error: " . $e->getMessage() . "\n";
}
