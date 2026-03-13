<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class StaffProfileController extends Controller
{
 

public function update(Request $request)
{
    $staff = auth()->user();
    
    // Debug logging
    \Log::info('Profile update attempt', [
        'staff_id' => $staff->id,
        'has_file' => $request->hasFile('profile_photo'),
        'all_files' => $request->allFiles(),
        'request_data' => $request->except(['password', 'current_password'])
    ]);

    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'profile_photo' => 'nullable|image|max:2048',
        'current_password' => 'required_with:password',
        'password' => 'nullable|string|min:8|confirmed',
    ]);

    $updatedFields = [];

    if ($request->filled('name') && $request->name !== $staff->name) {
        $staff->name = $request->name;
        $updatedFields[] = 'Name';
    }

    if ($request->filled('email') && $request->email !== $staff->email) {
        $staff->email = $request->email;
        $updatedFields[] = 'Email';
    }

    if ($request->hasFile('profile_photo')) {
        // Handle file upload
        $file = $request->file('profile_photo');
        \Log::info('Processing profile photo', [
            'file_info' => [
                'original_name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]
        ]);
        
        $filename = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('profile_photos', $filename, 'public');
        $staff->photo = $path;
        $updatedFields[] = 'Profile Photo';
        
        \Log::info('Profile photo stored', ['path' => $path]);
    }

    if ($request->filled('password')) {
        if (\Hash::check($request->current_password, $staff->password)) {
            $staff->password = \Hash::make($request->password);
            $updatedFields[] = 'Password';
        } else {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Current password is incorrect.'
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
    }

    \Log::info('Updated fields', ['fields' => $updatedFields]);

    if (count($updatedFields)) {
        $staff->save();
        $message = 'Profile updated successfully. Updated: ' . implode(', ', $updatedFields);
        
        \Log::info('Profile saved successfully', ['message' => $message]);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_fields' => $updatedFields
            ]);
        }
        
        return back()->with('success', $message)
                     ->with('updated_fields', $updatedFields);
    } else {
        $message = 'No changes were made.';
        
        \Log::info('No changes made');
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        }
        
        return back()->with('success', $message);
    }
}

}

