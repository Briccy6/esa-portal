<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    if (empty($content)) {
        echo "Log file is empty.\n";
    } else {
        echo "Log file contents:\n";
        echo $content;
    }
} else {
    echo "Log file not found.\n";
}
