@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title)

@push('styles')
<style>
.results-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.results-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2.5rem;
    border-radius: 20px 20px 0 0;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.results-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.results-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
}

.results-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    position: relative;
    z-index: 1;
}

.status-badge {
    display: inline-block;
    padding: 0.75rem 2rem;
    border-radius: 50px;
    font-weight: 600;
    font-size: 1.1rem;
    margin: 1.5rem 0;
    position: relative;
    z-index: 1;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.status-pass {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    box-shadow: 0 8px 32px rgba(34, 197, 94, 0.3);
}

.status-fail {
    background: linear-gradient(135deg, #f87171, #ef4444);
    box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
}

.results-card {
    background: white;
    border-radius: 0 0 20px 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.score-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin: 2rem 0;
}

.score-card {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.score-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #8b5cf6);
}

.score-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.score-label {
    font-size: 0.9rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.score-value {
    font-size: 2.5rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
}

.progress-section {
    margin: 2.5rem 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #475569;
}

.progress-bar-container {
    background: #f1f5f9;
    height: 32px;
    border-radius: 16px;
    overflow: hidden;
    position: relative;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 16px;
    transition: width 1s ease-in-out;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.progress-bar-fill::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { left: -100%; }
    100% { left: 100%; }
}

.progress-pass {
    background: linear-gradient(135deg, #4ade80, #22c55e);
}

.progress-fail {
    background: linear-gradient(135deg, #f87171, #ef4444);
}

.exam-details {
    background: linear-gradient(135deg, #fafbfc, #f8fafd);
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 2rem;
    margin: 2rem 0;
}

.details-header {
    display: flex;
    align-items: center;
    margin-bottom: 1.5rem;
    color: #1e293b;
    font-weight: 600;
    font-size: 1.2rem;
}

.details-header i {
    margin-right: 0.75rem;
    color: #3b82f6;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.detail-item {
    display: flex;
    flex-direction: column;
}

.detail-label {
    font-size: 0.85rem;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.25rem;
}

.detail-value {
    font-size: 1.1rem;
    color: #1e293b;
    font-weight: 500;
}

.course-info {
    background: linear-gradient(135deg, #eff6ff, #dbeafe);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 1rem;
    margin-top: 0.5rem;
}

.course-title {
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 0.25rem;
}

.course-code {
    font-size: 0.9rem;
    color: #3b82f6;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 2.5rem;
    flex-wrap: wrap;
}

.btn-action {
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: white;
    box-shadow: 0 4px 20px rgba(59, 130, 246, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(59, 130, 246, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #4ade80, #22c55e);
    color: white;
    box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
}

.btn-success:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(34, 197, 94, 0.4);
}

@media (max-width: 768px) {
    .results-container {
        margin: 1rem auto;
        padding: 0 0.5rem;
    }
    
    .results-header {
        padding: 2rem 1.5rem;
    }
    
    .results-title {
        font-size: 2rem;
    }
    
    .score-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-action {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endpush

@section('content')
<div class="results-container">
    <div class="results-header">
        <h1 class="results-title">
            <i class="fas fa-graduation-cap me-3"></i>
            Exam Results
        </h1>
        <p class="results-subtitle">{{ $exam->title }}</p>
        <div class="status-badge {{ $passed ? 'status-pass' : 'status-fail' }}">
            <i class="fas {{ $passed ? 'fa-check-circle' : 'fa-times-circle' }} me-2"></i>
            {{ $passed ? 'Congratulations! You Passed!' : 'Better Luck Next Time!' }}
        </div>
    </div>

    <div class="results-card">
        <!-- Score Display -->
        <div class="score-grid">
            <div class="score-card">
                <div class="score-label">
                    <i class="fas fa-percentage me-2"></i>Your Score
                </div>
                <div class="score-value">{{ $score }}%</div>
            </div>
            <div class="score-card">
                <div class="score-label">
                    <i class="fas fa-check-double me-2"></i>Correct Answers
                </div>
                <div class="score-value">{{ $correctAnswers }}</div>
            </div>
            <div class="score-card">
                <div class="score-label">
                    <i class="fas fa-list-ol me-2"></i>Total Questions
                </div>
                <div class="score-value">{{ $totalQuestions }}</div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-section">
            <div class="progress-label">
                <span>Performance Score</span>
                <span>{{ $score }}%</span>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar-fill {{ $passed ? 'progress-pass' : 'progress-fail' }}" 
                     style="width: {{ $score }}%">
                    {{ $score }}%
                </div>
            </div>
        </div>

        <!-- Exam Details -->
        <div class="exam-details">
            <div class="details-header">
                <i class="fas fa-info-circle"></i>
                Exam Information
            </div>
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Exam Title</div>
                    <div class="detail-value">{{ $exam->title }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Subject</div>
                    <div class="detail-value">{{ $exam->subject ?? 'General' }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Exam Type</div>
                    <div class="detail-value">{{ ucfirst($exam->exam_type ?? 'Standard') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Total Marks</div>
                    <div class="detail-value">{{ $exam->total_marks ?? 100 }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Passing Score</div>
                    <div class="detail-value">{{ $exam->passing_score ?? 50 }}%</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Completed</div>
                    <div class="detail-value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
                </div>
            </div>
            
            <!-- Course Information -->
            @if($exam->courses && $exam->courses->count() > 0)
                <div class="details-header mt-4">
                    <i class="fas fa-book"></i>
                    Course Information
                </div>
                <div class="row">
                    @foreach($exam->courses as $course)
                        <div class="col-md-6 mb-3">
                            <div class="course-info">
                                <div class="course-title">
                                    <i class="fas fa-graduation-cap me-2"></i>{{ $course->name }}
                                </div>
                                @if($course->code)
                                    <div class="course-code">Code: {{ $course->code }}</div>
                                @endif
                                @if($course->description)
                                    <div class="mt-2 text-muted small">{{ Str::limit($course->description, 100) }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Additional Information -->
            @if($exam->academic_year || $exam->term || $exam->teacher)
                <div class="details-header mt-4">
                    <i class="fas fa-school"></i>
                    Academic Information
                </div>
                <div class="details-grid">
                    @if($exam->academic_year)
                        <div class="detail-item">
                            <div class="detail-label">Academic Year</div>
                            <div class="detail-value">{{ $exam->academic_year->name ?? 'N/A' }}</div>
                        </div>
                    @endif
                    @if($exam->term)
                        <div class="detail-item">
                            <div class="detail-label">Term</div>
                            <div class="detail-value">{{ $exam->term->name ?? 'N/A' }}</div>
                        </div>
                    @endif
                    @if($exam->teacher)
                        <div class="detail-item">
                            <div class="detail-label">Instructor</div>
                            <div class="detail-value">{{ $exam->teacher->name ?? 'N/A' }}</div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <a href="{{ route('public.exams.index') }}" class="btn-action btn-primary">
                <i class="fas fa-list"></i>
                Browse More Exams
            </a>
            @if($exam->is_public && $exam->public_price > 0)
                <a href="{{ route('public.exams.payment', $exam) }}" class="btn-action btn-success">
                    <i class="fas fa-redo"></i>
                    Retake Exam
                </a>
            @else
                <a href="{{ route('public.exams.start', $exam) }}" class="btn-action btn-success">
                    <i class="fas fa-redo"></i>
                    Retake Exam
                </a>
            @endif
        </div>
    </div>
</div>
@endsection
