<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "=== EXAMS TABLE STRUCTURE ===\n";
$columns = Schema::getColumnListing('exams');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\n=== CHECKING IF STATUS COLUMN EXISTS ===\n";
echo "Status column exists: " . (Schema::hasColumn('exams', 'status') ? 'YES' : 'NO') . "\n";

echo "\n=== CHECKING IF IS_PUBLIC COLUMN EXISTS ===\n";
echo "Is_public column exists: " . (Schema::hasColumn('exams', 'is_public') ? 'YES' : 'NO') . "\n";

echo "\n=== SAMPLE EXAM RECORD ===\n";
$exam = DB::table('exams')->first();
if ($exam) {
    foreach ((array) $exam as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "No exam records found.\n";
}
