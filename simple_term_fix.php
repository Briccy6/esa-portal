<?php

// This script fixes the "No Term Assigned" issue
// Run this script in your browser or via command line

echo "<h2>Fixing 'No Term Assigned' Issue</h2>";

try {
    // Try to use Laravel's database
    require_once __DIR__ . '/vendor/autoload.php';

    // Simple approach - use PDO directly
    $host = '127.0.0.1';
    $port = '3307';
    $database = 'esa';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<p>✅ Connected to database</p>";

    // 1. Check if term_id column exists
    $stmt = $pdo->query("DESCRIBE marks");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!in_array('term_id', $columns)) {
        echo "<p>❌ term_id column missing. Please run: php artisan migrate</p>";
        exit;
    }

    echo "<p>✅ term_id column exists</p>";

    // 2. Get available terms
    $stmt = $pdo->query("SELECT id, name FROM terms ORDER BY id LIMIT 1");
    $term = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$term) {
        echo "<p>❌ No terms found. Please create terms first.</p>";
        exit;
    }

    $defaultTermId = $term['id'];
    $defaultTermName = $term['name'];

    echo "<p>✅ Using default term: {$defaultTermName} (ID: {$defaultTermId})</p>";

    // 3. Update marks with missing terms
    $stmt = $pdo->prepare("UPDATE marks SET term_id = ? WHERE term_id IS NULL OR term_id = 0");
    $stmt->execute([$defaultTermId]);

    $updatedCount = $stmt->rowCount();

    echo "<p>✅ Updated {$updatedCount} marks with default term</p>";

    // 4. Verify fix
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM marks WHERE term_id IS NULL OR term_id = 0");
    $stmt->execute();
    $remainingNull = $stmt->fetch(PDO::FETCH_ASSOC)['count'];

    if ($remainingNull == 0) {
        echo "<p>🎉 SUCCESS: All marks now have valid term assignments!</p>";
        echo "<p>The teacher dashboard should no longer show 'No Term Assigned'</p>";
        echo "<p><a href='/teacher/marks/view'>Check Teacher Dashboard</a></p>";
    }
    else {
        echo "<p>⚠️ Still {$remainingNull} marks need manual review</p>";
    }


}
catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Solution:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure MySQL is running on 127.0.0.1:3307</li>";
    echo "<li>Check your .env file database settings</li>";
    echo "<li>Run: php artisan migrate</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><small>Script completed. You can now refresh your teacher dashboard.</small></p>";
?>
