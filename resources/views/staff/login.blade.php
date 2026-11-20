@extends('layouts.portal-login')

@section('title', 'Staff sign in')
@section('portal-brand-label', 'Staff portal')
@section('portal-eyebrow', 'Staff access')
@section('portal-heading', 'Welcome back.')
@section('portal-description', 'Sign in to continue to your staff workspace.')

@section('content')
    <form method="POST" action="{{ route('staff.login.submit') }}">
        @csrf
        <div class="portal-form-field">
            <label class="portal-form-label" for="email">Email address</label>
            <input class="portal-form-control"
                   id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   autocomplete="username"
                   placeholder="you@school.edu"
                   required
                   data-autofocus>
        </div>
        <div class="portal-form-field">
            <label class="portal-form-label" for="password">Password</label>
            <div class="portal-password-wrap">
                <input class="portal-form-control"
                       id="password"
                       type="password"
                       name="password"
                       autocomplete="current-password"
                       placeholder="Enter your password"
                       required>
                <button class="portal-password-toggle" type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false">
                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>
        <div class="portal-form-row">
            <label class="portal-form-check" for="remember">
                <input id="remember" type="checkbox" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
                Remember me
            </label>
            <a class="portal-form-link" href="{{ route('staff.password.request') }}">Forgot password?</a>
        </div>
        <button class="portal-submit" type="submit">
            <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>Sign in
        </button>
    </form>
@endsection

@section('portal-links')
    <span>For academy staff, including DOS, secretaries, and accountants.</span>
@endsection
