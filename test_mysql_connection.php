<?php
require __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'school',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

try {
    $pdo = $capsule->getConnection()->getPdo();
    echo 'Connected: ' . get_class($pdo) . "\n";
    var_dump(PDO::getAvailableDrivers());
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
