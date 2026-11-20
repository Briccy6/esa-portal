<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @param string $userType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle($userType = 'user')
    {
        // Determine user type from current route
        $request = request();
        $path = $request->path();

        if (str_contains($path, 'student')) {
            $userType = 'student';
        } elseif (str_contains($path, 'teacher')) {
            $userType = 'teacher';
        } elseif (str_contains($path, 'staff')) {
            $userType = 'staff';
        } elseif (str_contains($path, 'admin')) {
            $userType = 'admin';
        }

        // Store the user type in session for callback
        session(['google_user_type' => $userType]);

        // Set the redirect URI based on user type - use production URL
        $redirectUri = match($userType) {
            'student' => 'https://elearning.excellentservantacademy.com/student/auth/google/callback',
            'teacher' => 'https://elearning.excellentservantacademy.com/teacher/auth/google/callback',
            'staff' => 'https://elearning.excellentservantacademy.com/staff/auth/google/callback',
            'admin' => 'https://elearning.excellentservantacademy.com/admin/auth/google/callback',
            default => 'https://elearning.excellentservantacademy.com/auth/google/callback',
        };

        config(['services.google.redirect' => $redirectUri]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle the callback from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $userType = session('google_user_type', 'user');

            // Find or create user based on user type
            $user = $this->findOrCreateUser($googleUser, $userType);

            if ($user) {
                // Login the user
                $this->loginUser($user, $userType);

                // Redirect to appropriate dashboard
                return $this->redirectToDashboard($userType);
            } else {
                // If user not found and email doesn't match, redirect to login with error
                return redirect()->route($this->getLoginRoute($userType))
                    ->with('error', 'No account found with this email. Please register first.');
            }
        } catch (Exception $e) {
            return redirect()->route($this->getLoginRoute($userType ?? 'user'))
                ->with('error', 'Google authentication failed: ' . $e->getMessage());
        }
    }

    /**
     * Find or create user based on Google account and user type.
     *
     * @param \Laravel\Socialite\Two\User $googleUser
     * @param string $userType
     * @return mixed
     */
    protected function findOrCreateUser($googleUser, $userType)
    {
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $name = $googleUser->getName();
        $avatar = $googleUser->getAvatar();

        // Try to find user by Google ID first
        $user = $this->findUserByGoogleId($googleId, $userType);

        if ($user) {
            // Update Google token and avatar
            $this->updateGoogleInfo($user, $googleUser);
            return $user;
        }

        // If not found by Google ID, try to find by email
        $user = $this->findUserByEmail($email, $userType);

        if ($user) {
            // Link Google account to existing user
            $this->linkGoogleAccount($user, $googleUser);
            return $user;
        }

        // User not found - return null (redirect to registration)
        return null;
    }

    /**
     * Find user by Google ID.
     *
     * @param string $googleId
     * @param string $userType
     * @return mixed
     */
    protected function findUserByGoogleId($googleId, $userType)
    {
        return match($userType) {
            'student' => Student::where('google_id', $googleId)->first(),
            'teacher' => Teacher::where('google_id', $googleId)->first(),
            'staff' => Staff::where('google_id', $googleId)->first(),
            'admin' => Admin::where('google_id', $googleId)->first(),
            default => User::where('google_id', $googleId)->first(),
        };
    }

    /**
     * Find user by email.
     *
     * @param string $email
     * @param string $userType
     * @return mixed
     */
    protected function findUserByEmail($email, $userType)
    {
        return match($userType) {
            'student' => Student::where('email', $email)->first(),
            'teacher' => Teacher::where('email', $email)->first(),
            'staff' => Staff::where('email', $email)->first(),
            'admin' => Admin::where('email', $email)->first(),
            default => User::where('email', $email)->first(),
        };
    }

    /**
     * Update Google information for user.
     *
     * @param mixed $user
     * @param \Laravel\Socialite\Two\User $googleUser
     * @return void
     */
    protected function updateGoogleInfo($user, $googleUser)
    {
        $user->update([
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'google_avatar' => $googleUser->getAvatar(),
        ]);
    }

    /**
     * Link Google account to existing user.
     *
     * @param mixed $user
     * @param \Laravel\Socialite\Two\User $googleUser
     * @return void
     */
    protected function linkGoogleAccount($user, $googleUser)
    {
        $user->update([
            'google_id' => $googleUser->getId(),
            'google_token' => $googleUser->token,
            'google_refresh_token' => $googleUser->refreshToken,
            'google_avatar' => $googleUser->getAvatar(),
        ]);
    }

    /**
     * Login user based on user type.
     *
     * @param mixed $user
     * @param string $userType
     * @return void
     */
    protected function loginUser($user, $userType)
    {
        $guard = match($userType) {
            'student' => 'student',
            'teacher' => 'teacher',
            'staff' => 'staff',
            'admin' => 'admin',
            default => 'web',
        };

        Auth::guard($guard)->login($user, true);
    }

    /**
     * Redirect to appropriate dashboard.
     *
     * @param string $userType
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectToDashboard($userType)
    {
        return match($userType) {
            'student' => redirect()->route('student.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'staff' => redirect()->route('staff.dashboard'),
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }

    /**
     * Get login route for user type.
     *
     * @param string $userType
     * @return string
     */
    protected function getLoginRoute($userType)
    {
        return match($userType) {
            'student' => 'student.login',
            'teacher' => 'teacher.login',
            'staff' => 'staff.login',
            'admin' => 'admin.login.form',
            default => 'login',
        };
    }

    /**
     * Unlink Google account from user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function unlinkGoogleAccount(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->update([
                'google_id' => null,
                'google_token' => null,
                'google_refresh_token' => null,
                'google_avatar' => null,
            ]);

            return back()->with('success', 'Google account unlinked successfully.');
        }

        return back()->with('error', 'Unable to unlink Google account.');
    }
}
