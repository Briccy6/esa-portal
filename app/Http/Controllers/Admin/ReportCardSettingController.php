<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportCardSetting;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportCardSettingController extends Controller
{
    public function index()
    {
        $settings = ReportCardSetting::orderBy('academic_year', 'desc')
                                     ->orderBy('term', 'desc')
                                     ->paginate(10);
        
        return view('admin.report-card-settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.report-card-settings.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string|unique:report_card_settings,academic_year,NULL,id,term,' . $request->term,
            'term' => 'required|string',
            'report_card_available_at' => 'required|date|after:now',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        ReportCardSetting::create([
            'academic_year' => $request->academic_year,
            'term' => $request->term,
            'report_card_available_at' => $request->report_card_available_at,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.report-card-settings.index')
                         ->with('success', 'Report card setting created successfully.');
    }

    public function edit(ReportCardSetting $reportCardSetting)
    {
        return view('admin.report-card-settings.edit', compact('reportCardSetting'));
    }

    public function update(Request $request, ReportCardSetting $reportCardSetting)
    {
        $request->validate([
            'academic_year' => 'required|string|unique:report_card_settings,academic_year,' . $reportCardSetting->id . ',id,term,' . $request->term,
            'term' => 'required|string',
            'report_card_available_at' => 'required|date',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $reportCardSetting->update([
            'academic_year' => $request->academic_year,
            'term' => $request->term,
            'report_card_available_at' => $request->report_card_available_at,
            'description' => $request->description,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.report-card-settings.index')
                         ->with('success', 'Report card setting updated successfully.');
    }

    public function destroy(ReportCardSetting $reportCardSetting)
    {
        $reportCardSetting->delete();

        return redirect()->route('admin.report-card-settings.index')
                         ->with('success', 'Report card setting deleted successfully.');
    }

    public function toggleStatus(ReportCardSetting $reportCardSetting)
    {
        $reportCardSetting->update(['is_active' => !$reportCardSetting->is_active]);

        $status = $reportCardSetting->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('admin.report-card-settings.index')
                         ->with('success', "Report card setting {$status} successfully.");
    }
}
