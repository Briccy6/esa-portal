<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    public function edit()
    {
        $admin = Auth::guard('admin')->user();
        return view('admin.profile.edit', compact('admin'));
    }

    public function update(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        $request->validate([
            'email' => 'required|email|unique:admins,email,' . $admin->id,
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Verify current password if new password is provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $admin->password)) {
                throw ValidationException::withMessages([
                    'current_password' => ['Current password does not match our records.'],
                ]);
            }
            $admin->password = Hash::make($request->password);
        }

        // Update email
        $admin->email = $request->email;

        $admin->save();

        return redirect()->route('admin.profile.edit')->with('success', 'Profile updated successfully.');
    }
} 