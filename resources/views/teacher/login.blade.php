@extends('layouts.portal-login')

@section('title', 'Teacher sign in')
@section('portal-brand-label', 'Teacher portal')
@section('portal-eyebrow', 'Teacher access')
@section('portal-heading', 'Welcome back.')
@section('portal-description', 'Sign in to access your classes and teaching workspace.')

@section('content')
    <form method="POST" action="{{ route('teacher.login.submit') }}">
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
            <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>Sign in
        </button>
    </form>
@endsection

@section('portal-links')
    <a href="{{ route('teacher.password.request') }}">Forgot your password?</a>
    <span class="portal-link-separator" aria-hidden="true">·</span>
    <a href="{{ url('/') }}">Back to home</a>
@endsection
