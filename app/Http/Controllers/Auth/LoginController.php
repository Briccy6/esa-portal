<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest; // Adjust the import if you have a custom request

class LoginController extends Controller
{
    // Show the login form view
    public function showLoginForm()
    {
        return view('auth.login'); // Make sure you have resources/views/auth/login.blade.php
    }

    // Add a login method if you want to handle login POST requests
    public function login( $request)
    {
        // handle login logic here
    }
}
