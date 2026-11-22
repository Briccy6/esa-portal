<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\LessonUnit;
use App\Models\LessonBranchCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LessonUnitsImport;
use App\Exports\LessonUnitsExport;

class LessonUnitController extends Controller
{
    public function index()
    {
        $teacher = Auth::guard('teacher')->user();

        // Get lessons assigned to this teacher through lesson_branch_course
        $lessonIds = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->pluck('lesson_id')
            ->unique();

        $lessons = Lesson::with(['units', 'courses'])
            ->whereIn('id', $lessonIds)
            ->get();

        return view('teacher.lesson-units.index', compact('lessons'));
    }

    public function showLessonUnits($lessonId)
    {
        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $lessonId)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        $lesson = Lesson::with(['units', 'courses'])->findOrFail($lessonId);

        return view('teacher.lesson-units.show', compact('lesson'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
            'unit_name' => 'required|string|max:255',
            'hours' => 'required|integer|min:1',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,csv|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('lesson_units', $fileName, 'public');
        }

        LessonUnit::create([
            'lesson_id' => $request->lesson_id,
            'unit_name' => $request->unit_name,
            'hours' => $request->hours,
            'file_path' => $filePath,
        ]);

        return back()->with('success', 'Unit added successfully.');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'unit_name' => 'required|string|max:255',
            'hours' => 'required|integer|min:1',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,txt,csv|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $unit = LessonUnit::findOrFail($id);
        $lesson = $unit->lesson;

        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        $unit->unit_name = $request->unit_name;
        $unit->hours = $request->hours;

        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($unit->file_path && Storage::disk('public')->exists($unit->file_path)) {
                Storage::disk('public')->delete($unit->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $unit->file_path = $file->storeAs('lesson_units', $fileName, 'public');
        }

        $unit->save();

        return back()->with('success', 'Unit updated successfully.');
    }

    public function destroy($id)
    {
        $unit = LessonUnit::findOrFail($id);
        $lesson = $unit->lesson;

        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        // Delete file if exists
        if ($unit->file_path && Storage::disk('public')->exists($unit->file_path)) {
            Storage::disk('public')->delete($unit->file_path);
        }

        $unit->delete();

        return back()->with('success', 'Unit deleted successfully.');
    }

    public function downloadFile($id)
    {
        $unit = LessonUnit::findOrFail($id);
        $lesson = $unit->lesson;

        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $lesson->id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        if (!$unit->file_path || !Storage::disk('public')->exists($unit->file_path)) {
            return back()->with('error', 'File not found.');
        }

        return Storage::disk('public')->download($unit->file_path);
    }

    public function importCSV(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'lesson_id' => 'required|exists:lessons,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $request->lesson_id)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        try {
            Excel::import(new LessonUnitsImport($request->lesson_id), $request->file('csv_file'));
            return back()->with('success', 'Units imported successfully from CSV.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing CSV: ' . $e->getMessage());
        }
    }

    public function exportCSV($lessonId)
    {
        $teacher = Auth::guard('teacher')->user();

        // Verify the teacher is assigned to this lesson
        $isAssigned = LessonBranchCourse::where('teacher_id', $teacher->id)
            ->where('lesson_id', $lessonId)
            ->exists();

        if (!$isAssigned) {
            return back()->with('error', 'You are not assigned to this lesson.');
        }

        $lesson = Lesson::findOrFail($lessonId);

        return Excel::download(new LessonUnitsExport($lessonId), 'lesson_units_' . $lesson->title . '.csv');
    }
}
