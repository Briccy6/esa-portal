<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\AcademicYear;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration form with courses list.
     */
    public function create()
    {
        $courses = Course::all();
        return view('auth.register', compact('courses'));
    }

    /**
     * Handle registration form submission.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date'],
            'gender' => ['required', 'in:female,male,other'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'course' => ['required', 'exists:courses,id'],
            'study_mode' => ['required', 'in:online,day,night'],
            'location' => ['required', 'in:kigali,musanze'],
            'passport_photo' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:1024'],  // max size 100 KB
            'id_document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],  // max size 2 MB
            'result_slip' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'], // max size 2 MB
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $course = Course::findOrFail($request->course);

        $academicYear = AcademicYear::where('is_ongoing', true)->first();

        if (!$academicYear) {
            return back()->withErrors(['academic_year' => 'No ongoing academic year found. Please contact admin.']);
        }

        $studentCount = User::where('course_id', $course->id)
            ->where('academic_year_id', $academicYear->id)
            ->count() + 1;

        $studentNumber = str_pad($studentCount, 3, '0', STR_PAD_LEFT);

        $regNumber = 'ESA' . strtoupper($course->abbreviation) . $studentNumber . $academicYear->year_range;

        // Store uploaded files
        $passportPhotoPath = $request->file('passport_photo')->store('passport_photos', 'public');
        $idUploadPath = $request->file('id_document')->store('id_document', 'public');
        $resultSlipPath = $request->file('result_slip')->store('result_slips', 'public');

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => $request->email,
            'course_id' => $course->id,
            'academic_year_id' => $academicYear->id,
            'reg_number' => $regNumber,
            'study_mode' => $request->study_mode,
            'location' => $request->location,
            'passport_photo' => $passportPhotoPath,
            'id_document' => $idUploadPath,
            'result_slip' => $resultSlipPath,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
