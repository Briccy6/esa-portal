<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\SchoolFee;

class SchoolFeeController extends Controller
{
     public function index()
    {
        // Load academic years with their terms
        $academicYears = AcademicYear::with('terms')->get();

        return view('admin.fees.index', compact('academicYears'));
    }

    // Show the form to create/set school fees
    public function create()
    {
        // Same data for form dropdowns
        $academicYears = AcademicYear::with('terms')->get();

        return view('admin.fees.create', compact('academicYears'));
    }

    // Store or update the school fee for a given academic year and term
    public function store(Request $request)
    {
        $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'term_id' => 'required|exists:terms,id',
            'amount' => 'required|numeric|min:0',
        ]);

        SchoolFee::updateOrCreate(
            [
                'academic_year_id' => $request->academic_year_id,
                'term_id' => $request->term_id,
            ],
            [
                'amount' => $request->amount,
            ]
        );

        return redirect()->back()->with('success', 'School fees set successfully.');
    }
}
