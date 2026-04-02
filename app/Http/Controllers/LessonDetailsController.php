<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\LessonUnit;
use App\Models\Term;
use Illuminate\Http\Request;

class LessonDetailsController extends Controller
{
    /**
     * Display a listing of the lesson details.
     */
public function index(Request $request) {
    $query = LessonUnit::with('lesson.course', 'lesson.teacher', 'term')
                        ->whereHas('lesson');
    
    // Search across multiple fields
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('unit_name', 'like', "%$search%")
              ->orWhereHas('lesson', function($q1) use ($search) {
                  $q1->where('title', 'like', "%$search%");
              })
              ->orWhereHas('lesson.course', function($q2) use ($search) {
                  $q2->where('course_name', 'like', "%$search%");
              })
              ->orWhereHas('lesson.teacher', function($q3) use ($search) {
                  $q3->where('teacher_name', 'like', "%$search%");
              })
              ->orWhereHas('term', function($q4) use ($search) {
                  $q4->where('name', 'like', "%$search%");
              });
        });
    }
    
    $lessonDetails = $query->paginate(15);
    
    // If this is an AJAX request, return only the table body and pagination
    if ($request->ajax() || $request->input('ajax')) {
        return view('details.index', compact('lessonDetails'));
    }
    
    return view('details.index', compact('lessonDetails'));
}


    /**
     * Show the form for creating a new lesson detail.
     */
    public function create(Request $request, $lessonId)
    {
        $lesson = Lesson::with('course', 'branchCourses')->findOrFail($lessonId);
        $terms = Term::with('academicYear')->get();
        return view('details.create', compact('lesson', 'terms'));
    }

    /**
     * Store a newly created lesson detail in storage.
     */
    public function store(Request $request, $lessonId)
{
    $request->validate([
        'terms' => 'required|array',
        'course_level' => 'required|string',
        'units' => 'required|array',
        'units.*.name' => 'required|string|max:255',
        'units.*.hours' => 'required|integer|min:1',
    ]);

    // Create each unit only once, associated with the lesson
    foreach ($request->units as $unit) {
        LessonUnit::create([
            'lesson_id' => $lessonId,
            'term_id' => $request->terms[0], // Associate with the first selected term
            'unit_name' => $unit['name'],
            'hours' => $unit['hours'],
            'course_level' => $request->course_level,
        ]);
    }

    $lesson = Lesson::with('branchCourses')->find($lessonId);
    $branchCount = $lesson->branchCourses->count();
    $branchInfo = $branchCount > 0 ? " and are now available across {$branchCount} branch-course combinations" : '';
    
    return redirect()->route('staff.lessons.show', $lessonId)
                     ->with('success', 'Lesson details saved successfully! Created ' . count($request->units) . ' unit(s)' . $branchInfo . '.');
}


    /**
     * Display the specified lesson detail.
     */
    public function show($id)
    {
        $detail = LessonUnit::with('lesson', 'term')->findOrFail($id);

        return redirect()->route('staff.lessons.show', $detail->lesson_id);
    }

    /**
     * Show the form for editing the specified lesson detail.
     */
    public function edit($id)
    {
        $detail = LessonUnit::with('lesson.course')->findOrFail($id);
        $terms = Term::with('academicYear')->orderBy('name')->get();
        return view('details.edit', compact('detail', 'terms'));
    }

    /**
     * Update the specified lesson detail in storage.
     */
    public function update(Request $request, $id)
{
    $request->validate([
        'unit_name' => 'required|string|max:255',
        'hours' => 'required|numeric|min:0',
        'term_id' => 'required|exists:terms,id',
    ]);

    $detail = LessonUnit::findOrFail($id);

    $detail->update($request->only('unit_name', 'hours', 'term_id'));

    return redirect()->route('staff.lessons.show', $detail->lesson_id)
                     ->with('success', 'Lesson detail updated successfully.');
}


    /**
     * Remove the specified lesson detail from storage.
     */
    public function destroy($id)
    {
        $detail = LessonUnit::findOrFail($id);
        $lessonId = $detail->lesson_id;
        $detail->delete();

        return redirect()->route('staff.lessons.show', $lessonId)
                         ->with('success', 'Lesson detail deleted successfully.');
    }
}
