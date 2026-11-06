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
                function ($attribute, $value, $fail) {
                    // No repeated numbers (e.g., 111, 2222)
                    if (preg_match('/(\d)\1{1,}/', $value)) {
                        $fail('The password must not contain repeated numbers.');
                    }
                }
            ],
        ]);

        // Verify current password if changing password
        if (!empty($data['password'])) {
            if (!\Hash::check($data['current_password'], $teacher->password)) {
                return back()->with('error', 'Current password is incorrect.');
            }
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        unset($data['current_password']);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $photoName = time() . '_' . $teacher->id . '.' . $photo->getClientOriginalExtension();
            $photo->storeAs('teachers', $photoName, 'public');
            $data['photo'] = $photoName;
        }

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully.');
    }
}
