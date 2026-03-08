<?php

namespace App\Http\Controllers\Auth\Client;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class ClientAuthController extends Controller
{
    /**
     * Show the client dashboard
     */
    public function showDashboard()
    {
        return view('client.dashboard');
    }

    /**
     * Show the client registration form
     */
    public function showRegistrationForm()
    {
        return view('auth.client.register');
    }

    /**
     * Handle a registration request for the application
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            'phone' => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'terms' => ['accepted'],
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_active' => true,
            'verification_status' => 'verified',
        ]);

        event(new Registered($client));

        // Send welcome email
        $this->sendWelcomeEmail($client);

        Auth::guard('client')->login($client);

        return redirect()->route('client.dashboard');
    }

    /**
     * Show the client login form
     */
    public function showLoginForm()
    {
        return view('auth.client.login');
    }

    /**
     * Handle a login request to the application
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('client')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('client.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Destroy an authenticated session
     */
    public function logout(Request $request)
    {
        Auth::guard('client')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.auth.login');
    }

    /**
     * Redirect the user to the Google authentication page
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Find or create client
            $client = Client::firstOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name' => $googleUser->getName(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // Random password for OAuth users
                    'is_active' => true,
                    'verification_status' => 'verified',
                ]
            );

            // Update Google ID if client exists but doesn't have it
            if (!$client->google_id) {
                $client->update(['google_id' => $googleUser->getId()]);
            }

            // Send welcome email only for new clients
            if ($client->wasRecentlyCreated) {
                $this->sendWelcomeEmail($client);
            }

            Auth::guard('client')->login($client);

            return redirect()->route('client.dashboard');
        } catch (\Exception $e) {
            return redirect()->route('client.auth.login')
                ->with('error', 'Unable to login with Google. Please try again.');
        }
    }

    /**
     * Send welcome email to new client
     */
    private function sendWelcomeEmail($client)
    {
        try {
            $data = [
                'name' => $client->name,
                'email' => $client->email,
                'login_url' => route('client.auth.login'),
                'dashboard_url' => route('client.dashboard'),
                'exams_url' => route('public.exams.index'),
                'subscriptions_url' => route('public.subscriptions.index'),
                'tokens_url' => route('public.tokens.purchase'),
            ];

            Mail::send('emails.client.welcome', $data, function ($message) use ($client) {
                $message->to($client->email, $client->name)
                    ->subject('Welcome to ESA Portal - Your Learning Journey Begins!')
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });
        } catch (\Exception $e) {
            // Log error but don't stop registration process
            \Log::error('Failed to send welcome email to client: ' . $e->getMessage());
        }
    }
}
