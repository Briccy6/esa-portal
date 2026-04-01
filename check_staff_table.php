<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$schema = DB::select('DESCRIBE staff');
echo "Staff table structure:\n";
foreach($schema as $column) {
    echo $column->Field . ' - ' . $column->Type . PHP_EOL;
}
