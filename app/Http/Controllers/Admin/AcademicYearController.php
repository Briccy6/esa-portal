<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index()
    {
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

        AcademicYear::create($validated);

        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year created successfully.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_year.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year' => 'required|string|unique:academic_years,year,' . $academicYear->id,
            'starting_date' => 'required|date|before_or_equal:expiry_date',
            'expiry_date' => 'required|date|after_or_equal:starting_date',
            'is_ongoing' => 'required|boolean',
        ]);

        $academicYear->update($validated);

        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year updated successfully.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic_year.index')->with('success', 'Academic year deleted.');
    }
    public function lock(){
        return redirect()->route('admin.academic_year.index')->with('error', 'Access to this function is restricted.');
    }
}
