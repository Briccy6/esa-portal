<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=school';
$user = 'root';
$pass = '';
$tables = ['teacher_course','branch_course','resources','academic_year_resource','courses','academic_years'];
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
        $exists = $stmt->fetch() ? 'exists' : 'missing';
        echo "$table: $exists\n";
        if ($exists === 'exists') {
            $cols = $pdo->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $col) {
                echo '  ' . $col['Field'] . ' | ' . $col['Type'] . ' | ' . $col['Key'] . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
