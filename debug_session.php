<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

// Check session at the very start
echo "=== PRE-REQUEST SESSION CHECK ===\n";
echo "Session ID: " . session()->getId() . "\n";
echo "Session exists: " . (session()->has('session.started') ? 'YES' : 'NO') . "\n";
echo "Session data: " . json_encode(session()->all()) . "\n";

echo "\n=== AUTHENTICATION CHECKS ===\n";
echo "Admin authenticated: " . (auth('admin')->check() ? 'YES' : 'NO') . "\n";
echo "Staff authenticated: " . (auth('staff')->check() ? 'YES' : 'NO') . "\n";
echo "Teacher authenticated: " . (auth('teacher')->check() ? 'YES' : 'NO') . "\n";
echo "Web authenticated: " . (auth()->check() ? 'YES' : 'NO') . "\n";

if (auth('admin')->check()) {
    echo "Admin user: " . auth('admin')->user()->email . " (ID: " . auth('admin')->user()->id . ")\n";
}
