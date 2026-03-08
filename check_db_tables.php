<?php

// Simple database check without Laravel
$host = '127.0.0.1';
$dbname = 'esa';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE TABLES CHECK ===\n\n";
    
    // Get all tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tables in database '$dbname':\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    
    // Check specifically for courses table
    if (in_array('courses', $tables)) {
        echo "\n✅ Courses table exists!\n";
        
        // Count courses
        $stmt = $pdo->query("SELECT COUNT(*) FROM courses");
        $count = $stmt->fetchColumn();
        echo "   Number of courses: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, course_name, abbreviation FROM courses LIMIT 5");
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "   Sample courses:\n";
            foreach ($courses as $course) {
                echo "   - ID: {$course['id']}, Name: {$course['course_name']} ({$course['abbreviation']})\n";
            }
        }
    } else {
        echo "\n❌ Courses table does NOT exist!\n";
    }
    
    // Check for branches table
    if (in_array('branches', $tables)) {
        echo "\n✅ Branches table exists!\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM branches");
        $count = $stmt->fetchColumn();
        echo "   Number of branches: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("SELECT id, name FROM branches");
            $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "   Available branches:\n";
            foreach ($branches as $branch) {
                echo "   - ID: {$branch['id']}, Name: {$branch['name']}\n";
            }
        }
    } else {
        echo "\n❌ Branches table does NOT exist!\n";
    }
    
    // Check for branch_course table
    if (in_array('branch_course', $tables)) {
        echo "\n✅ Branch_course table exists!\n";
        
        $stmt = $pdo->query("SELECT COUNT(*) FROM branch_course");
        $count = $stmt->fetchColumn();
        echo "   Number of branch-course assignments: $count\n";
        
        if ($count > 0) {
            $stmt = $pdo->query("
                SELECT b.name as branch_name, c.course_name, c.abbreviation 
                FROM branch_course bc 
                JOIN branches b ON bc.branch_id = b.id 
                JOIN courses c ON bc.course_id = c.id 
                LIMIT 5
            ");
            $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "   Sample assignments:\n";
            foreach ($assignments as $assignment) {
                echo "   - {$assignment['branch_name']} → {$assignment['course_name']} ({$assignment['abbreviation']})\n";
            }
        } else {
            echo "   ❌ No branch-course assignments found!\n";
        }
    } else {
        echo "\n❌ Branch_course table does NOT exist!\n";
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}
