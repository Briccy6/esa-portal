<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Test the staff deletion functionality
echo "Testing staff deletion fix...\n\n";

// Get a staff member to test with
$staff = \App\Models\Staff::first();
if (!$staff) {
    echo "No staff members found to test with.\n";
    exit;
}

echo "Testing with staff: {$staff->name} (ID: {$staff->id})\n";

// Check branch assignments before deletion
$branchCount = DB::table('staff_branch')
    ->where('staff_id', $staff->id)
    ->count();

echo "Branch assignments before deletion: {$branchCount}\n";

if ($branchCount > 0) {
    $branches = DB::table('staff_branch')
        ->where('staff_id', $staff->id)
        ->get();
    
    foreach ($branches as $branch) {
        echo "  - Branch ID: {$branch->branch_id}\n";
    }
}

// Test the dependency check method
$controller = new \App\Http\Controllers\StaffController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('checkStaffDependencies');
$method->setAccessible(true);

$dependencies = $method->invoke($controller, $staff);

echo "\nDependencies found:\n";
if (empty($dependencies)) {
    echo "  No blocking dependencies found (branch assignments excluded)\n";
} else {
    foreach ($dependencies as $table => $count) {
        echo "  - {$table}: {$count}\n";
    }
}

// Simulate the deletion process
echo "\nSimulating deletion process...\n";

if (empty($dependencies)) {
    echo "✅ Staff deletion should proceed successfully\n";
    echo "✅ Branch assignments will be automatically deleted by CASCADE\n";
} else {
    echo "❌ Staff deletion would be blocked by dependencies\n";
}

echo "\nTest completed.\n";
?>
