<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Test the exact logic from TermController create method
use App\Models\AcademicYear;
use App\Models\Term;

$academicYear = AcademicYear::where('is_ongoing', true)->first();

if (!$academicYear) {
    echo "ERROR: No ongoing academic year found.\n";
    exit;
}

echo "Academic Year: " . $academicYear->year . " (ID: " . $academicYear->id . ")\n";

$existingTerms = Term::where('academic_year_id', $academicYear->id)
                     ->orderBy('name')
                     ->get();

echo "Existing terms count: " . $existingTerms->count() . "\n";

$terms = $existingTerms;

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

echo "\nFinal result:\n";
echo "nextTerm: " . ($nextTerm ?? 'NULL') . "\n";
echo "nextTerm === null: " . ($nextTerm === null ? 'TRUE' : 'FALSE') . "\n";
echo "nextTerm === '': " . ($nextTerm === '' ? 'TRUE' : 'FALSE') . "\n";
echo "!nextTerm: " . (!$nextTerm ? 'TRUE' : 'FALSE') . "\n";

if (!$nextTerm) {
    echo "\nERROR: All terms have already been created for this academic year.\n";
} else {
    echo "\nSUCCESS: Next term to create is $nextTerm\n";
}
