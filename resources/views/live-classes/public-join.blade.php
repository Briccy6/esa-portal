@extends('layouts.app')

@section('title', 'Join Live Class - {{ $liveClass->title }}')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --primary-color: #dc2626;
        --primary-dark: #b91c1c;
        --background: #0f172a;
        --surface: #1e293b;
        --surface-light: #334155;
        --text-primary: #f8fafc;
        --text-secondary: #cbd5e1;
        --text-muted: #64748b;
        --border-color: #334155;
        --success: #10b981;
        --warning: #f59e0b;
        --error: #ef4444;
        --gradient: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        background: var(--background);
        color: var(--text-primary);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .join-container {
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 1rem;
        padding: 3rem;
        max-width: 600px;
        width: 100%;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        backdrop-filter: blur(10px);
        background: rgba(30, 41, 59, 0.95);
    }

    .logo {
        text-align: center;
        margin-bottom: 2rem;
    }

    .logo i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 1rem;
    }

    .logo h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .logo p {
        color: var(--text-secondary);
        margin-top: 0.5rem;
    }

    .class-info {
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .class-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .class-meta {
        color: var(--text-secondary);
        font-size: 0.875rem;
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .form-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-title i {
        color: var(--primary-color);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .form-input {
        width: 100%;
        background: var(--surface);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 0.75rem 1rem;
        color: var(--text-primary);
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        width: 100%;
    }

    .btn-primary {
        background: var(--primary-color);
        color: white;
    }

    .btn-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .btn-secondary {
        background: var(--surface-light);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--border-color);
        transform: translateY(-1px);
    }

    .btn-success {
        background: var(--success);
        color: white;
    }

    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .divider {
        text-align: center;
        margin: 2rem 0;
        position: relative;
    }

    .divider::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 0;
        right: 0;
        height: 1px;
        background: var(--border-color);
    }

    .divider span {
        background: var(--surface);
        padding: 0 1rem;
        color: var(--text-muted);
        font-size: 0.875rem;
        position: relative;
        z-index: 1;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .alert-success {
        background: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.3);
        color: var(--success);
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        color: var(--error);
    }

    .alert-info {
        background: rgba(220, 38, 38, 0.1);
        border: 1px solid rgba(220, 38, 38, 0.3);
        color: var(--primary-color);
    }

    .alert-warning {
        background: rgba(245, 158, 11, 0.1);
        border: 1px solid rgba(245, 158, 11, 0.3);
        color: var(--warning);
    }

    .loading {
        display: none;
        text-align: center;
        padding: 1rem;
    }

    .loading.active {
        display: block;
    }

    .spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 2px solid var(--border-color);
        border-top: 2px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .payment-info {
        background: var(--surface-light);
        border: 1px solid var(--border-color);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: var(--text-secondary);
    }

    .payment-info strong {
        color: var(--text-primary);
    }

    @media (max-width: 640px) {
        body {
            padding: 1rem;
        }
        
        .join-container {
            padding: 2rem;
        }
        
        .logo i {
            font-size: 2.5rem;
        }
        
        .logo h1 {
            font-size: 1.25rem;
        }
    }
</style>
@endsection

@section('content')
<div class="join-container">
    <div class="logo">
        <i class="fas fa-graduation-cap"></i>
        <h1>ESA Portal</h1>
        <p>Excellent Servant Academy</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Success!</strong>
                <p>{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Error!</strong>
                <p>{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <div class="class-info">
        <h2 class="class-title">{{ $liveClass->title }}</h2>
        <div class="class-meta">
            <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($liveClass->start_time)->format('M j, Y') }}</span>
            <span><i class="fas fa-clock"></i> {{ \Carbon\Carbon::parse($liveClass->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($liveClass->end_time)->format('H:i') }}</span>
            @if($liveClass->courses->count() > 0)
                <span><i class="fas fa-book"></i> {{ $liveClass->courses->pluck('name')->join(', ') }}</span>
            @endif
            @if($liveClass->teacher)
                <span><i class="fas fa-user-tie"></i> {{ $liveClass->teacher->full_name }}</span>
            @endif
        </div>
        
        @if($liveClass->public_fee > 0)
            <div class="payment-info">
                <strong><i class="fas fa-money-bill"></i> Payment Required:</strong> 
                {{ strtoupper($liveClass->currency) }} {{ number_format($liveClass->public_fee, 2) }}
                <br><small>If you've already paid, enter your email below to verify and join.</small>
            </div>
        @endif
    </div>

    @if($liveClass->status !== 'active')
        <div class="alert alert-warning">
            <i class="fas fa-clock"></i>
            <div>
                <strong>Class Not Active</strong>
                <p>This class is not currently active. Please check back when the class is scheduled to start.</p>
            </div>
        </div>
        <div style="text-align: center;">
            <a href="{{ route('welcome') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('live-classes.public-join', $liveClass) }}" id="joinForm">
            @csrf
            
            <div class="form-section">
                <h3 class="form-title">
                    <i class="fas fa-sign-in-alt"></i>
                    Enter Your Details to Join
                </h3>
                
                @if($liveClass->device_restriction)
                    <div class="alert alert-warning">
                        <i class="fas fa-mobile-alt"></i>
                        <div>
                            <strong>Device Restriction</strong>
                            <p>This class allows only one device per email address. If you're already logged in on another device, please close that session first.</p>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="email" class="form-label">Email Address *</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        class="form-input" 
                        placeholder="Enter your email address"
                        required
                        value="{{ old('email', session('public_user_email', session('email'))) }}"
                    >
                    @error('email')
                        <div class="alert alert-error" style="margin-top: 0.5rem; padding: 0.75rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                    <small style="color: var(--text-muted); margin-top: 0.25rem; display: block;">
                        This email will be used to verify your payment and access
                    </small>
                </div>

                @if($liveClass->public_fee > 0)
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Payment Verification</strong>
                            <p>Enter your email to check if you've already paid for this class. If your payment is found, you'll join immediately. If not, you'll be redirected to payment.</p>
                        </div>
                    </div>
                @endif

                <div class="form-group">
                    <label for="name" class="form-label">Full Name *</label>
                    <input 
                        type="text" 
                        id="name" 
                        name="name" 
                        class="form-input" 
                        placeholder="Enter your full name"
                        required
                        value="{{ old('name', session('name')) }}"
                    >
                    @error('name')
                        <div class="alert alert-error" style="margin-top: 0.5rem; padding: 0.75rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="phone" class="form-label">Phone Number (Optional)</label>
                    <input 
                        type="tel" 
                        id="phone" 
                        name="phone" 
                        class="form-input" 
                        placeholder="+250 788 123 456"
                        value="{{ old('phone', session('phone')) }}"
                    >
                    <small style="color: var(--text-muted); margin-top: 0.25rem; display: block;">
                        Used for payment verification if needed
                    </small>
                </div>

                <button type="submit" class="btn btn-primary" id="joinBtn">
                    <i class="fas fa-arrow-right"></i>
                    <span>Continue to Class</span>
                </button>
            </div>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 1rem; color: var(--text-secondary);">Verifying your access...</p>
        </div>

        @if($liveClass->device_restriction)
            <div class="divider">
                <span>SECURITY FEATURES</span>
            </div>

            <button type="button" class="btn btn-secondary" onclick="checkDeviceUsage()" style="background: var(--surface-light); border: 1px solid var(--border-color);">
                <i class="fas fa-shield-alt"></i>
                <span>Check Device Usage</span>
            </button>
            <small style="color: var(--text-muted); margin-top: 0.5rem; display: block; text-align: center;">
                Click to check if your email is being used on another device
            </small>
        @endif

        @if($liveClass->public_fee > 0)
            <div class="divider">
                <span>OR</span>
            </div>

            <a href="{{ route('live-classes.payment', $liveClass) }}" class="btn btn-secondary">
                <i class="fas fa-credit-card"></i>
                <span>Make a New Payment</span>
            </a>
        @endif

        <div style="text-align: center; margin-top: 2rem;">
            <a href="{{ route('welcome') }}" class="btn btn-secondary" style="background: transparent; border-color: var(--border-color); color: var(--text-muted);">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Home</span>
            </a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('joinForm');
    const submitBtn = document.getElementById('joinBtn');
    const loading = document.getElementById('loading');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('email').value.trim();
        const name = document.getElementById('name').value.trim();
        
        if (!email) {
            showError('Please enter your email address');
            return;
        }
        
        if (!name) {
            showError('Please enter your full name');
            return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.style.display = 'none';
        loading.classList.add('active');
        
        // Submit form
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'text/html',
            }
        })
        .then(response => {
            if (response.redirected) {
                window.location.href = response.url;
            } else {
                return response.text();
            }
        })
        .then(html => {
            if (html) {
                // If we got HTML back, check for errors
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const errorAlert = doc.querySelector('.alert-error');
                
                if (errorAlert) {
                    showError(errorAlert.textContent.trim());
                } else {
                    // Redirect to room if no errors
                    window.location.href = '{{ route("live-classes.room", $liveClass) }}?email=' + encodeURIComponent(email);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Network error. Please try again.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.style.display = 'flex';
            loading.classList.remove('active');
        });
    });
    
    function showError(message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new error alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-error';
        alertDiv.innerHTML = `
            <i class="fas fa-exclamation-circle"></i>
            <div>
                <strong>Error!</strong>
                <p>${message}</p>
            </div>
        `;
        
        // Insert after class info
        const classInfo = document.querySelector('.class-info');
        classInfo.parentNode.insertBefore(alertDiv, classInfo.nextSibling);
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    
    // Auto-focus email field
    document.getElementById('email').focus();
    
    // Device usage check function
    window.checkDeviceUsage = function() {
        const email = document.getElementById('email').value.trim();
        if (!email) {
            showError('Please enter your email address first');
            return;
        }
        
        // Show loading state
        const checkBtn = event.target;
        const originalText = checkBtn.innerHTML;
        checkBtn.disabled = true;
        checkBtn.innerHTML = '<div class="spinner"></div> <span>Checking...</span>';
        
        // Check device usage via API
        fetch(`{{ route('api.live-classes.check-access', $liveClass) }}?email=${encodeURIComponent(email)}`)
            .then(response => response.json())
            .then(data => {
                if (data.canJoin) {
                    showSuccess('This email is not currently in use on another device. You can proceed to join.');
                } else {
                    if (data.reason && data.reason.includes('device')) {
                        showError('This email is currently being used on another device. Please close the other session first.');
                    } else {
                        showError(data.reason || 'Unable to check device usage. Please try again.');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Network error. Please try again.');
            })
            .finally(() => {
                checkBtn.disabled = false;
                checkBtn.innerHTML = originalText;
            });
    };
    
    function showSuccess(message) {
        // Remove existing alerts
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
        
        // Create new success alert
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success';
        alertDiv.innerHTML = `
            <i class="fas fa-check-circle"></i>
            <div>
                <strong>Success!</strong>
                <p>${message}</p>
            </div>
        `;
        
        // Insert after class info
        const classInfo = document.querySelector('.class-info');
        classInfo.parentNode.insertBefore(alertDiv, classInfo.nextSibling);
        
        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
});
</script>
@endpush
