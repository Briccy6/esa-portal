@extends('layouts.portal-login')

@section('title', 'Admin sign in')
@section('portal-brand-label', 'Administration')
@section('portal-eyebrow', 'Secure admin access')
@section('portal-heading', 'Admin sign in')
@section('portal-description', 'Sign in with your administrator account to manage the academy.')

@section('content')
    <form method="POST" action="{{ route('admin.login') }}">
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
        <button class="portal-submit" type="submit">
            <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>Sign in securely
        </button>
    </form>
@endsection

@section('portal-links')
    <a href="{{ route('admin.password.request') }}">Forgot your password?</a>
    <span class="portal-link-separator" aria-hidden="true">·</span>
    <a href="{{ url('/') }}">Back to home</a>
@endsection
