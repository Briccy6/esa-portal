<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Checking Term Fees Setup ===\n\n";

// Check if terms exist
$terms = \App\Models\Term::with('academicYear')->get();
echo "Total terms in database: " . $terms->count() . "\n\n";

if ($terms->count() > 0) {
    echo "Available terms:\n";
    foreach ($terms as $term) {
        echo "- ID: {$term->id}, Name: {$term->name}, Year: " . ($term->academicYear->year ?? 'N/A') . "\n";
    }
} else {
    echo "❌ No terms found in database!\n";
    echo "You need to create terms first before assigning fees.\n";
}

// Check if courses exist
$courses = \App\Models\Course::get();
echo "\nTotal courses in database: " . $courses->count() . "\n";

if ($courses->count() > 0) {
    echo "Available courses:\n";
    foreach ($courses->take(5) as $course) {
        echo "- ID: {$course->id}, Name: {$course->course_name}, Code: {$course->course_code}\n";
    }
    if ($courses->count() > 5) {
        echo "... and " . ($courses->count() - 5) . " more\n";
    }
} else {
    echo "❌ No courses found in database!\n";
}

// Check if course_term_fees table exists and has data
try {
    $courseTermFees = \App\Models\CourseTermFee::get();
    echo "\nTotal course term fees in database: " . $courseTermFees->count() . "\n";
    
    if ($courseTermFees->count() > 0) {
        echo "Sample course term fees:\n";
        foreach ($courseTermFees->take(3) as $fee) {
            echo "- Course ID: {$fee->course_id}, Term ID: {$fee->term_id}, Total: {$fee->total_fees} RWF\n";
        }
    }
} catch (\Exception $e) {
    echo "\n❌ Error accessing course_term_fees table: " . $e->getMessage() . "\n";
    echo "This might mean the migration hasn't been run.\n";
}

echo "\n=== Test Complete ===\n";
