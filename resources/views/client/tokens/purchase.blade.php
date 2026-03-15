@extends('layouts.client')

@section('title', 'Buy Tokens - ESA Portal')

@section('content')
<style>
:root {
    --bg: #f4f6fb;
    --surface: #ffffff;
    --surface-2: #f8f9fc;
    --border: #e5e7eb;
    --accent: #7c3aed;
    --accent-2: #8b5cf6;
    --accent-3: #06b6d4;
    --accent-warm: #f59e0b;
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --text-dim: #9ca3af;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Topbar */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    border-radius: 12px 12px 0 0;
    margin-bottom: 24px;
}

.topbar-left h1 {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.topbar-left p {
    font-size: 14px;
    color: var(--text-muted);
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Page Content Wrapper */
.page-content {
    padding: 0 24px 24px;
}

/* Token Packages Grid */
.packages-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 48px;
}

/* Token Package Card */
.package-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.package-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: var(--accent);
}

.package-header {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    padding: 32px 24px;
    position: relative;
    overflow: hidden;
}

.package-header::before {
    content: '';
    position: absolute;
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.package-badge {
    position: absolute;
    top: 16px;
    right: 16px;
    background: var(--accent-warm);
    color: white;
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
    color: white;
    margin-bottom: 8px;
    line-height: 1.3;
}

.package-tokens {
    display: flex;
    align-items: baseline;
    gap: 8px;
    margin-bottom: 8px;
}

.package-tokens .number {
    font-size: 36px;
    font-weight: 800;
    color: white;
    line-height: 1;
}

.package-tokens .label {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.9);
    font-weight: 500;
}

.package-price {
    font-size: 20px;
    font-weight: 700;
    color: white;
}

.package-body {
    padding: 24px;
}

.package-details {
    margin-bottom: 24px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border);
    font-size: 14px;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    color: var(--text-muted);
    font-weight: 500;
}

.detail-value {
    color: var(--text-primary);
    font-weight: 600;
}

.detail-value.highlight {
    color: var(--accent-3);
    font-weight: 700;
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
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
}

.btn-purchase:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(6, 182, 212, 0.4);
}

/* Info Section */
.info-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.info-header {
    font-family: 'Syne', sans-serif;
    font-size: 20px;
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
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-title i {
    color: var(--accent);
    font-size: 18px;
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
    padding: 8px 0;
    font-size: 14px;
    color: var(--text-primary);
    line-height: 1.5;
}

.info-item i {
    color: var(--accent);
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 2px;
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
    opacity: 0.3;
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

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-outline {
    background: transparent;
    color: var(--accent);
    border: 2px solid var(--accent);
}

.btn-outline:hover {
    background: var(--accent);
    color: white;
}

/* Responsive */
@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .topbar-right {
        width: 100%;
        justify-content: flex-start;
    }
    
    .packages-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .page-content {
        padding: 0 16px 16px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
        gap: 24px;
    }
    
    .package-header {
        padding: 24px 20px;
    }
    
    .package-tokens .number {
        font-size: 32px;
    }
}

@media (max-width: 480px) {
    .package-header {
        padding: 20px 16px;
    }
    
    .package-tokens .number {
        font-size: 28px;
    }
    
    .package-body {
        padding: 20px 16px;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Buy Tokens</h1>
        <p>Choose the perfect token package for your learning needs</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.tokens.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i>
            Token History
        </a>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <!-- Token Packages Grid -->
    @if($tokenPackages && $tokenPackages->count() > 0)
        <div class="packages-grid">
            @foreach($tokenPackages as $package)
                <form action="{{ route('client.tokens.process') }}" method="POST">
                    @csrf
                    <div class="package-card">
                        <div class="package-header">
                            @if($package->is_popular)
                                <div class="package-badge">Popular</div>
                            @endif
                            <div class="package-title">{{ $package->name }}</div>
                            <div class="package-tokens">
                                <span class="number">{{ $package->tokens }}</span>
                                <span class="label">Tokens</span>
                            </div>
                            <div class="package-price">RWF {{ number_format($package->price, 2) }}</div>
                        </div>
                        
                        <div class="package-body">
                            <div class="package-details">
                                <div class="detail-item">
                                    <span class="detail-label">Token Value</span>
                                    <span class="detail-value">RWF {{ number_format($package->token_value, 2) }} each</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Bonus Tokens</span>
                                    <span class="detail-value highlight">+{{ $package->bonus_tokens ?? 0 }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Total Value</span>
                                    <span class="detail-value">RWF {{ number_format(($package->tokens + ($package->bonus_tokens ?? 0)) * $package->token_value, 2) }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Savings</span>
                                    <span class="detail-value highlight">{{ $package->discount_percentage ?? 0 }}% off</span>
                                </div>
                            </div>
                            
                            <div class="package-action">
                                <input type="hidden" name="package_id" value="{{ $package->id }}">
                                <button type="submit" class="btn-purchase">
                                    <i class="fas fa-shopping-cart"></i>
                                    Buy Package
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-coins"></i>
            <h3>No Token Packages Available</h3>
            <p>There are currently no token packages available. Please check back later.</p>
            <a href="{{ route('client.tokens.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Back to Tokens
            </a>
        </div>
    @endif

    <!-- How Tokens Work Section -->
    <div class="info-section">
        <h2 class="info-header">How Tokens Work</h2>
        
        <div class="info-grid">
            <div class="info-card">
                <h3 class="info-title">
                    <i class="fas fa-graduation-cap"></i>
                    Practice Exams
                </h3>
                <ul class="info-list">
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Each exam attempt consumes tokens based on exam difficulty</span>
                    </li>
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>View detailed results and performance analytics</span>
                    </li>
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Retake exams to improve your scores</span>
                    </li>
                </ul>
            </div>
            
            <div class="info-card">
                <h3 class="info-title">
                    <i class="fas fa-coins"></i>
                    Token Benefits
                </h3>
                <ul class="info-list">
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Flexible pricing - pay only for what you use</span>
                    </li>
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Bonus tokens on larger packages</span>
                    </li>
                    <li class="info-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Tokens never expire</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
