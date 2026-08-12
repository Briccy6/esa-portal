<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=school';
$user = 'root';
$pass = '';
$tables = [
    'academic_year_course',
    'academic_year_teacher',
    'academic_year_branch',
    'academic_year_resource',
    'branch_course',
    'teacher_course',
    'courses',
    'resources',
];

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
        $exists = $stmt->fetch() ? true : false;
        echo "$table: " . ($exists ? 'exists' : 'missing') . "\n";
        if ($exists) {
            $count = $pdo->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "  count = $count\n";
        }
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
