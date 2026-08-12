<?php
$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=school';
$user = 'root';
$pass = '';
$tables = ['academic_year_resource','teacher_course','branch_course','resources','courses','academic_years','academic_year_teacher','academic_year_branch','academic_year_course'];
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '" . $table . "'");
        $exists = $stmt->fetch() ? 'exists' : 'missing';
        echo "$table: $exists\n";
        if ($exists === 'exists') {
            $ddl = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
            echo $ddl['Create Table'] . "\n\n";
        }
    }
    echo "Tables containing course in name:\n";
    $stmt = $pdo->query("SHOW TABLES LIKE '%course%'");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo '  ' . $row[0] . "\n";
    }
} catch (Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
