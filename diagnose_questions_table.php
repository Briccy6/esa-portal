<?php

/**
 * Diagnostic script to check questions table structure
 * Run this to understand the current database state before making changes
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Questions Table Diagnostic ===\n\n";

try {
    // Check if questions table exists
    if (!Schema::hasTable('questions')) {
        echo "❌ Error: questions table does not exist!\n";
        exit(1);
    }

    echo "✅ Questions table exists.\n\n";

    // Show table structure
    echo "=== Table Structure ===\n";
    $columns = DB::select("DESCRIBE questions");
    foreach ($columns as $column) {
        $nullStatus = $column->Null === 'YES' ? 'NULLABLE' : 'NOT NULL';
        echo "{$column->Field}: {$column->Type} ({$nullStatus})\n";
    }

    echo "\n=== Foreign Key Constraints ===\n";
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, DELETE_RULE
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
        JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc 
        ON kcu.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
        WHERE kcu.TABLE_SCHEMA = DATABASE() 
        AND kcu.TABLE_NAME = 'questions'
        AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
    ");

    if (empty($constraints)) {
        echo "No foreign key constraints found.\n";
    } else {
        foreach ($constraints as $constraint) {
            echo "{$constraint->CONSTRAINT_NAME}: {$constraint->COLUMN_NAME} -> {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME} (ON DELETE {$constraint->DELETE_RULE})\n";
        }
    }

    echo "\n=== Question Bank Questions Count ===\n";
    $questionBankQuestions = DB::select("
        SELECT COUNT(*) as count 
        FROM questions 
        WHERE question_bank_id IS NOT NULL AND exam_id IS NULL
    ");
    echo "Questions in question banks without exam: {$questionBankQuestions[0]->count}\n";

    echo "\n=== Sample Question Data ===\n";
    $sampleQuestions = DB::select("
        SELECT id, exam_id, question_bank_id, text 
        FROM questions 
        WHERE question_bank_id IS NOT NULL 
        LIMIT 3
    ");
    
    if (empty($sampleQuestions)) {
        echo "No questions found in question banks.\n";
    } else {
        foreach ($sampleQuestions as $question) {
            $examStatus = $question->exam_id ? "Exam ID: {$question->exam_id}" : "No Exam ID";
            echo "Question {$question->id}: {$examStatus}, Bank ID: {$question->question_bank_id}\n";
            echo "Text: " . substr($question->text, 0, 50) . "...\n\n";
        }
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Diagnostic Complete ===\n";
