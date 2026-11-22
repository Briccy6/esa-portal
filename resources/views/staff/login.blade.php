@extends('layouts.portal-login')

@section('title', 'Staff sign in')
@section('portal-brand-label', 'Staff portal')
@section('portal-eyebrow', 'Staff access')
@section('portal-heading', 'Welcome back.')
@section('portal-description', 'Sign in to continue to your staff workspace.')

@section('content')
    <a href="{{ route('staff.auth.google') }}" class="portal-submit portal-submit-google" style="background-color: #4285f4; color: white; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-bottom: 1rem;">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
        </svg>
        Continue with Google
    </a>

    <div class="portal-divider" style="display: flex; align-items: center; margin: 1.5rem 0; color: #6b7280;">
        <span style="flex: 1; height: 1px; background: #e5e7eb;"></span>
        <span style="padding: 0 1rem; font-size: 0.875rem;">or sign in with email</span>
        <span style="flex: 1; height: 1px; background: #e5e7eb;"></span>
    </div>

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

    <!-- Account Lock Popup -->
    @if(session('locked'))
    <div class="modal fade show" id="lockModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-lock me-2"></i>Account Locked
                    </h5>
                    <button type="button" class="btn-close btn-close-white" onclick="window.location.href='{{ route('staff.login') }}'"></button>
                </div>
                <div class="modal-body">
                    @if(session('lock_reason'))
                    <div class="alert alert-danger">
                        <strong>Reason:</strong> {{ session('lock_reason') }}
                    </div>
                    @endif

                    @if(session('lock_details'))
                    <div class="alert alert-info">
                        <strong>Details:</strong>
                        @if(is_array(session('lock_details')))
                            @if(isset(session('lock_details')['current_percentage']))
                                <p>Current Payment: {{ round(session('lock_details')['current_percentage'], 2) }}%</p>
                                <p>Required: {{ session('lock_details')['required_percentage'] }}%</p>
                            @elseif(isset(session('lock_details')['days_without_expense']))
                                <p>Days without recording expenses: {{ session('lock_details')['days_without_expense'] }}</p>
                                <p>Required limit: {{ session('lock_details')['required_days'] }} days</p>
                            @endif
                        @endif
                    </div>
                    @endif

                    @if(session('conditions'))
                    <div class="alert alert-warning">
                        <strong>Conditions:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach(session('conditions') as $condition)
                            <li>{{ $condition['message'] }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="alert alert-primary">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Please contact IT staff for assistance.</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="window.location.href='{{ route('staff.login') }}'">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Conditions Warning Popup -->
    @if(session('conditions') && !session('locked'))
    <div class="modal fade show" id="conditionsModal" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Action Required
                    </h5>
                    <button type="button" class="btn-close" onclick="document.getElementById('conditionsModal').style.display='none'"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">Please review the following conditions:</p>
                    <ul class="list-group mb-3">
                        @foreach(session('conditions') as $condition)
                        <li class="list-group-item {{ $condition['lock_active'] ?? false ? 'list-group-item-danger' : 'list-group-item-warning' }}">
                            <strong>{{ $condition['type'] ?? 'Condition' }}</strong>: {{ $condition['message'] }}
                            @if(isset($condition['action_required']))
                            <br><small class="text-muted">Action: {{ $condition['action_required'] }}</small>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Please address these issues to avoid account locking. Contact IT staff if you need assistance.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('conditionsModal').style.display='none'">I Understand</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Flash Alert for Conditions (non-modal) -->
    @if(session('warning'))
    <div class="alert alert-warning mt-3" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        {{ session('warning') }}
    </div>
    @endif
@endsection

@section('portal-links')
    <span>For academy staff, including DOS, secretaries, and accountants.</span>
@endsection
