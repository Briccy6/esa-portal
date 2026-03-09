<?php

// Test script to verify blob column types
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach(DB::select('DESCRIBE resources') as $col) {
    if(in_array($col->Field, ['document_blob', 'video_blob'])) {
        echo $col->Field . ' => ' . $col->Type . PHP_EOL;
    }
}
