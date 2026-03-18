<?php

namespace App\Http\Controllers;

use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class QuestionBankController extends Controller
{
    use AuthorizesRequests {
        authorize as protected baseAuthorize;
    }

    /**
     * Ensure the correct guard is used for authorization when multiple guards are in use.
     */
    public function authorize($ability, $arguments = [])
    {
        if (auth('admin')->check()) {
            Auth::shouldUse('admin');
        } elseif (auth('staff')->check()) {
            Auth::shouldUse('staff');
        } elseif (auth('teacher')->check()) {
            Auth::shouldUse('teacher');
        }
        return $this->baseAuthorize($ability, $arguments);
    }
    public function index()
    {
        $user = $this->getAuthUser();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (auth('admin')->check()) {
            // Admin can see all question banks
            $questionBanks = QuestionBank::with(['course', 'academicYear', 'teacher', 'questions' => function($query) {
                $query->select('id', 'question_bank_id', 'question_type', 'text', 'marks');
            }])
            ->orderBy('name')
            ->paginate(10);
        } elseif (auth('staff')->check()) {
            // Staff can see all question banks
            $questionBanks = QuestionBank::with(['course', 'academicYear', 'teacher', 'questions' => function($query) {
                $query->select('id', 'question_bank_id', 'question_type', 'text', 'marks');
            }])
            ->orderBy('name')
            ->paginate(10);
        } elseif (auth('teacher')->check()) {
            $teacher = auth('teacher')->user();
            $questionBanks = QuestionBank::where('teacher_id', $teacher->id)
                ->with(['course', 'academicYear', 'teacher', 'questions' => function($query) {
                    $query->select('id', 'question_bank_id', 'question_type', 'text', 'marks');
                }])
                ->orderBy('name')
                ->paginate(10);
        } else {
            abort(403, 'Unauthorized');
        }

        $viewPath = $this->getViewPath() . '.index';
        return view($viewPath, compact('questionBanks'));
    }

    /**
     * Get the appropriate redirect route based on user type.
     */
    protected function getRedirectRoute()
    {
        if (auth('admin')->check()) {
            return 'admin';
        } elseif (auth('staff')->check()) {
            return 'staff';
        } elseif (auth('teacher')->check()) {
            return 'teacher';
        }
        return 'admin'; // fallback
    }

    /**
     * Get the appropriate view path based on user type.
     */
    protected function getViewPath()
    {
        if (auth('admin')->check()) {
            return 'question-banks';
        } elseif (auth('staff')->check()) {
            return 'staff.question-banks';
        } elseif (auth('teacher')->check()) {
            return 'teacher.question-banks';
        }
        return 'question-banks'; // fallback
    }

    /**
     * Get the authenticated user from admin, staff, or teacher guard.
     */
    protected function getAuthUser()
    {
        if (auth('admin')->check()) {
            return auth('admin')->user();
        }
        if (auth('staff')->check()) {
            return auth('staff')->user();
        }
        if (auth('teacher')->check()) {
            return auth('teacher')->user();
        }
        return null;
    }

    public function create()
    {
        $user = $this->getAuthUser();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (auth('admin')->check()) {
            $courses = Course::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = \App\Models\Teacher::all();
        } elseif (auth('staff')->check()) {
            $courses = Course::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = \App\Models\Teacher::all();
        } elseif (auth('teacher')->check()) {
            $teacher = auth('teacher')->user();
            $courses = $teacher->courses;
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = collect([$teacher]);
        } else {
            abort(403, 'Unauthorized');
        }

        $viewPath = $this->getViewPath() . '.create';
        return view($viewPath, compact('courses', 'academicYears', 'teachers'));
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

        return redirect()->route($this->getRedirectRoute() . '.question-banks.index')
            ->with('success', 'Question bank created successfully!');
    }

    public function show(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);
        
        $questionBank->load(['questions' => function($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $viewPath = $this->getViewPath() . '.show';
        return view($viewPath, compact('questionBank'));
    }

    public function edit(QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        if (auth('admin')->check() || auth('staff')->check()) {
            $courses = Course::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = \App\Models\Teacher::all();
        } else {
            $teacher = auth('teacher')->user();
            $courses = $teacher->courses;
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $teachers = collect([$teacher]);
        }

        $viewPath = $this->getViewPath() . '.edit';
        return view($viewPath, compact('questionBank', 'courses', 'academicYears', 'teachers'));
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

        return redirect()->route($this->getRedirectRoute() . '.question-banks.show', $questionBank)
            ->with('success', 'Question bank updated successfully!');
    }

    public function destroy(QuestionBank $questionBank)
    {
        $this->authorize('delete', $questionBank);
        
        $questionBank->delete();
        
        return redirect()->route($this->getRedirectRoute() . '.question-banks.index')
            ->with('success', 'Question bank deleted successfully!');
    }

    public function addQuestion(QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $viewPath = $this->getViewPath() . '.add-question';
        return view($viewPath, compact('questionBank'));
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
            'subject_area' => 'nullable|in:mathematics,biology,general',
            'biology_topic' => 'nullable|string',
            'diagram_labels' => 'nullable|string',
            'question_image' => 'nullable|image|max:5120', // 5MB max
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
            'subject_area' => $request->subject_area ?? 'general',
            'biology_topic' => $request->biology_topic,
            'diagram_labels' => $request->diagram_labels,
        ];

        // Handle image upload
        if ($request->hasFile('question_image')) {
            $image = $request->file('question_image');
            $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images/question_images'), $imageName);
            $questionData['question_image'] = $imageName;
        }

        // Add question type specific fields
        if (in_array($request->question_type, ['multiple_choice', 'multiple_select'])) {
            // Handle options from array or individual fields
            if ($request->has('options')) {
                $options = $request->options;
                $questionData['option_a'] = $options[0] ?? '';
                $questionData['option_b'] = $options[1] ?? '';
                $questionData['option_c'] = $options[2] ?? '';
                $questionData['option_d'] = $options[3] ?? '';
            } else {
                $questionData['option_a'] = $request->option_a;
                $questionData['option_b'] = $request->option_b;
                $questionData['option_c'] = $request->option_c;
                $questionData['option_d'] = $request->option_d;
            }
            
            if ($request->question_type === 'multiple_choice') {
                $questionData['correct_answer'] = $request->correct_answer;
            } else {
                $questionData['correct_answers'] = $request->correct_answers;
            }
        } elseif (in_array($request->question_type, ['true_false', 'short_answer'])) {
            $questionData['correct_answer'] = $request->correct_answer;
        }

        Question::create($questionData);

        return redirect()->route($this->getRedirectRoute() . '.question-banks.show', $questionBank)
            ->with('success', 'Question added successfully!');
    }

    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
        $this->authorize('update', $questionBank);

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        if (!$file) {
            return back()->with('error', 'No file was uploaded.');
        }

        // Check if it's a valid CSV file by checking the extension and content
        $extension = strtolower($file->getClientOriginalExtension());
        if (!in_array($extension, ['csv', 'txt'])) {
            return back()->with('error', 'File must be a CSV or TXT file.');
        }
        $path = $file->getRealPath();
        
        // Read CSV file properly
        $csvData = [];
        if (($handle = fopen($path, 'r')) !== FALSE) {
            while (($row = fgetcsv($handle, 1000, ',')) !== FALSE) {
                $csvData[] = $row;
            }
            fclose($handle);
        }
        
        if (empty($csvData)) {
            return back()->with('error', 'CSV file is empty or could not be read.');
        }

        $headers = array_shift($csvData); // Remove header row
        // Clean headers - trim whitespace, normalize to lowercase for case-insensitive matching
        $headers = array_map(function($header) {
            return strtolower(trim(preg_replace('/\s+/', ' ', $header)));
        }, $headers);
        
        $imported = 0;
        $errors = [];

        foreach ($csvData as $rowIndex => $row) {
            try {
                // Skip empty rows
                if (empty(array_filter($row))) {
                    continue;
                }
                
                // Pad or trim row to match header count (handles extra/missing columns)
                $row = array_pad(array_slice($row, 0, count($headers)), count($headers), null);
                $row = array_map(function($val) { return $val !== null ? trim((string) $val) : ''; }, $row);
                
                $questionData = array_combine($headers, $row);
                if ($questionData === false) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Could not parse columns.";
                    continue;
                }
                
                // Get text - support common column names
                $text = $questionData['text'] ?? $questionData['question'] ?? $questionData['question text'] ?? '';
                $text = trim($text);
                
                // Validate required fields
                if ($text === '') {
                    $errors[] = "Row " . ($rowIndex + 2) . ": Question text is required";
                    continue;
                }
                
                // Enhanced question data with support for math/biology
                $questionCreateData = [
                    'exam_id' => null, // Allow questions in question banks without being tied to an exam
                    'question_bank_id' => $questionBank->id,
                    'question_type' => $questionData['question_type'] ?? 'multiple_choice',
                    'text' => $text,
                    'option_a' => $questionData['option_a'] ?? null,
                    'option_b' => $questionData['option_b'] ?? null,
                    'option_c' => $questionData['option_c'] ?? null,
                    'option_d' => $questionData['option_d'] ?? null,
                    'correct_answer' => isset($questionData['correct_answer']) && $questionData['correct_answer'] !== '' ? $questionData['correct_answer'] : null,
                    'marks' => is_numeric($questionData['marks'] ?? 1) ? (int)$questionData['marks'] : 1,
                    'explanation' => $questionData['explanation'] ?? null,
                    'subject_area' => $questionData['subject_area'] ?? 'general',
                    'biology_topic' => $questionData['biology_topic'] ?? null,
                    'diagram_labels' => $questionData['diagram_labels'] ?? null,
                    'keywords' => $questionData['keywords'] ?? null,
                    'difficulty_level' => is_numeric($questionData['difficulty_level'] ?? 1) ? (int)$questionData['difficulty_level'] : 1,
                    'weight' => is_numeric($questionData['weight'] ?? 1.0) ? (float)$questionData['weight'] : 1.0,
                ];
                
                Question::create($questionCreateData);
                
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        $message = "Imported {$imported} questions successfully!";
        if (!empty($errors)) {
            $message .= " However, there were " . count($errors) . " errors.";
        }

        return redirect()->route($this->getRedirectRoute() . '.question-banks.show', $questionBank)
            ->with('success', $message)
            ->with('errors', $errors);
    }

    public function exportQuestions(QuestionBank $questionBank)
    {
        $this->authorize('view', $questionBank);

        $questions = $questionBank->questions;
        $csvData = [];

        // Enhanced headers with math/biology support
        $csvData[] = [
            'question_type', 'text', 'option_a', 'option_b', 'option_c', 'option_d',
            'correct_answer', 'marks', 'explanation', 'difficulty_level', 'subject_area',
            'biology_topic', 'diagram_labels', 'keywords', 'weight'
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
                $question->subject_area ?? 'general',
                $question->biology_topic,
                $question->diagram_labels,
                $question->keywords,
                $question->weight,
            ];
        }

        $filename = "question_bank_{$questionBank->id}_questions_enhanced.csv";
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
