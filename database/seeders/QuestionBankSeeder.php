<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Course;

class QuestionBankSeeder extends Seeder
{
    public function run()
    {
        // Get some courses to associate with question banks
        $courses = Course::take(3)->get();
        
        if ($courses->isEmpty()) {
            $this->command->warn('No courses found. Please run CoursesTableSeeder first.');
            return;
        }

        // Create a teacher if none exists
        $teacher = \App\Models\Teacher::first();
        if (!$teacher) {
            $teacher = \App\Models\Teacher::create([
                'teacher_name' => 'Test Teacher',
                'teacher_id_number' => 'T001',
                'id_number' => '1234567890123',
                'telephone' => '1234567890',
                'email' => 'teacher@example.com',
                'password' => bcrypt('password'),
                'photo' => 'default-avatar.png',
                'employment_type' => 'Permanent',
                'level_of_education' => 'Bachelor',
                'salary' => 500000,
            ]);
        }

        // Get current academic year
        $academicYear = \App\Models\AcademicYear::first();

        // Create sample question banks
        $questionBanks = [
            [
                'name' => 'Mathematics Questions',
                'description' => 'Basic mathematics questions for various levels',
                'course_id' => $courses[0]->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear?->id,
                'is_active' => true,
            ],
            [
                'name' => 'Science Questions',
                'description' => 'Science and biology questions',
                'course_id' => $courses[1]->id ?? $courses[0]->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear?->id,
                'is_active' => true,
            ],
            [
                'name' => 'General Knowledge',
                'description' => 'General knowledge and aptitude questions',
                'course_id' => $courses[2]->id ?? $courses[0]->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear?->id,
                'is_active' => true,
            ],
        ];

        foreach ($questionBanks as $bankData) {
            $questionBank = QuestionBank::create($bankData);
            
            // Add sample questions to each bank
            $this->createSampleQuestions($questionBank);
        }
    }

    private function createSampleQuestions(QuestionBank $questionBank)
    {
        $questions = [
            [
                'question_type' => 'multiple_choice',
                'text' => 'What is 2 + 2?',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'correct_answer' => 'B',
                'marks' => 1,
            ],
            [
                'question_type' => 'multiple_choice',
                'text' => 'What is the capital of France?',
                'option_a' => 'London',
                'option_b' => 'Berlin',
                'option_c' => 'Paris',
                'option_d' => 'Madrid',
                'correct_answer' => 'C',
                'marks' => 2,
            ],
            [
                'question_type' => 'true_false',
                'text' => 'The Earth is flat.',
                'option_a' => 'True',
                'option_b' => 'False',
                'correct_answer' => 'B',
                'marks' => 1,
            ],
            [
                'question_type' => 'multiple_choice',
                'text' => 'What is H2O?',
                'option_a' => 'Hydrogen',
                'option_b' => 'Water',
                'option_c' => 'Oxygen',
                'option_d' => 'Carbon Dioxide',
                'correct_answer' => 'B',
                'marks' => 2,
            ],
            [
                'question_type' => 'short_answer',
                'text' => 'Who wrote Romeo and Juliet?',
                'correct_answer' => 'William Shakespeare',
                'marks' => 3,
            ],
        ];

        foreach ($questions as $questionData) {
            $questionBank->questions()->create($questionData);
        }
    }
}
