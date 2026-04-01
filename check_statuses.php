<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Student Transaction Statuses...\n";

$statuses = DB::table('student_transactions')->select('status')->distinct()->pluck('status');

echo "Statuses found: " . json_encode($statuses) . "\n";

echo "\nSample transactions:\n";
$sample = DB::table('student_transactions')->take(5)->get(['id', 'status', 'amount', 'paid_at']);

foreach ($sample as $tx) {
    echo "ID: {$tx->id}, Status: {$tx->status}, Amount: {$tx->amount}, Paid: {$tx->paid_at}\n";
}
