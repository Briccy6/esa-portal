<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "=== Term Creation Debug ===\n\n";
    
    // Check current database structure
    echo "1. Current terms table structure:\n";
    $columns = \DB::select('DESCRIBE terms');
    foreach ($columns as $column) {
        echo "   - {$column->Field} ({$column->Type}) " . ($column->Null === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
    }
    
    // Check if there's a term column that we missed
    echo "\n2. Checking for 'term' column specifically:\n";
    $termColumns = collect($columns)->filter(function($col) {
        return stripos($col->Field, 'term') !== false;
    });
    
    if ($termColumns->isEmpty()) {
        echo "   No 'term' column found in terms table\n";
    } else {
        echo "   Found term-related columns:\n";
        foreach ($termColumns as $col) {
            echo "   - {$col->Field}\n";
        }
    }
    
    // Test the exact query that's failing
    echo "\n3. Testing the problematic insert:\n";
    $testData = [
        'academic_year_id' => 4,
        'name' => 'Term 1',
        'start_date' => '2026-01-12',
        'end_date' => '2026-04-03',
    ];
    
    echo "   Data to insert: " . json_encode($testData) . "\n";
    
    // Check if academic year exists
    $academicYear = \App\Models\AcademicYear::find(4);
    echo "   Academic year 4 exists: " . ($academicYear ? 'YES' : 'NO') . "\n";
    
    // Try to build the SQL query like Laravel would
    $sql = "INSERT INTO terms (" . implode(', ', array_keys($testData)) . ", created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
    echo "   Generated SQL: $sql\n";
    
    // Try the actual insert
    try {
        \DB::insert($sql, array_values($testData));
        echo "   ✅ Insert successful!\n";
        
        // Clean up - remove the test record
        \DB::delete("DELETE FROM terms WHERE name = ? AND academic_year_id = ?", ['Term 1', 4]);
        echo "   🧹 Test record cleaned up\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Insert failed: " . $e->getMessage() . "\n";
    }
    
    // Check Laravel's Term model
    echo "\n4. Checking Term model fillable fields:\n";
    $term = new \App\Models\Term();
    echo "   Fillable: " . implode(', ', $term->getFillable()) . "\n";
    
    // Try Laravel's create method
    echo "\n5. Testing Laravel Term::create():\n";
    try {
        $createdTerm = \App\Models\Term::create($testData);
        echo "   ✅ Laravel create successful! ID: " . $createdTerm->id . "\n";
        
        // Clean up
        $createdTerm->delete();
        echo "   🧹 Test record cleaned up\n";
        
    } catch (\Exception $e) {
        echo "   ❌ Laravel create failed: " . $e->getMessage() . "\n";
        echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
    
} catch (\Exception $e) {
    echo "Global error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
