<?php

echo "=== EXAM QUESTIONS FIX SCRIPT ===\n\n";

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Check if questions table exists
    if (!\Illuminate\Support\Facades\Schema::hasTable('questions')) {
        echo "❌ Questions table doesn't exist. Running migrations first...\n";
        echo "Run: php artisan migrate --force\n";
        exit(1);
    }

    // Check if exams table exists
    if (!\Illuminate\Support\Facades\Schema::hasTable('exams')) {
        echo "❌ Exams table doesn't exist. Running migrations first...\n";
        echo "Run: php artisan migrate --force\n";
        exit(1);
    }

    // Get counts
    $totalQuestions = \App\Models\Question::count();
    $questionsWithExams = \App\Models\Question::whereNotNull('exam_id')->count();
    $totalExams = \App\Models\Exam::count();

    echo "📊 Current Status:\n";
    echo "   Total Questions: {$totalQuestions}\n";
    echo "   Questions with Exams: {$questionsWithExams}\n";
    echo "   Total Exams: {$totalExams}\n\n";

    if ($totalQuestions > 0 && $questionsWithExams == 0) {
        echo "🔧 Fixing: Assigning questions to exams...\n";
        
        // Get all exams
        $exams = \App\Models\Exam::all();
        
        if ($exams->isEmpty()) {
            echo "❌ No exams found. Please create exams first.\n";
            exit(1);
        }

        // Get questions without exams
        $availableQuestions = \App\Models\Question::whereNull('exam_id')->get();
        
        if ($availableQuestions->isEmpty()) {
            echo "❌ No available questions to assign.\n";
            exit(1);
        }

        // Assign questions to exams (distribute evenly)
        $questionsPerExam = max(1, intval($availableQuestions->count() / $exams->count()));
        $questionIndex = 0;

        foreach ($exams as $exam) {
            $examQuestions = $availableQuestions->slice($questionIndex, $questionsPerExam);
            
            foreach ($examQuestions as $question) {
                $question->update(['exam_id' => $exam->id]);
                echo "   ✓ Assigned Question ID {$question->id} to Exam '{$exam->title}'\n";
            }
            
            $questionIndex += $questionsPerExam;
        }

        echo "\n✅ Questions assigned to exams successfully!\n";
    } elseif ($totalQuestions == 0) {
        echo "🔧 Fixing: Running question seeder...\n";
        echo "Run: php artisan db:seed --class=QuestionBankSeeder\n";
    } else {
        echo "✅ Questions are properly assigned to exams.\n";
    }

    // Verify fix
    $finalQuestionsWithExams = \App\Models\Question::whereNotNull('exam_id')->count();
    echo "\n📊 Final Status:\n";
    echo "   Questions with Exams: {$finalQuestionsWithExams}\n";
    
    if ($finalQuestionsWithExams > 0) {
        echo "✅ Fix successful! Students should now see questions when taking exams.\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== NEXT STEPS ===\n";
echo "1. Run migrations: php artisan migrate --force\n";
echo "2. Run seeders: php artisan db:seed --force\n";
echo "3. Test exam taking functionality\n";
echo "4. Check browser console for debug output\n";
