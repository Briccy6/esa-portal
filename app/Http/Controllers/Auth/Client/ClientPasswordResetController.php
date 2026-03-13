<?php

namespace App\Http\Controllers\Auth\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\EmailVerification;
use App\Mail\ClientPasswordResetCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ClientPasswordResetController extends Controller
{
    public function showRequestForm()
    {
        return view('auth.client.forgot-password');
    }

    public function sendCode(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $client = Client::where('email', $request->email)->first();

        if (!$client) {
            return back()
                ->withErrors(['email' => 'We could not find a client with that email address.'])
                ->withInput();
        }

        $code = rand(100000, 999999);

        EmailVerification::where('email', $client->email)->delete();

        $nextId = DB::table('email_verifications')->max('id') + 1;

        DB::table('email_verifications')->insert([
            'id'         => $nextId,
            'email'      => $client->email,
            'code'       => $code,
            'expires_at' => now()->addMinutes(15),
            'verified'   => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            Mail::to($client->email)->send(new ClientPasswordResetCodeMail($client, $code));
        } catch (\Exception $e) {
            Log::error('Failed to send client password reset code', [
                'email' => $client->email,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('client.auth.password.reset.form', ['email' => $client->email])
            ->with('status', 'We have emailed you a 6‑digit code to reset your password.');
    }

    public function showResetForm(Request $request)
    {
        $email = $request->query('email');

        return view('auth.client.reset-password', [
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

        $client = Client::where('email', $request->email)->first();

        if (!$client) {
            return back()
                ->withErrors(['email' => 'We could not find a client with that email address.'])
                ->withInput($request->only('email'));
        }

        $record = EmailVerification::where('email', $client->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', now())
            ->first();

        if (!$record) {
            return back()
                ->withErrors(['code' => 'The code is invalid or has expired.'])
                ->withInput($request->only('email'));
        }

        $client->password = Hash::make($request->password);
        $client->save();

        $record->delete();

        auth('client')->login($client);

        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Your password has been updated and you are now logged in.');
    }
}

