<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check authentication guards
echo "Checking authentication guards:\n";
echo "Admin guard check: " . (auth('admin')->check() ? 'YES' : 'NO') . "\n";
echo "Teacher guard check: " . (auth('teacher')->check() ? 'YES' : 'NO') . "\n";
echo "Staff guard check: " . (auth('staff')->check() ? 'YES' : 'NO') . "\n";
echo "Student guard check: " . (auth('student')->check() ? 'YES' : 'NO') . "\n";

// Check current user
if (auth('admin')->check()) {
    echo "Admin user: " . auth('admin')->user()->email . "\n";
}
if (auth('teacher')->check()) {
    echo "Teacher user: " . auth('teacher')->user()->email . "\n";
}
if (auth('staff')->check()) {
    echo "Staff user: " . auth('staff')->user()->email . "\n";
}
if (auth('student')->check()) {
    echo "Student user: " . auth('student')->user()->email . "\n";
}

echo "\nAll guards: " . json_encode(array_keys(auth()->guards())) . "\n";
