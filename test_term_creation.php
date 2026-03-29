<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Testing Term 3 Creation ===\n";

// Get the ongoing academic year
$academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
if (!$academicYear) {
    echo "No ongoing academic year found!\n";
    exit;
}

echo "Academic Year: {$academicYear->year}\n";

// Check existing terms
$existingTerms = \App\Models\Term::where('academic_year_id', $academicYear->id)->get();
echo "Existing terms count: " . $existingTerms->count() . "\n";

// Determine next term
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
    echo "All terms already exist!\n";
    exit;
}

echo "Next term to create: {$nextTerm}\n";

// Simulate the exact form data that would be submitted
$formData = [
    'academic_year_id' => $academicYear->id,
    'name' => $nextTerm,
    'start_date' => '2026-04-06', // Day after Term 2 ends
    'end_date' => '2026-07-31'
];

echo "\nForm data to be submitted:\n";
print_r($formData);

// Test the validation rules exactly as in the controller
$rules = [
    'academic_year_id' => 'required|exists:academic_years,id',
    'name'             => 'required|string|in:Term 1,Term 2,Term 3',
    'start_date'       => 'required|date',
    'end_date'         => 'required|date|after_or_equal:start_date',
];

$validator = \Validator::make($formData, $rules);

if ($validator->fails()) {
    echo "\n❌ Validation failed:\n";
    foreach ($validator->errors()->all() as $error) {
        echo "- {$error}\n";
    }
} else {
    echo "\n✅ Validation passed!\n";
    
    // Test the actual creation logic
    try {
        // Check if Term 2 exists (required before Term 3)
        $term2 = $existingTerms->firstWhere('name', 'Term 2');
        if (!$term2) {
            echo "❌ Term 2 must be created first!\n";
            exit;
        }
        
        echo "✅ Term 2 exists, can create Term 3\n";
        
        // Check date constraints
        $minStartDate = \Carbon\Carbon::parse($term2->end_date)->addDay();
        $maxEndDate = \Carbon\Carbon::parse($academicYear->expiry_date);
        
        $startingDate = \Carbon\Carbon::parse($formData['start_date']);
        $endingDate = \Carbon\Carbon::parse($formData['end_date']);
        
        echo "Min start date: {$minStartDate->format('Y-m-d')}\n";
        echo "Max end date: {$maxEndDate->format('Y-m-d')}\n";
        echo "Proposed start: {$startingDate->format('Y-m-d')}\n";
        echo "Proposed end: {$endingDate->format('Y-m-d')}\n";
        
        if ($startingDate->lt($minStartDate)) {
            echo "❌ Start date too early!\n";
        } elseif ($endingDate->gt($maxEndDate)) {
            echo "❌ End date too late!\n";
        } else {
            echo "✅ Date constraints satisfied!\n";
            
            // Actually create the term
            $term = \App\Models\Term::create([
                'academic_year_id' => $academicYear->id,
                'name' => $formData['name'],
                'start_date' => $startingDate->format('Y-m-d'),
                'end_date' => $endingDate->format('Y-m-d'),
            ]);
            
            echo "🎉 Term 3 created successfully! ID: {$term->id}\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}
