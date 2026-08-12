<?php

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=school';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SHOW CREATE TABLE resources');
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        echo $row['Create Table'];
    } else {
        echo "No table found.\n";
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
