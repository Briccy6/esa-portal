<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Term;
use Carbon\Carbon;

class TermController extends Controller
{
    public function index()
    {
        $academicYear = AcademicYear::where('is_ongoing', true)->first();

        if (!$academicYear) {
            return redirect()->back()->with('error', 'No ongoing academic year found.');
        }

        $terms = Term::where('academic_year_id', $academicYear->id)
                     ->orderBy('term')
                     ->get();

        return view('admin.terms.index', compact('academicYear', 'terms'));
    }

    public function create()
    {
        $academicYear = AcademicYear::where('is_ongoing', true)->first();

        if (!$academicYear) {
            return redirect()->back()->with('error', 'No ongoing academic year found.');
        }

        $existingTerms = Term::where('academic_year_id', $academicYear->id)
                             ->orderBy('term')
                             ->get();

        $terms = $existingTerms;

        $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
        $createdTerms = $existingTerms->pluck('term')->toArray();

        $nextTerm = null;
        foreach ($termOrder as $term => $order) {
            if (!in_array($term, $createdTerms)) {
                $nextTerm = $term;
                break;
            }
        }

        if (!$nextTerm) {
            return redirect()->back()->with('error', 'All terms have already been created for this academic year.');
        }

        return view('admin.terms.create', [
            'academicYear' => $academicYear,
            'nextTerm' => $nextTerm,
            'existingTerms' => $existingTerms,
            'terms' => $terms,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'academic_year_id' => 'required|exists:academic_years,id',
            'term' => 'required|string|in:Term 1,Term 2,Term 3',
            'starting_date' => 'required|date',
            'ending_date' => 'required|date|after_or_equal:starting_date',
        ];

        $validated = $request->validate($rules);

        $academicYear = AcademicYear::find($validated['academic_year_id']);
        if (!$academicYear) {
            return back()->withErrors(['academic_year_id' => 'Invalid academic year.'])->withInput();
        }

        $term = $validated['term'];
        $existingTerms = Term::where('academic_year_id', $academicYear->id)->get();

        $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];

        foreach ($termOrder as $t => $order) {
            if ($order < $termOrder[$term] && !$existingTerms->contains('term', $t)) {
                return back()->withErrors(['term' => "Please create $t before creating $term."])->withInput();
            }
            if ($order == $termOrder[$term] && $existingTerms->contains('term', $term)) {
                return back()->withErrors(['term' => "$term already exists."])->withInput();
            }
        }

        $minStartDate = Carbon::parse($academicYear->starting_date);
        $maxEndDate = Carbon::parse($academicYear->expiry_date);

        if ($term === 'Term 2') {
            $term1 = $existingTerms->firstWhere('term', 'Term 1');
            if (!$term1) {
                return back()->withErrors(['term' => 'Term 1 must be created first.'])->withInput();
            }
            $minStartDate = Carbon::parse($term1->ending_date)->addDay();
        } elseif ($term === 'Term 3') {
            $term2 = $existingTerms->firstWhere('term', 'Term 2');
            if (!$term2) {
                return back()->withErrors(['term' => 'Term 2 must be created first.'])->withInput();
            }
            $minStartDate = Carbon::parse($term2->ending_date)->addDay();
        }

        $startingDate = Carbon::parse($validated['starting_date']);
        $endingDate = Carbon::parse($validated['ending_date']);

        if ($startingDate->lt($minStartDate)) {
            return back()->withErrors(['starting_date' => "Starting date cannot be before {$minStartDate->format('Y-m-d')}"])->withInput();
        }

        if ($endingDate->gt($maxEndDate)) {
            return back()->withErrors(['ending_date' => "Ending date cannot be after {$maxEndDate->format('Y-m-d')}"])->withInput();
        }

        Term::create([
            'academic_year_id' => $academicYear->id,
            'term' => $term,
            'starting_date' => $startingDate->format('Y-m-d'),
            'ending_date' => $endingDate->format('Y-m-d'),
        ]);

        return redirect()->route('admin.terms.index')->with('success', "$term created successfully.");
    }

    public function edit($id)
    {
        $term = Term::findOrFail($id);
        $academicYear = $term->academicYear;

        return view('admin.terms.edit', compact('term', 'academicYear'));
    }

    public function update(Request $request, $id)
    {
        $term = Term::findOrFail($id);

        $validated = $request->validate([
            'starting_date' => 'required|date',
            'ending_date' => 'required|date|after_or_equal:starting_date',
        ]);

        $term->starting_date = $validated['starting_date'];
        $term->ending_date = $validated['ending_date'];
        $term->save();

        return redirect()->route('admin.terms.index')->with('success', 'Term updated successfully.');
    }

    public function destroy($id)
    {
        $term = Term::findOrFail($id);
        $term->delete();

        return redirect()->route('admin.terms.index')->with('success', 'Term deleted successfully.');
    }
    public function getTermsByAcademicYear($academicYearId)
{
    try {
        // Validate the academic year ID
        if (!is_numeric($academicYearId)) {
            return response()->json(['error' => 'Invalid academic year ID'], 400);
        }
        
        // Check if academic year exists
        $academicYear = AcademicYear::find($academicYearId);
        if (!$academicYear) {
            return response()->json(['error' => 'Academic year not found'], 404);
        }
        
        // Fetch terms for the given academic year
        $terms = Term::where('academic_year_id', $academicYearId)
            ->select('id', 'term', 'starting_date', 'ending_date')
            ->orderBy('term')
            ->get();
        
        return response()->json($terms);
        
    } catch (\Exception $e) {
        Log::error('Error fetching terms by academic year: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to fetch terms'], 500);
    }
}
}
