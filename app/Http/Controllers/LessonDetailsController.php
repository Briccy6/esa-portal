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
public function index() {
    $lessonDetails = LessonUnit::with('lesson.course', 'lesson.teacher', 'term')
                               ->whereHas('lesson') // Only get units with valid lessons
                               ->get();
    return view('details.create', compact('lessonDetails'));
}


    /**
     * Show the form for creating a new lesson detail.
     */
    public function create(Request $request, $lessonId)
    {
        $lesson = Lesson::with('course')->findOrFail($lessonId);
        $terms = Term::all();
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

    foreach ($request->terms as $termId) {
        foreach ($request->units as $unit) {
            LessonUnit::create([
                'lesson_id' => $lessonId,
                'term_id' => $termId,
                'unit_name' => $unit['name'],
                'hours' => $unit['hours'],
                'course_level' => $request->course_level,
            ]);
        }
    }

    return redirect()->route('lessons.show', $lessonId)
                     ->with('success', 'Lesson details saved successfully.');
}


    /**
     * Display the specified lesson detail.
     */
    public function show($id)
    {
        $detail = LessonUnit::with('lesson', 'term')->findOrFail($id);
        return view('details.show', compact('detail'));
    }

    /**
     * Show the form for editing the specified lesson detail.
     */
    public function edit($id)
    {
        $detail = LessonUnit::findOrFail($id);
        $terms = Term::all();
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

    return redirect()->route('lessons.show', $detail->lesson_id)
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

        return redirect()->route('lessons.show', $lessonId)
                         ->with('success', 'Lesson detail deleted successfully.');
    }
}
