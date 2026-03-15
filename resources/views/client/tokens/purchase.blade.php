@extends('layouts.client')

@section('title', 'Buy Tokens - ESA Portal')

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
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Token Packages Grid */
    .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    @media (max-width: 900px) {
        .packages-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .packages-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Token Package Card */
    .package-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .package-card:hover {
        transform: translateY(-4px);
        border-color: rgba(79,142,247,0.3);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }

    .package-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    .package-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 100px; height: 100px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .package-badge {
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

    .package-title {
        font-family: 'Syne', sans-serif;
        font-size: 20px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .package-tokens {
        font-size: 28px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 4px;
        display: flex;
        align-items: baseline;
        gap: 8px;
    }

    .package-tokens .number {
        font-size: 36px;
    }

    .package-tokens .label {
        font-size: 14px;
        opacity: 0.8;
    }

    .package-bonus {
        font-size: 14px;
        color: rgba(255,255,255,0.9);
        margin-bottom: 8px;
    }

    .package-price {
        font-size: 20px;
        font-weight: 700;
        color: #fff;
    }

    .package-body {
        padding: 24px;
    }

    /* Package Details */
    .package-details {
        margin-bottom: 24px;
    }

    .detail-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .detail-item:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .detail-value {
        font-size: 14px;
        color: var(--text-primary);
        font-weight: 600;
    }

    .detail-value.highlight {
        color: var(--accent-3);
    }

    .detail-value.large {
        font-size: 16px;
    }

    /* Action Button */
    .package-action {
        margin-top: 24px;
    }

    .btn-purchase {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
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

    /* Info Section */
    .info-section {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 32px;
        margin-bottom: 32px;
    }

    .info-header {
        font-family: 'Syne', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 24px;
        text-align: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 32px;
    }

    @media (max-width: 768px) {
        .info-grid {
            grid-template-columns: 1fr;
            gap: 24px;
        }
    }

    .info-card {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 24px;
    }

    .info-title {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 12px 0;
        border-bottom: 1px solid var(--border);
    }

    .info-item:last-child {
        border-bottom: none;
    }

    .info-icon {
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

    .info-text {
        flex: 1;
        font-size: 14px;
        line-height: 1.5;
        color: var(--text-primary);
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

    /* Responsive */
    @media (max-width: 768px) {
        .page-content {
            padding: 20px;
        }
        
        .package-card {
            margin-bottom: 20px;
        }
        
        .package-header {
            padding: 20px;
        }
        
        .package-tokens {
            font-size: 24px;
        }
        
        .package-tokens .number {
            font-size: 32px;
        }
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Buy Tokens</h1>
        <p>Choose a token package that fits your needs</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.tokens.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Token History
        </a>
    </div>
</div>

<!-- Token Packages -->
@if($tokenPackages && $tokenPackages->count() > 0)
    <div class="packages-grid">
        @foreach($tokenPackages as $package)
            <div class="package-card">
                <div class="package-header">
                    @if($package->featured)
                        <div class="package-badge">Best Value</div>
                    @endif
                    
                    <h3 class="package-title">{{ $package->name }}</h3>
                    <div class="package-tokens">
                        <span class="number">{{ $package->tokens + $package->bonus_tokens }}</span>
                        <span class="label">Tokens</span>
                    </div>
                    @if($package->bonus_tokens > 0)
                        <div class="package-bonus">+{{ $package->bonus_tokens }} Bonus Tokens</div>
                    @endif
                    <div class="package-price">{{ number_format($package->price, 2) }} RWF</div>
                </div>
                
                <div class="package-body">
                    <!-- Package Details -->
                    <div class="package-details">
                        <div class="detail-item">
                            <span class="detail-label">Base Tokens</span>
                            <span class="detail-value">{{ $package->tokens }}</span>
                        </div>
                        
                        @if($package->bonus_tokens > 0)
                            <div class="detail-item">
                                <span class="detail-label">Bonus Tokens</span>
                                <span class="detail-value highlight">+{{ $package->bonus_tokens }}</span>
                            </div>
                        @endif
                        
                        <div class="detail-item">
                            <span class="detail-label">Total Tokens</span>
                            <span class="detail-value large">{{ $package->tokens + $package->bonus_tokens }}</span>
                        </div>
                        
                        <div class="detail-item">
                            <span class="detail-label">Cost per Token</span>
                            <span class="detail-value">{{ number_format($package->price / ($package->tokens + $package->bonus_tokens), 2) }} RWF</span>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="package-action">
                        <form action="{{ route('client.tokens.process') }}" method="POST">
                            @csrf
                            <input type="hidden" name="package_id" value="{{ $package->id }}">
                            <button type="submit" class="btn-purchase">
                                <i class="fas fa-shopping-cart"></i>
                                Buy Package
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-coins"></i>
        <h3>No Token Packages Available</h3>
        <p>There are currently no token packages available. Please check back later.</p>
        <a href="{{ route('client.dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
@endif

<!-- Information Section -->
<div class="info-section">
    <div class="info-header">How Tokens Work</div>
    
    <div class="info-grid">
        <div class="info-card">
            <h4 class="info-title">Token Usage</h4>
            <ul class="info-list">
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="info-text">
                        <strong>Access Practice Exams</strong> - Use tokens to take any practice exam
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="info-text">
                        <strong>Flexible Pricing</strong> - Pay only for the exams you take
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-text">
                        <strong>No Expiration</strong> - Your tokens never expire
                    </div>
                </li>
            </ul>
        </div>
        
        <div class="info-card">
            <h4 class="info-title">Token Benefits</h4>
            <ul class="info-list">
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="info-text">
                        <strong>Cost Effective</strong> - Better value than individual exam purchases
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div class="info-text">
                        <strong>Bonus Tokens</strong> - Get extra tokens on larger packages
                    </div>
                </li>
                <li class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="info-text">
                        <strong>Secure Payments</strong> - Safe and secure payment processing
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection
