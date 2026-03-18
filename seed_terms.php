<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Seeding terms for academic year...\n";

// Get the current academic year
$academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();

if (!$academicYear) {
    echo "No ongoing academic year found. Creating academic year first...\n";
    \App\Models\AcademicYear::create([
        'year' => '2025-2026',
        'starting_date' => '2025-09-01',
        'expiry_date' => '2026-08-31',
        'is_ongoing' => true,
    ]);
    $academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();
}

echo "Found academic year: {$academicYear->year}\n";

// Check if terms already exist
$existingTerms = \App\Models\Term::where('academic_year_id', $academicYear->id)->count();
if ($existingTerms > 0) {
    echo "Terms already exist ({$existingTerms} found). Skipping seeding.\n";
} else {
    // Create terms
    \App\Models\Term::create([
        'academic_year_id' => $academicYear->id,
        'name' => 'Term 1',
        'start_date' => '2025-09-01',
        'end_date' => '2025-12-15',
    ]);

    \App\Models\Term::create([
        'academic_year_id' => $academicYear->id,
        'name' => 'Term 2',
        'start_date' => '2026-01-05',
        'end_date' => '2026-04-10',
    ]);

    \App\Models\Term::create([
        'academic_year_id' => $academicYear->id,
        'name' => 'Term 3',
        'start_date' => '2026-04-20',
        'end_date' => '2026-08-15',
    ]);

    echo "Created 3 terms for academic year {$academicYear->year}\n";
}

// Verify terms were created
$terms = \App\Models\Term::where('academic_year_id', $academicYear->id)->get();
echo "\nAvailable terms:\n";
foreach ($terms as $term) {
    echo "- ID: {$term->id}, Name: {$term->name} ({$term->start_date} to {$term->end_date})\n";
}

echo "\nTerms seeding completed!\n";
