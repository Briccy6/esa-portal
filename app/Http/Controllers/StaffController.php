<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class StaffController extends Controller
{
    public function index()
    {
        $staffs = Staff::all();
        return view('admin.staff.index', compact('staffs'));
    }

    public function create()
    {
        $roles = ['DOS', 'Accountant', 'Secretary'];
        $branches = \App\Models\Branch::all();
        return view('admin.staff.create', compact('roles', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:DOS,Accountant,Secretary,Teacher',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
            'email' => 'required|email|unique:staff,email',
            'phone' => 'required|unique:staff,phone',
            'branches' => 'required|array|min:1',
            'branches.*' => 'exists:branches,id',
        ]);

        $generatedPassword = Str::random(8);

        // Store photo as base64 string
        $photoBase64 = null;
        if ($request->hasFile('photo')) {
            $photoBase64 = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        }

        $staff = Staff::create([
            'name' => $request->name,
            'role' => $request->role,
            'photo' => $photoBase64,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($generatedPassword),
            'qr_code' => '',
        ]);

        // Assign branches
        $staff->branches()->attach($request->branches);

        // Generate a signed URL that expires in 1 hour
        $loginUrl = URL::temporarySignedRoute(
            'staff.one_time_login',
            now()->addHour(),
            ['staff' => $staff->id]
        );

        // Send welcome email
        Mail::to($staff->email)->send(new \App\Mail\StaffWelcomeMail($staff, $generatedPassword, $loginUrl));

        // Redirect with the generated password (only once)
        // Instead of ->with('password', $generatedPassword), do this:

        return redirect()->route('admin.staff.show', ['staff' => $staff->id, 'temp_password' => $generatedPassword])
            ->with('success', 'Staff member created successfully. See temporary password below.');
    }

    public function edit(Staff $staff)
    {
        $roles = ['DOS', 'Accountant', 'Secretary', 'Teacher'];
        $branches = \App\Models\Branch::all(); // Add this line
        return view('admin.staff.edit', compact('staff', 'roles', 'branches')); // Add 'branches'
    }

    public function update(Request $request, Staff $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'role' => 'required|in:DOS,Accountant,Secretary,Teacher',
            'photo' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $staff->name = $request->name;
        $staff->role = $request->role;

        if ($request->hasFile('photo')) {
            $staff->photo = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        }

        if ($request->filled('password')) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        // Sync branches
        $staff->branches()->sync($request->input('branches', []));

        return redirect()->route('admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(Staff $staff)
    {
        if ($staff->photo) Storage::disk('public')->delete($staff->photo);
        if ($staff->staff_card) Storage::disk('public')->delete($staff->staff_card);
        if ($staff->qr_code) Storage::disk('public')->delete($staff->qr_code);

        $staff->delete();

        return redirect()->route('admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }

    public function show(Staff $staff, Request $request)
    {
        // Get temp_password from query string, or null if not present
        $temp_password = $request->query('temp_password');

        return view('admin.staff.show', compact('staff', 'temp_password'));
    }

    public function courseFees()
    {
        $courses = \App\Models\Course::with(['branches', 'teachers', 'students', 'transcripts'])->get();
        return view('staff.course_fees', compact('courses'));
    }

    // One-time login method
    public function oneTimeLogin(Request $request, Staff $staff)
    {
        // Log in the staff (you may need to use the correct guard)
        auth()->login($staff);

        // Optionally, invalidate the link after use (e.g., set a flag in DB or use a short expiry)
        // Redirect to dashboard or force password change
        return redirect()->route('staff.dashboard')->with('success', 'Welcome! Please change your password.');
    }

    public function updateProfile(Request $request)
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

    public function resetPassword(Staff $staff)
    {
        $newPassword = \Illuminate\Support\Str::random(8);
        $staff->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $staff->save();

        // Send email to staff
        \Illuminate\Support\Facades\Mail::to($staff->email)->send(
            new \App\Mail\StaffPasswordResetMail($staff, $newPassword)
        );

        return redirect()->route('admin.staff.index')->with('success', 'Password reset and sent to staff email.');
    }
}
