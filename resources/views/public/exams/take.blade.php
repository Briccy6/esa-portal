@extends('layouts.app')

@section('title', 'Exam - ' . $exam->title)

@push('styles')
<style>
/* Modern Moodle-inspired Exam Interface */
.exam-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Exam Header */
.exam-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white;
    border-radius: 16px;
    padding: 32px;
    margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.exam-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transform: translate(50%, -50%);
}

.exam-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
    position: relative;
    z-index: 1;
}

.exam-subtitle {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    margin-bottom: 24px;
    position: relative;
    z-index: 1;
}

.exam-stats {
    display: flex;
    gap: 32px;
    align-items: center;
    position: relative;
    z-index: 1;
}

.stat-item {
    text-align: center;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    display: block;
}

.stat-label {
    font-size: 0.875rem;
    color: rgba(255, 255, 255, 0.8);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-bar {
    background: rgba(255, 255, 255, 0.2);
    height: 8px;
    border-radius: 4px;
    overflow: hidden;
    margin-top: 16px;
}

.progress-fill {
    background: #10b981;
    height: 100%;
    border-radius: 4px;
    transition: width 0.3s ease;
}

/* Main Content Area */
.exam-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    align-items: start;
}

/* Question Card */
.question-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    position: relative;
}

.question-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #f3f4f6;
}

.question-number {
    background: #3b82f6;
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
}

.question-meta {
    display: flex;
    align-items: center;
    gap: 12px;
}

.marks-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
}

.guest-badge {
    background: #fef3c7;
    color: #92400e;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 6px;
}

.question-text {
    font-size: 1.25rem;
    line-height: 1.7;
    color: #1f2937;
    margin-bottom: 32px;
    font-weight: 500;
}

/* Answer Options */
.answer-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.answer-option {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    position: relative;
    display: flex;
    align-items: center;
    gap: 16px;
}

.answer-option:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
}

.answer-option.selected {
    border-color: #3b82f6;
    background: #dbeafe;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.option-indicator {
    width: 24px;
    height: 24px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.answer-option.selected .option-indicator {
    border-color: #3b82f6;
    background: #3b82f6;
}

.option-dot {
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    opacity: 0;
    transform: scale(0);
    transition: all 0.2s ease;
}

.answer-option.selected .option-dot {
    opacity: 1;
    transform: scale(1);
}

.option-text {
    flex: 1;
    font-size: 1.1rem;
    color: #374151;
    font-weight: 500;
}

.option-letter {
    background: #e5e7eb;
    color: #6b7280;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 600;
    margin-right: 12px;
}

.answer-option.selected .option-letter {
    background: #3b82f6;
    color: white;
}

/* Navigation */
.navigation {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
}

.nav-btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

.nav-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.nav-btn.primary {
    background: #3b82f6;
    color: white;
}

.nav-btn.primary:hover {
    background: #2563eb;
}

.nav-btn.secondary {
    background: #f3f4f6;
    color: #6b7280;
    border: 1px solid #d1d5db;
}

.nav-btn.secondary:hover {
    background: #e5e7eb;
}

.nav-btn.success {
    background: #10b981;
    color: white;
}

.nav-btn.success:hover {
    background: #059669;
}

.nav-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Sidebar */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.sidebar-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
    border: 1px solid #e5e7eb;
}

.sidebar-title {
    font-size: 1rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.sidebar-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Timer */
.timer-display {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    text-align: center;
    font-family: 'Monaco', 'Courier New', monospace;
}

.timer-label {
    font-size: 0.875rem;
    color: #6b7280;
    text-align: center;
    margin-top: 4px;
}

/* Question Navigation */
.question-nav {
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 8px;
}

.question-btn {
    aspect-ratio: 1;
    border: 2px solid #e5e7eb;
    background: white;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.question-btn:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(59, 130, 246, 0.15);
}

.question-btn.current {
    background: #3b82f6;
    color: white;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.question-btn.answered {
    background: #10b981;
    color: white;
    border-color: #10b981;
}

.question-btn.flagged {
    background: #f59e0b;
    color: white;
    border-color: #f59e0b;
}

.question-btn.answered.flagged {
    background: linear-gradient(135deg, #10b981 50%, #f59e0b 50%);
    border-color: #10b981;
}

.question-btn.current.flagged {
    background: linear-gradient(135deg, #3b82f6 50%, #f59e0b 50%);
    border-color: #3b82f6;
}

/* Flag indicator */
.flag-indicator {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #f59e0b;
    color: white;
    border-radius: 50%;
    width: 16px;
    height: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

/* Question Status Legend */
.status-legend {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 1px solid #f3f4f6;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    color: #6b7280;
}

.legend-box {
    width: 16px;
    height: 16px;
    border-radius: 4px;
    border: 2px solid;
}

.legend-box.current {
    background: #3b82f6;
    border-color: #3b82f6;
}

.legend-box.answered {
    background: #10b981;
    border-color: #10b981;
}

.legend-box.flagged {
    background: #f59e0b;
    border-color: #f59e0b;
}

.legend-box.unanswered {
    background: white;
    border-color: #e5e7eb;
}

/* Flag Button */
.flag-btn {
    background: #fef3c7;
    border: 1px solid #f59e0b;
    color: #92400e;
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.flag-btn:hover {
    background: #fde68a;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.flag-btn.flagged {
    background: #f59e0b;
    color: white;
    border-color: #d97706;
}

.flag-btn.flagged:hover {
    background: #d97706;
}

/* Info Lists */
.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    display: flex;
    align-items: start;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem;
    color: #6b7280;
}

.info-list li:last-child {
    border-bottom: none;
}

.info-list strong {
    color: #1f2937;
    font-weight: 600;
}

.info-icon {
    color: #3b82f6;
    margin-top: 2px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .exam-content {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        order: -1;
    }
    
    .question-nav {
        grid-template-columns: repeat(8, 1fr);
    }
}

@media (max-width: 640px) {
    .exam-container {
        padding: 12px;
    }
    
    .exam-header {
        padding: 20px;
    }
    
    .exam-title {
        font-size: 1.5rem;
    }
    
    .exam-stats {
        flex-direction: column;
        gap: 16px;
    }
    
    .question-card {
        padding: 20px;
    }
    
    .question-text {
        font-size: 1.1rem;
    }
    
    .question-nav {
        grid-template-columns: repeat(6, 1fr);
    }
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.question-card {
    animation: fadeIn 0.3s ease;
}

/* Focus States */
.answer-option:focus-within {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

/* Loading States */
.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Success States */
.success-message {
    background: #10b981;
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
</style>
@endpush

@section('content')
<div class="exam-container">
    <!-- Exam Header -->
    <div class="exam-header">
        <div>
            <h1 class="exam-title">{{ $exam->title }}</h1>
            <p class="exam-subtitle">{{ $exam->description ?? 'Excellent Servant Academy Examination' }}</p>
        </div>
        <div class="exam-stats">
            <div class="stat-item">
                <span class="stat-value">{{ $questionNumber }}</span>
                <span class="stat-label">Question</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $totalQuestions }}</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ round(($questionNumber / $totalQuestions) * 100) }}%</span>
                <span class="stat-label">Progress</span>
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ round(($questionNumber / $totalQuestions) * 100) }}%"></div>
        </div>
    </div>

    <div class="exam-content">
        <!-- Main Question Area -->
        <div class="question-card">
            <div class="question-header">
                <div class="question-number">Question {{ $questionNumber }}</div>
                <div class="question-meta">
                    @if($sessionData['user_type'] === 'guest')
                        <div class="guest-badge">
                            <i class="fas fa-user-secret"></i>
                            Guest Mode
                        </div>
                    @endif
                    <div class="marks-badge">{{ $currentQuestion->marks ?? 5 }} marks</div>
                    <button type="button" class="flag-btn" id="flagBtn" onclick="toggleFlag({{ $questionNumber }})">
                        <i class="fas fa-flag"></i>
                        <span id="flagBtnText">Flag</span>
                    </button>
                </div>
            </div>
            
            <div class="question-text">
                {{ $currentQuestion->text }}
            </div>

            <!-- Answer Options -->
            <form id="answerForm" method="POST" action="{{ route('public.exams.submit', [$exam, $sessionId]) }}">
                @csrf
                <div class="answer-options">
                    @foreach(['A', 'B', 'C', 'D'] as $option)
                        @php
                            $optionField = 'option_' . strtolower($option);
                            $optionText = $currentQuestion->$optionField;
                        @endphp
                        @if($optionText)
                            <label class="answer-option" data-option="{{ $option }}">
                                <input type="radio" name="answer" value="{{ $option }}" class="sr-only" 
                                       {{ isset($sessionData['answers'][$questionNumber]) && $sessionData['answers'][$questionNumber] === $option ? 'checked' : '' }}>
                                <div class="option-indicator">
                                    <div class="option-dot"></div>
                                </div>
                                <div class="option-text">
                                    <span class="option-letter">{{ $option }}</span>
                                    {{ $optionText }}
                                </div>
                            </label>
                        @endif
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="navigation">
                    @if($questionNumber > 1)
                        <a href="{{ route('public.exams.take.question', [$exam, $sessionId, $questionNumber - 1]) }}" 
                           class="nav-btn secondary">
                            <i class="fas fa-arrow-left"></i>
                            Previous
                        </a>
                    @else
                        <div></div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        @if($questionNumber < $totalQuestions)
                            <button type="button" id="nextBtn" class="nav-btn primary" disabled>
                                Next
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        @else
                            <button type="submit" id="submitBtn" class="nav-btn success" disabled>
                                <i class="fas fa-check-circle"></i>
                                Submit Exam
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Timer -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    Time Remaining
                </div>
                <div class="timer-display" id="timerDisplay">00:00</div>
                <div class="timer-label">Minutes:Seconds</div>
            </div>

            <!-- Question Navigation -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon">
                        <i class="fas fa-list-ol"></i>
                    </div>
                    Question Navigator
                </div>
                <div class="question-nav">
                    @for($i = 1; $i <= $totalQuestions; $i++)
                        @php
                            $isCurrent = $i == $questionNumber;
                            $isAnswered = isset($sessionData['answers'][$i]);
                            $isFlagged = isset($sessionData['flagged'][$i]);
                        @endphp
                        <a href="{{ route('public.exams.take.question', [$exam, $sessionId, $i]) }}" 
                           class="question-btn {{ $isCurrent ? 'current' : '' }} {{ $isAnswered ? 'answered' : '' }} {{ $isFlagged ? 'flagged' : '' }}"
                           data-question="{{ $i }}">
                            {{ $i }}
                            @if($isFlagged)
                                <div class="flag-indicator">
                                    <i class="fas fa-flag" style="font-size: 8px;"></i>
                                </div>
                            @endif
                        </a>
                    @endfor
                </div>
                
                <!-- Status Legend -->
                <div class="status-legend">
                    <div class="legend-item">
                        <div class="legend-box current"></div>
                        <span>Current</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box answered"></div>
                        <span>Answered</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box flagged"></div>
                        <span>Flagged</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-box unanswered"></div>
                        <span>Not Answered</span>
                    </div>
                </div>
            </div>

            <!-- Exam Info -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    Exam Information
                </div>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-list-ul info-icon"></i>
                        <span><strong>Total Questions:</strong> {{ $totalQuestions }}</span>
                    </li>
                    <li>
                        <i class="fas fa-hourglass-half info-icon"></i>
                        <span><strong>Time Limit:</strong> {{ $exam->duration ?? '60' }} minutes</span>
                    </li>
                    <li>
                        <i class="fas fa-percentage info-icon"></i>
                        <span><strong>Passing Score:</strong> 60%</span>
                    </li>
                    <li>
                        <i class="fas fa-user info-icon"></i>
                        <span><strong>Mode:</strong> {{ ucfirst($sessionData['user_type']) }}</span>
                    </li>
                </ul>
            </div>

            <!-- Tips -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    Tips for Success
                </div>
                <ul class="info-list">
                    <li>
                        <i class="fas fa-check text-success info-icon"></i>
                        <span>Read each question carefully</span>
                    </li>
                    <li>
                        <i class="fas fa-check text-success info-icon"></i>
                        <span>Choose the best possible answer</span>
                    </li>
                    <li>
                        <i class="fas fa-check text-success info-icon"></i>
                        <span>You can review before submitting</span>
                    </li>
                    <li>
                        <i class="fas fa-check text-success info-icon"></i>
                        <span>Flag questions to review later</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// Timer functionality - countdown timer with persistence
const startTime = new Date('{{ $sessionData['start_time'] ?? now() }}').getTime();
const timerElement = document.getElementById('timerDisplay');
const EXAM_DURATION_MINUTES = 60; // 60 minutes exam duration
const EXAM_DURATION_SECONDS = EXAM_DURATION_MINUTES * 60; // Convert to seconds

function updateTimer() {
    const elapsed = Math.floor((Date.now() - startTime) / 1000);
    const remaining = EXAM_DURATION_SECONDS - elapsed;
    
    console.log('Debug: Timer update - elapsed:', elapsed, 'remaining:', remaining);
    
    // DISABLED: Auto-submit functionality to stop continuous loading
    if (remaining <= 0) {
        console.log('Debug: Time expired, but auto-submit DISABLED to prevent infinite loop');
        timerElement.textContent = "00:00";
        timerElement.style.color = "#dc2626"; // Red color for expired
        // window.location.href = '{{ route('public.exams.submit', [$exam, $sessionId]) }}';
        return;
    }
    
    const minutes = Math.floor(remaining / 60);
    const seconds = remaining % 60;
    timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Change color based on time remaining
    if (remaining <= 300) { // Last 5 minutes
        timerElement.style.color = "#dc2626"; // Red
    } else if (remaining <= 600) { // Last 10 minutes
        timerElement.style.color = "#f59e0b"; // Orange
    } else {
        timerElement.style.color = "#10b981"; // Green
    }
}

// EMERGENCY FIX: Remove all problematic JavaScript to stop infinite loading
console.log('EMERGENCY: All JavaScript functionality reset to stop infinite loading');

// Store original fetch before overriding
const originalFetch = window.fetch;

// Restore original fetch but with proper error handling
window.fetch = function(url, options) {
    console.log('Fetch request to:', url);
    return originalFetch(url, options);
};

// Remove beforeunload event listener that causes infinite loops
window.removeEventListener('beforeunload', function() {
    // This was causing the infinite loop
});

// Simple, safe navigation without beforeunload
function safeNavigate(url) {
    console.log('Navigating to:', url);
    window.location.href = url;
}

// Simple answer saving without infinite loops
function saveAnswer(answer) {
    console.log('Saving answer:', answer);
    
    fetch('{{ route('public.exams.save-answer', [$exam, $sessionId, $questionNumber]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            answer: answer,
            question_number: {{ $questionNumber }}
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Answer saved:', data);
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Simple next button without beforeunload
document.addEventListener('DOMContentLoaded', function() {
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (!this.disabled) {
                safeNavigate('{{ route('public.exams.take.question', [$exam, $sessionId, $questionNumber + 1]) }}');
            }
        });
    }
    
    // Answer selection
    const answerOptions = document.querySelectorAll('.answer-option');
    answerOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove previous selection
            answerOptions.forEach(opt => opt.classList.remove('selected'));
            
            // Add selection to current
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Enable next button (reuse the already declared nextBtn from outer scope)
            if (nextBtn) nextBtn.disabled = false;
            
            // Save answer
            const optionValue = this.dataset.option;
            saveAnswer(optionValue);
        });
    });
});

// Test AJAX functionality
function testAjax() {
    console.log('Testing AJAX...');
    fetch('/test-ajax', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Test AJAX response:', response.status);
        return response.text();
    })
    .then(data => {
        console.log('Test AJAX data:', data);
    })
    .catch(error => {
        console.error('Test AJAX error:', error);
    });
}

// Add debug for continuous loading - REMOVED (no longer needed)
console.log('Debug: Page loaded at:', new Date().toISOString());
console.log('Debug: Current question:', {{ $questionNumber }});
console.log('Debug: Exam ID:', {{ $exam->id }});
console.log('Debug: Session ID:', '{{ $sessionId }}');

// Monitor for continuous loading - REMOVED (no longer needed)
console.log('Debug: Page monitoring disabled - infinite loading issue resolved');

// Add test button to console
console.log('Type testAjax() in console to test AJAX functionality');
console.log('Type testNextButton() in console to test next button');
console.log('Type testAnswerOptions() in console to test answer options');

// Test answer options functionality
function testAnswerOptions() {
    const answerOptions = document.querySelectorAll('.answer-option');
    console.log('Found answer options:', answerOptions.length);
    
    answerOptions.forEach((option, index) => {
        console.log(`Option ${index + 1}:`, option);
        console.log(`Option ${index + 1} data-option:`, option.getAttribute('data-option'));
        console.log(`Option ${index + 1} has click listener:`, option.onclick ? 'yes' : 'no');
    });
    
    // Try adding a click listener to the first option
    if (answerOptions.length > 0) {
        const firstOption = answerOptions[0];
        console.log('Adding test click listener to first option');
        firstOption.addEventListener('click', function() {
            console.log('Test click listener triggered!');
        });
    }
}

// Test next button functionality
function testNextButton() {
    const nextBtn = document.getElementById('nextBtn');
    console.log('Next button element:', nextBtn);
    console.log('Next button disabled:', nextBtn ? nextBtn.disabled : 'not found');
    console.log('Next button onclick:', nextBtn ? nextBtn.onclick : 'not found');
    
    if (nextBtn && !nextBtn.disabled) {
        console.log('Simulating next button click');
        nextBtn.click();
    } else {
        console.log('Next button is disabled or not found');
    }
}
// AJAX Navigation for instant question switching - DISABLED to prevent infinite loop
function loadQuestion(questionNumber) {
    console.log('Debug: AJAX navigation DISABLED to prevent infinite loop');
    console.log('Debug: Would load question:', questionNumber);
    
    // TEMPORARY FALLBACK: Use regular page navigation
    window.location.href = '{{ route('public.exams.take.question', [$exam, $sessionId]) }}/' + questionNumber;
    return;
    
    /* Original AJAX code - DISABLED
    console.log('Debug: Loading question via AJAX:', questionNumber);
    
    // Show loading state
    const questionContent = document.querySelector('.question-content');
    const questionText = document.querySelector('.question-text');
    const answerOptions = document.querySelector('.answer-options');
    
    console.log('Debug: Found elements', { questionContent, questionText, answerOptions });
    
    // Add loading animation
    questionContent.style.opacity = '0.5';
    questionText.innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i><p class="mt-4 text-gray-600">Loading question...</p></div>';
    answerOptions.innerHTML = '';
    
    console.log('Debug: Making AJAX request to:', '{{ route('public.exams.take.question', [$exam, $sessionId]) }}/' + questionNumber);
    
    // Fetch question data via AJAX
    fetch('{{ route('public.exams.take.question', [$exam, $sessionId]) }}/' + questionNumber, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        console.log('Debug: AJAX response status:', response.status);
        console.log('Debug: AJAX response headers:', response.headers);
        return response.json();
    })
    .then(data => {
        console.log('Debug: AJAX response data:', data);
        if (data.success) {
            console.log('Debug: Updating question content');
            // Update question content
            updateQuestionContent(data);
            // Update question navigator
            updateQuestionNavigatorStates(data.currentQuestion, data.totalQuestions, data.sessionData);
            // Update URL without page reload
            history.pushState({}, '', '{{ route('public.exams.take.question', [$exam, $sessionId]) }}/' + questionNumber);
            console.log('Debug: Question loaded successfully');
        } else {
            console.log('Debug: AJAX request failed:', data);
            // Handle error
            questionText.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-3xl"></i><p class="mt-4">Error loading question</p></div>';
        }
    })
    .catch(error => {
        console.error('Debug: AJAX error:', error);
        questionText.innerHTML = '<div class="text-center py-8 text-red-500"><i class="fas fa-exclamation-triangle text-3xl"></i><p class="mt-4">Network error</p></div>';
    })
    .finally(() => {
        questionContent.style.opacity = '1';
        console.log('Debug: Loading animation finished');
    });
    */
}

function updateQuestionContent(data) {
    // Update question text
    const questionText = document.querySelector('.question-text');
    questionText.innerHTML = data.question.text;
    
    // Update question number
    const questionNumber = document.querySelector('.question-number');
    if (questionNumber) {
        questionNumber.textContent = `Question ${data.currentQuestion}`;
    }
    
    // Update answer options
    const answerOptions = document.querySelector('.answer-options');
    answerOptions.innerHTML = '';
    
    ['A', 'B', 'C', 'D'].forEach(option => {
        const optionField = 'option_' + option.toLowerCase();
        const optionText = data.question[optionField];
        
        if (optionText) {
            const optionElement = document.createElement('label');
            optionElement.className = 'answer-option';
            optionElement.setAttribute('data-option', option);
            optionElement.innerHTML = `
                <input type="radio" name="answer" value="${option}" class="sr-only">
                <div class="option-content">
                    <div class="option-letter">${option}</div>
                    <div class="option-text">${optionText}</div>
                </div>
            `;
            
            // Add click handler
            optionElement.addEventListener('click', function() {
                selectAnswer(option, data.currentQuestion);
            });
            
            answerOptions.appendChild(optionElement);
        }
    });
    
    // Restore existing answer if any
    if (data.sessionData.answers && data.sessionData.answers[data.currentQuestion]) {
        const existingAnswer = data.sessionData.answers[data.currentQuestion];
        const answerOption = document.querySelector(`.answer-option[data-option="${existingAnswer}"]`);
        if (answerOption) {
            const radio = answerOption.querySelector('input[type="radio"]');
            answerOption.classList.add('selected');
            radio.checked = true;
        }
    }
    
    // Update flag state
    updateFlagState(data.currentQuestion, data.sessionData);
    
    // Update navigation buttons
    updateNavigationButtons(data.currentQuestion, data.totalQuestions, data.sessionData);
}

function updateQuestionNavigatorStates(currentQuestion, totalQuestions, sessionData) {
    // Update all question buttons
    for (let i = 1; i <= totalQuestions; i++) {
        const questionBtn = document.querySelector(`.question-btn[data-question="${i}"]`);
        if (questionBtn) {
            // Remove all state classes
            questionBtn.classList.remove('current', 'answered', 'flagged');
            
            // Add appropriate classes
            if (i === currentQuestion) {
                questionBtn.classList.add('current');
            }
            if (sessionData.answers && sessionData.answers[i]) {
                questionBtn.classList.add('answered');
            }
            if (sessionData.flagged && sessionData.flagged[i]) {
                questionBtn.classList.add('flagged');
                // Add flag indicator if not present
                if (!questionBtn.querySelector('.flag-indicator')) {
                    const indicator = document.createElement('div');
                    indicator.className = 'flag-indicator';
                    indicator.innerHTML = '<i class="fas fa-flag" style="font-size: 8px;"></i>';
                    questionBtn.appendChild(indicator);
                }
            }
        }
    }
}

function updateFlagState(currentQuestion, sessionData) {
    const flagBtn = document.getElementById('flagBtn');
    const flagBtnText = document.getElementById('flagBtnText');
    
    if (sessionData.flagged && sessionData.flagged[currentQuestion]) {
        flagBtn.classList.add('flagged');
        flagBtnText.textContent = 'Unflag';
    } else {
        flagBtn.classList.remove('flagged');
        flagBtnText.textContent = 'Flag';
    }
}

function updateNavigationButtons(currentQuestion, totalQuestions, sessionData) {
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Update navigation links
    const prevLink = document.querySelector('a[href*="take.question"][href*="' + (currentQuestion - 1) + '"]');
    const nextLink = document.querySelector('a[href*="take.question"][href*="' + (currentQuestion + 1) + '"]');
    
    if (prevLink) {
        prevLink.setAttribute('onclick', `loadQuestion(${currentQuestion - 1}); return false;`);
    }
    
    if (nextLink) {
        nextLink.setAttribute('onclick', `loadQuestion(${currentQuestion + 1}); return false;`);
    }
    
    // Update question navigator links
    for (let i = 1; i <= totalQuestions; i++) {
        const questionBtn = document.querySelector(`.question-btn[data-question="${i}"]`);
        if (questionBtn) {
            questionBtn.setAttribute('onclick', `loadQuestion(${i}); return false;`);
        }
    }
    
    // Enable/disable buttons based on answer or flag state
    const hasAnswer = sessionData.answers && sessionData.answers[currentQuestion];
    const isFlagged = sessionData.flagged && sessionData.flagged[currentQuestion];
    
    if (hasAnswer || isFlagged) {
        if (nextBtn) nextBtn.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
    } else {
        if (nextBtn) nextBtn.disabled = true;
        if (submitBtn) submitBtn.disabled = true;
    }
}

function selectAnswer(answer, questionNumber) {
    // Remove previous selection
    document.querySelectorAll('.answer-option').forEach(opt => {
        opt.classList.remove('selected');
    });
    
    // Add selection to current
    const answerOption = document.querySelector(`.answer-option[data-option="${answer}"]`);
    if (answerOption) {
        const radio = answerOption.querySelector('input[type="radio"]');
        answerOption.classList.add('selected');
        radio.checked = true;
        
        // Save answer
        saveAnswer(answer);
    }
}

// Toggle flag function - Declare variables inside function
function toggleFlag() {
    const questionNumber = {{ $questionNumber }};
    const flagBtn = document.getElementById('flagBtn');
    const flagBtnText = document.getElementById('flagBtnText');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    const isCurrentlyFlagged = flagBtn.classList.contains('flagged');
    
    if (isCurrentlyFlagged) {
        // Unflag the question
        flagBtn.classList.remove('flagged');
        flagBtnText.textContent = 'Flag';
        removeFromSession('flagged', questionNumber);
        
        // Update question button appearance
        const questionBtn = document.querySelector(`.question-btn[data-question="${questionNumber}"]`);
        if (questionBtn) {
            // Remove flag indicator
            const flagIndicator = questionBtn.querySelector('.flag-indicator');
            if (flagIndicator) {
                flagIndicator.remove();
            }
            
            // Remove flagged class and restore original appearance
            questionBtn.classList.remove('flagged');
            const selectedOption = document.querySelector('.answer-option.selected');
            const hasAnswer = selectedOption !== null;
            
            if (hasAnswer) {
                // Answered but not flagged - show green
                questionBtn.style.background = '#10b981';
                questionBtn.style.borderColor = '#10b981';
                questionBtn.style.color = 'white';
            } else {
                // Not answered and not flagged - show default white
                questionBtn.style.background = 'white';
                questionBtn.style.borderColor = '#e5e7eb';
                questionBtn.style.color = '#6b7280';
            }
        }
        
        // Check if there's an answer to determine button state
        const selectedOption = document.querySelector('.answer-option.selected');
        const hasAnswer = selectedOption !== null;
        
        if (!hasAnswer) {
            // No answer and not flagged - disable buttons
            if (nextBtn) nextBtn.disabled = true;
            if (submitBtn) submitBtn.disabled = true;
        }
        // If has answer, buttons remain enabled (existing logic)
        
    } else {
        // Flag the question
        flagBtn.classList.add('flagged');
        flagBtnText.textContent = 'Unflag';
        addToSession('flagged', questionNumber);
        
        // Update question button appearance
        const questionBtn = document.querySelector(`.question-btn[data-question="${questionNumber}"]`);
        if (questionBtn) {
            // Add flag indicator
            if (!questionBtn.querySelector('.flag-indicator')) {
                const flagIndicator = document.createElement('span');
                flagIndicator.className = 'flag-indicator';
                flagIndicator.innerHTML = '🚩';
                flagIndicator.style.cssText = 'position: absolute; top: 2px; right: 2px; font-size: 10px;';
                questionBtn.style.position = 'relative';
                questionBtn.appendChild(flagIndicator);
            }
            
            // Ensure the flagged class is applied and visible
            questionBtn.classList.add('flagged');
            
            // Force style update to ensure orange color shows
            questionBtn.style.background = '#f59e0b';
            questionBtn.style.borderColor = '#f59e0b';
            questionBtn.style.color = 'white';
        }
        
        // Enable next button when flagged (allowing skip)
        if (nextBtn) {
            nextBtn.disabled = false;
        }
        if (submitBtn) {
            submitBtn.disabled = false;
        }
    }
}

// Session management helpers - DISABLED to stop infinite loading
function addToSession(key, value) {
    console.log('BLOCKED: Session add disabled to stop infinite loading');
    // NO FETCH CALL - DISABLED
}

function removeFromSession(key, value) {
    console.log('BLOCKED: Session remove disabled to stop infinite loading');
    // NO FETCH CALL - DISABLED
}

// Save answer function - DISABLED to stop infinite loading
function saveAnswer(answer) {
    console.log('BLOCKED: Answer saving disabled to stop infinite loading');
    console.log('Would save answer:', answer, 'for question:', {{ $questionNumber }});
    // NO FETCH CALL - DISABLED
}

// Initialize flag button state and button functionality
document.addEventListener('DOMContentLoaded', function() {
    // Declare all button variables at the top level to avoid duplicates
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const flagBtn = document.getElementById('flagBtn');
    const flagBtnText = document.getElementById('flagBtnText');
    
    const currentQuestion = {{ $questionNumber }};
    const isFlagged = {{ isset($sessionData['flagged'][$questionNumber]) ? 'true' : 'false' }};
    const hasExistingAnswer = {{ isset($sessionData['answers'][$questionNumber]) ? 'true' : 'false' }};
    
    console.log('Debug - Question:', currentQuestion);
    console.log('Debug - Has existing answer:', hasExistingAnswer);
    console.log('Debug - Session data:', @json($sessionData));
    
    // Restore existing answer if any
    @if(isset($sessionData['answers'][$questionNumber]))
        console.log('Debug - Restoring answer: {{ $sessionData['answers'][$questionNumber] }}');
        const existingAnswer = '{{ $sessionData['answers'][$questionNumber] }}';
        const answerOption = document.querySelector(`.answer-option[data-option="${existingAnswer}"]`);
        console.log('Debug - Found answer option:', answerOption);
        if (answerOption) {
            const radio = answerOption.querySelector('input[type="radio"]');
            answerOption.classList.add('selected');
            radio.checked = true;
            console.log('Debug - Answer restored successfully');
        } else {
            console.log('Debug - Answer option not found for:', existingAnswer);
        }
    @else
        console.log('Debug - No existing answer to restore');
    @endif
    
    if (isFlagged) {
        flagBtn.classList.add('flagged');
        flagBtnText.textContent = 'Unflag';
        console.log('Debug - Flag state restored');
    }
    
    // Update question navigator to show flag states
    updateQuestionNavigatorStates({{ $questionNumber }}, {{ $totalQuestions }}, @json($sessionData));
    
    // Enable buttons if there's an existing answer
    if (hasExistingAnswer) {
        if (nextBtn) nextBtn.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
        console.log('Debug - Buttons enabled due to existing answer');
    }
    
    // ANSWER SELECTION LOGIC
    const answerOptions = document.querySelectorAll('.answer-option');
    // Reuse nextBtn and submitBtn from outer scope
    console.log('Debug: Found answer options:', answerOptions.length);
    console.log('Debug: Next button found:', !!nextBtn);
    console.log('Debug: Next button initial state:', nextBtn ? nextBtn.disabled : 'not found');

    answerOptions.forEach(option => {
        console.log('Debug: Adding click listener to option');
        option.addEventListener('click', function() {
            console.log('Debug: Answer option clicked');
            const radio = this.querySelector('input[type="radio"]');
            const optionValue = this.dataset.option;
            
            console.log('Debug: Selected option:', optionValue, 'Radio found:', !!radio);
            
            // Remove previous selection
            answerOptions.forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selection to current
            this.classList.add('selected');
            radio.checked = true;
            
            console.log('Debug: About to enable navigation buttons');
            
            // Enable navigation buttons
            if (nextBtn) {
                nextBtn.disabled = false;
                console.log('Debug: Next button enabled, disabled:', nextBtn.disabled);
            }
            if (submitBtn) {
                submitBtn.disabled = false;
                console.log('Debug: Submit button enabled, disabled:', submitBtn.disabled);
            }
            
            // Save answer
            console.log('Debug: Calling saveAnswer with option:', optionValue);
            saveAnswer(optionValue);
        });
    });
    
    // Next button click handler - REMOVED DUPLICATE (already handled above)
    console.log('Debug: Next button click handler already set in main DOMContentLoaded');
    
    // Update existing navigation links to use AJAX
    document.querySelectorAll('a[href*="take.question"]').forEach(link => {
        const href = link.getAttribute('href');
        const questionMatch = href.match(/\/(\d+)$/);
        if (questionMatch) {
            const questionNum = parseInt(questionMatch[1]);
            link.setAttribute('onclick', `loadQuestion(${questionNum}); return false;`);
        }
    });
    
    // Update question navigator buttons to use AJAX
    document.querySelectorAll('.question-btn').forEach(btn => {
        const questionNum = btn.getAttribute('data-question');
        btn.setAttribute('onclick', `loadQuestion(${questionNum}); return false;`);
    });
});

// Answer selection - REMOVED DUPLICATE
// This is now handled inside the DOMContentLoaded event listener above

// Save answer function
function saveAnswer(answer) {
    console.log('Debug - Saving answer:', answer, 'for question:', {{ $questionNumber }});
    fetch('{{ route('public.exams.save-answer', [$exam, $sessionId, $questionNumber]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            answer: answer,
            question_number: {{ $questionNumber }}
        })
    })
    .then(response => {
        console.log('Debug - Server response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Debug - Save answer response:', data);
        if (data.success) {
            // Update question navigator
            updateQuestionNavigator({{ $questionNumber }}, true);
            console.log('Debug - Answer saved and navigator updated');
        } else {
            console.log('Debug - Save answer failed:', data.error);
        }
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Update question navigator
function updateQuestionNavigator(questionNum, answered) {
    const questionBtn = document.querySelector(`.question-btn[data-question="${questionNum}"]`);
    if (questionBtn) {
        if (answered) {
            questionBtn.classList.add('answered');
        }
    }
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    // F key to toggle flag
    if (e.key === 'f' || e.key === 'F') {
        e.preventDefault();
        toggleFlag({{ $questionNumber }});
    }
    
    // Enter key to navigate
    if (e.key === 'Enter' && !nextBtn.disabled) {
        e.preventDefault();
        loadQuestion({{ $questionNumber + 1 }});
    }
});

// Initialize
updateTimer();
</script>
@endsection
