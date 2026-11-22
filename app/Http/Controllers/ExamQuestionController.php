<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ExamQuestionController extends Controller
{
    public function index($examId)
    {
        // Comprehensive debugging for authentication troubleshooting
        \Log::info('=== EXAM QUESTIONS ACCESS DEBUG ===', [
            'timestamp' => now()->toDateTimeString(),
            'exam_id' => $examId,
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
            'request_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'session_exists' => session()->has('session.started'),
            'session_data' => session()->all(),
        ]);

        // Check all authentication guards
        $authChecks = [
            'admin_authenticated' => auth('admin')->check(),
            'admin_user_id' => auth('admin')->check() ? auth('admin')->user()->id : null,
            'admin_user_email' => auth('admin')->check() ? auth('admin')->user()->email : null,
            'staff_authenticated' => auth('staff')->check(),
            'staff_user_id' => auth('staff')->check() ? auth('staff')->user()->id : null,
            'teacher_authenticated' => auth('teacher')->check(),
            'teacher_user_id' => auth('teacher')->check() ? auth('teacher')->user()->id : null,
            'web_authenticated' => auth()->check(),
            'web_user_id' => auth()->check() ? auth()->user()->id : null,
        ];

        \Log::info('Authentication checks results', $authChecks);

        // Validate and initialize session
        if (!session()->has('session.started')) {
            session()->put('session.started', now());
            session()->put('debug_info', [
                'initialized_at' => now(),
                'exam_access' => $examId,
                'user_agent' => request()->userAgent(),
            ]);
            \Log::info('Session initialized for exam access', ['exam_id' => $examId]);
        }

        // Check if user is authenticated for any allowed guard
        $isAuthenticated = auth('admin')->check() || auth('staff')->check() || auth('teacher')->check();
        
        if (!$isAuthenticated) {
            \Log::warning('Unauthorized access attempt to exam questions', [
                'exam_id' => $examId,
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'guards_checked' => ['admin', 'staff', 'teacher'],
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('login')->with('error', 'Please login to access exam questions.');
        }

        \Log::info('User is authenticated, proceeding to load exam questions', [
            'exam_id' => $examId,
            'authenticated_as' => [
                'admin' => auth('admin')->check(),
                'staff' => auth('staff')->check(),
                'teacher' => auth('teacher')->check(),
            ],
            'session_id' => session()->getId(),
        ]);

        try {
            // Fetch exam and its questions with question bank relationships
            $exam = \App\Models\Exam::with(['questions.questionBank'])->findOrFail($examId);
            $courses = \App\Models\Course::all(); // Or filter as needed

            \Log::info('Exam loaded successfully', [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'questions_count' => $exam->questions->count(),
                'session_id' => session()->getId(),
            ]);

            // Return appropriate view based on authentication
            if (auth('admin')->check()) {
                \Log::info('Rendering admin layout for exam questions', ['exam_id' => $examId]);
                return view('exams.questions.index', compact('exam', 'courses'));
            } elseif (auth('staff')->check()) {
                \Log::info('Rendering staff layout for exam questions', ['exam_id' => $examId]);
                return view('exams.questions.index', compact('exam', 'courses'));
            } elseif (auth('teacher')->check()) {
                \Log::info('Rendering teacher layout for exam questions', ['exam_id' => $examId]);
                return view('exams.questions.index', compact('exam', 'courses'));
            } else {
                \Log::error('No valid authentication found for exam questions', [
                    'exam_id' => $examId,
                    'session_id' => session()->getId(),
                    'auth_checks' => $authChecks,
                ]);
                return redirect()->route('login')->with('error', 'Please login to access exam questions.');
            }

        } catch (\Exception $e) {
            \Log::error('Exception in ExamQuestionController@index', [
                'exam_id' => $examId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
            ]);
            
            return redirect()->route('login')->with('error', 'An error occurred while loading exam questions.');
        }
    }
    
    public function create($examId)
    {
        // Comprehensive debugging for authentication troubleshooting
        \Log::info('=== EXAM QUESTIONS CREATE ACCESS DEBUG ===', [
            'timestamp' => now()->toDateTimeString(),
            'exam_id' => $examId,
            'request_url' => request()->fullUrl(),
            'request_method' => request()->method(),
            'request_ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
            'session_exists' => session()->has('session.started'),
            'session_data' => session()->all(),
        ]);

        // Check all authentication guards
        $authChecks = [
            'admin_authenticated' => auth('admin')->check(),
            'admin_user_id' => auth('admin')->check() ? auth('admin')->user()->id : null,
            'admin_user_email' => auth('admin')->check() ? auth('admin')->user()->email : null,
            'staff_authenticated' => auth('staff')->check(),
            'staff_user_id' => auth('staff')->check() ? auth('staff')->user()->id : null,
            'teacher_authenticated' => auth('teacher')->check(),
            'teacher_user_id' => auth('teacher')->check() ? auth('teacher')->user()->id : null,
            'web_authenticated' => auth()->check(),
            'web_user_id' => auth()->check() ? auth()->user()->id : null,
        ];

        \Log::info('Authentication checks results', $authChecks);

        // Validate and initialize session
        if (!session()->has('session.started')) {
            session()->put('session.started', now());
            session()->put('debug_info', [
                'initialized_at' => now(),
                'exam_create_access' => $examId,
                'user_agent' => request()->userAgent(),
            ]);
            \Log::info('Session initialized for exam create access', ['exam_id' => $examId]);
        }

        // Check if user is authenticated for any allowed guard
        $isAuthenticated = auth('admin')->check() || auth('staff')->check() || auth('teacher')->check();
        
        if (!$isAuthenticated) {
            \Log::warning('Unauthorized access attempt to exam questions create', [
                'exam_id' => $examId,
                'session_id' => session()->getId(),
                'session_data' => session()->all(),
                'guards_checked' => ['admin', 'staff', 'teacher'],
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return redirect()->route('login')->with('error', 'Please login to access exam questions.');
        }

        \Log::info('User is authenticated, proceeding to load exam create form', [
            'exam_id' => $examId,
            'authenticated_as' => [
                'admin' => auth('admin')->check(),
                'staff' => auth('staff')->check(),
                'teacher' => auth('teacher')->check(),
            ],
            'session_id' => session()->getId(),
        ]);

        try {
            $exam = \App\Models\Exam::findOrFail($examId);
            // Pass any other data needed for the form, e.g. courses
            $courses = \App\Models\Course::all();

            \Log::info('Exam loaded successfully for create form', [
                'exam_id' => $examId,
                'exam_title' => $exam->title,
                'session_id' => session()->getId(),
            ]);

            return view('exams.questions.create', compact('exam', 'courses'));

        } catch (\Exception $e) {
            \Log::error('Exception in ExamQuestionController@create', [
                'exam_id' => $examId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_id' => session()->getId(),
            ]);
            
            return redirect()->route('login')->with('error', 'An error occurred while loading exam questions.');
        }
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
            // Map CSV columns to database fields
            // CSV format: question_type,text,option_a,option_b,option_c,option_d,correct_answer,marks,difficulty_level,subject_area,explanation,keywords,weight
            $questions[] = [
                'text' => $row[1] ?? '', // text column (index 1)
                'option_a' => $row[2] ?? '', // option_a column (index 2)
                'option_b' => $row[3] ?? '', // option_b column (index 3)
                'option_c' => $row[4] ?? '', // option_c column (index 4)
                'option_d' => $row[5] ?? '', // option_d column (index 5)
                'correct_answer' => $row[6] ?? '', // correct_answer column (index 6)
                'marks' => $row[7] ?? 1, // marks column (index 7)
                'exam_id' => $request->exam_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        fclose($handle);

        if (!empty($questions)) {
            // Bulk insert
            \App\Models\Question::insert($questions);
            return back()->with('success', count($questions) . ' questions imported successfully!');
        }

        return back()->with('error', 'No questions found in the CSV file.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="questions_template.csv"',
        ];

        $csvContent = "question_type,text,option_a,option_b,option_c,option_d,correct_answer,marks,difficulty_level,subject_area,explanation,keywords,weight\n";
        $csvContent .= "multiple_choice,\"What is the capital of France?\",\"London\",\"Paris\",\"Berlin\",\"Madrid\",\"Paris\",5,easy,Geography,\"Paris is the capital city of France.\",\"capital,france,europe\",1\n";
        $csvContent .= "multiple_choice,\"What is 2 + 2?\",\"3\",\"4\",\"5\",\"6\",\"4\",2,easy,Mathematics,\"Basic addition problem.\",\"addition,math\",1\n";

        return response($csvContent, 200, $headers);
    }
}
