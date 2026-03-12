@extends('layouts.app')

@section('title', 'Exam - ' . $exam->title)

@section('content')
@include('components.public-navigation')

@push('styles')
<!-- KaTeX CSS for mathematical expressions -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.css" crossorigin="anonymous">
<!-- KaTeX JS loaded SYNCHRONOUSLY (no defer) so it is available on DOMContentLoaded -->
<script src="https://cdn.jsdelivr.net/npm/katex@0.16.9/dist/katex.min.js" crossorigin="anonymous"></script>
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

/* KaTeX display math centering */
.katex-display {
    margin: 0.5em 0;
    overflow-x: auto;
    overflow-y: hidden;
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

.legend-box.current { background: #3b82f6; border-color: #3b82f6; }
.legend-box.answered { background: #10b981; border-color: #10b981; }
.legend-box.flagged { background: #f59e0b; border-color: #f59e0b; }
.legend-box.unanswered { background: white; border-color: #e5e7eb; }

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

.flag-btn:hover { background: #fde68a; transform: translateY(-1px); }
.flag-btn.flagged { background: #f59e0b; color: white; border-color: #d97706; }
.flag-btn.flagged:hover { background: #d97706; }

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

.info-list li:last-child { border-bottom: none; }
.info-list strong { color: #1f2937; font-weight: 600; }
.info-icon { color: #3b82f6; margin-top: 2px; }

/* Responsive Design */
@media (max-width: 1024px) {
    .exam-content { grid-template-columns: 1fr; }
    .sidebar { order: -1; }
    .question-nav { grid-template-columns: repeat(8, 1fr); }
}

@media (max-width: 768px) {
    .exam-container { padding: 1rem !important; }
    .exam-header { padding: 1.5rem !important; margin-bottom: 1rem !important; }
    .exam-title { font-size: 1.5rem !important; }
    .exam-subtitle { font-size: 1rem !important; margin-bottom: 1rem !important; }
    .question-text { font-size: 1.125rem !important; line-height: 1.6 !important; margin-bottom: 1.5rem !important; }
    .answer-option { padding: 1rem !important; gap: 0.75rem !important; }
    .option-text { font-size: 1rem !important; }
    .question-nav { grid-template-columns: repeat(4, 1fr) !important; gap: 0.5rem !important; }
}

@media (max-width: 640px) {
    .exam-header { padding: 1rem !important; }
    .exam-title { font-size: 1.25rem !important; }
    .question-card { padding: 20px; }
    .question-text { font-size: 1.1rem; }
    .exam-stats { flex-direction: column; gap: 16px; }
    .question-nav { grid-template-columns: repeat(6, 1fr); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.question-card { animation: fadeIn 0.3s ease; }
.answer-option:focus-within { outline: 2px solid #3b82f6; outline-offset: 2px; }

@media (hover: none) and (pointer: coarse) {
    .answer-option { min-height: 44px; padding: 1rem !important; }
    .question-btn { min-height: 44px; min-width: 44px; }
    .flag-btn, .nav-btn { min-height: 44px; }
}
</style>
@endpush

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

            <!-- Question text: rendered server-side then processed by KaTeX on load -->
            <div class="question-text">
                <div id="publicQuestionText">{{ $currentQuestion->text }}</div>
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
                                <input type="radio" name="answer" value="{{ $option }}" class="sr-only">
                                <div class="option-indicator">
                                    <div class="option-dot"></div>
                                </div>
                                <div class="option-text">
                                    <span class="option-letter">{{ $option }}</span>
                                    <div class="option-content">{{ $optionText }}</div>
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
                                Next <i class="fas fa-arrow-right"></i>
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
                    <div class="sidebar-icon"><i class="fas fa-clock"></i></div>
                    Time Remaining
                </div>
                <div class="timer-display" id="timerDisplay">00:00</div>
                <div class="timer-label">Minutes:Seconds</div>
            </div>

            <!-- Question Navigation -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon"><i class="fas fa-list-ol"></i></div>
                    Question Navigator
                </div>
                <div class="question-nav">
                    @for($i = 1; $i <= $totalQuestions; $i++)
                        @php
                            $isCurrent  = $i == $questionNumber;
                            $isAnswered = isset($sessionData['answers'][$i]);
                            $isFlagged  = isset($sessionData['flagged'][$i]);
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
                <div class="status-legend">
                    <div class="legend-item"><div class="legend-box current"></div><span>Current</span></div>
                    <div class="legend-item"><div class="legend-box answered"></div><span>Answered</span></div>
                    <div class="legend-item"><div class="legend-box flagged"></div><span>Flagged</span></div>
                    <div class="legend-item"><div class="legend-box unanswered"></div><span>Not Answered</span></div>
                </div>
            </div>

            <!-- Exam Info -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon"><i class="fas fa-info-circle"></i></div>
                    Exam Information
                </div>
                <ul class="info-list">
                    <li><i class="fas fa-list-ul info-icon"></i><span><strong>Total Questions:</strong> {{ $totalQuestions }}</span></li>
                    <li><i class="fas fa-hourglass-half info-icon"></i><span><strong>Time Limit:</strong> {{ $exam->duration ?? '60' }} minutes</span></li>
                    <li><i class="fas fa-percentage info-icon"></i><span><strong>Passing Score:</strong> 60%</span></li>
                    <li><i class="fas fa-user info-icon"></i><span><strong>Mode:</strong> {{ ucfirst($sessionData['user_type']) }}</span></li>
                </ul>
            </div>

            <!-- Tips -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon"><i class="fas fa-lightbulb"></i></div>
                    Tips for Success
                </div>
                <ul class="info-list">
                    <li><i class="fas fa-check text-success info-icon"></i><span>Read each question carefully</span></li>
                    <li><i class="fas fa-check text-success info-icon"></i><span>Choose the best possible answer</span></li>
                    <li><i class="fas fa-check text-success info-icon"></i><span>You can review before submitting</span></li>
                    <li><i class="fas fa-check text-success info-icon"></i><span>Flag questions to review later</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
// ─────────────────────────────────────────────
// KaTeX Math Rendering
// KaTeX is loaded synchronously in @push('styles') above,
// so it is always available when this script runs.
// Process $$...$$ (display) BEFORE $...$ (inline) to avoid double-matching.
// ─────────────────────────────────────────────
function renderMathInText(text) {
    if (!text || typeof katex === 'undefined') return text || '';

    // 1. Display math: $$...$$ — must come FIRST
    let result = text.replace(/\$\$([^$]+)\$\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), { throwOnError: false, displayMode: true });
        } catch (e) { return match; }
    });

    // 2. Inline math: $...$
    result = result.replace(/\$([^$\n]+)\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), { throwOnError: false, displayMode: false });
        } catch (e) { return match; }
    });

    return result;
}

// Apply math rendering to question text and all answer option contents
function applyMathRendering() {
    // Question text
    const questionEl = document.getElementById('publicQuestionText');
    if (questionEl) {
        questionEl.innerHTML = renderMathInText(questionEl.textContent);
    }

    // Answer option contents
    document.querySelectorAll('.option-content').forEach(function(el) {
        el.innerHTML = renderMathInText(el.textContent);
    });
}

// ─────────────────────────────────────────────
// Timer
// ─────────────────────────────────────────────
const startTime = new Date('{{ $sessionData['start_time'] ?? now() }}').getTime();
const timerElement = document.getElementById('timerDisplay');
const EXAM_DURATION_SECONDS = 60 * 60;

function updateTimer() {
    const elapsed   = Math.floor((Date.now() - startTime) / 1000);
    const remaining = EXAM_DURATION_SECONDS - elapsed;

    if (remaining <= 0) {
        timerElement.textContent = '00:00';
        timerElement.style.color = '#dc2626';
        return;
    }

    const minutes = Math.floor(remaining / 60);
    const seconds = remaining % 60;
    timerElement.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    timerElement.style.color = remaining <= 300 ? '#dc2626' : remaining <= 600 ? '#f59e0b' : '#10b981';
    setTimeout(updateTimer, 1000);
}

// ─────────────────────────────────────────────
// Save Answer
// ─────────────────────────────────────────────
function saveAnswer(answer) {
    fetch('{{ route('public.exams.save-answer', [$exam, $sessionId, $questionNumber]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ answer: answer, question_number: {{ $questionNumber }} })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            updateQuestionNavigator({{ $questionNumber }}, true);
        }
    })
    .catch(err => console.error('Error saving answer:', err));
}

function updateQuestionNavigator(questionNum, answered) {
    const btn = document.querySelector(`.question-btn[data-question="${questionNum}"]`);
    if (btn && answered) btn.classList.add('answered');
}

// ─────────────────────────────────────────────
// Flag Toggle
// ─────────────────────────────────────────────
function toggleFlag() {
    const flagBtn     = document.getElementById('flagBtn');
    const flagBtnText = document.getElementById('flagBtnText');
    const nextBtn     = document.getElementById('nextBtn');
    const submitBtn   = document.getElementById('submitBtn');
    const questionNum = {{ $questionNumber }};
    const isFlagged   = flagBtn.classList.contains('flagged');

    if (isFlagged) {
        flagBtn.classList.remove('flagged');
        flagBtnText.textContent = 'Flag';

        const questionBtn = document.querySelector(`.question-btn[data-question="${questionNum}"]`);
        if (questionBtn) {
            questionBtn.classList.remove('flagged');
            const fi = questionBtn.querySelector('.flag-indicator');
            if (fi) fi.remove();
        }

        const hasAnswer = !!document.querySelector('.answer-option.selected');
        if (!hasAnswer) {
            if (nextBtn)   nextBtn.disabled   = true;
            if (submitBtn) submitBtn.disabled = true;
        }
    } else {
        flagBtn.classList.add('flagged');
        flagBtnText.textContent = 'Unflag';

        const questionBtn = document.querySelector(`.question-btn[data-question="${questionNum}"]`);
        if (questionBtn) {
            questionBtn.classList.add('flagged');
            if (!questionBtn.querySelector('.flag-indicator')) {
                const fi = document.createElement('div');
                fi.className = 'flag-indicator';
                fi.innerHTML = '<i class="fas fa-flag" style="font-size:8px;"></i>';
                questionBtn.appendChild(fi);
            }
        }

        if (nextBtn)   nextBtn.disabled   = false;
        if (submitBtn) submitBtn.disabled = false;
    }
}

// ─────────────────────────────────────────────
// DOMContentLoaded — wire everything up
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Apply KaTeX math rendering first
    applyMathRendering();

    // Timer
    updateTimer();

    const nextBtn   = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    const flagBtn   = document.getElementById('flagBtn');
    const flagBtnText = document.getElementById('flagBtnText');

    // Restore flag state
    @if(isset($sessionData['flagged'][$questionNumber]))
        flagBtn.classList.add('flagged');
        flagBtnText.textContent = 'Unflag';
    @endif

    // Restore existing answer selection
    @if(isset($sessionData['answers'][$questionNumber]))
        const existingAnswer = '{{ $sessionData['answers'][$questionNumber] }}';
        const existingOption = document.querySelector(`.answer-option[data-option="${existingAnswer}"]`);
        if (existingOption) {
            existingOption.classList.add('selected');
            const radio = existingOption.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            if (nextBtn)   nextBtn.disabled   = false;
            if (submitBtn) submitBtn.disabled = false;
        }
    @endif

    // Update question navigator states from session
    updateQuestionNavigatorStates({{ $questionNumber }}, {{ $totalQuestions }}, @json($sessionData));

    // Answer click handlers
    document.querySelectorAll('.answer-option').forEach(function(option) {
        option.addEventListener('click', function() {
            document.querySelectorAll('.answer-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');

            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            if (nextBtn)   nextBtn.disabled   = false;
            if (submitBtn) submitBtn.disabled = false;

            saveAnswer(this.dataset.option);
        });
    });

    // Next button
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (!this.disabled) {
                window.location.href = '{{ route('public.exams.take.question', [$exam, $sessionId, $questionNumber + 1]) }}';
            }
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'f' || e.key === 'F') { e.preventDefault(); toggleFlag(); }
    });
});

// ─────────────────────────────────────────────
// Question navigator state updater
// ─────────────────────────────────────────────
function updateQuestionNavigatorStates(currentQ, totalQ, sessionData) {
    for (let i = 1; i <= totalQ; i++) {
        const btn = document.querySelector(`.question-btn[data-question="${i}"]`);
        if (!btn) continue;

        btn.classList.remove('current', 'answered', 'flagged');

        if (i === currentQ) btn.classList.add('current');
        if (sessionData.answers && sessionData.answers[i]) btn.classList.add('answered');
        if (sessionData.flagged && sessionData.flagged[i]) {
            btn.classList.add('flagged');
            if (!btn.querySelector('.flag-indicator')) {
                const fi = document.createElement('div');
                fi.className = 'flag-indicator';
                fi.innerHTML = '<i class="fas fa-flag" style="font-size:8px;"></i>';
                btn.appendChild(fi);
            }
        }
    }
}
</script>
@endsection