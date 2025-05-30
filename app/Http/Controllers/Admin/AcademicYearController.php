<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
        // Use starting_date, not start_date
        $academicYears = AcademicYear::orderByDesc('starting_date')->get();
        return view('admin.academic_year.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic_year.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year' => 'required|string|unique:academic_years,year',
            'starting_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after_or_equal:starting_date',
            'is_ongoing' => 'required|boolean',
        ]);

        // Prevent more than one active academic year
        if ($validated['is_ongoing']) {
            $existingActive = AcademicYear::where('is_ongoing', true)->first();
            if ($existingActive) {
                return back()
                    ->withErrors(['is_ongoing' => 'You cannot have 2 running academic years at once.'])
                    ->withInput();
            }
        }

        AcademicYear::create($validated);

        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year created successfully.');
    }

    public function edit(AcademicYear $academic_year)
    {
        return view('admin.academic_year.edit', compact('academic_year'));
    }

    public function update(Request $request, AcademicYear $academic_year)
    {
        $validated = $request->validate([
            'year' => 'required|string|unique:academic_years,year,' . $academic_year->id,
            'starting_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after_or_equal:starting_date',
            'is_ongoing' => 'nullable|boolean',
        ]);

        // Prevent more than one active academic year when updating
        if (!empty($validated['is_ongoing']) && $validated['is_ongoing']) {
            $existingActive = AcademicYear::where('is_ongoing', true)
                ->where('id', '!=', $academic_year->id)
                ->first();
            if ($existingActive) {
                return back()
                    ->withErrors(['is_ongoing' => 'You cannot have 2 running academic years at once.'])
                    ->withInput();
            }
        }

        $academic_year->update($validated);

        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academic_year)
    {
        $academic_year->delete();
        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year deleted.');
    }
}
