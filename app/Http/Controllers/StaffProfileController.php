<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffProfileController extends Controller
{
    public function update(Request $request)
    {
        $staff = Auth::guard('staff')->user();

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('staff', 'email')->ignore($staff->id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'profile_photo' => 'nullable|image|max:2048',
            'current_password' => 'required_with:password',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $updatedFields = [];

        foreach (['name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'address' => 'Address'] as $field => $label) {
            if (array_key_exists($field, $validated) && $validated[$field] !== $staff->{$field}) {
                $staff->{$field} = $validated[$field];
                $updatedFields[] = $label;
            }
        }

        if ($request->hasFile('profile_photo')) {
            $photo = $request->file('profile_photo');
            $staff->photo = 'data:' . $photo->getMimeType() . ';base64,' . base64_encode($photo->get());
            $updatedFields[] = 'Profile Photo';
        }

        if (!empty($validated['password'])) {
            if (!Hash::check($validated['current_password'], $staff->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }

            $staff->password = Hash::make($validated['password']);
            $updatedFields[] = 'Password';
        }

        if ($updatedFields) {
            $staff->save();
            $message = 'Profile updated successfully. Updated: ' . implode(', ', $updatedFields);
        } else {
            $message = 'No changes were made.';
        }

        return back()->with('success', $message);
    }
}
