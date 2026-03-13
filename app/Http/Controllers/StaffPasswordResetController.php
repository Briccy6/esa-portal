<?php

namespace App\Http\Controllers;

use App\Mail\StaffPasswordResetCodeMail;
use App\Models\Staff;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class StaffPasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('staff.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff) {
            return back()
                ->withErrors(['email' => 'We could not find a staff account with that email address.'])
                ->withInput();
        }

        $code = rand(100000, 999999);

        EmailVerification::where('email', $staff->email)->delete();

        $nextId = DB::table('email_verifications')->max('id') + 1;

        DB::table('email_verifications')->insert([
            'id'         => $nextId,
            'email'      => $staff->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
            'verified'   => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($staff->email)->send(new StaffPasswordResetCodeMail($staff, $code));
        } catch (\Exception $e) {
            Log::error('Failed to send staff password reset code', [
                'email' => $staff->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('staff.password.reset.form', ['email' => $staff->email])
            ->with('status', 'We have emailed you a 6‑digit code to reset your password.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        return view('staff.reset-password', [
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

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff) {
            return back()
                ->withErrors(['email' => 'We could not find a staff account with that email address.'])
                ->withInput($request->only('email'));
        }

        $record = EmailVerification::where('email', $staff->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()
                ->withErrors(['code' => 'The code is invalid or has expired.'])
                ->withInput($request->only('email'));
        }

        $staff->password = Hash::make($request->password);
        $staff->save();

        $record->delete();

        auth('staff')->login($staff);

        // Redirect based on role as in StaffAuthController
        $role = $staff->role;
        if ($role === 'DOS') {
            return redirect()->route('staff.dashboard.dos')
                ->with('success', 'Your password has been updated and you are now logged in.');
        }

        if ($role === 'Accountant') {
            return redirect()->route('staff.dashboard.accountant')
                ->with('success', 'Your password has been updated and you are now logged in.');
        }

        auth('staff')->logout();

        return redirect()->route('staff.login')
            ->with('error', 'Password updated but your role is not allowed to log in.');
    }
}

