<?php

namespace Database\Seeders;

use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Course;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionBankSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        QuestionBank::query()->delete();
        Question::query()->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Get first teacher for question bank ownership
        $teacher = Teacher::first();
        if (!$teacher) {
            $this->command->error('No teacher found. Please seed teachers first.');
            return;
        }

        // Create question banks for different subjects
        $questionBanks = [
            [
                'name' => 'Mathematics Question Bank',
                'description' => 'Comprehensive mathematics questions covering algebra, geometry, statistics, and more',
                'course_id' => $this->getCourseId('Mathematics'),
            ],
            [
                'name' => 'English Language Question Bank',
                'description' => 'English grammar, vocabulary, comprehension, and literature questions',
                'course_id' => $this->getCourseId('English'),
            ],
            [
                'name' => 'Science Question Bank',
                'description' => 'Physics, Chemistry, and Biology questions for various levels',
                'course_id' => $this->getCourseId('Science'),
            ],
            [
                'name' => 'Computer Science Question Bank',
                'description' => 'Programming, algorithms, and computer fundamentals questions',
                'course_id' => $this->getCourseId('Computer Science'),
            ],
            [
                'name' => 'General Knowledge Question Bank',
                'description' => 'Current affairs, history, geography, and general knowledge questions',
                'course_id' => null, // General knowledge
            ],
        ];

        $createdBanks = [];
        foreach ($questionBanks as $bankData) {
            $bank = QuestionBank::create([
                'name' => $bankData['name'],
                'description' => $bankData['description'],
                'teacher_id' => $teacher->id,
                'course_id' => $bankData['course_id'],
                'is_active' => true,
            ]);
            $createdBanks[] = $bank;
        }

        // Generate 1000 questions distributed across banks
        $this->command->info('Generating 1000 questions...');
        
        $questionsPerBank = [
            'Mathematics' => 250,
            'English Language' => 200,
            'Science' => 250,
            'Computer Science' => 150,
            'General Knowledge' => 150,
        ];

        foreach ($createdBanks as $bank) {
            $bankName = explode(' ', $bank->name)[0];
            $count = $questionsPerBank[$bankName] ?? 200;
            
            $this->command->info("Creating {$count} questions for {$bank->name}...");
            
            switch ($bankName) {
                case 'Mathematics':
                    $this->createMathematicsQuestions($bank, $count);
                    break;
                case 'English':
                    $this->createEnglishQuestions($bank, $count);
                    break;
                case 'Science':
                    $this->createScienceQuestions($bank, $count);
                    break;
                case 'Computer':
                    $this->createComputerScienceQuestions($bank, $count);
                    break;
                case 'General':
                    $this->createGeneralKnowledgeQuestions($bank, $count);
                    break;
            }
        }

        $this->command->info('Successfully created 1000 questions across 5 question banks!');
    }

    private function getCourseId($courseName)
    {
        $course = Course::where('course_name', 'like', "%{$courseName}%")->first();
        return $course ? $course->id : null;
    }

    private function createMathematicsQuestions($bank, $count)
    {
        $topics = [
            'algebra' => [
                ['question' => 'What is the value of x in the equation 2x + 5 = 15?', 'options' => ['5', '10', '7.5', '20'], 'correct' => 'A'],
                ['question' => 'Simplify: (3x + 2y) - (x - y)', 'options' => ['2x + 3y', '4x + y', '2x + y', '4x + 3y'], 'correct' => 'A'],
                ['question' => 'What is the slope of the line y = 3x - 2?', 'options' => ['3', '-2', '1', '-3'], 'correct' => 'A'],
                ['question' => 'Factor: x² - 9', 'options' => ['(x-3)(x+3)', '(x-9)(x+1)', '(x-1)(x+9)', '(x-3)(x-3)'], 'correct' => 'A'],
                ['question' => 'What is the solution to 2x² - 8x + 6 = 0?', 'options' => ['x=1, x=3', 'x=2, x=3', 'x=1, x=2', 'x=3, x=6'], 'correct' => 'A'],
            ],
            'geometry' => [
                ['question' => 'What is the area of a circle with radius 5 units?', 'options' => ['25π', '10π', '5π', '50π'], 'correct' => 'A'],
                ['question' => 'What is the perimeter of a square with side length 8 cm?', 'options' => ['32 cm', '16 cm', '64 cm', '24 cm'], 'correct' => 'A'],
                ['question' => 'What is the sum of angles in a triangle?', 'options' => ['180°', '90°', '360°', '270°'], 'correct' => 'A'],
                ['question' => 'What is the volume of a cube with side length 4 units?', 'options' => ['64 units³', '16 units³', '48 units³', '32 units³'], 'correct' => 'A'],
                ['question' => 'What is the circumference of a circle with diameter 10 cm?', 'options' => ['10π cm', '20π cm', '5π cm', '25π cm'], 'correct' => 'A'],
            ],
            'statistics' => [
                ['question' => 'What is the mean of 2, 4, 6, 8, 10?', 'options' => ['6', '5', '7', '8'], 'correct' => 'A'],
                ['question' => 'What is the median of 3, 1, 4, 2, 5?', 'options' => ['3', '2', '4', '5'], 'correct' => 'A'],
                ['question' => 'What is the mode of 2, 3, 2, 4, 2, 5?', 'options' => ['2', '3', '4', '5'], 'correct' => 'A'],
                ['question' => 'What is the range of 5, 8, 12, 4, 9?', 'options' => ['8', '7', '6', '9'], 'correct' => 'A'],
                ['question' => 'What is the standard deviation of 1, 2, 3, 4, 5?', 'options' => ['√2', '2', '√3', '3'], 'correct' => 'A'],
            ],
        ];

        $this->generateQuestionsFromTopics($bank, $topics, $count);
    }

    private function createEnglishQuestions($bank, $count)
    {
        $topics = [
            'grammar' => [
                ['question' => 'Which sentence is grammatically correct?', 'options' => ['The team are playing well.', 'The team is playing well.', 'The team be playing well.', 'The team am playing well.'], 'correct' => 'B'],
                ['question' => 'What is the past tense of "go"?', 'options' => ['went', 'gone', 'going', 'goes'], 'correct' => 'A'],
                ['question' => 'Which word is an adverb?', 'options' => ['quickly', 'quick', 'quickness', 'quicken'], 'correct' => 'A'],
                ['question' => 'What is the plural form of "child"?', 'options' => ['children', 'childs', 'childrens', 'childes'], 'correct' => 'A'],
                ['question' => 'Which sentence uses the correct pronoun?', 'options' => ['Me and him went to the store.', 'He and I went to the store.', 'Him and me went to the store.', 'I and him went to the store.'], 'correct' => 'B'],
            ],
            'vocabulary' => [
                ['question' => 'What does "ubiquitous" mean?', 'options' => ['Everywhere', 'Rare', 'Expensive', 'Ancient'], 'correct' => 'A'],
                ['question' => 'What is a synonym for "ephemeral"?', 'options' => ['Temporary', 'Permanent', 'Large', 'Small'], 'correct' => 'A'],
                ['question' => 'What does "meticulous" mean?', 'options' => ['Very careful', 'Careless', 'Fast', 'Slow'], 'correct' => 'A'],
                ['question' => 'What is the opposite of "benevolent"?', 'options' => ['Malevolent', 'Beneficial', 'Kind', 'Generous'], 'correct' => 'A'],
                ['question' => 'What does "ambiguous" mean?', 'options' => ['Unclear', 'Clear', 'Certain', 'Obvious'], 'correct' => 'A'],
            ],
            'comprehension' => [
                ['question' => 'In the sentence "The cat sat on the mat," what is the subject?', 'options' => ['The cat', 'The mat', 'Sat', 'On'], 'correct' => 'A'],
                ['question' => 'What type of word is "quickly" in "She runs quickly"?', 'options' => ['Adverb', 'Adjective', 'Noun', 'Verb'], 'correct' => 'A'],
                ['question' => 'Which is a compound sentence?', 'options' => ['I like coffee, but she prefers tea.', 'I like coffee.', 'Because I like coffee, I drink it daily.', 'Drinking coffee is enjoyable.'], 'correct' => 'A'],
                ['question' => 'What is the main idea of a paragraph about the benefits of exercise?', 'options' => ['Exercise is good for health', 'Exercise is expensive', 'Exercise is difficult', 'Exercise is boring'], 'correct' => 'A'],
                ['question' => 'Which transition word shows contrast?', 'options' => ['However', 'Therefore', 'Additionally', 'Consequently'], 'correct' => 'A'],
            ],
        ];

        $this->generateQuestionsFromTopics($bank, $topics, $count);
    }

    private function createScienceQuestions($bank, $count)
    {
        $topics = [
            'physics' => [
                ['question' => 'What is Newton\'s First Law of Motion?', 'options' => ['An object at rest stays at rest', 'Force equals mass times acceleration', 'For every action there is an equal reaction', 'Energy cannot be created or destroyed'], 'correct' => 'A'],
                ['question' => 'What is the speed of light in vacuum?', 'options' => ['3×10⁸ m/s', '3×10⁶ m/s', '3×10¹⁰ m/s', '3×10⁴ m/s'], 'correct' => 'A'],
                ['question' => 'What is the unit of force?', 'options' => ['Newton', 'Joule', 'Watt', 'Pascal'], 'correct' => 'A'],
                ['question' => 'What is the formula for kinetic energy?', 'options' => ['½mv²', 'mgh', 'F×d', 'P×V'], 'correct' => 'A'],
                ['question' => 'What type of wave is sound?', 'options' => ['Longitudinal wave', 'Transverse wave', 'Electromagnetic wave', 'Surface wave'], 'correct' => 'A'],
            ],
            'chemistry' => [
                ['question' => 'What is the chemical symbol for gold?', 'options' => ['Au', 'Ag', 'Fe', 'Cu'], 'correct' => 'A'],
                ['question' => 'What is the atomic number of carbon?', 'options' => ['6', '8', '12', '14'], 'correct' => 'A'],
                ['question' => 'What is H₂O?', 'options' => ['Water', 'Hydrogen', 'Oxygen', 'Carbon dioxide'], 'correct' => 'A'],
                ['question' => 'What is the pH of pure water?', 'options' => ['7', '0', '14', '1'], 'correct' => 'A'],
                ['question' => 'What is the smallest unit of matter?', 'options' => ['Atom', 'Molecule', 'Element', 'Compound'], 'correct' => 'A'],
            ],
            'biology' => [
                ['question' => 'What is the powerhouse of the cell?', 'options' => ['Mitochondria', 'Nucleus', 'Ribosome', 'Chloroplast'], 'correct' => 'A'],
                ['question' => 'What process do plants use to make food?', 'options' => ['Photosynthesis', 'Respiration', 'Digestion', 'Circulation'], 'correct' => 'A'],
                ['question' => 'What is the largest organ in the human body?', 'options' => ['Skin', 'Liver', 'Heart', 'Brain'], 'correct' => 'A'],
                ['question' => 'What carries oxygen in the blood?', 'options' => ['Hemoglobin', 'Platelets', 'Plasma', 'White blood cells'], 'correct' => 'A'],
                ['question' => 'What is the basic unit of life?', 'options' => ['Cell', 'Tissue', 'Organ', 'Organ system'], 'correct' => 'A'],
            ],
        ];

        $this->generateQuestionsFromTopics($bank, $topics, $count);
    }

    private function createComputerScienceQuestions($bank, $count)
    {
        $topics = [
            'programming' => [
                ['question' => 'What does HTML stand for?', 'options' => ['HyperText Markup Language', 'High Tech Modern Language', 'Home Tool Markup Language', 'Hyperlinks and Text Markup Language'], 'correct' => 'A'],
                ['question' => 'Which is not a programming language?', 'options' => ['HTML', 'Python', 'Java', 'C++'], 'correct' => 'A'],
                ['question' => 'What is a variable in programming?', 'options' => ['A named storage location', 'A fixed value', 'A function', 'A loop'], 'correct' => 'A'],
                ['question' => 'What does CSS stand for?', 'options' => ['Cascading Style Sheets', 'Computer Style Sheets', 'Creative Style Sheets', 'Colorful Style Sheets'], 'correct' => 'A'],
                ['question' => 'What is a function in programming?', 'options' => ['A reusable block of code', 'A variable', 'A comment', 'A data type'], 'correct' => 'A'],
            ],
            'algorithms' => [
                ['question' => 'What is Big O notation used for?', 'options' => ['Describing algorithm complexity', 'Naming variables', 'Writing comments', 'Creating functions'], 'correct' => 'A'],
                ['question' => 'What is a binary search?', 'options' => ['A divide and conquer search algorithm', 'A linear search', 'A random search', 'A sequential search'], 'correct' => 'A'],
                ['question' => 'What is recursion?', 'options' => ['A function that calls itself', 'A loop that never ends', 'A variable declaration', 'A conditional statement'], 'correct' => 'A'],
                ['question' => 'What is a sorting algorithm?', 'options' => ['An algorithm to arrange data in order', 'An algorithm to search data', 'An algorithm to delete data', 'An algorithm to encrypt data'], 'correct' => 'A'],
                ['question' => 'What is time complexity?', 'options' => ['How runtime grows with input size', 'How much memory is used', 'How fast the code runs', 'How many lines of code'], 'correct' => 'A'],
            ],
            'computer_basics' => [
                ['question' => 'What is RAM?', 'options' => ['Random Access Memory', 'Read Only Memory', 'Remote Access Memory', 'Real Application Memory'], 'correct' => 'A'],
                ['question' => 'What is CPU?', 'options' => ['Central Processing Unit', 'Computer Processing Unit', 'Central Program Unit', 'Computer Personal Unit'], 'correct' => 'A'],
                ['question' => 'What is an IP address?', 'options' => ['A unique address for devices on a network', 'A password for computers', 'A type of software', 'A hardware component'], 'correct' => 'A'],
                ['question' => 'What is a firewall?', 'options' => ['Network security system', 'Computer cooling system', 'Data backup system', 'File management system'], 'correct' => 'A'],
                ['question' => 'What is cloud computing?', 'options' => ['Storing and accessing data over the internet', 'Weather prediction software', 'Computer hardware', 'Local storage only'], 'correct' => 'A'],
            ],
        ];

        $this->generateQuestionsFromTopics($bank, $topics, $count);
    }

    private function createGeneralKnowledgeQuestions($bank, $count)
    {
        $topics = [
            'geography' => [
                ['question' => 'What is the capital of France?', 'options' => ['Paris', 'London', 'Berlin', 'Madrid'], 'correct' => 'A'],
                ['question' => 'Which is the largest ocean?', 'options' => ['Pacific Ocean', 'Atlantic Ocean', 'Indian Ocean', 'Arctic Ocean'], 'correct' => 'A'],
                ['question' => 'What is the smallest country?', 'options' => ['Vatican City', 'Monaco', 'San Marino', 'Liechtenstein'], 'correct' => 'A'],
                ['question' => 'Which river is the longest in the world?', 'options' => ['Nile River', 'Amazon River', 'Yangtze River', 'Mississippi River'], 'correct' => 'A'],
                ['question' => 'What is the highest mountain?', 'options' => ['Mount Everest', 'K2', 'Kangchenjunga', 'Lhotse'], 'correct' => 'A'],
            ],
            'history' => [
                ['question' => 'In which year did World War II end?', 'options' => ['1945', '1939', '1950', '1940'], 'correct' => 'A'],
                ['question' => 'Who was the first President of the United States?', 'options' => ['George Washington', 'Thomas Jefferson', 'Abraham Lincoln', 'John Adams'], 'correct' => 'A'],
                ['question' => 'Which ancient wonder of the world still stands today?', 'options' => ['Great Pyramid of Giza', 'Hanging Gardens', 'Colossus of Rhodes', 'Lighthouse of Alexandria'], 'correct' => 'A'],
                ['question' => 'When did the Renaissance period begin?', 'options' => ['14th century', '12th century', '16th century', '10th century'], 'correct' => 'A'],
                ['question' => 'Who discovered America?', 'options' => ['Christopher Columbus', 'Marco Polo', 'Vasco da Gama', 'Ferdinand Magellan'], 'correct' => 'A'],
            ],
            'current_affairs' => [
                ['question' => 'What is the most populous country in the world?', 'options' => ['China', 'India', 'United States', 'Indonesia'], 'correct' => 'A'],
                ['question' => 'Which organization was founded after World War II?', 'options' => ['United Nations', 'NATO', 'European Union', 'World Bank'], 'correct' => 'A'],
                ['question' => 'What is the official currency of the European Union?', 'options' => ['Euro', 'Dollar', 'Pound', 'Yen'], 'correct' => 'A'],
                ['question' => 'Which continent is known as the "Dark Continent"?', 'options' => ['Africa', 'Asia', 'South America', 'Australia'], 'correct' => 'A'],
                ['question' => 'What is the largest democracy in the world?', 'options' => ['India', 'United States', 'Brazil', 'Indonesia'], 'correct' => 'A'],
            ],
        ];

        $this->generateQuestionsFromTopics($bank, $topics, $count);
    }

    private function generateQuestionsFromTopics($bank, $topics, $totalQuestions)
    {
        $allQuestions = [];
        foreach ($topics as $topicName => $questions) {
            foreach ($questions as $question) {
                $allQuestions[] = [
                    'text' => $question['question'],
                    'option_a' => $question['options'][0],
                    'option_b' => $question['options'][1],
                    'option_c' => $question['options'][2],
                    'option_d' => $question['options'][3],
                    'correct_answer' => $question['correct'],
                    'question_type' => 'multiple_choice',
                    'marks' => 1,
                    'weight' => 1.00,
                    'difficulty_level' => rand(1, 5),
                    'explanation' => 'This question tests basic knowledge in ' . $topicName,
                ];
            }
        }

        // If we need more questions, duplicate and modify existing ones
        while (count($allQuestions) < $totalQuestions) {
            $original = $allQuestions[array_rand($allQuestions)];
            $modified = $original;
            $modified['text'] = $this->modifyQuestionText($original['text']);
            $modified['difficulty_level'] = rand(1, 5);
            $allQuestions[] = $modified;
        }

        // Shuffle and take the required number
        shuffle($allQuestions);
        $selectedQuestions = array_slice($allQuestions, 0, $totalQuestions);

        // Insert questions
        foreach ($selectedQuestions as $index => $questionData) {
            Question::create([
                'question_bank_id' => $bank->id,
                'exam_id' => null, // Not associated with specific exam
                'text' => $questionData['text'],
                'option_a' => $questionData['option_a'],
                'option_b' => $questionData['option_b'],
                'option_c' => $questionData['option_c'],
                'option_d' => $questionData['option_d'],
                'correct_answer' => $questionData['correct_answer'],
                'question_type' => $questionData['question_type'],
                'explanation' => $questionData['explanation'],
                'marks' => $questionData['marks'],
                'weight' => $questionData['weight'],
                'difficulty_level' => $questionData['difficulty_level'],
            ]);
        }
    }

    private function modifyQuestionText($originalText)
    {
        $modifiers = [
            'According to recent studies, ' => '',
            'In modern context, ' => '',
            'Based on current understanding, ' => '',
            'As per expert analysis, ' => '',
        ];

        $prefix = array_rand($modifiers);
        return $prefix . $modifiers[$prefix] . lcfirst($originalText);
    }
}
