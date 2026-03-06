<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionBankController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->admin) {
            // Admin can see all question banks
            $questionBanks = QuestionBank::with(['course', 'academicYear', 'questions' => function($query) {
                $query->select('id', 'question_bank_id', 'question_type', 'text', 'marks');
            }])
            ->orderBy('name')
            ->paginate(10);
        } elseif ($user->teacher) {
            // Teacher can only see their own question banks
            $teacher = $user->teacher;
            $questionBanks = QuestionBank::where('teacher_id', $teacher->id)
                ->with(['course', 'academicYear', 'questions' => function($query) {
                    $query->select('id', 'question_bank_id', 'question_type', 'text', 'marks');
                }])
                ->orderBy('name')
                ->paginate(10);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('question-banks.index', compact('questionBanks'));
    }

    public function create()
    {
        $user = Auth::user();
        
        if ($user->admin) {
            // Admin can access all courses and academic years
            $courses = Course::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = \App\Models\Teacher::all();
        } elseif ($user->teacher) {
            // Teacher can only access their assigned courses
            $teacher = $user->teacher;
            $courses = $teacher->courses;
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = collect([$teacher]);
        } else {
            abort(403, 'Unauthorized');
        }

        return view('question-banks.create', compact('courses', 'academicYears', 'teachers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        QuestionBank::create([
            'name' => $request->name,
            'description' => $request->description,
            'teacher_id' => $request->teacher_id,
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => true,
        ]);

        return redirect()->route('question-banks.index')
            ->with('success', 'Question bank created successfully!');
    }

    public function show(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);
        
        $questionBank->load(['questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        return view('question-banks.show', compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);
        
        $teacher = Auth::user()->teacher;
        $courses = $teacher->courses;
        $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();

        return view('question-banks.edit', compact('questionBank', 'courses', 'academicYears'));
    }

    public function update(Request $request, QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'course_id' => 'nullable|exists:courses,id',
            'academic_year_id' => 'nullable|exists:academic_years,id',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $questionBank->update([
            'name' => $request->name,
            'description' => $request->description,
            'course_id' => $request->course_id,
            'academic_year_id' => $request->academic_year_id,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('question-banks.show', $questionBank)
            ->with('success', 'Question bank updated successfully!');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $this->authorize('delete', $questionBank);
        
        $questionBank->delete();
        
        return redirect()->route('question-banks.index')
            ->with('success', 'Question bank deleted successfully!');
    }

    public function addQuestion(QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        return view('question-banks.add-question', compact('questionBank'));
    }

    public function storeQuestion(Request $request, QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $validator = Validator::make($request->all(), [
            'question_type' => 'required|in:multiple_choice,multiple_select,true_false,short_answer,essay',
            'text' => 'required|string',
            'marks' => 'required|integer|min:1',
            'weight' => 'nullable|numeric|min:0|max:10',
            'difficulty_level' => 'nullable|integer|min:1|max:5',
            'explanation' => 'nullable|string',
            'keywords' => 'nullable|string',
            // Multiple choice options
            'option_a' => 'required_if:question_type,multiple_choice,multiple_select|string',
            'option_b' => 'required_if:question_type,multiple_choice,multiple_select|string',
            'option_c' => 'nullable|string',
            'option_d' => 'nullable|string',
            'correct_answer' => 'required_if:question_type,multiple_choice,true_false|string',
            'correct_answers' => 'required_if:question_type,multiple_select|array',
            'correct_answers.*' => 'string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $questionData = [
            'question_bank_id' => $questionBank->id,
            'question_type' => $request->question_type,
            'text' => $request->text,
            'marks' => $request->marks,
            'weight' => $request->weight ?? 1.00,
            'difficulty_level' => $request->difficulty_level ?? 1,
            'explanation' => $request->explanation,
            'keywords' => $request->keywords,
        ];

        // Add question type specific fields
        if (in_array($request->question_type, ['multiple_choice', 'multiple_select'])) {
            $questionData['option_a'] = $request->option_a;
            $questionData['option_b'] = $request->option_b;
            $questionData['option_c'] = $request->option_c;
            $questionData['option_d'] = $request->option_d;
            
            if ($request->question_type === 'multiple_choice') {
                $questionData['correct_answer'] = $request->correct_answer;
            } else {
                $questionData['correct_answers'] = $request->correct_answers;
            }
        } elseif (in_array($request->question_type, ['true_false', 'short_answer'])) {
            $questionData['correct_answer'] = $request->correct_answer;
        }

        Question::create($questionData);

        return redirect()->route('question-banks.show', $questionBank)
            ->with('success', 'Question added successfully!');
    }

    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $data = array_map('str_getcsv', file($path));

        $headers = array_shift($data); // Remove header row
        $imported = 0;
        $errors = [];

        foreach ($data as $row) {
            try {
                $questionData = array_combine($headers, $row);
                
                Question::create([
                    'question_bank_id' => $questionBank->id,
                    'question_type' => $questionData['question_type'] ?? 'multiple_choice',
                    'text' => $questionData['text'],
                    'option_a' => $questionData['option_a'] ?? null,
                    'option_b' => $questionData['option_b'] ?? null,
                    'option_c' => $questionData['option_c'] ?? null,
                    'option_d' => $questionData['option_d'] ?? null,
                    'correct_answer' => $questionData['correct_answer'] ?? null,
                    'marks' => $questionData['marks'] ?? 1,
                    'explanation' => $questionData['explanation'] ?? null,
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
            }
        }

        return redirect()->route('question-banks.show', $questionBank)
            ->with('success', "Imported {$imported} questions successfully!")
            ->with('errors', $errors);
    }

    public function exportQuestions(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);

        $questions = $questionBank->questions;
        $csvData = [];

        // Headers
        $csvData[] = [
            'question_type', 'text', 'option_a', 'option_b', 'option_c', 'option_d',
            'correct_answer', 'marks', 'explanation', 'difficulty_level'
        ];

        // Data rows
        foreach ($questions as $question) {
            $csvData[] = [
                $question->question_type,
                $question->text,
                $question->option_a,
                $question->option_b,
                $question->option_c,
                $question->option_d,
                $question->correct_answer,
                $question->marks,
                $question->explanation,
                $question->difficulty_level,
            ];
        }

        $filename = "question_bank_{$questionBank->id}_questions.csv";
        $handle = fopen('php://memory', 'w');

        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }

        fseek($handle, 0);
        $content = stream_get_contents($handle);
        fclose($handle);

        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"{$filename}\"");
    }
}
