<?php

echo "=== FIXING MISSING TERMS IN MARKS ===\n\n";

// Simple database connection
$host = '127.0.0.1';
$port = '3307';
$database = 'esa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✅ Database connected successfully\n\n";

    // 1. Check if term_id column exists
    $stmt = $pdo->prepare("DESCRIBE marks");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('term_id', $columns)) {
        echo "❌ term_id column doesn't exist. Please run migration: 2026_03_14_004728_add_term_id_to_marks_table\n";
        exit;
    }

    echo "✅ term_id column exists\n\n";

    // 2. Check available terms
    $stmt = $pdo->prepare("SELECT id, name FROM terms ORDER BY id");
    $stmt->execute();
    $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($terms)) {
        echo "❌ No terms found in database. Please create terms first.\n";
        exit;
    }

    echo "Available terms:\n";
    foreach ($terms as $term) {
        echo "- Term ID: {$term['id']}, Name: {$term['name']}\n";
    }

    // Get the first term as default
    $defaultTermId = $terms[0]['id'];
    $defaultTermName = $terms[0]['name'];

    echo "\nUsing '{$defaultTermName}' (ID: {$defaultTermId}) as default term\n\n";

    // 3. Find marks with missing/null/zero term_id
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM marks WHERE term_id IS NULL OR term_id = 0");
    $stmt->execute();
    $nullTermCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    echo "Marks needing term assignment: $nullTermCount\n\n";

    if ($nullTermCount > 0) {
        echo "Updating marks with missing terms...\n";

        // Update marks with null or zero term_id
        $stmt = $pdo->prepare("UPDATE marks SET term_id = ? WHERE term_id IS NULL OR term_id = 0");
        $stmt->execute([$defaultTermId]);

        $updatedCount = $stmt->rowCount();
        echo "✅ Updated $updatedCount marks with default term\n\n";

        // Verify the update
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM marks WHERE term_id IS NULL OR term_id = 0");
        $stmt->execute();
        $remainingNull = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

        if ($remainingNull == 0) {
            echo "✅ All marks now have valid term assignments\n";
        }
        else {
            echo "⚠️  Still $remainingNull marks without terms (may need manual review)\n";
        }
    }
    else {
        echo "✅ All marks already have valid term assignments\n";
    }

    // 4. Summary
    echo "\n=== SUMMARY ===\n";
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM marks");
    $stmt->execute();
    $totalMarks = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $pdo->prepare("SELECT COUNT(*) as valid FROM marks WHERE term_id IS NOT NULL AND term_id > 0");
    $stmt->execute();
    $validMarks = $stmt->fetch(PDO::FETCH_ASSOC)['valid'];

    echo "Total marks: $totalMarks\n";
    echo "Marks with valid terms: $validMarks\n";
    echo "Marks without terms: " . ($totalMarks - $validMarks) . "\n";

    if ($validMarks == $totalMarks) {
        echo "\n🎉 SUCCESS: All marks now have term assignments!\n";
        echo "The teacher dashboard should no longer show 'No Term Assigned'\n";
    }


}
catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nPossible solutions:\n";
    echo "1. Make sure MySQL is running on 127.0.0.1:3307\n";
    echo "2. Check database credentials in .env file\n";
    echo "3. Ensure the 'esa' database exists\n";
}

echo "\n=== FIX COMPLETE ===\n";
