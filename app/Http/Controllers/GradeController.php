<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function index()
    {
        $grades = Grade::all();
        return view('grades.index', compact('grades'));
    }

    public function create()
    {
        return view('grades.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'min_mark' => 'required|integer|min:0|max:100',
            'max_mark' => 'required|integer|min:1|max:100|gte:min_mark',
            'remark' => 'nullable|string|max:255',
        ]);

        // Check for overlapping grades
        $overlap = \App\Models\Grade::where(function($q) use ($request) {
            $q->whereBetween('min_mark', [$request->min_mark, $request->max_mark])
              ->orWhereBetween('max_mark', [$request->min_mark, $request->max_mark])
              ->orWhere(function($q2) use ($request) {
                  $q2->where('min_mark', '<=', $request->min_mark)
                     ->where('max_mark', '>=', $request->max_mark);
              });
        })->exists();

        if ($overlap) {
            return back()
                ->withInput()
                ->withErrors(['min_mark' => 'This range overlaps with an existing grade.']);
        }

        \App\Models\Grade::create($request->all());

        return redirect()->route('admin.grades.index')->with('success', 'Grade created successfully.');
    }

    public function edit(Grade $grade)
    {
        return view('grades.edit', compact('grade'));
    }

    public function update(Request $request, Grade $grade)
    {
        $request->validate([
            'name' => 'required|string|max:50',
            'min_mark' => 'required|integer|min:0',
            'max_mark' => 'required|integer|min:0|gte:min_mark',
            'remark' => 'nullable|string|max:255',
        ]);

        $grade->update($request->all());

        return redirect()->route('admin.grades.index')->with('success', 'Grade updated successfully.');
    }

    public function destroy(Grade $grade)
    {
        $grade->delete();
        return redirect()->route('admin.grades.index')->with('success', 'Grade deleted successfully.');
    }
}