@extends('layouts.client')

@section('title', 'Purchase Subscription - ESA Portal')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;1,9..40,300&display=swap');

    :root {
        --sidebar-w: 260px;
        --bg: #0f1117;
        --surface: #181c27;
        --surface-2: #1e2436;
        --border: rgba(255,255,255,0.07);
        --accent: #4f8ef7;
        --accent-2: #a78bfa;
        --accent-3: #34d399;
        --accent-warm: #fbbf24;
        --text-primary: #f0f2f8;
        --text-muted: #6b7a99;
        --text-dim: #3d4663;
    }

    body {
        font-family: 'DM Sans', sans-serif;
        background: var(--bg);
        color: var(--text-primary);
        min-height: 100vh;
    }

    /* Page content */
    .page-content {
        padding: 32px;
        max-width: 900px;
        margin: 0 auto;
    }

    /* Purchase Form */
    .purchase-container {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 32px;
    }

    .purchase-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 32px;
        position: relative;
        overflow: hidden;
    }

    .purchase-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .plan-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 24px;
    }

    .plan-info h3 {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .plan-info p {
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        line-height: 1.5;
    }

    .plan-price {
        text-align: right;
    font-family: 'Syne', sans-serif;
    font-size: 32px;
        font-weight: 800;
        color: #fff;
        line-height: 1;
    }

    .plan-price .currency {
        font-size: 18px;
        opacity: 0.8;
    }

    .purchase-body {
        padding: 32px;
    }

    /* Grid Layout */
    .details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 768px) {
        .details-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Details Card */
    .details-card {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
    }

    .details-title {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
    }

    /* Feature List */
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .feature-item:last-child {
        border-bottom: none;
    }

    .feature-icon {
        width: 20px;
        height: 20px;
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }

    .feature-text {
        flex: 1;
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-primary);
    }

    .feature-text strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Payment Info */
    .payment-info {
        background: rgba(251,191,36,0.1);
        border: 1px solid rgba(251,191,36,0.2);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .payment-info-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .payment-info-content {
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-primary);
    }

    /* Form Elements */
    .form-group {
        margin-bottom: 24px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .form-select {
        width: 100%;
        padding: 12px 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px;
        color: var(--text-primary);
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(79,142,247,0.1);
    }

    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px;
    }

    .checkbox-input {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border);
        border-radius: 4px;
        background: var(--surface);
        margin-top: 2px;
        flex-shrink: 0;
    }

    .checkbox-input:checked {
        background: var(--accent);
        border-color: var(--accent);
    }

    .checkbox-input:checked::after {
        content: '✓';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 12px;
        font-weight: bold;
    }

    .checkbox-label {
        flex: 1;
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-primary);
    }

    .checkbox-label a {
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
    }

    .checkbox-label a:hover {
        text-decoration: underline;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 16px;
        margin-top: 32px;
    }

    .btn-purchase {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 24px;
        background: linear-gradient(135deg, var(--accent-3), var(--accent));
        color: #fff;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 12px rgba(52,211,153,0.3);
    }

    .btn-purchase:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(52,211,153,0.4);
    }

    .btn-cancel {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 20px;
        background: var(--surface-2);
        color: var(--text-muted);
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-cancel:hover {
        background: var(--surface);
        border-color: rgba(79,142,247,0.3);
        color: var(--text-primary);
    }

    /* Error State */
    .error-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .error-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
        display: block;
    }

    .error-state h3 {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .error-state p {
        font-size: 14px;
        margin-bottom: 24px;
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Purchase Subscription</h1>
        <p>Complete your subscription purchase</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.subscriptions.index') }}" class="btn-cancel">
            <i class="fas fa-arrow-left"></i>
            Back to Plans
        </a>
    </div>
</div>

<!-- Purchase Form -->
@if($subscription)
    <div class="purchase-container">
        <!-- Plan Summary -->
        <div class="purchase-header">
            <div class="plan-summary">
                <div class="plan-info">
                    <h3>{{ $subscription->name }}</h3>
                    <p>{{ $subscription->description }}</p>
                </div>
                <div class="plan-price">
                    {{ number_format($subscription->price, 2) }}
                    <span class="currency">RWF</span>
                </div>
            </div>
        </div>
        
        <div class="purchase-body">
            <!-- Details Grid -->
            <div class="details-grid">
                <!-- Subscription Details -->
                <div class="details-card">
                    <h4 class="details-title">Subscription Details</h4>
                    <ul class="features-list">
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Duration:</strong> {{ $subscription->duration_days ?? 'Unlimited' }} days
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Max Exams:</strong> {{ $subscription->max_exams ?? 'Unlimited' }}
                            </div>
                        </li>
                        @if($subscription->tokens_included)
                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-coins"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Tokens Included:</strong> {{ $subscription->formatted_tokens }}
                                </div>
                            </li>
                        @endif
                    </ul>
                </div>
                
                <!-- What You Get -->
                <div class="details-card">
                    <h4 class="details-title">What You Get</h4>
                    <ul class="features-list">
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Unlimited access to all practice exams</strong>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Progress tracking and analytics</strong>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-download"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Certificate downloads</strong>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Priority customer support</strong>
                            </div>
                        </li>
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Mobile app access</strong>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Payment Information -->
            <div class="payment-info">
                <div class="payment-info-title">
                    <i class="fas fa-info-circle"></i>
                    Payment Information
                </div>
                <div class="payment-info-content">
                    You will be redirected to Flutterwave secure payment gateway to complete your purchase.
                    Your subscription will be activated immediately after successful payment.
                </div>
            </div>
            
            <!-- Purchase Form -->
            <form action="{{ route('client.subscriptions.process', $subscription->id) }}" method="POST">
                @csrf
                
                <!-- Payment Method -->
                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="flutterwave">Flutterwave (Secure Payment)</option>
                    </select>
                </div>
                
                <!-- Terms and Conditions -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" name="terms" required class="checkbox-input">
                        <div class="checkbox-label">
                            I agree to the <a href="#" class="text-blue-600">Terms and Conditions</a> 
                            and <a href="#" class="text-blue-600">Privacy Policy</a>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="action-buttons">
                    <a href="{{ route('client.subscriptions.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i>
                        Cancel
                    </a>
                    <button type="submit" class="btn-purchase">
                        <i class="fas fa-lock"></i>
                        Proceed to Secure Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
@else
    <div class="error-state">
        <i class="fas fa-exclamation-triangle"></i>
        <h3>Subscription Not Found</h3>
        <p>The subscription you're looking for is not available.</p>
        <a href="{{ route('client.subscriptions.index') }}" class="btn-purchase">
            <i class="fas fa-arrow-left"></i>
            View All Plans
        </a>
    </div>
@endif
@endsection
