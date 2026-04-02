<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Course;
use App\Models\Term;

echo "Checking course and term data...\n";

// Check if course 18 exists
$course = Course::find(18);
if (!$course) {
    echo "✗ Course 18 does not exist\n";
    exit(1);
}

echo "✓ Course 18 found: {$course->course_name}\n";

// Check available terms
$terms = Term::all();
echo "Available terms:\n";
foreach ($terms as $term) {
    echo "  - Term {$term->id}: {$term->name} ({$term->start_date} to {$term->end_date})\n";
}

// Check existing course_term_fees for course 18
$existingFees = DB::table('course_term_fees')->where('course_id', 18)->get();
echo "\nExisting course_term_fees for course 18:\n";
if ($existingFees->isEmpty()) {
    echo "  No fees found for course 18\n";
    
    // Create sample fees for the first term
    if ($terms->isNotEmpty()) {
        $firstTerm = $terms->first();
        echo "Creating sample fees for term {$firstTerm->id} ({$firstTerm->name})...\n";
        
        try {
            DB::table('course_term_fees')->insert([
                'course_id' => 18,
                'term_id' => $firstTerm->id,
                'registration_fee' => $course->registration_fee ?? 0,
                'tuition_fee' => $course->tuition_fee ?? 0,
                'practical_fee' => $course->practical_fee ?? 0,
                'trip_fee' => $course->trip_fee ?? 0,
                'student_card_fee' => 2000,
                'total_fee' => ($course->registration_fee ?? 0) + ($course->tuition_fee ?? 0) + ($course->practical_fee ?? 0) + ($course->trip_fee ?? 0) + 2000,
                'is_active' => 1,
                'notes' => 'Auto-generated fee structure',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✓ Sample fees created successfully\n";
        } catch (Exception $e) {
            echo "✗ Error creating sample fees: " . $e->getMessage() . "\n";
        }
    }
} else {
    foreach ($existingFees as $fee) {
        echo "  - Term {$fee->term_id}: Registration {$fee->registration_fee}, Tuition {$fee->tuition_fee}, Practical {$fee->practical_fee}, Trip {$fee->trip_fee}, Student Card {$fee->student_card_fee}, Total {$fee->total_fee}, Active: " . ($fee->is_active ? 'Yes' : 'No') . "\n";
    }
}

// Test the final query
echo "\nTesting final query...\n";
try {
    $activeFees = DB::table('course_term_fees')
        ->where('course_id', 18)
        ->where('course_id', '!=', null)  // This is redundant but matches the original query
        ->where('is_active', 1)
        ->with('term')  // This won't work in raw DB query, but let's see
        ->get();
    
    echo "✓ Query executed successfully\n";
    echo "Found " . $activeFees->count() . " active fee records for course 18\n";
    
    if ($activeFees->isNotEmpty()) {
        foreach ($activeFees as $fee) {
            echo "  - Term {$fee->term_id}: Total fee {$fee->total_fee}\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Query failed: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
