<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Mark;
use Illuminate\Http\Request;
use App\Mail\ExamCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Announcement;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth('teacher')->check()) {
            $teacher = auth('teacher')->user();
            $courseIds = $teacher->courses()->pluck('courses.id')->toArray();
            $lessonIds = $teacher->lessons()->pluck('lessons.id')->toArray();

            $exams = Exam::where(function($q) use ($courseIds, $lessonIds) {
                    $q->whereHas('courses', function($q2) use ($courseIds) {
                        $q2->whereIn('courses.id', $courseIds);
                    })
                    ->orWhereHas('lessons', function($q2) use ($lessonIds) {
                        $q2->whereIn('lessons.id', $lessonIds);
                    });
                })
                ->with(['courses', 'lessons'])
                ->latest()
                ->paginate(10);
        } else {
            $exams = Exam::with(['courses', 'lessons'])->latest()->paginate(10);
        }

        $courses = Course::all();
        return view('exams.index', compact('exams', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */

public function create()
    {
        // Check if admin is authenticated
        if (auth('admin')->check()) {
            $user = auth('admin')->user();
            // Admin can access all courses, lessons, and branches
            $courses = Course::all();
            $lessons = Lesson::all();
            $branches = \App\Models\Branch::all();
            $teachers = \App\Models\Teacher::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
        } elseif (auth('teacher')->check()) {
            $user = auth('teacher')->user();
            // Teacher can only access their assigned courses and lessons
            $courses = $user->courses;
            $lessons = $user->lessons;
            $branches = $user->branches ?? collect();
            $teachers = collect([$user]);
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
        } else {
            abort(403, 'Unauthorized');
        }
        
        $questionBanks = \App\Models\QuestionBank::where(function($query) use ($user) {
            if (auth('admin')->check()) {
                // Admin can see all question banks
                return $query;
            } elseif (auth('teacher')->check()) {
                return $query->where('teacher_id', $user->id);
            }
        })->get();

        $examPassword = \Illuminate\Support\Str::random(8);

        return view('exams.create', compact(
            'courses', 'lessons', 'branches', 'teachers', 
            'academicYears', 'terms', 'questionBanks', 'examPassword'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exam_weight' => 'required|integer|min:1|max:100',
            'total_marks' => 'required|integer|min:1',
            'class_date' => 'required|date',
            'exam_type' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'required|exists:teachers,id',
            'term_id' => 'required|exists:terms,id',
            'notes' => 'nullable|string',
            'exam_password' => 'nullable|string|max:255',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'lesson_ids' => 'required|array|min:1',
            'lesson_ids.*' => 'exists:lessons,id',
        ]);
        
        $request->validate([
            'question_bank_ids' => 'nullable|array',
            'question_bank_ids.*' => 'exists:question_banks,id',
            'questions_per_bank' => 'nullable|integer|min:1|max:50',
        ]);
        
        try {
            // Debug: Log the request data first
            \Log::info('Request data: ' . json_encode($request->all()));
            
            // Create a single exam for multiple courses and lessons
            $examData = [
                'title' => $request->title,
                'description' => $request->description,
                'exam_type' => $request->exam_type,
                'class_date' => $request->class_date,
                'academic_year_id' => $request->academic_year_id,
                'teacher_id' => $request->teacher_id,
                'term_id' => $request->term_id,
                'notes' => $request->notes,
                'exam_password' => $request->exam_password,
                'is_active' => true, // Default to active
            ];
            
            // Add optional fields if they exist in the database
            if (Schema::hasColumn('exams', 'exam_weight')) {
                $examData['exam_weight'] = $request->exam_weight;
            }
            if (Schema::hasColumn('exams', 'total_marks')) {
                $examData['total_marks'] = $request->total_marks;
            }
            if (Schema::hasColumn('exams', 'branch_id')) {
                $examData['branch_id'] = $request->branch_id;
            }
            
            \Log::info('Exam data to create: ' . json_encode($examData));
            
            $exam = Exam::create($examData);
            
            \Log::info('Exam created successfully with ID: ' . $exam->id);
            
            // Attach courses to this exam
            $exam->courses()->attach($request->course_ids);
            
            // Attach lessons to this exam
            $exam->lessons()->attach($request->lesson_ids);
            
            \Log::info('Courses and lessons attached successfully');
            
            // Process question bank selections
            if ($request->question_bank_ids && $request->questions_per_bank) {
                $this->addQuestionsFromBanks($exam, $request->question_bank_ids, $request->questions_per_bank);
            }
            
            // Redirect based on authentication
            if (auth('admin')->check()) {
                return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
            } else {
                return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
            }
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            \Log::error('Exam creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('Request data: ' . json_encode($request->all()));
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create exam: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['courses', 'lessons', 'academic_year', 'term', 'teacher', 'branch']);
        return view('exams.show', compact('exam'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $courses = Course::all();
        $lessons = Lesson::all();
        $exam->load(['courses', 'lessons']);
        return view('exams.edit', compact('exam', 'courses', 'lessons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'lesson_ids' => 'required|array|min:1',
            'lesson_ids.*' => 'exists:lessons,id',
            'total_marks' => 'required|integer|min:1',
            'exam_weight' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
        ]);

        $exam->update([
            'title' => $request->title,
            'date' => $request->date,
            'total_marks' => $request->total_marks,
            'exam_weight' => $request->exam_weight,
            'description' => $request->description,
            'instructions' => $request->instructions,
        ]);

        $exam->courses()->sync($request->course_ids);
        $exam->lessons()->sync($request->lesson_ids);

        return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        $exam->courses()->detach();
        $exam->lessons()->detach();
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
    }

    /**
     * Show the exam page for the student to take the exam.
     */
    public function take(Exam $exam)
    {
        $student = auth('student')->user();

        // Prevent re-taking if disqualified (session)
        if (session("disqualified_exam_{$exam->id}")) {
            return redirect()->route('dashboard')->with('error', 'You have been disqualified from this exam.');
        }

        // Prevent re-taking if disqualified (DB)
        $existingMark = Mark::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingMark && $existingMark->status === 'disqualified') {
            return redirect()->route('dashboard')->with('error', 'You have been disqualified from this exam.');
        }

        // Prevent re-attempts
        if ($existingMark) {
            return redirect()->route('dashboard')->with('error', 'You already took this exam.');
        }

        // Check and deduct tokens if required
        if ($exam->requiresTokens()) {
            $studentToken = $student->token;
            
            if (!$studentToken || !$studentToken->canAfford($exam->token_cost)) {
                $balance = $studentToken->balance ?? 0;
                return redirect()->route('student.dashboard')->with('error', 
                    "Insufficient tokens! This exam requires {$exam->token_cost} tokens. You have {$balance} tokens.");
            }

            // Deduct tokens for exam attempt
            $transaction = $studentToken->spendTokens(
                $exam->token_cost, 
                "Exam attempt: {$exam->title}", 
                'exam'
            );

            if (!$transaction) {
                return redirect()->route('student.dashboard')->with('error', 'Failed to deduct tokens. Please try again.');
            }
        }

        // Start cheating detection session or timestamp
        session(["exam_start_time_{$exam->id}" => now()]);
        session(["cheating_attempts_{$exam->id}" => 0]);
        $questions = $exam->questions->shuffle()->values();
        return view('exams.take', [
            'exam' => $exam,
            'questions' => $questions,
            'student' => $student,
        ]);
    }

    /**
     * Disqualify the student and show the disqualified page.
     */
    public function disqualify(Exam $exam)
    {
        $student = auth('student')->user();
        session(["disqualified_exam_{$exam->id}" => true]);

        // Record disqualification in DB
        $existingMark = Mark::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingMark) {
            $existingMark->update([
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => 0,
                'grade_id' => null,
                'status' => 'disqualified',
            ]);
        } else {
            Mark::create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => 0,
                'grade_id' => null,
                'status' => 'disqualified',
            ]);
        }

        return view('exams.disqualified', compact('exam'));
    }

    /**
     * Submit the exam answers.
     */
    public function submit(Request $request, Exam $exam)
    {
        $student = auth('student')->user();

        // Fetch all questions for this exam
        $questions = $exam->questions;

        $answers = $request->input('answers', []);
        $rawScore = 0;
        $totalPossible = 0;

        foreach ($questions as $question) {
            $studentAnswer = $answers[$question->id] ?? null;
            // Compare exactly as stored (no strtoupper, no conversion)
            if ($studentAnswer && $studentAnswer === $question->correct_answer) {
                $rawScore += $question->marks;
            }
            $totalPossible += $question->marks;
        }

        // Normalize to 100 if needed
        if ($totalPossible != 100 || $totalPossible < 10) {
            $score = $totalPossible > 0 ? round(($rawScore / $totalPossible) * 100, 2) : 0;
        } else {
            $score = $rawScore;
        }

        // Save the mark
        $existingMark = Mark::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingMark) {
            $existingMark->update([
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => $score,
                'grade_id' => null,
                'status' => 'completed',
            ]);
        } else {
            Mark::create([
                'student_id' => $student->id,
                'exam_id' => $exam->id,
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => $score,
                'grade_id' => null,
                'status' => 'completed',
            ]);
        }

        return redirect()->route('student.dashboard')->with('success', 'Exam submitted! Your score: ' . $score . '/100');
    }

    /**
     * Add questions from question banks to an exam
     */
    private function addQuestionsFromBanks(Exam $exam, array $questionBankIds, int $questionsPerBank)
    {
        $allQuestions = collect();
        
        foreach ($questionBankIds as $bankId) {
            $bank = \App\Models\QuestionBank::find($bankId);
            if ($bank) {
                // Get random questions from this bank
                $questions = $bank->questions()->inRandomOrder()->limit($questionsPerBank)->get();
                $allQuestions = $allQuestions->concat($questions);
            }
        }
        
        // Create new questions for the exam
        foreach ($allQuestions as $question) {
            \App\Models\Question::create([
                'exam_id' => $exam->id,
                'question_bank_id' => $question->question_bank_id,
                'text' => $question->text,
                'question_type' => $question->question_type,
                'option_a' => $question->option_a,
                'option_b' => $question->option_b,
                'option_c' => $question->option_c,
                'option_d' => $question->option_d,
                'correct_answer' => $question->correct_answer,
                'correct_answers' => $question->correct_answers,
                'marks' => $question->marks,
                'weight' => $question->weight,
                'difficulty_level' => $question->difficulty_level,
                'explanation' => $question->explanation,
                'keywords' => $question->keywords,
            ]);
        }
        
        \Log::info('Added ' . $allQuestions->count() . ' questions from question banks to exam ' . $exam->id);
    }

    /**
     * Preview questions from selected question banks
     */
    public function previewQuestions(Request $request)
    {
        $request->validate([
            'question_bank_ids' => 'required|array',
            'question_bank_ids.*' => 'exists:question_banks,id',
            'questions_per_bank' => 'required|integer|min:1|max:50',
        ]);
        
        $allQuestions = collect();
        
        foreach ($request->question_bank_ids as $bankId) {
            $bank = \App\Models\QuestionBank::find($bankId);
            if ($bank) {
                $questions = $bank->questions()->inRandomOrder()->limit($request->questions_per_bank)->get();
                $allQuestions = $allQuestions->concat($questions);
            }
        }
        
        // Format questions for preview
        $formattedQuestions = $allQuestions->map(function($question) {
            return [
                'text' => $question->text,
                'question_type' => $question->question_type,
                'marks' => $question->marks,
                'options' => $this->formatOptions($question),
                'question_bank_name' => $question->questionBank->name ?? 'Unknown',
            ];
        });
        
        return response()->json(['questions' => $formattedQuestions]);
    }

    private function formatOptions($question)
    {
        $options = [];
        if ($question->option_a) $options[] = $question->option_a;
        if ($question->option_b) $options[] = $question->option_b;
        if ($question->option_c) $options[] = $question->option_c;
        if ($question->option_d) $options[] = $question->option_d;
        
        return implode(' | ', $options);
    }
}