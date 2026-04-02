<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Find the student by reg number
$student = \App\Models\Student::where('reg_number', 'ESATOR992025-2026')->first();

if (!$student) {
    echo "Student not found\n";
    exit;
}

echo "Student: " . $student->full_name . " (ID: " . $student->id . ")\n\n";

// Get all transactions for this student
$transactions = \App\Models\StudentTransaction::where('student_id', $student->id)
    ->with('term')
    ->get();

echo "Transactions:\n";
foreach ($transactions as $tx) {
    echo "ID: " . $tx->id . 
         ", Amount: " . $tx->amount . 
         ", Type: " . $tx->type . 
         ", Term ID: " . ($tx->term_id ?? 'NULL') . 
         ", Term Name: " . ($tx->term ? $tx->term->name : 'No Term') . 
         ", Paid At: " . $tx->paid_at . "\n";
}

// Check available terms
echo "\nAvailable Terms:\n";
$terms = \App\Models\Term::all();
foreach ($terms as $term) {
    echo "ID: " . $term->id . ", Name: " . $term->name . ", Start: " . $term->start_date . ", End: " . $term->end_date . "\n";
}
