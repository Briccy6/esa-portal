@extends('layouts.client')

@section('title', 'Practice Exams - ESA Portal')

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

/* Filters Section */
.filters-section {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 24px;
    margin-bottom: 32px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.filters-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.filters-title {
    font-family: 'Syne', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: var(--text-primary);
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-muted);
    margin-bottom: 4px;
}

.filter-select {
    padding: 10px 12px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    color: var(--text-primary);
    font-size: 14px;
    transition: all 0.2s ease;
}

.filter-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

/* Exams Grid */
.exams-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* Exam Card */
.exam-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
}

.exam-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    border-color: var(--accent);
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
    top: -20px;
    right: -20px;
    width: 80px;
    height: 80px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    pointer-events: none;
}

.exam-title {
    font-family: 'Syne', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: white;
    margin-bottom: 6px;
    line-height: 1.3;
}

.exam-description {
    color: rgba(255, 255, 255, 0.9);
    font-size: 13px;
    line-height: 1.5;
    margin-bottom: 0;
}

.exam-body {
    padding: 24px;
}

/* Exam Meta */
.exam-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
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
    background: rgba(124, 58, 237, 0.15);
    color: var(--accent);
    border: 1px solid rgba(124, 58, 237, 0.2);
}

.badge-duration {
    background: rgba(6, 182, 212, 0.15);
    color: var(--accent-3);
    border: 1px solid rgba(6, 182, 212, 0.2);
}

.badge-difficulty {
    background: rgba(245, 158, 11, 0.15);
    color: var(--accent-warm);
    border: 1px solid rgba(245, 158, 11, 0.2);
}

/* Exam Stats */
.exam-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 20px;
}

.stat-item {
    text-align: center;
    padding: 16px 8px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 12px;
}

.stat-value {
    font-family: 'Syne', sans-serif;
    font-size: 20px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1;
}

.stat-label {
    font-size: 11px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

/* Access Requirements */
.access-requirements {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.access-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
}

.access-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.access-item {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 14px;
    color: var(--text-primary);
}

.access-item i {
    width: 20px;
    text-align: center;
    font-size: 14px;
    color: var(--accent);
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
.exam-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.btn-start {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    color: white;
}

.btn-start:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
    background: var(--surface-2);
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-secondary:hover {
    background: var(--surface);
    border-color: var(--accent);
    color: var(--accent);
}

.btn-tokens {
    background: var(--accent-warm);
    color: white;
}

.btn-tokens:hover {
    background: #f59e0b;
    transform: translateY(-1px);
}

.btn-subscription {
    background: var(--accent-2);
    color: white;
}

.btn-subscription:hover {
    background: #8b5cf6;
    transform: translateY(-1px);
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
    
    .page-content {
        padding: 0 16px 16px;
    }
    
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 12px;
    }
    
    .exams-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .exam-stats {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .access-items {
        gap: 8px;
    }
}

@media (max-width: 480px) {
    .exam-header {
        padding: 20px 16px;
    }
    
    .exam-body {
        padding: 20px 16px;
    }
    
    .exam-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .exam-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Practice Exams</h1>
        <p>Available exams for practice and improvement</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.tokens.purchase') }}" class="btn btn-tokens">
            <i class="fas fa-coins"></i>
            Get Tokens
        </a>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <!-- Filters Section -->
    <div class="filters-section">
        <div class="filters-header">
            <h2 class="filters-title">Filter Exams</h2>
        </div>
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Subject</label>
                <select class="filter-select">
                    <option value="">All Subjects</option>
                    <option value="mathematics">Mathematics</option>
                    <option value="biology">Biology</option>
                    <option value="chemistry">Chemistry</option>
                    <option value="physics">Physics</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Difficulty</label>
                <select class="filter-select">
                    <option value="">All Levels</option>
                    <option value="easy">Easy</option>
                    <option value="medium">Medium</option>
                    <option value="hard">Hard</option>
                </select>
            </div>
            <div class="filter-group">
                <label class="filter-label">Duration</label>
                <select class="filter-select">
                    <option value="">All Durations</option>
                    <option value="30">30 minutes</option>
                    <option value="60">60 minutes</option>
                    <option value="90">90 minutes</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Exams Grid -->
    @if($exams && $exams->count() > 0)
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
                            
                            <div class="access-items">
                                @if($exam->public_price > 0)
                                    <div class="access-item">
                                        <i class="fas fa-coins"></i>
                                        <span class="price-tag">{{ $exam->public_price }} RWF</span>
                                        <span>or</span>
                                        <span class="subscription-tag">Active Subscription</span>
                                    </div>
                                @else
                                    <div class="access-item">
                                        <i class="fas fa-check-circle"></i>
                                        <span class="free-tag">Free with Active Subscription</span>
                                    </div>
                                @endif
                                
                                <div class="access-item">
                                    <i class="fas fa-coins"></i>
                                    <span>{{ $exam->token_cost ?? 0 }} tokens required</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="exam-actions">
                            @if(auth()->guard('client')->user()->canAccessExam($exam))
                                <a href="{{ route('client.exams.take', $exam->id) }}" class="btn btn-start">
                                    <i class="fas fa-play"></i>
                                    Start Exam
                                </a>
                            @else
                                <div style="text-align: center; color: var(--text-muted); font-size: 13px; margin-bottom: 12px;">
                                    You need tokens or an active subscription to access this exam.
                                </div>
                                <a href="{{ route('client.tokens.purchase') }}" class="btn btn-tokens">
                                    <i class="fas fa-coins"></i>
                                    Get Tokens
                                </a>
                                <a href="{{ route('client.subscriptions.index') }}" class="btn btn-subscription">
                                    <i class="fas fa-crown"></i>
                                    Subscribe
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>No Exams Available</h3>
            <p>There are currently no practice exams available. Please check back later.</p>
            <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
