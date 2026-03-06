<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CHECKING MISSING TABLES ===\n";

// Check if live_class_courses exists
try {
    \DB::table('live_class_courses')->count();
    echo "✅ live_class_courses table exists\n";
} catch (Exception $e) {
    echo "❌ live_class_courses table missing: " . $e->getMessage() . "\n";
    
    // Create the table manually
    echo "Creating live_class_courses table...\n";
    \Schema::create('live_class_courses', function (Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('live_class_id');
        $table->unsignedBigInteger('course_id');
        $table->timestamps();

        $table->foreign('live_class_id')->references('id')->on('live_classes')->onDelete('cascade');
        $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        $table->unique(['live_class_id', 'course_id']);
    });
    echo "✅ live_class_courses table created\n";
}

// Check if the live class has courses attached
$liveClass = \App\Models\LiveClass::find(7);
if ($liveClass) {
    $courses = $liveClass->courses;
    echo "Live class course count: " . $courses->count() . "\n";
    
    if ($courses->count() > 0) {
        foreach ($courses as $course) {
            echo "Course ID: {$course->id}, Name: {$course->name}\n";
        }
    } else {
        echo "No courses attached to live class\n";
        
        // Attach the course manually if needed
        if ($liveClass->courses->count() === 0) {
            echo "Attaching course to live class...\n";
            $liveClass->courses()->attach(18); // Course ID 18 from our test
            echo "✅ Course 18 attached to live class 7\n";
        }
    }
}

echo "\n=== TESTING API AGAIN ===\n";

// Test the API again
$app->instance('request', \Illuminate\Http\Request::create('/api/live-classes/homepage'));

try {
    $controller = new \App\Http\Controllers\LiveClassController();
    $response = $controller->homepageLiveClasses();
    
    $data = json_decode($response->getContent(), true);
    echo "API Response Count: " . $data['count'] . "\n";
    
    if ($data['count'] > 0) {
        foreach ($data['liveClasses'] as $class) {
            echo "- ID: {$class['id']}, Title: {$class['title']}, Status: {$class['status']}\n";
        }
    }
} catch (Exception $e) {
    echo "API Error: " . $e->getMessage() . "\n";
}
