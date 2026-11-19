<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class TeacherProfileController extends Controller
{
    public function edit()
    {
        $teacher = Auth::guard('teacher')->user();
        $systemName = config('app.name', 'School System');
        $courses = $teacher->courses()->with('lessons.units')->get();

        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();

        return view('teacher.profile.edit', compact('teacher', 'systemName', 'courses', 'announcements'));
    }

    public function update(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'teacher_name' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'current_password' => 'nullable|required_with:password',
            'password' => [
                'nullable',
                'string',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ], [
            'password.min' => 'Password must be at least 8 characters',
            'password.mixed_case' => 'Password must contain both uppercase and lowercase letters',
            'password.letters' => 'Password must contain letters',
            'password.numbers' => 'Password must contain numbers',
            'password.symbols' => 'Password must contain symbols',
            'current_password.required_with' => 'Current password is required when changing password',
        ]);

        // Verify current password if changing password
        if (!empty($request->password)) {
            if (!\Hash::check($request->current_password, $teacher->password)) {
                return back()
                    ->withInput()
                    ->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        // Handle photo upload - store as base64 to match navbar display
        if ($request->hasFile('photo')) {
            $data['photo'] = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        } else {
            unset($data['photo']);
        }

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
