<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

// Check current academic year and terms
$academicYear = \App\Models\AcademicYear::where('is_ongoing', true)->first();

if ($academicYear) {
    echo "Current Academic Year: " . $academicYear->year . " (ID: " . $academicYear->id . ")\n";
    echo "Start Date: " . $academicYear->starting_date . "\n";
    echo "End Date: " . $academicYear->expiry_date . "\n\n";
    
    $terms = \App\Models\Term::where('academic_year_id', $academicYear->id)->get();
    
    if ($terms->count() > 0) {
        echo "Existing Terms:\n";
        foreach ($terms as $term) {
            echo "- " . $term->name . ": " . $term->start_date . " to " . $term->end_date . "\n";
        }
    } else {
        echo "No terms found for this academic year.\n";
    }
} else {
    echo "No ongoing academic year found.\n";
}
