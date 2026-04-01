<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Expenses Table...\n";

$count = DB::table('expenses')->count();
echo "Total expenses: {$count}\n";

if ($count > 0) {
    echo "\nSample expenses:\n";
    $sample = DB::table('expenses')->take(5)->get(['id', 'expense_date', 'expense_type', 'amount', 'description']);

    foreach ($sample as $exp) {
        echo "ID: {$exp->id}, Date: {$exp->expense_date}, Type: {$exp->expense_type}, Amount: {$exp->amount}, Desc: {$exp->description}\n";
    }
}
