<?php

namespace App\Http\Controllers;

use App\Models\SchoolInformation;
use App\Services\SchoolImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SchoolInformationController extends Controller
{
    private SchoolImageService $imageService;

    public function __construct(SchoolImageService $imageService)
    {
        $this->imageService = $imageService;
    }

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

        // Some deployments may not have run the latest school images migrations yet.
        // In that case we must NOT write to *_file_path columns (would cause SQL errors).
        $hasLogoFilePathCols = Schema::hasColumn('school_informations', 'logo_file_path');
        $hasPrincipalFilePathCols = Schema::hasColumn('school_informations', 'principal_signature_file_path');
        $hasStampFilePathCols = Schema::hasColumn('school_informations', 'stamp_signature_file_path');

        // Handle logo upload (file storage)
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            try {
                // Delete old file if exists
                if ($hasLogoFilePathCols && $schoolInfo->logo_file_path) {
                    $this->imageService->deleteImage($schoolInfo->logo_file_path);
                }
                
                // Store new file
                $filePath = $this->imageService->storeImage($request->file('logo'), 'logo');
                if ($filePath) {
                    if ($hasLogoFilePathCols) {
                        $schoolInfo->logo_file_path = $filePath;
                        // Clear old BLOB data after migration
                        $schoolInfo->logo = null;
                        $schoolInfo->logo_path = null;
                    } else {
                        // Legacy schema: persist into existing string column
                        $schoolInfo->logo_path = $filePath;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Logo upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload logo');
            }
        }

        // Handle principal signature upload (file storage)
        if ($request->hasFile('principal_signature') && $request->file('principal_signature')->isValid()) {
            try {
                // Delete old file if exists
                if ($hasPrincipalFilePathCols && $schoolInfo->principal_signature_file_path) {
                    $this->imageService->deleteImage($schoolInfo->principal_signature_file_path);
                }
                
                // Store new file
                $filePath = $this->imageService->storeImage($request->file('principal_signature'), 'principal_signature');
                if ($filePath) {
                    if ($hasPrincipalFilePathCols) {
                        $schoolInfo->principal_signature_file_path = $filePath;
                        // Clear old BLOB data after migration
                        $schoolInfo->principal_signature_data = null;
                        $schoolInfo->principal_signature = null;
                    } else {
                        // Legacy schema: persist into existing string column
                        $schoolInfo->principal_signature = $filePath;
                    }
                }
            } catch (\Exception $e) {
                Log::error('Principal signature upload failed: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to upload principal signature');
            }
        }

        // Handle stamp signature upload (file storage)
        if ($request->hasFile('stamp_signature') && $request->file('stamp_signature')->isValid()) {
            try {
                // Delete old file if exists
                if ($hasStampFilePathCols && $schoolInfo->stamp_signature_file_path) {
                    $this->imageService->deleteImage($schoolInfo->stamp_signature_file_path);
                }
                
                // Store new file
                $filePath = $this->imageService->storeImage($request->file('stamp_signature'), 'stamp_signature');
                if ($filePath) {
                    if ($hasStampFilePathCols) {
                        $schoolInfo->stamp_signature_file_path = $filePath;
                        // Clear old BLOB data after migration
                        $schoolInfo->stamp_signature_data = null;
                        $schoolInfo->stamp_signature = null;
                    } else {
                        // Legacy schema: persist into existing string column
                        $schoolInfo->stamp_signature = $filePath;
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
