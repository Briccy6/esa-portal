<?php

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (file_exists($logFile)) {
    $content = file_get_contents($logFile);
    $lines = explode("\n", $content);
    
    echo "=== LATEST LOG ENTRIES ===\n";
    foreach ($lines as $line) {
        if (!empty(trim($line))) {
            echo $line . "\n";
        }
    }
} else {
    echo "Log file not found.\n";
}
