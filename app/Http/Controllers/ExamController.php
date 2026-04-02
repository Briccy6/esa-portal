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
use Illuminate\Support\Facades\Log;

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

            $courses = $teacher->courses ?? collect();
            return view('teacher.exams.index', compact('exams', 'courses'));
        } elseif (auth('staff')->check()) {
            $staff = auth('staff')->user();
            if ($staff->role === 'DOS') {
                // DOS can see all exams
                $exams = Exam::with(['courses', 'lessons'])->latest()->paginate(10);
                $courses = Course::all();
                return view('exams.dos_index', compact('exams', 'courses'));
            } else {
                // Other staff can see all exams
                $exams = Exam::with(['courses', 'lessons'])->latest()->paginate(10);
            }
        } elseif (auth('admin')->check()) {
            $exams = Exam::with(['courses', 'lessons'])->latest()->paginate(10);
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
            \Log::info('Admin user detected, returning admin create view');
            $user = auth('admin')->user();
            // Admin can access all courses, lessons, and branches
            $courses = Course::all();
            $lessons = Lesson::all();
            $branches = \App\Models\Branch::all();
            $teachers = \App\Models\Teacher::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
            $examPassword = \Illuminate\Support\Str::random(8);
            
            // Get question banks for admin (can see all)
            try {
                $questionBanks = \App\Models\QuestionBank::with(['questions', 'teacher', 'course'])->get();
            } catch (\Exception $e) {
                $questionBanks = collect();
                \Log::error('Question banks table not available: ' . $e->getMessage());
            }
            
            return view('exams.create', compact(
                'courses', 'lessons', 'branches', 'teachers', 
                'academicYears', 'terms', 'questionBanks', 'examPassword'
            ));
        } elseif (auth('teacher')->check()) {
            \Log::info('Teacher user detected, returning teacher create view');
            $user = auth('teacher')->user();
            // Teacher can only access their assigned courses and lessons
            $courses = $user->courses ?? collect();
            $lessons = $user->lessons ?? collect();
            $branches = $user->branches ?? collect();
            $teachers = collect([$user]);
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
            $examPassword = \Illuminate\Support\Str::random(8);
            
            // Get question banks for teacher (can only see their own)
            try {
                $questionBanks = \App\Models\QuestionBank::where('teacher_id', $user->id)
                    ->with(['questions', 'teacher', 'course'])->get();
            } catch (\Exception $e) {
                $questionBanks = collect();
                \Log::error('Question banks table not available: ' . $e->getMessage());
            }
            
            return view('teacher.exams.create', compact(
                'courses', 'lessons', 'branches', 'teachers', 
                'academicYears', 'terms', 'questionBanks', 'examPassword'
            ));
        } elseif (auth('staff')->check()) {
            $user = auth('staff')->user();
            \Log::info('Staff user detected, role: ' . $user->role);
            if ($user->role === 'DOS') {
                \Log::info('DOS user detected, returning dos_create view');
                // DOS can only access courses in their assigned branches
                $userBranches = $user->branches;
                \Log::info('DOS user branches: ' . $userBranches->pluck('id')->implode(', '));
                
                // Filter courses to only those in DOS's assigned branches
                $courses = Course::whereHas('branches', function($query) use ($userBranches) {
                    $query->whereIn('branches.id', $userBranches->pluck('id'));
                })->get();
                
                // Filter lessons to only those in DOS's assigned branches
                $lessons = Lesson::whereHas('course', function($query) use ($userBranches) {
                    $query->whereHas('branches', function($branchQuery) use ($userBranches) {
                        $branchQuery->whereIn('branches.id', $userBranches->pluck('id'));
                    });
                })->get();
                
                // Only show branches assigned to DOS
                $branches = $userBranches;
                $teachers = \App\Models\Teacher::all();
                $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
                $terms = \App\Models\Term::all();
                $examPassword = \Illuminate\Support\Str::random(8);
                
                // Get question banks for DOS (can only see those from courses in their branches)
                try {
                    $questionBanks = \App\Models\QuestionBank::whereHas('course', function($query) use ($userBranches) {
                        $query->whereHas('branches', function($branchQuery) use ($userBranches) {
                            $branchQuery->whereIn('branches.id', $userBranches->pluck('id'));
                        });
                    })->with(['questions', 'teacher', 'course'])->get();
                } catch (\Exception $e) {
                    $questionBanks = collect();
                    \Log::error('Question banks table not available: ' . $e->getMessage());
                }
                
                return view('exams.dos_create', compact(
                    'courses', 'lessons', 'branches', 'teachers', 
                    'academicYears', 'terms', 'questionBanks', 'examPassword'
                ));
            } else {
                // Other staff can access all courses, lessons, and branches
                $courses = Course::all();
                $lessons = Lesson::all();
                $branches = \App\Models\Branch::all();
                $teachers = \App\Models\Teacher::all();
                $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
                $terms = \App\Models\Term::all();
                $examPassword = \Illuminate\Support\Str::random(8);
                
                // Get question banks for other staff (can see all)
                try {
                    $questionBanks = \App\Models\QuestionBank::with(['questions', 'teacher', 'course'])->get();
                } catch (\Exception $e) {
                    $questionBanks = collect();
                    \Log::error('Question banks table not available: ' . $e->getMessage());
                }
                
                return view('exams.create', compact(
                    'courses', 'lessons', 'branches', 'teachers', 
                    'academicYears', 'terms', 'questionBanks', 'examPassword'
                ));
            }
        } else {
            abort(403, 'Unauthorized');
        }
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
            Log::info('Request data: ' . json_encode($request->all()));
            
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
            
            Log::info('Exam data to create: ' . json_encode($examData));
            
            $exam = Exam::create($examData);
            
            Log::info('Exam created successfully with ID: ' . $exam->id);
            
            // Attach courses to this exam
            $exam->courses()->attach($request->course_ids);
            
            // Attach lessons to this exam
            $exam->lessons()->attach($request->lesson_ids);
            
            Log::info('Courses and lessons attached successfully');
            
            // Process question bank selections
            if ($request->question_bank_ids && $request->questions_per_bank) {
                $this->addQuestionsFromBanks($exam, $request->question_bank_ids, $request->questions_per_bank);
            }
            
            // Auto-calculate token cost based on number of questions
            $exam->updateTokenCostBasedOnQuestions();
            
            Log::info('Token cost calculated based on questions: ' . $exam->token_cost . ' tokens');
            
            // Redirect based on authentication
            if (auth('admin')->check()) {
                return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
            } elseif (auth('teacher')->check()) {
                return redirect()->route('teacher.exams.index')->with('success', 'Exam created successfully.');
            } elseif (auth('staff')->check()) {
                $staff = auth('staff')->user();
                if ($staff->role === 'DOS') {
                    return redirect()->route('dos.exams.index')->with('success', 'Exam created successfully.');
                }
                return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
            } else {
                return redirect()->route('exams.index')->with('success', 'Exam created successfully.');
            }
        } catch (\Exception $e) {
            // Log the detailed error for debugging
            Log::error('Exam creation failed: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            Log::error('Request data: ' . json_encode($request->all()));
            
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
        
        // Load marks for this exam (both exam and test marks related to this exam)
        $marks = \App\Models\Mark::where('exam_id', $exam->id)
            ->with(['student', 'course', 'branch', 'lesson'])
            ->get()
            ->groupBy('student_id');
            
        // Also load test marks from lessons related to this exam
        $lessonIds = $exam->lessons->pluck('id');
        $testMarks = \App\Models\Mark::whereIn('lesson_id', $lessonIds)
            ->where('assessment_type', 'test')
            ->with(['student', 'course', 'branch', 'lesson'])
            ->get()
            ->groupBy('student_id');

        // Return appropriate view based on authentication
        if (auth('teacher')->check()) {
            return view('teacher.exams.show', compact('exam', 'marks', 'testMarks'));
        } elseif (auth('staff')->check() && auth('staff')->user()->role === 'DOS') {
            return view('exams.dos_show', compact('exam', 'marks', 'testMarks'));
        } else {
            return view('exams.show', compact('exam', 'marks', 'testMarks'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        if (auth('teacher')->check()) {
            $user = auth('teacher')->user();
            // Teacher can only access their assigned courses and lessons
            $courses = $user->courses ?? collect();
            $lessons = $user->lessons ?? collect();
            $branches = $user->branches ?? collect();
            $teachers = collect([$user]);
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
            $exam->load(['courses', 'lessons', 'academic_year', 'term', 'teacher', 'branch']);
            
            return view('teacher.exams.edit', compact('exam', 'courses', 'lessons', 'branches', 'teachers', 'academicYears', 'terms'));
        } elseif (auth('staff')->check() && auth('staff')->user()->role === 'DOS') {
            $courses = Course::all();
            $lessons = Lesson::all();
            $branches = \App\Models\Branch::all();
            $teachers = \App\Models\Teacher::all();
            $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
            $terms = \App\Models\Term::all();
            $exam->load(['courses', 'lessons', 'academic_year', 'term', 'teacher', 'branch']);
            
            return view('exams.dos_edit', compact('exam', 'courses', 'lessons', 'branches', 'teachers', 'academicYears', 'terms'));
        }
        
        $courses = Course::all();
        $lessons = Lesson::all();
        $branches = \App\Models\Branch::all();
        $teachers = \App\Models\Teacher::all();
        $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
        $terms = \App\Models\Term::all();
        $exam->load(['courses', 'lessons', 'academic_year', 'term', 'teacher', 'branch']);
        
        return view('exams.edit', compact('exam', 'courses', 'lessons', 'branches', 'teachers', 'academicYears', 'terms'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        // Debug: Log the incoming request data
        \Log::info('Exam update request data:', [
            'is_public' => $request->has('is_public'),
            'is_public_value' => $request->input('is_public'),
            'public_price' => $request->input('public_price'),
            'all_request_data' => $request->all()
        ]);

        // Set default token cost to 10 if not provided or empty
        if (!$request->has('token_cost') || $request->token_cost === '' || $request->token_cost === null) {
            $request->merge(['token_cost' => 10]);
        }

        // Only include exam_password in validation if it's being changed
        $rules = [
            'title' => 'required|string|max:255',
            'class_date' => 'required|date',
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'lesson_ids' => 'required|array|min:1',
            'lesson_ids.*' => 'exists:lessons,id',
            'total_marks' => 'required|integer|min:1',
            'exam_weight' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'token_cost' => 'required|integer|min:0',
            'exam_type' => 'required|string',
            'branch_id' => 'required|exists:branches,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'teacher_id' => 'required|exists:teachers,id',
            'term_id' => 'required|exists:terms,id',
            'notes' => 'nullable|string',
            'is_public' => 'nullable|boolean',
            'public_price' => 'nullable|numeric|min:0',
        ];

        // Add exam_password validation only if it's provided in the request
        if ($request->has('exam_password')) {
            $rules['exam_password'] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        $examData = [
            'title' => $request->title,
            'class_date' => $request->class_date,
            'total_marks' => $request->total_marks,
            'exam_weight' => $request->exam_weight,
            'description' => $request->description,
            'instructions' => $request->instructions,
            'token_cost' => $request->token_cost,
            'exam_type' => $request->exam_type,
        ];

        if (Schema::hasColumn('exams', 'branch_id')) {
            $examData['branch_id'] = $request->branch_id;
        }
        if (Schema::hasColumn('exams', 'academic_year_id')) {
            $examData['academic_year_id'] = $request->academic_year_id;
        }
        if (Schema::hasColumn('exams', 'teacher_id')) {
            $examData['teacher_id'] = $request->teacher_id;
        }
        if (Schema::hasColumn('exams', 'term_id')) {
            $examData['term_id'] = $request->term_id;
        }
        if (Schema::hasColumn('exams', 'notes')) {
            $examData['notes'] = $request->notes;
        }
        if (Schema::hasColumn('exams', 'exam_password')) {
            $examData['exam_password'] = $request->exam_password ?? $exam->exam_password;
        }
        if (Schema::hasColumn('exams', 'is_public')) {
            $examData['is_public'] = $request->boolean('is_public');
        }
        if (Schema::hasColumn('exams', 'public_price')) {
            $examData['public_price'] = $request->public_price ?? 0;
        }

        $exam->update($examData);

        // Debug: Log what was actually saved
        \Log::info('Exam data after update:', [
            'exam_id' => $exam->id,
            'is_public_after' => $exam->fresh()->is_public,
            'public_price_after' => $exam->fresh()->public_price,
            'exam_data_saved' => $examData
        ]);

        $exam->courses()->sync($request->course_ids);
        $exam->lessons()->sync($request->lesson_ids);

        // Redirect based on authentication
        if (auth('admin')->check()) {
            return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
        } elseif (auth('teacher')->check()) {
            return redirect()->route('teacher.exams.index')->with('success', 'Exam updated successfully.');
        } elseif (auth('staff')->check()) {
            $staff = auth('staff')->user();
            if ($staff->role === 'DOS') {
                return redirect()->route('dos.exams.index')->with('success', 'Exam updated successfully.');
            }
            return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
        } else {
            return redirect()->route('exams.index')->with('success', 'Exam updated successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        try {
            // Begin transaction to ensure data integrity
            \DB::beginTransaction();
            
            // Delete related records in proper order to avoid foreign key constraint violations
            // 1. Delete exam analytics (depends on questions and exams)
            try {
                $exam->analytics()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam analytics: ' . $e->getMessage());
            }
            
            // 2. Delete exam settings
            try {
                $exam->settings()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam settings: ' . $e->getMessage());
            }
            
            // 3. Delete exam sessions (depends on attempts)
            try {
                $exam->sessions()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam sessions: ' . $e->getMessage());
            }
            
            // 4. Delete exam attempts
            try {
                $exam->attempts()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam attempts: ' . $e->getMessage());
            }
            
            // 5. Delete questions (will cascade delete student answers)
            try {
                $exam->questions()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam questions: ' . $e->getMessage());
            }
            
            // 6. Delete marks
            try {
                $exam->marks()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam marks: ' . $e->getMessage());
            }
            
            // 7. Delete exam tokens (if table exists)
            try {
                $exam->examTokens()->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete exam tokens: ' . $e->getMessage());
            }
            
            // 8. Delete report card test marks (if table exists)
            try {
                \DB::table('report_card_test_marks')->where('exam_id', $exam->id)->delete();
            } catch (\Exception $e) {
                \Log::warning('Could not delete report card test marks: ' . $e->getMessage());
            }
            
            // 9. Detach many-to-many relationships
            $exam->courses()->detach();
            $exam->lessons()->detach();
            
            // 10. Finally delete the exam
            $exam->delete();
            
            \DB::commit();
            
            // Redirect based on authentication
            if (auth('admin')->check()) {
                return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
            } elseif (auth('teacher')->check()) {
                return redirect()->route('teacher.exams.index')->with('success', 'Exam deleted successfully.');
            } elseif (auth('staff')->check()) {
                $staff = auth('staff')->user();
                if ($staff->role === 'DOS') {
                    return redirect()->route('dos.exams.index')->with('success', 'Exam deleted successfully.');
                }
                return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
            } else {
                return redirect()->route('exams.index')->with('success', 'Exam deleted successfully.');
            }
            
        } catch (\Exception $e) {
            \DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Failed to delete exam: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return with error message
            return redirect()->back()->with('error', 'Failed to delete exam: ' . $e->getMessage());
        }
    }

    /**
     * Show exam page for student to take exam.
     */
    public function take(Exam $exam)
    {
        // DEBUG: Log method entry
        Log::info('=== EXAM TAKE METHOD STARTED ===');
        Log::info('Exam ID: ' . $exam->id);
        Log::info('Exam Title: ' . $exam->title);
        
        // DEBUG: Check authentication status
        $isAuthenticated = auth('student')->check();
        Log::info('Student authenticated: ' . ($isAuthenticated ? 'YES' : 'NO'));
        
        if (!$isAuthenticated) {
            Log::info('Student not authenticated, available guards:');
            Log::info('Web guard check: ' . (auth()->check() ? 'YES' : 'NO'));
            Log::info('Default guard: ' . auth()->getDefaultDriver());
            return redirect()->route('student.login')->with('error', 'Please login to take exam.');
        }
        
        $student = auth('student')->user();
        Log::info('Student ID: ' . $student->id);
        Log::info('Student Name: ' . $student->full_name);
        Log::info('Student Reg Number: ' . $student->reg_number);

        // Prevent re-taking if disqualified (session)
        // TEMPORARILY DISABLED FOR DEBUGGING
        // if (session("disqualified_exam_{$exam->id}")) {
        //     Log::info('Student disqualified via session');
        //     return redirect()->route('dashboard')->with('error', 'You have been disqualified from this exam.');
        // }

        // Prevent re-taking if disqualified (DB)
        $existingMark = Mark::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        Log::info('Existing mark found: ' . ($existingMark ? 'YES' : 'NO'));
        if ($existingMark) {
            Log::info('Existing mark status: ' . $existingMark->status);
            Log::info('Existing mark value: ' . $existingMark->value);
        }

        if ($existingMark && $existingMark->status === 'disqualified') {
            Log::info('Student disqualified via database');
            return redirect()->route('dashboard')->with('error', 'You have been disqualified from this exam.');
        }

        // Prevent re-attempts
        if ($existingMark) {
            Log::info('Student already attempted exam');
            return redirect()->route('dashboard')->with('error', 'You already took this exam.');
        }

        // Check and deduct tokens if required
        Log::info('Exam requires tokens: ' . ($exam->requiresTokens() ? 'YES' : 'NO'));
        
        if ($exam->requiresTokens()) {
            $studentToken = $student->token;
            $tokenCost = $exam->getCalculatedTokenCost();
            
            Log::info('Token cost: ' . $tokenCost);
            Log::info('Student token exists: ' . ($studentToken ? 'YES' : 'NO'));
            
            if ($studentToken) {
                Log::info('Student token balance: ' . $studentToken->balance);
                Log::info('Can afford tokens: ' . ($studentToken->canAfford($tokenCost) ? 'YES' : 'NO'));
            }
            
            if (!$studentToken || !$studentToken->canAfford($tokenCost)) {
                $balance = $studentToken->balance ?? 0;
                Log::info('Insufficient tokens - required: ' . $tokenCost . ', has: ' . $balance);
                return redirect()->route('student.dashboard')->with('error', 
                    "Insufficient tokens! This exam requires {$tokenCost} tokens. You have {$balance} tokens.");
            }

            // Deduct tokens for exam attempt
            Log::info('Attempting to deduct tokens...');
            $transaction = $studentToken->spendTokens(
                $tokenCost, 
                "Exam attempt: {$exam->title}", 
                'exam'
            );

            Log::info('Token transaction successful: ' . ($transaction ? 'YES' : 'NO'));

            if (!$transaction) {
                Log::info('Failed to deduct tokens');
                return redirect()->route('student.dashboard')->with('error', 'Failed to deduct tokens. Please try again.');
            }
        }

        // Start cheating detection session or timestamp
        session(["exam_start_time_{$exam->id}" => now()]);
        session(["cheating_attempts_{$exam->id}" => 0]);
        
        Log::info('Session variables set for exam');
        
        // Load questions with optimized query - limit to 100 questions max to prevent timeout
        Log::info('Loading questions...');
        $questions = $exam->questions()
            ->select(['id', 'exam_id', 'text', 'question_type', 'option_a', 'option_b', 'option_c', 'option_d', 'correct_answer', 'marks'])
            ->limit(100)
            ->inRandomOrder()
            ->get();

        Log::info('Questions loaded: ' . $questions->count());
        Log::info('Returning view: exams.take');
        Log::info('=== EXAM TAKE METHOD COMPLETED ===');

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
     * Import questions from question banks to an existing exam
     */
    public function importFromBanks(Request $request, Exam $exam)
    {
        try {
            $request->validate([
                'question_bank_ids' => 'required|array',
                'question_bank_ids.*' => 'exists:question_banks,id',
                'questions_per_bank' => 'required|integer|min:1|max:50',
                'import_type' => 'sometimes|in:perBank,total',
            ]);
            
            $importType = $request->import_type ?? 'perBank'; // Default to perBank for backward compatibility
            $importedCount = $this->addQuestionsFromBanks($exam, $request->question_bank_ids, $request->questions_per_bank, $importType);
            
            return response()->json([
                'success' => true,
                'imported_count' => $importedCount,
                'message' => "Successfully imported {$importedCount} questions to the exam."
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to import questions from banks: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to import questions: ' . $e->getMessage()
            ], 500);
        }
    }
    /**
     * Add questions from question banks to an exam
     */
    private function addQuestionsFromBanks(Exam $exam, array $questionBankIds, int $questionsPerBank, string $importType = 'perBank')
    {
        $allQuestions = collect();
        
        if ($importType === 'total') {
            // Total questions mode - distribute across selected banks
            $totalQuestionsNeeded = $questionsPerBank;
            
            // Get all available questions from selected banks
            $availableQuestions = collect();
            foreach ($questionBankIds as $bankId) {
                $bank = \App\Models\QuestionBank::find($bankId);
                if ($bank) {
                    $questions = $bank->questions()->get();
                    $availableQuestions = $availableQuestions->concat($questions);
                }
            }
            
            // Randomly select the required number of questions
            $allQuestions = $availableQuestions->shuffle()->take($totalQuestionsNeeded);
            
        } else {
            // Per bank mode (original behavior)
            foreach ($questionBankIds as $bankId) {
                $bank = \App\Models\QuestionBank::find($bankId);
                if ($bank) {
                    // Get random questions from this bank
                    $questions = $bank->questions()->inRandomOrder()->limit($questionsPerBank)->get();
                    $allQuestions = $allQuestions->concat($questions);
                }
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
        
        Log::info('Added ' . $allQuestions->count() . ' questions from question banks to exam ' . $exam->id . ' (' . $importType . ' mode)');
        return $allQuestions->count();
    }

    /**
     * Preview questions from selected question banks
     */
    public function previewQuestions(Request $request)
    {
        try {
            $request->validate([
                'question_bank_ids' => 'required|array',
                'question_bank_ids.*' => 'exists:question_banks,id',
                'questions_per_bank' => 'required|integer|min:1|max:50',
                'import_type' => 'sometimes|in:perBank,total',
            ]);
            
            $allQuestions = collect();
            $importType = $request->import_type ?? 'perBank'; // Default to perBank for backward compatibility
            
            if ($importType === 'total') {
                // Total questions mode - distribute across selected banks
                $totalQuestionsNeeded = $request->questions_per_bank;
                $selectedBanks = $request->question_bank_ids;
                
                // Get all available questions from selected banks
                $availableQuestions = collect();
                foreach ($selectedBanks as $bankId) {
                    $bank = \App\Models\QuestionBank::find($bankId);
                    if ($bank) {
                        $questions = $bank->questions()->with('questionBank')->get();
                        $availableQuestions = $availableQuestions->concat($questions);
                    }
                }
                
                // Randomly select the required number of questions
                $allQuestions = $availableQuestions->shuffle()->take($totalQuestionsNeeded);
                
            } else {
                // Per bank mode (original behavior)
                foreach ($request->question_bank_ids as $bankId) {
                    $bank = \App\Models\QuestionBank::find($bankId);
                    if ($bank) {
                        $questions = $bank->questions()->with('questionBank')->inRandomOrder()->limit($request->questions_per_bank)->get();
                        $allQuestions = $allQuestions->concat($questions);
                    }
                }
            }
            
            // Format questions for preview
            $formattedQuestions = $allQuestions->map(function($question) {
                return [
                    'text' => $question->text,
                    'question_type' => $question->question_type,
                    'marks' => $question->marks,
                    'options' => $this->formatOptions($question),
                    'question_bank_name' => $question->questionBank ? $question->questionBank->name : 'Unknown',
                ];
            });
            
            return response()->json(['questions' => $formattedQuestions]);
            
        } catch (\Exception $e) {
            \Log::error('Preview questions error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to preview questions: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Preview exam as it will appear to students
     */
    public function preview(Exam $exam)
    {
        // Load exam with questions and their relationships
        $exam->load(['questions.questionBank', 'settings']);
        
        // Get questions in random order (similar to actual exam taking)
        $questions = $exam->questions->shuffle();
        
        // Prepare questions with options (similar to ExamTakingController)
        $preparedQuestions = $questions->map(function($question) {
            // Prepare options based on question data
            $options = [];
            
            if ($question->option_a) $options[] = $question->option_a;
            if ($question->option_b) $options[] = $question->option_b;
            if ($question->option_c) $options[] = $question->option_c;
            if ($question->option_d) $options[] = $question->option_d;
            
            return [
                'id' => $question->id,
                'question_text' => $question->text,
                'question_image' => $question->question_image ?? null,
                'question_type' => $question->question_type,
                'points' => $question->marks,
                'options' => $options,
                'correct_answer' => $question->correct_answer,
                'question_bank_name' => $question->questionBank ? $question->questionBank->name : 'Manual',
            ];
        });
        
        return view('admin.exams.preview', compact('exam', 'preparedQuestions'));
    }
    public function makePublic(Request $request, Exam $exam)
    {
        $request->validate([
            'public_price' => 'required|numeric|min:0',
            'token_cost' => 'required|integer|min:0',
        ]);

        try {
            $exam->update([
                'is_public' => true,
                'public_price' => $request->public_price,
                'token_cost' => $request->token_cost,
            ]);

            $message = $request->public_price > 0 
                ? 'Exam is now public and available for students to purchase.'
                : 'Exam is now public and available for students to take for free.';

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to make exam public: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Make exam private
     */
    public function makePrivate(Exam $exam)
    {
        try {
            $exam->update([
                'is_public' => false,
                'public_price' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Exam is now private and not available for public access.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to make exam private: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show bulk question upload form
     */
    public function bulkUploadForm()
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized access');
        }

        $exams = Exam::with(['courses', 'lessons'])->get();
        return view('exams.bulk-upload', compact('exams'));
    }

    /**
     * Process bulk question upload
     */
    public function bulkUpload(Request $request)
    {
        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'staff'])) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access'
            ], 403);
        }

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject' => 'required|in:mathematics,biology',
            'question_file' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'image_files' => 'nullable|array',
            'image_files.*' => 'file|image|mimes:jpeg,jpg,png,gif|max:2048'
        ]);

        try {
            $exam = Exam::findOrFail($request->exam_id);
            $subject = $request->subject;
            $file = $request->file('question_file');

            // Process the file based on its type
            $imageFiles = $request->file('image_files', []);
            $questions = $this->processQuestionFile($file, $subject, $imageFiles);

            if (empty($questions)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid questions found in the file'
                ]);
            }

            // Create questions in database
            $createdCount = 0;
            foreach ($questions as $questionData) {
                $question = Question::create([
                    'exam_id' => $exam->id,
                    'text' => $questionData['text'],
                    'question_type' => $questionData['question_type'] ?? 'multiple_choice',
                    'option_a' => $questionData['option_a'] ?? null,
                    'option_b' => $questionData['option_b'] ?? null,
                    'option_c' => $questionData['option_c'] ?? null,
                    'option_d' => $questionData['option_d'] ?? null,
                    'correct_answer' => $questionData['correct_answer'],
                    'marks' => $questionData['marks'] ?? 1,
                    'difficulty_level' => $questionData['difficulty_level'] ?? 1,
                    'explanation' => $questionData['explanation'] ?? null,
                    'subject_area' => $subject === 'mathematics' ? 'Mathematics' : 'Biology',
                    'biology_topic' => $subject === 'biology' ? ($questionData['biology_topic'] ?? null) : null,
                ]);
                $createdCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully uploaded {$createdCount} questions for {$subject}",
                'count' => $createdCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download question template
     */
    public function downloadTemplate($subject)
    {
        if (!in_array($subject, ['mathematics', 'biology'])) {
            abort(404, 'Invalid subject');
        }

        $user = auth()->user();
        if (!$user || !in_array($user->role, ['admin', 'staff'])) {
            abort(403, 'Unauthorized access');
        }

        $filename = $subject . '_questions_template.csv';
        $filepath = storage_path('app/templates/' . $filename);

        if (!file_exists($filepath)) {
            $this->createTemplateFile($subject);
        }

        return response()->download($filepath, $filename);
    }

    /**
     * Process uploaded question file
     */
    private function processQuestionFile($file, $subject, $imageFiles = [])
    {
        $questions = [];
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv') {
            $questions = $this->processCsvFile($file, $subject, $imageFiles);
        } elseif (in_array($extension, ['xlsx', 'xls'])) {
            $questions = $this->processExcelFile($file, $subject, $imageFiles);
        }

        return $questions;
    }

    /**
     * Process CSV file
     */
    private function processCsvFile($file, $subject, $imageFiles = [])
    {
        $questions = [];
        $handle = fopen($file->getPathname(), 'r');
        
        if ($handle) {
            // Skip header row
            fgetcsv($handle);
            
            $rowNumber = 2;
            while (($row = fgetcsv($handle)) !== false) {
                $questionData = $this->validateQuestionRow($row, $subject, $rowNumber);
                if ($questionData) {
                    // Process image for biology questions
                    if ($subject === 'biology' && !empty($questionData['image_path'])) {
                        $questionData['question_image'] = $this->processQuestionImage($questionData['image_path'], $imageFiles, $rowNumber);
                    }
                    $questions[] = $questionData;
                }
                $rowNumber++;
            }
            fclose($handle);
        }
        
        return $questions;
    }

    /**
     * Process Excel file
     */
    private function processExcelFile($file, $subject, $imageFiles = [])
    {
        // For now, convert Excel to CSV processing
        // In a real implementation, you'd use phpspreadsheet
        return $this->processCsvFile($file, $subject, $imageFiles);
    }

    /**
     * Process question image
     */
    private function processQuestionImage($imagePath, $imageFiles, $rowNumber)
    {
        if (empty($imageFiles)) {
            return null;
        }

        // Find the corresponding image file
        // Assuming image files are named with row numbers or specific pattern
        foreach ($imageFiles as $index => $imageFile) {
            // Match image by row number or file name pattern
            if (strpos($imageFile->getClientOriginalName(), (string)$rowNumber) !== false || 
                strpos($imageFile->getClientOriginalName(), str_replace([' ', '.'], '_', $imagePath)) !== false) {
                
                // Store the image
                $filename = 'biology_' . time() . '_' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(public_path('images/question_images/biology'), $filename);
                
                return 'biology/' . $filename;
            }
        }

        return null;
    }

    /**
     * Validate and format question row
     */
    private function validateQuestionRow($row, $subject, $rowNumber)
    {
        if (empty($row[0]) || empty($row[2])) {
            return null; // Skip invalid rows
        }

        $questionData = [
            'text' => trim($row[0]),
            'question_type' => 'multiple_choice',
            'option_a' => trim($row[1] ?? ''),
            'option_b' => trim($row[2] ?? ''),
            'option_c' => trim($row[3] ?? ''),
            'option_d' => trim($row[4] ?? ''),
            'correct_answer' => strtoupper(trim($row[5] ?? '')),
            'marks' => is_numeric($row[6] ?? 1) ? (int)$row[6] : 1,
            'difficulty_level' => is_numeric($row[7] ?? 1) ? (int)$row[7] : 1,
            'explanation' => trim($row[8] ?? ''),
        ];

        // Subject-specific validation
        if ($subject === 'biology') {
            $questionData['biology_topic'] = trim($row[9] ?? '');
            // Add image path support (column 10)
            $questionData['image_path'] = trim($row[10] ?? '');
        }

        // Validate correct answer
        if (!in_array($questionData['correct_answer'], ['A', 'B', 'C', 'D'])) {
            return null;
        }

        return $questionData;
    }

    /**
     * Create template file
     */
    private function createTemplateFile($subject)
    {
        $templateDir = storage_path('app/templates');
        if (!is_dir($templateDir)) {
            mkdir($templateDir, 0755, true);
        }

        $filename = $subject . '_questions_template.csv';
        $filepath = $templateDir . '/' . $filename;

        $headers = ['Question Text', 'Option A', 'Option B', 'Option C', 'Option D', 'Correct Answer (A/B/C/D)', 'Marks', 'Difficulty Level (1-5)', 'Explanation'];
        
        if ($subject === 'biology') {
            $headers[] = 'Biology Topic';
            $headers[] = 'Image Path (optional)';
        }

        $handle = fopen($filepath, 'w');
        fputcsv($handle, $headers);

        // Add sample rows
        if ($subject === 'mathematics') {
            fputcsv($handle, [
                'What is 2 + 2?', '3', '4', '5', '6', 'B', '1', '1', 'Basic addition problem'
            ]);
            fputcsv($handle, [
                'Solve for x: 2x + 4 = 10', 'x = 3', 'x = 4', 'x = 5', 'x = 6', 'A', '2', '2', 'Linear equation solving'
            ]);
        } elseif ($subject === 'biology') {
            fputcsv($handle, [
                'What is the powerhouse of the cell?', 'Nucleus', 'Mitochondria', 'Ribosome', 'Golgi apparatus', 'B', '1', '1', 'Cell organelles', 'Cell Biology', 'mitochondria_diagram.jpg'
            ]);
            fputcsv($handle, [
                'What process occurs in the chloroplast?', 'Photosynthesis', 'Respiration', 'Digestion', 'Excretion', 'A', '2', '2', 'Plant cell processes', 'Plant Biology', 'chloroplast_structure.png'
            ]);
            fputcsv($handle, [
                'Identify the structure shown in this image', 'Heart', 'Brain', 'Lungs', 'Liver', 'A', '2', '2', 'Anatomy with image reference', 'Anatomy', 'human_heart_diagram.jpg'
            ]);
        }

        fclose($handle);
    }
}
