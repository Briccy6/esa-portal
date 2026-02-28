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

    $request->validate([
        'name' => 'nullable|string|max:255',
        'email' => 'nullable|email|max:255',
        'photo' => 'nullable|image|max:2048',
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

    if ($request->hasFile('photo')) {
        // Debug: Check if file is received
        // dd($request->file('photo'));
        $staff->photo = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        $updatedFields[] = 'Photo';
    }

    if ($request->filled('password')) {
        if (\Hash::check($request->current_password, $staff->password)) {
            $staff->password = \Hash::make($request->password);
            $updatedFields[] = 'Password';
        } else {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
    }

    if (count($updatedFields)) {
        $staff->save();
        return back()->with('success', 'Profile updated successfully.')
                     ->with('updated_fields', $updatedFields);
    } else {
        return back()->with('success', 'No changes were made.');
    }
}

}

