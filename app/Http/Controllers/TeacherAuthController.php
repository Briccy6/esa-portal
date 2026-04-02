<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Teacher;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Unit;
class TeacherAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('teacher.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::guard('teacher')->attempt($credentials)) {
            $teacher = Auth::guard('teacher')->user();

            // Gather login info
            $loginTime = now()->toDayDateTimeString();
            $ipAddress = $request->ip();
            $userAgent = $request->header('User-Agent');
            $logoutUrl = route('teacher.force-logout');

            // Send welcome back email
            \Mail::to($teacher->email)->send(
                new \App\Mail\TeacherWelcomeBackMail($teacher, $loginTime, $ipAddress, $userAgent, $logoutUrl)
            );

            return redirect()->route('teacher.dashboard');
        }
        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout()
    {
        Auth::guard('teacher')->logout();
        return redirect()->route('teacher.login');
    }

    public function dashboard()
    {
        $teacher = Auth::guard('teacher')->user();
        $systemName = config('app.name', 'School System');
        $courses = $teacher->courses()->with('lessons.units')->get();
        $announcements = \App\Models\Announcement::latest()->take(5)->get(); // or whatever logic you want
        return view('teacher.dashboard', compact('teacher', 'systemName', 'courses', 'announcements'));
    }
    public function courseDetails()
    {
        $teacher = Auth::guard('teacher')->user();
        $systemName = config('app.name', 'School System');
        
        // Get announcements for teacher layout
        $announcements = \App\Models\Announcement::latest()->take(5)->get();
        
        $courses = $teacher->courses()->with([
            // Only lessons where teacher_id matches the logged-in teacher
            'lessons' => function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
            },
            'lessons.units',
            'students'
        ])->get();

        // Collect all unit IDs for this teacher's courses
        $unitIds = [];
        foreach ($courses as $course) {
            foreach ($course->lessons as $lesson) {
                foreach ($lesson->units as $unit) {
                    $unitIds[] = $unit->id;
                }
            }
        }

        // Get daily hours per unit for this teacher
        $unitDailyHours = \App\Models\TeacherDaily::where('teacher_id', $teacher->id)
            ->whereIn('unit_id', $unitIds)
            ->selectRaw('unit_id, SUM(hours_studied) as total_hours')
            ->groupBy('unit_id')
            ->pluck('total_hours', 'unit_id')
            ->toArray();

        return view('teacher.courses.details', compact('teacher', 'systemName', 'courses', 'unitDailyHours', 'announcements'));
    }
    public function showForceLogout()
    {
        return view('teacher.force-logout');
    }
    public function updateProfile(Request $request)
    {
        $teacher = Auth::guard('teacher')->user();

        $data = $request->validate([
            'teacher_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:100',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = base64_encode(file_get_contents($request->file('photo')->getRealPath()));
        } else {
            unset($data['photo']);
        }

        if (!empty($data['password'])) {
            $data['password'] = \Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $teacher->update($data);

        return back()->with('success', 'Profile updated successfully!');
    }
}
