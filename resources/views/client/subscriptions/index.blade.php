@extends('layouts.client')

@section('title', 'Subscriptions - ESA Portal')

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
    }

    /* Subscriptions Grid */
    .subscriptions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 768px) {
        .subscriptions-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Subscription Card */
    .subscription-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .subscription-card:hover {
        transform: translateY(-4px);
        border-color: rgba(79,142,247,0.3);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }

    .subscription-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    .subscription-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 100px; height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .subscription-badge {
        position: absolute;
        top: 16px; right: 16px;
        background: var(--accent-warm);
        color: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .subscription-title {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .subscription-price {
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 4px;
    }

    .subscription-price .currency {
        font-size: 18px;
        opacity: 0.8;
    }

    .subscription-price .period {
        font-size: 14px;
        opacity: 0.7;
    }

    .subscription-body {
        padding: 24px;
    }

    /* Features List */
    .features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
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

    /* Action Button */
    .subscription-action {
        padding: 20px 24px;
        border-top: 1px solid var(--border);
    }

    .btn-subscribe {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 16px 24px;
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: #fff;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        box-shadow: 0 4px 12px rgba(79,142,247,0.3);
    }

    .btn-subscribe:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(79,142,247,0.4);
    }

    /* Current Subscription */
    .current-subscription {
        background: rgba(52,211,153,0.1);
        border: 1px solid rgba(52,211,153,0.2);
    }

    .current-subscription .subscription-header {
        background: linear-gradient(135deg, var(--accent-3), var(--accent));
    }

    .current-subscription .subscription-badge {
        background: var(--accent-3);
        color: #fff;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-muted);
    }

    .empty-state i {
        font-size: 48px;
        margin-bottom: 16px;
        opacity: 0.4;
        display: block;
    }

    .empty-state h3 {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 14px;
        margin-bottom: 24px;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        background: var(--accent);
        color: #fff;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background: #3b82f6;
        transform: translateY(-2px);
    }

    /* Active Subscription Card */
    .active-subscription-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        margin-bottom: 32px;
        position: relative;
    }

    .active-subscription-header {
        background: linear-gradient(135deg, var(--accent-3), var(--accent));
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    .active-subscription-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 100px; height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .active-subscription-body {
        padding: 24px;
    }

    .usage-stats {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .usage-item {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        text-align: center;
    }

    .usage-value {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 4px;
    }

    .usage-label {
        font-size: 12px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Subscriptions</h1>
        <p>Choose the perfect plan for your learning journey</p>
    </div>
</div>

<!-- Current Subscription -->
@if(auth()->guard('client')->user()->hasActiveSubscription())
    @php $activeSubscription = auth()->guard('client')->user()->activeSubscription; @endphp
    <div class="active-subscription-card">
        <div class="active-subscription-header">
            <div class="subscription-badge">Active</div>
            <h3 class="subscription-title">{{ $activeSubscription->subscription->name }}</h3>
            <div class="subscription-price">
                {{ number_format($activeSubscription->subscription->price, 2) }}
                <span class="currency">RWF</span>
                <span class="period">/{{ $activeSubscription->subscription->billing_period }}</span>
            </div>
        </div>
        
        <div class="active-subscription-body">
            <!-- Usage Statistics -->
            <div class="usage-stats">
                <div class="usage-item">
                    <div class="usage-value">{{ $activeSubscription->exams_taken ?? 0 }}</div>
                    <div class="usage-label">Exams Taken</div>
                </div>
                <div class="usage-item">
                    <div class="usage-value">{{ $activeSubscription->created_at->diffInDays(now()) }}</div>
                    <div class="usage-label">Days Active</div>
                </div>
            </div>
            
            <!-- Features List -->
            <ul class="features-list">
                <li class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Valid Until:</strong> {{ $activeSubscription->ends_at->format('M j, Y') }}
                    </div>
                </li>
                <li class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Tokens Included:</strong> {{ $activeSubscription->subscription->formatted_tokens ?? 'Unlimited' }}
                    </div>
                </li>
                @if($activeSubscription->subscription->features)
                    @foreach(json_decode($activeSubscription->subscription->features, true) as $feature)
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div class="feature-text">{{ $feature }}</div>
                        </li>
                    @endforeach
                @endif
                <li class="feature-item">
                    <div class="feature-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="feature-text">
                        <strong>Access to all exams</strong>
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
                        <strong>Priority support</strong>
                    </div>
                </li>
            </ul>
        </div>
    </div>
@endif

<!-- Available Subscriptions -->
<div class="subscriptions-grid">
    @foreach($subscriptions as $subscription)
        <div class="subscription-card {{ (auth()->guard('client')->user()->hasActiveSubscription() && auth()->guard('client')->user()->activeSubscription->subscription_id === $subscription->id) ? 'current-subscription' : '' }}">
            <div class="subscription-header">
                @if(auth()->guard('client')->user()->hasActiveSubscription() && 
                   auth()->guard('client')->user()->activeSubscription->subscription_id === $subscription->id)
                    <div class="subscription-badge">Current</div>
                @endif
                
                <h3 class="subscription-title">{{ $subscription->name }}</h3>
                <div class="subscription-price">
                    {{ number_format($subscription->price, 2) }}
                    <span class="currency">RWF</span>
                    <span class="period">/{{ $subscription->billing_period }}</span>
                </div>
            </div>
            
            <div class="subscription-body">
                <ul class="features-list">
                    @if($subscription->tokens_included)
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-coins"></i>
                            </div>
                            <div class="feature-text">
                                <strong>{{ $subscription->formatted_tokens }} tokens</strong> included
                            </div>
                        </li>
                    @endif
                    
                    @if($subscription->features)
                        @foreach($subscription->features as $feature)
                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="feature-text">{{ $feature }}</div>
                            </li>
                        @endforeach
                    @endif
                    
                    @if($subscription->exam_access)
                        <li class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-graduation-cap"></i>
                            </div>
                            <div class="feature-text">
                                <strong>Access to all exams</strong>
                            </div>
                        </li>
                    @endif
                    
                    <li class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="feature-text">
                            <strong>{{ $subscription->duration_days ?? 'Unlimited' }} days</strong> access
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
                    </ul>
            </div>
            
            <div class="subscription-action">
                @if(auth()->guard('client')->user()->hasActiveSubscription() && 
                   auth()->guard('client')->user()->activeSubscription->subscription_id === $subscription->id)
                    <button class="btn-subscribe" disabled>
                        <i class="fas fa-check"></i>
                        Current Plan
                    </button>
                @else
                    <a href="{{ route('client.subscriptions.purchase', $subscription->id) }}" class="btn-subscribe">
                        <i class="fas fa-crown"></i>
                        {{ auth()->guard('client')->user()->hasActiveSubscription() ? 'Upgrade' : 'Subscribe' }}
                    </a>
                @endif
            </div>
        </div>
    @endforeach
</div>

@empty($subscriptions)
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Subscriptions Available</h3>
        <p>There are currently no subscription plans available. Please check back later.</p>
        <a href="{{ route('client.dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
@endempty
@endsection
