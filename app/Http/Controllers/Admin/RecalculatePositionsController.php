<?php

namespace App\Http\Controllers\Admin;

use App\Models\ReportCard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RecalculatePositionsController extends Controller
{
    public function index()
    {
        return view('admin.recalculate-positions');
    }

    public function execute(Request $request)
    {
        try {
            // Get all unique course + academic_year + term combinations
            $combinations = ReportCard::select('course_id', 'academic_year_id', 'term_id')
                ->distinct()
                ->get();

            if ($combinations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No report cards found to recalculate'
                ]);
            }

            $count = 0;
            $results = [];
            
            foreach ($combinations as $combo) {
                ReportCard::recalculatePositionsForCohort(
                    (int) $combo->course_id,
                    (int) $combo->academic_year_id,
                    (int) $combo->term_id
                );
                $count++;
                $results[] = "✓ Course {$combo->course_id}, Year {$combo->academic_year_id}, Term {$combo->term_id}";
            }

            \Log::info("Position recalculation complete: {$count} cohorts processed", $results);

            return response()->json([
                'success' => true,
                'message' => "✅ Successfully recalculated positions for {$count} cohorts",
                'results' => $results
            ]);
        } catch (\Exception $e) {
            \Log::error('Position recalculation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}
