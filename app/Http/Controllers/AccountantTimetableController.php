<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;

class AccountantTimetableController extends Controller
{
    public function index()
    {
        $staff = Auth::guard('staff')->user();

        // Get accountant's assigned branches
        $branchIds = $staff->branches->pluck('id')->toArray();

        if (empty($branchIds)) {
            $branchIds = \App\Models\Branch::pluck('id')->toArray();
        }

        // Get timetables for accountant's branches
        $timetables = Timetable::published()
            ->whereIn('branch_id', $branchIds)
            ->whereDate('valid_from', '<=', now())
            ->whereDate('valid_until', '>=', now())
            ->with(['branch', 'academicYear', 'term', 'sessions.lesson', 'sessions.courses'])
            ->orderBy('valid_from', 'desc')
            ->get();

        return view('accountant.timetables.index', compact('timetables'));
    }
}
