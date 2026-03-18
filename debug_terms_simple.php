<?php

// Simple database connection check
try {
    // Load environment
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    
    // Connect to database
    $host = $_ENV['DB_HOST'];
    $database = $_ENV['DB_DATABASE'];
    $username = $_ENV['DB_USERNAME'];
    $password = $_ENV['DB_PASSWORD'];
    
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE CONNECTION SUCCESSFUL ===\n\n";
    
    // Check marks table structure
    $stmt = $pdo->prepare("DESCRIBE marks");
    $stmt->execute();
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Marks table columns:\n";
    foreach ($columns as $column) {
        echo "- $column\n";
    }
    echo "\n";
    
    // Check if term_id exists
    if (in_array('term_id', $columns)) {
        echo "✅ term_id column EXISTS in marks table\n\n";
        
        // Check marks with null/zero term_id
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM marks WHERE term_id IS NULL OR term_id = 0");
        $stmt->execute();
        $nullTermCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        echo "Marks with no term assigned: $nullTermCount\n\n";
        
        if ($nullTermCount > 0) {
            $stmt = $pdo->prepare("SELECT id, student_id, lesson_id, term_id, value, test_date FROM marks WHERE term_id IS NULL OR term_id = 0 LIMIT 10");
            $stmt->execute();
            $problemMarks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Sample marks with no term:\n";
            foreach ($problemMarks as $mark) {
                echo "- Mark ID: {$mark['id']}, Student: {$mark['student_id']}, Term: {$mark['term_id']}, Value: {$mark['value']}\n";
            }
        }
    } else {
        echo "❌ term_id column MISSING from marks table\n\n";
    }
    
    // Check terms table
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM terms");
    $stmt->execute();
    $termsCount = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    echo "Available terms: $termsCount\n";
    
    if ($termsCount > 0) {
        $stmt = $pdo->prepare("SELECT id, name FROM terms ORDER BY id");
        $stmt->execute();
        $terms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "Available terms:\n";
        foreach ($terms as $term) {
            echo "- Term ID: {$term['id']}, Name: {$term['name']}\n";
        }
    }
    
    echo "\n=== DEBUG COMPLETE ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
