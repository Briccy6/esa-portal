<?php

namespace App\Http\Controllers;

use App\Mail\TeacherPasswordResetCodeMail;
use App\Models\Teacher;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TeacherPasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('teacher.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $teacher = Teacher::where('email', $request->email)->first();

        if (!$teacher) {
            return back()
                ->withErrors(['email' => 'We could not find a teacher with that email address.'])
                ->withInput();
        }

        $code = rand(100000, 999999);

        EmailVerification::where('email', $teacher->email)->delete();

        $nextId = DB::table('email_verifications')->max('id') + 1;

        DB::table('email_verifications')->insert([
            'id'         => $nextId,
            'email'      => $teacher->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
            'verified'   => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($teacher->email)->send(new TeacherPasswordResetCodeMail($teacher, $code));
        } catch (\Exception $e) {
            Log::error('Failed to send teacher password reset code', [
                'email' => $teacher->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('teacher.password.reset.form', ['email' => $teacher->email])
            ->with('status', 'We have emailed you a 6‑digit code to reset your password.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        return view('teacher.reset-password', [
            'email' => $email,
        ]);
    }

    public function resetWithCode(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'code'     => ['required', 'string'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $teacher = Teacher::where('email', $request->email)->first();

        if (!$teacher) {
            return back()
                ->withErrors(['email' => 'We could not find a teacher with that email address.'])
                ->withInput($request->only('email'));
        }

        $record = EmailVerification::where('email', $teacher->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()
                ->withErrors(['code' => 'The code is invalid or has expired.'])
                ->withInput($request->only('email'));
        }

        $teacher->password = Hash::make($request->password);
        $teacher->save();

        $record->delete();

        auth('teacher')->login($teacher);

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', 'Your password has been updated and you are now logged in.');
    }
}

