<?php

// Script to identify staff table dependencies
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== STAFF TABLE DEPENDENCY ANALYSIS ===\n\n";

try {
    // Get all foreign key constraints referencing staff table
    $constraints = DB::select("
        SELECT 
            TABLE_NAME as table_name,
            COLUMN_NAME as column_name,
            REFERENCED_TABLE_NAME as referenced_table,
            REFERENCED_COLUMN_NAME as referenced_column,
            CONSTRAINT_NAME as constraint_name
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
        WHERE REFERENCED_TABLE_SCHEMA = DATABASE() 
        AND REFERENCED_TABLE_NAME = 'staff'
        ORDER BY TABLE_NAME, COLUMN_NAME
    ");

    if (empty($constraints)) {
        echo "No foreign key constraints found referencing the staff table.\n";
    } else {
        echo "FOREIGN KEY CONSTRAINTS:\n";
        echo str_repeat("=", 50) . "\n";
        
        foreach ($constraints as $constraint) {
            echo "Table: {$constraint->table_name}\n";
            echo "  Column: {$constraint->column_name}\n";
            echo "  References: staff.{$constraint->referenced_column}\n";
            echo "  Constraint: {$constraint->constraint_name}\n";
            
            // Check if there's actual data in this table
            $count = DB::table($constraint->table_name)
                ->whereNotNull($constraint->column_name)
                ->count();
            echo "  Records with staff references: {$count}\n";
            echo "\n";
        }
    }

    // Check for any tables that might have staff_id columns without proper constraints
    echo "\nTABLES WITH POTENTIAL STAFF REFERENCES (no FK constraints):\n";
    echo str_repeat("=", 60) . "\n";
    
    $tables = DB::select("SHOW TABLES");
    $database = DB::getDatabaseName();
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        
        if ($tableName === 'staff') continue;
        
        $columns = DB::select("
            SELECT COLUMN_NAME, DATA_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '{$database}' 
            AND TABLE_NAME = '{$tableName}' 
            AND COLUMN_NAME LIKE '%staff%'
        ");
        
        if (!empty($columns)) {
            echo "Table: {$tableName}\n";
            foreach ($columns as $column) {
                echo "  Column: {$column->COLUMN_NAME} ({$column->DATA_TYPE})\n";
                
                // Count records with values in this column
                $count = DB::table($tableName)
                    ->whereNotNull($column->COLUMN_NAME)
                    ->count();
                echo "  Non-null records: {$count}\n";
            }
            echo "\n";
        }
    }

    // Check for any audit/trail columns that might reference staff
    echo "\nAUDIT/TRAIL COLUMNS (created_by, updated_by, etc.):\n";
    echo str_repeat("=", 50) . "\n";
    
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        
        if ($tableName === 'staff') continue;
        
        $auditColumns = DB::select("
            SELECT COLUMN_NAME, DATA_TYPE 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = '{$database}' 
            AND TABLE_NAME = '{$tableName}' 
            AND COLUMN_NAME IN ('created_by', 'updated_by', 'deleted_by', 'user_id')
        ");
        
        if (!empty($auditColumns)) {
            echo "Table: {$tableName}\n";
            foreach ($auditColumns as $column) {
                echo "  Column: {$column->COLUMN_NAME} ({$column->DATA_TYPE})\n";
                
                // Check if values might be staff IDs
                $count = DB::table($tableName)
                    ->whereNotNull($column->COLUMN_NAME)
                    ->count();
                echo "  Non-null records: {$count}\n";
            }
            echo "\n";
        }
    }

    // Check live_class_conditions table specifically (mentioned in migrations)
    if (Schema::hasTable('live_class_conditions')) {
        echo "\nLIVE_CLASS_CONDITIONS TABLE ANALYSIS:\n";
        echo str_repeat("=", 40) . "\n";
        
        $staffTypeCount = DB::table('live_class_conditions')
            ->where('user_type', 'staff')
            ->count();
        
        echo "Records with user_type = 'staff': {$staffTypeCount}\n";
        
        // Show sample records
        $samples = DB::table('live_class_conditions')
            ->where('user_type', 'staff')
            ->limit(3)
            ->get();
            
        if ($samples->count() > 0) {
            echo "Sample records:\n";
            foreach ($samples as $sample) {
                echo "  ID: {$sample->id}, Email: {$sample->email}, Name: {$sample->name}\n";
            }
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== ANALYSIS COMPLETE ===\n";
