<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class OptionalFeeResponseAnalytics
{
    public function byCourse(?int $academicYearId, array $branchIds, ?int $branchId = null): Collection
    {
        if ($branchIds === []) {
            return collect();
        }

        return DB::table('student_optional_fee_responses as responses')
            ->join('course_term_branch_fees as fees', 'fees.id', '=', 'responses.course_term_branch_fee_id')
            ->join('terms', 'terms.id', '=', 'fees.term_id')
            ->join('courses', 'courses.id', '=', 'fees.course_id')
            ->join('students', 'students.id', '=', 'responses.student_id')
            ->whereIn('fees.branch_id', $branchIds)
            ->whereColumn('students.course_id', 'fees.course_id')
            ->whereColumn('students.branch_id', 'fees.branch_id')
            ->whereColumn('students.academic_year_id', 'terms.academic_year_id')
            ->when($academicYearId, fn ($query) => $query->where('terms.academic_year_id', $academicYearId))
            ->when($branchId, fn ($query) => $query->where('fees.branch_id', $branchId))
            ->where(function ($query) {
                $query->where(function ($practical) {
                    $practical->where('responses.fee_type', 'practical')
                        ->where('fees.is_practical_mandatory', false)
                        ->where('fees.practical_fee', '>', 0);
                })->orWhere(function ($trip) {
                    $trip->where('responses.fee_type', 'trip')
                        ->where('fees.is_trip_mandatory', false)
                        ->where('fees.trip_fee', '>', 0);
                });
            })
            ->select('courses.id as course_id', 'courses.course_name')
            ->selectRaw(
                "SUM(CASE WHEN responses.response = 'accepted' AND (responses.notes IS NULL OR responses.notes NOT LIKE 'Assigned by accountant:%') THEN 1 ELSE 0 END) AS accepted"
            )
            ->selectRaw("SUM(CASE WHEN responses.response = 'declined' THEN 1 ELSE 0 END) AS declined")
            ->selectRaw("SUM(CASE WHEN responses.response = 'pending' THEN 1 ELSE 0 END) AS pending")
            ->selectRaw(
                "SUM(CASE WHEN responses.response = 'accepted' AND responses.notes LIKE 'Assigned by accountant:%' THEN 1 ELSE 0 END) AS assigned"
            )
            ->groupBy('courses.id', 'courses.course_name')
            ->orderBy('courses.course_name')
            ->get()
            ->map(function ($row) {
                $row->course_id = (int) $row->course_id;
                foreach (['accepted', 'declined', 'pending', 'assigned'] as $status) {
                    $row->{$status} = (int) $row->{$status};
                }

                return $row;
            });
    }
}
