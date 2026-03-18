<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== DEBUGGING MARKS AND TERMS ISSUE ===\n\n";

// Check if marks table has term_id column
try {
    $columns = \Schema::getColumnListing('marks');
    echo "Marks table columns:\n";
    echo "- " . implode("\n- ", $columns) . "\n\n";
    
    if (in_array('term_id', $columns)) {
        echo "✅ term_id column EXISTS in marks table\n\n";
    } else {
        echo "❌ term_id column MISSING from marks table\n\n";
    }
} catch (Exception $e) {
    echo "Error checking marks table: " . $e->getMessage() . "\n\n";
}

// Check existing marks data
try {
    $marks = \App\Models\Mark::with('term')->get();
    echo "Total marks in database: " . $marks->count() . "\n\n";
    
    $termIssues = [];
    foreach ($marks as $mark) {
        if ($mark->term_id === null || $mark->term_id == 0) {
            $termIssues[] = [
                'mark_id' => $mark->id,
                'student_id' => $mark->student_id,
                'lesson_id' => $mark->lesson_id,
                'term_id' => $mark->term_id,
                'value' => $mark->value,
                'test_date' => $mark->test_date
            ];
        }
    }
    
    if (count($termIssues) > 0) {
        echo "❌ Found " . count($termIssues) . " marks with NO TERM assigned:\n";
        foreach (array_slice($termIssues, 0, 10) as $issue) {
            echo "  - Mark ID: {$issue['mark_id']}, Student: {$issue['student_id']}, Term ID: {$issue['term_id']}, Value: {$issue['value']}\n";
        }
        if (count($termIssues) > 10) {
            echo "  ... and " . (count($termIssues) - 10) . " more\n";
        }
    } else {
        echo "✅ All marks have valid term_id values\n";
    }
} catch (Exception $e) {
    echo "Error checking marks data: " . $e->getMessage() . "\n";
}

echo "\n";

// Check available terms
try {
    $terms = \App\Models\Term::all();
    echo "Available terms in database:\n";
    if ($terms->count() > 0) {
        foreach ($terms as $term) {
            echo "  - Term ID: {$term->id}, Name: {$term->name}\n";
        }
    } else {
        echo "  ❌ No terms found in database\n";
    }
} catch (Exception $e) {
    echo "Error checking terms: " . $e->getMessage() . "\n";
}

echo "\n";

// Check migration status
try {
    $migrations = \DB::table('migrations')->where('migration', 'like', '%term%')->orWhere('migration', 'like', '%marks%')->get();
    echo "Related migrations:\n";
    foreach ($migrations as $migration) {
        echo "  - {$migration->migration}\n";
    }
} catch (Exception $e) {
    echo "Error checking migrations: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
