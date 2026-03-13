<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Debug the create() method logic
$academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();

if ($academicYear) {
    echo "Academic Year found: " . $academicYear->year . " (ID: " . $academicYear->id . ")\n";
    
    $existingTerms = \App\Models\Term::where('academic_year_id', $academicYear->id)
                         ->orderBy('name')
                         ->get();
    
    echo "Existing terms count: " . $existingTerms->count() . "\n";
    
    $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
    $createdTerms = $existingTerms->pluck('name')->toArray();
    
    echo "Created terms array: " . json_encode($createdTerms) . "\n";
    
    $nextTerm = null;
    foreach ($termOrder as $term => $order) {
        echo "Checking term: $term (order: $order)\n";
        echo "Is '$term' in created terms? " . (in_array($term, $createdTerms) ? 'YES' : 'NO') . "\n";
        
        if (!in_array($term, $createdTerms)) {
            $nextTerm = $term;
            echo "Found next term: $nextTerm\n";
            break;
        }
    }
    
    if (!$nextTerm) {
        echo "ERROR: All terms have already been created for this academic year.\n";
    } else {
        echo "SUCCESS: Next term to create: $nextTerm\n";
    }
} else {
    echo "No ongoing academic year found.\n";
}
