<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== REPORT CARD SETTINGS ===\n";
$settings = DB::table('report_card_settings')->get();
foreach ($settings as $s) {
    echo "ID: {$s->id}, Academic Year: {$s->academic_year}, Term: {$s->term}, Available At: {$s->report_card_available_at}, Active: {$s->is_active}\n";
}

echo "\n=== REPORT CARDS ===\n";
$cards = DB::table('report_cards')->select('id', 'student_id', 'academic_year_id', 'term_id', 'status', 'created_at')->get();
echo "Total Report Cards: " . count($cards) . "\n";
foreach ($cards as $c) {
    echo "ID: {$c->id}, Student: {$c->student_id}, AcademicYear: {$c->academic_year_id}, Term: {$c->term_id}, Status: {$c->status}\n";
}

echo "\n=== STUDENT TRANSACTIONS ===\n";
$txns = DB::table('student_transactions')->select('id', 'student_id', 'type', 'status', 'created_at')->limit(5)->get();
echo "Sample Transactions: " . count($txns) . "\n";
foreach ($txns as $t) {
    echo "ID: {$t->id}, Student: {$t->student_id}, Type: {$t->type}, Status: {$t->status}\n";
}

echo "\n=== CURRENT STUDENT ===\n";
$student = DB::table('students')->where('id', 1)->first();
if ($student) {
    $outstanding = isset($student->outstanding_amount) ? $student->outstanding_amount : 'N/A';
    echo "Student ID: {$student->id}, Name: {$student->full_name}, Outstanding: {$outstanding}\n";
}
