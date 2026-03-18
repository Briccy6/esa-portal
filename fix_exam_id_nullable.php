<?php

/**
 * Script to make exam_id nullable in questions table
 * Run this script on production server to fix CSV import issue
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting exam_id nullable fix...\n";

try {
    // Check if questions table exists
    if (!Schema::hasTable('questions')) {
        echo "Error: questions table does not exist!\n";
        exit(1);
    }

    // Get current foreign key constraint name
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'questions' 
        AND COLUMN_NAME = 'exam_id' 
        AND REFERENCED_TABLE_NAME = 'exams'
    ");

    if (empty($constraints)) {
        echo "No foreign key constraint found on exam_id column.\n";
    } else {
        $constraintName = $constraints[0]->CONSTRAINT_NAME;
        echo "Found foreign key constraint: $constraintName\n";
        
        // Drop the foreign key constraint
        try {
            DB::statement("ALTER TABLE questions DROP FOREIGN KEY $constraintName");
            echo "Dropped foreign key constraint successfully.\n";
        } catch (Exception $e) {
            echo "Error dropping foreign key: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    // Check if exam_id column is already nullable
    $columnInfo = DB::select("
        SELECT IS_NULLABLE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'questions' 
        AND COLUMN_NAME = 'exam_id'
    ");

    if (!empty($columnInfo) && $columnInfo[0]->IS_NULLABLE === 'YES') {
        echo "exam_id column is already nullable.\n";
    } else {
        // Make exam_id nullable
        try {
            DB::statement("ALTER TABLE questions MODIFY COLUMN exam_id INT NULL");
            echo "Made exam_id column nullable successfully.\n";
        } catch (Exception $e) {
            echo "Error making exam_id nullable: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    // Re-add foreign key constraint with ON DELETE SET NULL
    try {
        DB::statement("
            ALTER TABLE questions ADD CONSTRAINT questions_exam_id_foreign 
            FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE SET NULL
        ");
        echo "Re-added foreign key constraint with ON DELETE SET NULL.\n";
    } catch (Exception $e) {
        echo "Error re-adding foreign key: " . $e->getMessage() . "\n";
        exit(1);
    }

    // Verify the changes
    $finalCheck = DB::select("
        SELECT IS_NULLABLE 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'questions' 
        AND COLUMN_NAME = 'exam_id'
    ");

    if (!empty($finalCheck) && $finalCheck[0]->IS_NULLABLE === 'YES') {
        echo "✅ Success! exam_id column is now nullable.\n";
        echo "Questions can now be imported into question banks without being tied to an exam.\n";
    } else {
        echo "❌ Failed to make exam_id nullable.\n";
        exit(1);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Fix completed successfully!\n";
