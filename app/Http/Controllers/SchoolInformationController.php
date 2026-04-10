<?php

namespace App\Http\Controllers;

use App\Models\SchoolInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

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

        // Handle logo upload (store as BLOB in database)
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            try {
                $logoBinary = file_get_contents($request->file('logo')->getRealPath());
                if ($logoBinary !== false) {
                    $schoolInfo->logo = $logoBinary;
                    // Clear legacy file path columns
                    $schoolInfo->logo_path = null;
                    if (Schema::hasColumn('school_informations', 'logo_file_path')) {
                        $schoolInfo->logo_file_path = null;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Logo upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload logo');
            }
        }

        // Handle principal signature upload (store as BLOB in database)
        if ($request->hasFile('principal_signature') && $request->file('principal_signature')->isValid()) {
            try {
                $signatureBinary = file_get_contents($request->file('principal_signature')->getRealPath());
                if ($signatureBinary !== false) {
                    $schoolInfo->principal_signature_data = $signatureBinary;
                    // Clear legacy file path columns
                    $schoolInfo->principal_signature = null;
                    if (Schema::hasColumn('school_informations', 'principal_signature_file_path')) {
                        $schoolInfo->principal_signature_file_path = null;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Principal signature upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload principal signature');
            }
        }

        // Handle stamp signature upload (store as BLOB in database)
        if ($request->hasFile('stamp_signature') && $request->file('stamp_signature')->isValid()) {
            try {
                $stampBinary = file_get_contents($request->file('stamp_signature')->getRealPath());
                if ($stampBinary !== false) {
                    $schoolInfo->stamp_signature_data = $stampBinary;
                    // Clear legacy file path columns
                    $schoolInfo->stamp_signature = null;
                    if (Schema::hasColumn('school_informations', 'stamp_signature_file_path')) {
                        $schoolInfo->stamp_signature_file_path = null;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Stamp signature upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload stamp signature');
            }
        }

        $schoolInfo->fill($request->except(['logo', 'principal_signature', 'stamp_signature']));
        $schoolInfo->is_active = true;
        $schoolInfo->save();

        return redirect()->route('admin.school-information.edit')
            ->with('success', 'School information updated successfully!');
    }
}
