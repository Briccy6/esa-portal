<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking Database for Students...\n\n";

try {
    $studentCount = \App\Models\Student::count();
    echo "Total students: " . $studentCount . "\n\n";
    
    if ($studentCount > 0) {
        echo "First 5 students:\n";
        $students = \App\Models\Student::take(5)->get(['id', 'full_name', 'email', 'nesa_registered']);
        
        foreach ($students as $student) {
            echo "ID: " . $student->id . 
                 ", Name: " . $student->full_name . 
                 ", Email: " . ($student->email ?? 'No email') . 
                 ", NESA Registered: " . ($student->nesa_registered ? 'Yes' : 'No') . "\n";
        }
        
        // Test with student ID 269 specifically
        echo "\nChecking student ID 269:\n";
        $student269 = \App\Models\Student::find(269);
        if ($student269) {
            echo "✅ Found student 269:\n";
            echo "   Name: " . $student269->full_name . "\n";
            echo "   Email: " . ($student269->email ?? 'No email') . "\n";
            echo "   NESA Registered: " . ($student269->nesa_registered ? 'Yes' : 'No') . "\n";
            
            if ($student269->email) {
                echo "\nTesting NESA email to student 269...\n";
                try {
                    $emailContent = "Test NESA registration email for " . $student269->full_name;
                    Mail::to($student269->email)->send(new \App\Mail\NesaRegistrationStatusMail($student269, 'approved', $emailContent));
                    echo "✅ NESA email sent successfully to " . $student269->email . "\n";
                } catch (\Exception $e) {
                    echo "❌ NESA email failed: " . $e->getMessage() . "\n";
                }
            } else {
                echo "⚠️  Student 269 has no email address\n";
            }
        } else {
            echo "❌ Student 269 not found\n";
        }
    } else {
        echo "❌ No students found in database\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nDatabase check completed.\n";
