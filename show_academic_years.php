<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=school';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SELECT id, year, starting_date, expiry_date FROM academic_years ORDER BY starting_date');
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo $row['id'] . ' | ' . $row['year'] . ' | ' . $row['starting_date'] . ' | ' . $row['expiry_date'] . "\n";
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
