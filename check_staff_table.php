<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check the actual database structure for staff_branch table
$schema = DB::select('DESCRIBE staff_branch');
foreach($schema as $column) {
    echo $column->Field . ' - ' . $column->Type . ' - ' . $column->Null . ' - ' . $column->Key . PHP_EOL;
}

// Check foreign key constraints
$constraints = DB::select("
    SELECT 
        TABLE_NAME,
        COLUMN_NAME,
        CONSTRAINT_NAME,
        REFERENCED_TABLE_NAME,
        REFERENCED_COLUMN_NAME
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'staff_branch'
    AND REFERENCED_TABLE_NAME IS NOT NULL
");

echo "\nForeign Key Constraints:\n";
foreach($constraints as $constraint) {
    echo "Table: {$constraint->TABLE_NAME}, Column: {$constraint->COLUMN_NAME}, ";
    echo "References: {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}\n";
}

// Check REFERENTIAL_CONSTRAINTS for delete rules
$deleteRules = DB::select("
    SELECT 
        CONSTRAINT_NAME,
        DELETE_RULE
    FROM information_schema.REFERENTIAL_CONSTRAINTS 
    WHERE CONSTRAINT_SCHEMA = DATABASE()
    AND TABLE_NAME = 'staff_branch'
");

echo "\nDelete Rules:\n";
foreach($deleteRules as $rule) {
    echo "Constraint: {$rule->CONSTRAINT_NAME}, On Delete: {$rule->DELETE_RULE}\n";
}

// Check if there are any staff_branch records
$count = DB::table('staff_branch')->count();
echo "\nTotal staff_branch records: {$count}\n";

// Check a specific example if any exist
if ($count > 0) {
    $sample = DB::table('staff_branch')->limit(1)->first();
    echo "Sample record: staff_id={$sample->staff_id}, branch_id={$sample->branch_id}\n";
}
?>
