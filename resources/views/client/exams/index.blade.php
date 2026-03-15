@extends('layouts.client')

@section('title', 'Practice Exams - ESA Portal')

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

    /* Exams Grid */
    .exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }

    @media (max-width: 768px) {
        .exams-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }
    }

    .exam-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        position: relative;
    }

    .exam-card:hover {
        transform: translateY(-4px);
        border-color: rgba(79,142,247,0.3);
        box-shadow: 0 12px 24px rgba(0,0,0,0.2);
    }

    .exam-header {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        padding: 20px;
        position: relative;
        overflow: hidden;
    }

    .exam-header::before {
        content: '';
        position: absolute;
        top: -20px; right: -20px;
        width: 80px; height: 80px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .exam-title {
        font-family: 'Syne', sans-serif;
        font-size: 18px;
        font-weight: 700;
        color: #fff;
        margin-bottom: 6px;
        line-height: 1.3;
    }

    .exam-description {
        color: rgba(255,255,255,0.9);
        font-size: 13px;
        line-height: 1.5;
    }

    .exam-body {
        padding: 20px;
    }

    .exam-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .exam-badge {
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

    .badge-subject {
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        border: 1px solid rgba(79,142,247,0.2);
    }

    .badge-duration {
        background: rgba(52,211,153,0.15);
        color: var(--accent-3);
        border: 1px solid rgba(52,211,153,0.2);
    }

    .badge-difficulty {
        background: rgba(251,191,36,0.15);
        color: var(--accent-warm);
        border: 1px solid rgba(251,191,36,0.2);
    }

    .exam-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 12px 8px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 10px;
    }

    .stat-value {
        font-family: 'Syne', sans-serif;
        font-size: 16px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .stat-label {
        font-size: 10px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .access-requirements {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }

    .access-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 12px;
    }

    .access-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
        font-size: 13px;
    }

    .access-item:last-child {
        margin-bottom: 0;
    }

    .access-item i {
        width: 16px;
        text-align: center;
        font-size: 12px;
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

    .exam-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-exam {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 20px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        width: 100%;
    }

    .btn-start {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: #fff;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(79,142,247,0.3);
    }

    .btn-secondary {
        background: var(--surface-2);
        color: var(--text-primary);
        border: 1px solid var(--border);
    }

    .btn-secondary:hover {
        background: var(--surface);
        border-color: rgba(79,142,247,0.3);
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

    /* Empty state */
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

    /* Pagination */
    .pagination {
        margin-top: 32px;
        display: flex;
        justify-content: center;
    }

    .pagination ::v-deep .pagination {
        display: flex;
        gap: 8px;
    }

    .pagination ::v-deep .page-item {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border: 1px solid var(--border);
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: var(--text-muted);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination ::v-deep .page-item:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: rgba(79,142,247,0.1);
    }

    .pagination ::v-deep .page-item.active {
        background: var(--accent);
        border-color: var(--accent);
        color: #fff;
    }
</style>

<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Practice Exams</h1>
        <p>Available exams for practice and improvement</p>
    </div>
</div>

<!-- Exams Grid -->
@if($exams->count() > 0)
    <div class="exams-grid">
        @foreach($exams as $exam)
            <div class="exam-card">
                <!-- Exam Header -->
                <div class="exam-header">
                    <h3 class="exam-title">{{ $exam->title }}</h3>
                    <p class="exam-description">{{ Str::limit($exam->description, 80) }}</p>
                </div>
                
                <!-- Exam Body -->
                <div class="exam-body">
                    <!-- Meta Badges -->
                    <div class="exam-meta">
                        @if($exam->subject)
                            <span class="exam-badge badge-subject">
                                <i class="fas fa-book"></i>
                                {{ $exam->subject }}
                            </span>
                        @endif
                        
                        <span class="exam-badge badge-duration">
                            <i class="fas fa-clock"></i>
                            {{ $exam->formatted_duration ?? '60 min' }}
                        </span>
                        
                        <span class="exam-badge badge-difficulty">
                            <i class="fas fa-signal"></i>
                            {{ $exam->difficulty ?? 'Medium' }}
                        </span>
                    </div>
                    
                    <!-- Exam Stats -->
                    <div class="exam-stats">
                        <div class="stat-item">
                            <div class="stat-value">{{ $exam->questions_count ?? 0 }}</div>
                            <div class="stat-label">Questions</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $exam->total_marks ?? 0 }}</div>
                            <div class="stat-label">Marks</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value">{{ $exam->exam_weight ?? 100 }}%</div>
                            <div class="stat-label">Weight</div>
                        </div>
                    </div>
                    
                    <!-- Access Requirements -->
                    <div class="access-requirements">
                        <div class="access-title">Access Requirements</div>
                        
                        @if($exam->public_price > 0)
                            <div class="access-item">
                                <i class="fas fa-coins" style="color: var(--accent-warm);"></i>
                                <span class="price-tag">{{ $exam->public_price }} RWF</span>
                                <span style="color: var(--text-muted);">or</span>
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
                    <div class="exam-actions">
                        @if(auth()->guard('client')->user()->canAccessExam($exam))
                            <a href="{{ route('client.exams.take', $exam->id) }}" class="btn-exam btn-start">
                                <i class="fas fa-play"></i>
                                Start Exam
                            </a>
                        @else
                            <div class="text-center" style="color: var(--text-muted); font-size: 13px; margin-bottom: 12px;">
                                You need tokens or an active subscription to access this exam.
                            </div>
                            <a href="{{ route('client.tokens.purchase') }}" class="btn-exam btn-tokens">
                                <i class="fas fa-coins"></i>
                                Get Tokens
                            </a>
                            <a href="{{ route('client.subscriptions.index') }}" class="btn-exam btn-subscription">
                                <i class="fas fa-crown"></i>
                                Subscribe
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="pagination">
        {{ $exams->links() }}
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-inbox"></i>
        <h3>No Exams Available</h3>
        <p>There are currently no practice exams available. Please check back later.</p>
        <a href="{{ route('client.dashboard') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
@endif
@endsection
