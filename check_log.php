<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    $lastLines = array_slice($lines, -20);
    echo implode("\n", $lastLines);
} else {
    echo "Log file not found.\n";
}
