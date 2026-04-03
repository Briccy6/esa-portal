<?php
require 'vendor/autoload.php';
require 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get database connection
$db = \Illuminate\Support\Facades\DB::getFacadeRoot();

// Check tables
$tables = \DB::getSchemaBuilder()->getTableListing();
dd([
    'all_tables' => $tables,
    'report_card_tables' => array_filter($tables, fn($t) => strpos($t, 'report') !== false),
    'student_tables' => array_filter($tables, fn($t) => strpos($t, 'student') !== false),
]);
