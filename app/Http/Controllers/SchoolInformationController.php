<?php

namespace App\Http\Controllers;

use App\Models\SchoolInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SchoolInformationController extends Controller
{
    public function edit()
    {
        $schoolInfo = SchoolInformation::getActive() ?: new SchoolInformation();
        return view('admin.school-information.edit', compact('schoolInfo'));
    }

    public function update(Request $request)
    {
        $schoolInfo = SchoolInformation::getActive() ?: new SchoolInformation();
        
        $request->validate([
            'school_name' => 'required|string|max:255',
            'address' => 'required|string',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'motto' => 'nullable|string|max:255',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'principal_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'principal_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'stamp_signature' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle logo upload (database storage)
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            try {
                $logoData = file_get_contents($request->file('logo')->getPathname());
                $schoolInfo->logo = $logoData;
                $schoolInfo->logo_path = null; // Clear file path
            } catch (\Exception $e) {
                Log::error('Logo upload failed: ' . $e->getMessage());
            }
        }

        // Handle principal signature upload (database storage)
        if ($request->hasFile('principal_signature') && $request->file('principal_signature')->isValid()) {
            try {
                $signatureData = file_get_contents($request->file('principal_signature')->getPathname());
                $schoolInfo->principal_signature_data = $signatureData;
                $schoolInfo->principal_signature = null; // Clear file path
            } catch (\Exception $e) {
                Log::error('Principal signature upload failed: ' . $e->getMessage());
            }
        }

        // Handle stamp signature upload (database storage)
        if ($request->hasFile('stamp_signature') && $request->file('stamp_signature')->isValid()) {
            try {
                $stampData = file_get_contents($request->file('stamp_signature')->getPathname());
                $schoolInfo->stamp_signature_data = $stampData;
                $schoolInfo->stamp_signature = null; // Clear file path
            } catch (\Exception $e) {
                Log::error('Stamp signature upload failed: ' . $e->getMessage());
            }
        }

        $schoolInfo->fill($request->except(['logo', 'principal_signature', 'stamp_signature']));
        $schoolInfo->is_active = true;
        $schoolInfo->save();

        return redirect()->route('admin.school-information.edit')
            ->with('success', 'School information updated successfully!');
    }
}
