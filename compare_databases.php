<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get local database tables and structure
$localTables = [];
$localStructure = [];

try {
    $tables = DB::select('SHOW TABLES');
    foreach ($tables as $table) {
        $tableName = array_values((array)$table)[0];
        $localTables[] = $tableName;
        
        // Get table structure
        $columns = DB::select("DESCRIBE `$tableName`");
        $localStructure[$tableName] = [];
        foreach ($columns as $column) {
            $localStructure[$tableName][] = [
                'field' => $column->Field,
                'type' => $column->Type,
                'null' => $column->Null,
                'key' => $column->Key,
                'default' => $column->Default,
                'extra' => $column->Extra
            ];
        }
    }
} catch (Exception $e) {
    echo "Error getting local database structure: " . $e->getMessage() . "\n";
    exit(1);
}

// Read online database structure from SQL file
$onlineSQLFile = 'database/u244769603_esa (4).sql';
$onlineTables = [];
$onlineStructure = [];

if (!file_exists($onlineSQLFile)) {
    echo "Online database SQL file not found: $onlineSQLFile\n";
    exit(1);
}

$sqlContent = file_get_contents($onlineSQLFile);

// Parse CREATE TABLE statements
preg_match_all('/CREATE TABLE `([^`]+)` \((.*?)\) ENGINE=InnoDB/s', $sqlContent, $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    $tableName = $match[1];
    $tableDef = $match[2];
    
    $onlineTables[] = $tableName;
    $onlineStructure[$tableName] = [];
    
    // Parse column definitions
    $lines = explode("\n", $tableDef);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, 'KEY ') === 0 || strpos($line, 'PRIMARY KEY') === 0 || strpos($line, 'CONSTRAINT') === 0) {
            continue;
        }
        
        if (preg_match('/^`([^`]+)`\s+(.+?)(?:,|$)/', $line, $colMatch)) {
            $fieldName = $colMatch[1];
            $fieldDef = $colMatch[2];
            
            // Extract type
            $type = '';
            if (preg_match('/^(\w+(?:\([^)]+\))?)\s/', $fieldDef, $typeMatch)) {
                $type = $typeMatch[1];
            }
            
            // Extract NULL constraint
            $null = 'YES';
            if (strpos($fieldDef, 'NOT NULL') !== false) {
                $null = 'NO';
            }
            
            // Extract DEFAULT value
            $default = null;
            if (preg_match('/DEFAULT\s+(\S+)/', $fieldDef, $defaultMatch)) {
                $default = $defaultMatch[1];
                if ($default === 'NULL') {
                    $default = null;
                } elseif (preg_match("/^'(.*)'$/", $default, $strMatch)) {
                    $default = $strMatch[1];
                }
            }
            
            // Extract AUTO_INCREMENT
            $extra = '';
            if (strpos($fieldDef, 'AUTO_INCREMENT') !== false) {
                $extra = 'auto_increment';
            }
            
            $onlineStructure[$tableName][] = [
                'field' => $fieldName,
                'type' => $type,
                'null' => $null,
                'key' => '',
                'default' => $default,
                'extra' => $extra
            ];
        }
    }
}

// Compare structures
echo "=== DATABASE COMPARISON REPORT ===\n\n";

// Find tables missing in local database
$missingInLocal = array_diff($onlineTables, $localTables);
if (!empty($missingInLocal)) {
    echo "TABLES MISSING IN LOCAL DATABASE:\n";
    foreach ($missingInLocal as $table) {
        echo "- $table\n";
    }
    echo "\n";
}

// Find tables missing in online database
$missingInOnline = array_diff($localTables, $onlineTables);
if (!empty($missingInOnline)) {
    echo "TABLES MISSING IN ONLINE DATABASE:\n";
    foreach ($missingInOnline as $table) {
        echo "- $table\n";
    }
    echo "\n";
}

// Find common tables with different structures
$commonTables = array_intersect($localTables, $onlineTables);
$tablesWithDifferences = [];

foreach ($commonTables as $table) {
    $localColumns = array_column($localStructure[$table], 'field');
    $onlineColumns = array_column($onlineStructure[$table], 'field');
    
    $missingColumnsLocal = array_diff($onlineColumns, $localColumns);
    $missingColumnsOnline = array_diff($localColumns, $onlineColumns);
    
    if (!empty($missingColumnsLocal) || !empty($missingColumnsOnline)) {
        $tablesWithDifferences[$table] = [
            'missing_in_local' => $missingColumnsLocal,
            'missing_in_online' => $missingColumnsOnline
        ];
    }
}

if (!empty($tablesWithDifferences)) {
    echo "COLUMNS DIFFERENCES IN COMMON TABLES:\n";
    foreach ($tablesWithDifferences as $table => $differences) {
        echo "\nTABLE: $table\n";
        
        if (!empty($differences['missing_in_local'])) {
            echo "  Columns missing in local:\n";
            foreach ($differences['missing_in_local'] as $column) {
                $onlineCols = array_filter($onlineStructure[$table], fn($c) => $c['field'] === $column);
                if (!empty($onlineCols)) {
                    $onlineCol = array_values($onlineCols)[0];
                    echo "    - $column ({$onlineCol['type']})\n";
                }
            }
        }
        
        if (!empty($differences['missing_in_online'])) {
            echo "  Columns missing in online:\n";
            foreach ($differences['missing_in_online'] as $column) {
                $localCols = array_filter($localStructure[$table], fn($c) => $c['field'] === $column);
                if (!empty($localCols)) {
                    $localCol = array_values($localCols)[0];
                    echo "    - $column ({$localCol['type']})\n";
                }
            }
        }
    }
    echo "\n";
}

// Generate SQL commands to add missing tables and columns
echo "=== SQL COMMANDS TO SYNC LOCAL DATABASE ===\n\n";

if (!empty($missingInLocal)) {
    echo "-- Add missing tables:\n";
    foreach ($missingInLocal as $table) {
        if (isset($onlineStructure[$table])) {
            echo "\n-- Table: $table\n";
            echo "CREATE TABLE `$table` (\n";
            
            $columnDefs = [];
            foreach ($onlineStructure[$table] as $column) {
                $def = "  `{$column['field']}` {$column['type']}";
                if ($column['null'] === 'NO') {
                    $def .= ' NOT NULL';
                }
                if ($column['default'] !== null) {
                    if (is_numeric($column['default'])) {
                        $def .= ' DEFAULT ' . $column['default'];
                    } else {
                        $def .= " DEFAULT '" . addslashes($column['default']) . "'";
                    }
                }
                if ($column['extra'] === 'auto_increment') {
                    $def .= ' AUTO_INCREMENT';
                }
                $columnDefs[] = $def;
            }
            
            echo implode(",\n", $columnDefs);
            echo "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n";
        }
    }
    echo "\n";
}

if (!empty($tablesWithDifferences)) {
    echo "-- Add missing columns:\n";
    foreach ($tablesWithDifferences as $table => $differences) {
        if (!empty($differences['missing_in_local'])) {
            echo "\n-- Table: $table\n";
            foreach ($differences['missing_in_local'] as $column) {
                $onlineCols = array_filter($onlineStructure[$table], fn($c) => $c['field'] === $column);
                if (!empty($onlineCols)) {
                    $onlineCol = array_values($onlineCols)[0];
                    
                    echo "ALTER TABLE `$table` ADD COLUMN `{$onlineCol['field']}` {$onlineCol['type']}";
                    if ($onlineCol['null'] === 'NO') {
                        echo ' NOT NULL';
                    }
                    if ($onlineCol['default'] !== null) {
                        if (is_numeric($onlineCol['default'])) {
                            echo ' DEFAULT ' . $onlineCol['default'];
                        } else {
                            echo " DEFAULT '" . addslashes($onlineCol['default']) . "'";
                        }
                    }
                    if ($onlineCol['extra'] === 'auto_increment') {
                        echo ' AUTO_INCREMENT';
                    }
                    echo ";\n";
                }
            }
        }
    }
}

echo "\n=== SUMMARY ===\n";
echo "Local database tables: " . count($localTables) . "\n";
echo "Online database tables: " . count($onlineTables) . "\n";
echo "Missing tables in local: " . count($missingInLocal) . "\n";
echo "Tables with column differences: " . count($tablesWithDifferences) . "\n";
