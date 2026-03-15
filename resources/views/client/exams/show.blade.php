@extends('layouts.client')

@section('title', $exam->title . ' - ESA Portal')

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

    /* Exam Details Grid */
    .exam-details-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
        margin-bottom: 28px;
    }

    @media (max-width: 900px) {
        .exam-details-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Exam Card */
    .exam-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .exam-card:hover {
        transform: translateY(-2px);
        border-color: rgba(79,142,247,0.3);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }

    .exam-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    .exam-header::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .exam-title {
        font-family: 'Syne', sans-serif;
        font-size: 22px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 8px;
        line-height: 1.3;
    }

    .exam-description {
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        line-height: 1.5;
    }

    .exam-body {
        padding: 24px;
    }

    /* Info Grid */
    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 24px;
    }

    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 16px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px;
    }

    .info-label {
        font-size: 13px;
        color: var(--text-muted);
        font-weight: 500;
    }

    .info-value {
        font-size: 13px;
        color: var(--text-primary);
        font-weight: 600;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .badge-blue {
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        border: 1px solid rgba(79,142,247,0.2);
    }

    .badge-green {
        background: rgba(52,211,153,0.15);
        color: var(--accent-3);
        border: 1px solid rgba(52,211,153,0.2);
    }

    .badge-yellow {
        background: rgba(251,191,36,0.15);
        color: var(--accent-warm);
        border: 1px solid rgba(251,191,36,0.2);
    }

    /* Instructions Card */
    .instructions-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
    }

    .instructions-title {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
    }

    .instructions-content {
        font-size: 14px;
        line-height: 1.6;
        color: var(--text-muted);
    }

    .instructions-content p {
        margin-bottom: 12px;
    }

    .instructions-content p:last-child {
        margin-bottom: 0;
    }

    /* Access Requirements */
    .access-requirements {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
    }

    .access-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
    }

    .access-item {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .access-item:last-child {
        margin-bottom: 0;
    }

    .access-item i {
        width: 18px;
        text-align: center;
        font-size: 14px;
    }

    .price-tag {
        color: var(--accent-warm);
        font-weight: 600;
    }

    .free-tag {
        color: var(--accent-3);
        font-weight: 600;
    }

    .subscription-tag {
        color: var(--accent-2);
        font-weight: 600;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .btn-exam {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 16px 24px;
        border-radius: 12px;
        font-size: 15px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        width: 100%;
    }

    .btn-start {
        background: linear-gradient(135deg, var(--accent-3), var(--accent));
        color: #fff;
        box-shadow: 0 8px 16px rgba(52,211,153,0.3);
    }

    .btn-start:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 24px rgba(52,211,153,0.4);
    }

    .btn-tokens {
        background: var(--accent-warm);
        color: #fff;
    }

    .btn-tokens:hover {
        background: #f59e0b;
        transform: translateY(-2px);
    }

    .btn-subscription {
        background: var(--accent-2);
        color: #fff;
    }

    .btn-subscription:hover {
        background: #9333ea;
        transform: translateY(-2px);
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--surface-2);
        color: var(--text-primary);
        border: 1px solid var(--border);
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-back:hover {
        background: var(--surface);
        border-color: rgba(79,142,247,0.3);
        color: var(--accent);
    }

    /* Restricted Access */
    .restricted-access {
        text-align: center;
        padding: 20px;
        background: rgba(251,191,36,0.1);
        border: 1px solid rgba(251,191,36,0.2);
        border-radius: 12px;
        margin-bottom: 20px;
    }

    .restricted-text {
        font-size: 14px;
        color: var(--text-muted);
        margin-bottom: 16px;
    }

    .restricted-actions {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    @media (max-width: 640px) {
        .restricted-actions {
            flex-direction: column;
        }
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>{{ $exam->title }}</h1>
        <p>Exam details and instructions</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.exams.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Exams
        </a>
    </div>
</div>

<!-- Exam Details Grid -->
<div class="exam-details-grid">
    <!-- Left Column -->
    <div>
        <!-- Exam Header Card -->
        <div class="exam-card">
            <div class="exam-header">
                <h2 class="exam-title">{{ $exam->title }}</h2>
                <p class="exam-description">{{ $exam->description }}</p>
            </div>
            
            <div class="exam-body">
                <!-- Info Grid -->
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Subject</span>
                        <span class="info-badge badge-blue">
                            <i class="fas fa-book"></i>
                            {{ $exam->subject ?? 'General' }}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Duration</span>
                        <span class="info-badge badge-green">
                            <i class="fas fa-clock"></i>
                            {{ $exam->formatted_duration ?? '60 min' }}
                        </span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Questions</span>
                        <span class="info-value">{{ $exam->questions_count ?? 0 }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Total Marks</span>
                        <span class="info-value">{{ $exam->total_marks ?? 0 }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Passing Marks</span>
                        <span class="info-value">{{ $exam->passing_marks ?? 'N/A' }}</span>
                    </div>
                    
                    <div class="info-item">
                        <span class="info-label">Difficulty</span>
                        <span class="info-badge badge-yellow">
                            <i class="fas fa-signal"></i>
                            {{ $exam->difficulty ?? 'Medium' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column -->
    <div>
        <!-- Instructions Card -->
        <div class="instructions-card">
            <h3 class="instructions-title">Exam Instructions</h3>
            <div class="instructions-content">
                {!! $exam->instructions ?? 'Read each question carefully before answering. Take your time and think through each option before selecting your answer.' !!}
            </div>
        </div>
        
        <!-- Access Requirements -->
        <div class="access-requirements">
            <h3 class="access-title">Access Requirements</h3>
            
            @if($exam->public_price > 0)
                <div class="access-item">
                    <i class="fas fa-coins" style="color: var(--accent-warm);"></i>
                    <span class="price-tag">{{ $exam->public_price }} RWF</span>
                    <span style="color: var(--text-muted); margin: 0 8px;">or</span>
                    <span class="subscription-tag">Active Subscription</span>
                </div>
            @else
                <div class="access-item">
                    <i class="fas fa-check-circle" style="color: var(--accent-3);"></i>
                    <span class="free-tag">Free with Active Subscription</span>
                </div>
            @endif
            
            <div class="access-item">
                <i class="fas fa-coins" style="color: var(--accent);"></i>
                <span>{{ $exam->token_cost ?? 0 }} tokens required</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        @if(auth()->guard('client')->user()->canAccessExam($exam))
            <div class="action-buttons">
                <a href="{{ route('client.exams.take', $exam->id) }}" class="btn-exam btn-start">
                    <i class="fas fa-play"></i>
                    Start Exam
                </a>
            </div>
        @else
            <div class="restricted-access">
                <div class="restricted-text">
                    You don't have sufficient access to take this exam.
                </div>
                <div class="restricted-actions">
                    <a href="{{ route('client.tokens.purchase') }}" class="btn-exam btn-tokens">
                        <i class="fas fa-coins"></i>
                        Buy Tokens
                    </a>
                    <a href="{{ route('client.subscriptions.index') }}" class="btn-exam btn-subscription">
                        <i class="fas fa-crown"></i>
                        Get Subscription
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
