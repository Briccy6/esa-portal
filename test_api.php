<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TESTING API ENDPOINT ===\n";

// Simulate API request
$app->instance('request', \Illuminate\Http\Request::create('/api/live-classes/homepage'));

try {
    $controller = new \App\Http\Controllers\LiveClassController();
    $response = $controller->homepageLiveClasses();
    
    echo "API Response Status: " . $response->getStatusCode() . "\n";
    echo "API Response Data:\n";
    
    $data = json_decode($response->getContent(), true);
    echo "Count: " . $data['count'] . "\n";
    
    if ($data['count'] > 0) {
        echo "Live Classes Found:\n";
        foreach ($data['liveClasses'] as $class) {
            echo "- ID: {$class['id']}, Title: {$class['title']}, Status: {$class['status']}\n";
        }
    } else {
        echo "No live classes found\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== TESTING WITH STUDENT AUTH ===\n";

// Test with student logged in
$student = \App\Models\Student::first();
if ($student) {
    \Auth::guard('student')->login($student);
    
    try {
        $response = $controller->homepageLiveClasses();
        $data = json_decode($response->getContent(), true);
        
        echo "With student logged in:\n";
        echo "Count: " . $data['count'] . "\n";
        echo "Student Course ID: " . $student->course_id . "\n";
        
        if ($data['count'] > 0) {
            foreach ($data['liveClasses'] as $class) {
                echo "- ID: {$class['id']}, Title: {$class['title']}\n";
            }
        }
        
    } catch (Exception $e) {
        echo "Error with student: " . $e->getMessage() . "\n";
    }
    
    \Auth::guard('student')->logout();
}
