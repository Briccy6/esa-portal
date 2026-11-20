@extends('layouts.portal-login')

@section('title', 'Client sign in')
@section('portal-brand-label', 'Client portal')
@section('portal-eyebrow', 'Your learning account')
@section('portal-heading', 'Welcome back.')
@section('portal-description', 'Sign in to manage your learning dashboard and account.')

@section('content')
    <form action="{{ route('client.auth.login') }}" method="POST">
        @csrf
        <div class="portal-form-field">
            <label class="portal-form-label" for="email">Email address</label>
            <input class="portal-form-control"
                   id="email"
                   type="email"
                   name="email"
                   value="{{ old('email') }}"
                   autocomplete="username"
                   placeholder="you@example.com"
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
            <a class="portal-form-link" href="{{ route('client.auth.password.request') }}">Forgot password?</a>
        </div>
        <button class="portal-submit" type="submit">
            <i class="fa-solid fa-arrow-right-to-bracket" aria-hidden="true"></i>Sign in
        </button>
    </form>
@endsection

@section('portal-links')
    <p>New to ESA? <a href="{{ route('client.auth.register') }}">Create an account</a></p>
    <nav class="portal-quick-links" aria-label="Explore ESA">
        <a href="{{ route('public.exams.index') }}">Practice exams</a>
        <a href="{{ route('public.subscriptions.index') }}">Plans</a>
        <a href="{{ route('public.tokens.purchase') }}">Tokens</a>
    </nav>
@endsection
