<?php

namespace App\Http\Controllers;

use App\Mail\AdminPasswordResetCodeMail;
use App\Models\Admin;
use App\Models\EmailVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AdminPasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('admin.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()
                ->withErrors(['email' => 'We could not find an admin with that email address.'])
                ->withInput();
        }

        $code = rand(100000, 999999);

        EmailVerification::where('email', $admin->email)->delete();

        $nextId = DB::table('email_verifications')->max('id') + 1;

        DB::table('email_verifications')->insert([
            'id'         => $nextId,
            'email'      => $admin->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
            'verified'   => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($admin->email)->send(new AdminPasswordResetCodeMail($admin, $code));
        } catch (\Exception $e) {
            Log::error('Failed to send admin password reset code', [
                'email' => $admin->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.password.reset.form', ['email' => $admin->email])
            ->with('status', 'We have emailed you a 6‑digit code to reset your password.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        return view('admin.reset-password', [
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

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()
                ->withErrors(['email' => 'We could not find an admin with that email address.'])
                ->withInput($request->only('email'));
        }

        $record = EmailVerification::where('email', $admin->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()
                ->withErrors(['code' => 'The code is invalid or has expired.'])
                ->withInput($request->only('email'));
        }

        $admin->password = Hash::make($request->password);
        $admin->save();

        $record->delete();

        auth('admin')->login($admin);

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Your password has been updated and you are now logged in.');
    }
}

