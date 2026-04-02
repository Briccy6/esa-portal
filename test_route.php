<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$request = Illuminate\Http\Request::create('/staff/lessons/get-courses-for-branches', 'POST', [
    'branch_ids' => [4]
]);

$request->headers->set('Content-Type', 'application/json');

try {
    $response = $kernel->handle($request);
    echo "Response Status: " . $response->getStatusCode() . "\n";
    echo "Response Content: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
