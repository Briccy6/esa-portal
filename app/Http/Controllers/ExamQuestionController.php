<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    public function index($examId)
    {
        // Fetch exam and its questions with question bank relationships
        $exam = \App\Models\Exam::with(['questions.questionBank'])->findOrFail($examId);
        $courses = \App\Models\Course::all(); // Or filter as needed

        return view('exams.questions.index', compact('exam', 'courses'));
    }
    
    public function create($examId)
    {
        $exam = \App\Models\Exam::findOrFail($examId);
        // Pass any other data needed for the form, e.g. courses
        $courses = \App\Models\Course::all();

        return view('exams.questions.create', compact('exam', 'courses'));
    }

    public function store(Request $request, $examId)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'option_a' => 'nullable|string|max:255',
            'option_b' => 'nullable|string|max:255',
            'option_c' => 'nullable|string|max:255',
            'option_d' => 'nullable|string|max:255',
            'correct_answer' => 'nullable|string|max:255',
            'marks' => 'required|integer|min:1',
        ]);

        $validated['exam_id'] = $examId;

        \App\Models\Question::create($validated);

        return redirect()->route('exams.questions', $examId)
            ->with('success', 'Question added successfully!');
    }
    
    public function import(Request $request)
    {
        // Validate file
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle);

        $questions = [];
        while (($row = fgetcsv($handle)) !== false) {
            $questions[] = [
                'text' => $row[0],
                'option_a' => $row[1],
                'option_b' => $row[2],
                'option_c' => $row[3],
                'option_d' => $row[4],
                'correct_answer' => $row[5],
                'marks' => $row[6],
                'exam_id' => $request->exam_id, // Make sure to pass exam_id in your form
            ];
        }
        fclose($handle);

        // Bulk insert
        \App\Models\Question::insert($questions);

        return back()->with('success', 'Questions imported successfully!');
    }
}
