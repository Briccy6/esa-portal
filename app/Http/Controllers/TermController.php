<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Term;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TermController extends Controller
{
    public function index()
    {
        $academicYear = AcademicYear::where('is_ongoing', true)->first();

        if (!$academicYear) {
            return redirect()->back()->with('error', 'No ongoing academic year found.');
        }

        $terms = Term::where('academic_year_id', $academicYear->id)
                     ->orderBy('name')
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
                             ->orderBy('name')
                             ->get();

        $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];
        $createdTerms = $existingTerms->pluck('name')->toArray();

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
        ]);
    }

    public function store(Request $request)
    {
        try {
            // Get the ongoing academic year as default
            $ongoingYear = AcademicYear::where('is_ongoing', true)->first();
            
            $rules = [
                'academic_year_id' => 'required|exists:academic_years,id',
                'name'             => 'required|string|in:Term 1,Term 2,Term 3',
                'start_date'       => 'required|date',
                'end_date'         => 'required|date|after_or_equal:start_date',
            ];

            $validated = $request->validate($rules);

            // Double-check academic year exists and get details
            $academicYear = AcademicYear::find($validated['academic_year_id']);
            if (!$academicYear) {
                // If provided academic year doesn't exist, use the ongoing one
                if ($ongoingYear) {
                    $academicYear = $ongoingYear;
                    $validated['academic_year_id'] = $ongoingYear->id;
                    \Log::warning('Invalid academic year provided, using ongoing year', [
                        'invalid_id' => $request->academic_year_id,
                        'using_id' => $ongoingYear->id
                    ]);
                } else {
                    return back()->withErrors(['academic_year_id' => 'Invalid academic year selected and no ongoing academic year found.'])->withInput();
                }
            }

            // Log for debugging
            \Log::info('Creating term', [
                'academic_year_id' => $validated['academic_year_id'],
                'academic_year_name' => $academicYear->name ?? 'Unknown',
                'term_name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date']
            ]);

            $term = $validated['name'];
            $existingTerms = Term::where('academic_year_id', $academicYear->id)->get();

            $termOrder = ['Term 1' => 1, 'Term 2' => 2, 'Term 3' => 3];

            // Ensure previous terms exist and this term doesn't already exist
            foreach ($termOrder as $t => $order) {
                if ($order < $termOrder[$term] && !$existingTerms->contains('name', $t)) {
                    return back()->withErrors(['name' => "Please create $t before creating $term."])->withInput();
                }
                if ($order == $termOrder[$term] && $existingTerms->contains('name', $term)) {
                    return back()->withErrors(['name' => "$term already exists."])->withInput();
                }
            }

            $minStartDate = Carbon::parse($academicYear->starting_date);
            $maxEndDate   = Carbon::parse($academicYear->expiry_date);

            if ($term === 'Term 2') {
                $term1 = $existingTerms->firstWhere('name', 'Term 1');
                if (!$term1) {
                    return back()->withErrors(['name' => 'Term 1 must be created first.'])->withInput();
                }
                $minStartDate = Carbon::parse($term1->end_date)->addDay();
            } elseif ($term === 'Term 3') {
                $term2 = $existingTerms->firstWhere('name', 'Term 2');
                if (!$term2) {
                    return back()->withErrors(['name' => 'Term 2 must be created first.'])->withInput();
                }
                $minStartDate = Carbon::parse($term2->end_date)->addDay();
            }

            $startingDate = Carbon::parse($validated['start_date']);
            $endingDate   = Carbon::parse($validated['end_date']);

            if ($startingDate->lt($minStartDate)) {
                return back()->withErrors(['start_date' => "Start date cannot be before {$minStartDate->format('Y-m-d')}"])->withInput();
            }

            if ($endingDate->gt($maxEndDate)) {
                return back()->withErrors(['end_date' => "End date cannot be after {$maxEndDate->format('Y-m-d')}"])->withInput();
            }

            Term::create([
                'academic_year_id' => $academicYear->id,
                'name'             => $term,
                'start_date'       => $startingDate->format('Y-m-d'),
                'end_date'         => $endingDate->format('Y-m-d'),
            ]);

            return redirect()->route('admin.terms.index')->with('success', "$term created successfully.");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['name' => 'Failed to create term: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit($id)
    {
        $term = Term::findOrFail($id);
        $academicYear = $term->academicYear;
        
        // Get existing terms for validation
        $existingTerms = Term::where('academic_year_id', $academicYear->id)
                             ->where('id', '!=', $term->id)
                             ->orderBy('name')
                             ->get();

        return view('admin.terms.edit', compact('term', 'academicYear', 'existingTerms'));
    }

    public function update(Request $request, $id)
    {
        $term = Term::findOrFail($id);

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $term->start_date = $validated['start_date'];
        $term->end_date   = $validated['end_date'];
        $term->save();

        return redirect()->route('admin.terms.index')->with('success', 'Term updated successfully.');
    }

    public function destroy($id)
    {
        $term = Term::findOrFail($id);

        try {
            \App\Models\ReportCardSubject::whereHas('reportCard', function ($query) use ($term) {
                $query->where('term_id', $term->id);
            })->delete();

            \App\Models\ReportCard::where('term_id', $term->id)->delete();

            try {
                $term->schoolFees()->delete();
            } catch (\Exception $e) {
                // school_fees table might not exist, continue
            }

            $term->delete();

            return redirect()->route('admin.terms.index')->with('success', 'Term and all related data deleted successfully.');

        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->getCode() == '23000') {
                return redirect()->route('admin.terms.index')
                    ->with('error', 'Cannot delete this term because it is referenced by other records. Please contact administrator.');
            }

            throw $e;
        }
    }

    public function getTermsByAcademicYear($academicYearId)
    {
        try {
            if (!is_numeric($academicYearId)) {
                return response()->json(['error' => 'Invalid academic year ID'], 400);
            }

            $academicYear = AcademicYear::find($academicYearId);
            if (!$academicYear) {
                return response()->json(['error' => 'Academic year not found'], 404);
            }

            $terms = Term::where('academic_year_id', $academicYearId)
                ->select('id', 'name', 'start_date', 'end_date')
                ->orderBy('name')
                ->get();

            return response()->json($terms);

        } catch (\Exception $e) {
            Log::error('Error fetching terms by academic year: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch terms'], 500);
        }
    }
}