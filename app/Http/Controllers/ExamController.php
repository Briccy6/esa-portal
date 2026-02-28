<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Marks;
use Illuminate\Http\Request;
use App\Mail\ExamCreatedMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Announcement;
use App\Models\AcademicYear;

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
    $courses = Course::all();
    $lessons = Lesson::all();
    $teachers = \App\Models\Teacher::all();
    $academicYears = \App\Models\AcademicYear::orderBy('year', 'desc')->get();
    $terms = \App\Models\Term::all();
    $examPassword = \Illuminate\Support\Str::random(8); // Generate random password

    return view('exams.create', compact('courses', 'lessons', 'teachers', 'academicYears', 'terms', 'examPassword'));
}

/**
 * Store a newly created resource in storage.
 */
public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'subject' => 'nullable|string|max:255',
        'description' => 'nullable|string',
        'exam_weight' => 'required|integer|min:1|max:100',
        'total_marks' => 'required|integer|min:1',
        'class_date' => 'required|date',
        'instructions' => 'nullable|string',
        'exam_type' => 'required|string',
        'academic_year_id' => 'required|exists:academic_years,id',
        'teacher_id' => 'required|exists:teachers,id',
        'term_id' => 'required|exists:terms,id',
        'notes' => 'nullable|string',
        'exam_password' => 'nullable|string|max:255',
        'is_active' => 'required|boolean',
        'course_ids' => 'required|array|min:1',
        'course_ids.*' => 'exists:courses,id',
        'lesson_ids' => 'required|array|min:1',
        'lesson_ids.*' => 'exists:lessons,id',
    ]);
    
    try {
        // Loop through each course and create a separate exam
        foreach ($request->course_ids as $courseId) {
            $exam = Exam::create([
                'title' => $request->title,
                'subject' => $request->subject,
                'description' => $request->description,
                'exam_weight' => $request->exam_weight,
                'total_marks' => $request->total_marks,
                'class_date' => $request->class_date,
                'instructions' => $request->instructions,
                'exam_type' => $request->exam_type,
                'academic_year_id' => $request->academic_year_id,
                'teacher_id' => $request->teacher_id,
                'term_id' => $request->term_id,
                'notes' => $request->notes,
                'exam_password' => $request->exam_password,
                'is_active' => $request->is_active,
            ]);
            
            foreach ($request->course_ids as $courseId) {
                $exam->courses()->attach($courseId);
            }
            
            // Attach lessons to this exam
            foreach ($request->lesson_ids as $lessonId) {
                $exam->lessons()->attach($lessonId);
            }
        }
        
        return redirect()->route('exams.index')->with('success', 'Exams created successfully.');
    } catch (\Exception $e) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'Failed to create exams: ' . $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        $exam->load(['courses', 'lessons']);
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
        $existingMark = Marks::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        if ($existingMark && $existingMark->status === 'disqualified') {
            return redirect()->route('dashboard')->with('error', 'You have been disqualified from this exam.');
        }

        // Prevent re-attempts
        if ($existingMark) {
            return redirect()->route('dashboard')->with('error', 'You already took this exam.');
        }

        // Start cheating detection session or timestamp
        session(["exam_start_time_{$exam->id}" => now()]);
        session(["cheating_attempts_{$exam->id}" => 0]);
        $questions = $exam->questions->shuffle()->values();
        return view('exams.take', [
            'exam' => $exam,
            'questions' => $questions,
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
        Marks::updateOrCreate(
            [
                'student_id' => $student->id,
                'exam_id' => $exam->id,
            ],
            [
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => 0,
                'grade_id' => null,
                'status' => 'disqualified',
            ]
        );

        return redirect()->route('student.dashboard')->with('error', 'You have been disqualified from this exam.');
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
        Marks::updateOrCreate(
            [
                'student_id' => $student->id,
                'exam_id' => $exam->id,
            ],
            [
                'course_id' => $exam->courses->first()->id ?? null,
                'branch_id' => $student->branch_id,
                'value' => $score,
                'grade_id' => null,
                'status' => 'completed',
            ]
        );

        return redirect()->route('student.dashboard')->with('success', 'Exam submitted! Your score: ' . $score . '/100');
    }
} 