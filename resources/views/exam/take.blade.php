@extends('layouts.app')

@section('title', 'Exam - ' . $exam->title)

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
    top: 0; right: 0;
    width: 200px; height: 200px;
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

.stat-item { text-align: center; }
.stat-value { font-size: 1.5rem; font-weight: 700; display: block; }
.stat-label { font-size: 0.875rem; color: rgba(255,255,255,0.8); text-transform: uppercase; letter-spacing: 0.5px; }

.progress-bar {
    background: rgba(255,255,255,0.2);
    height: 8px; border-radius: 4px; overflow: hidden; margin-top: 16px;
}
.progress-fill {
    background: #10b981; height: 100%; border-radius: 4px; transition: width 0.3s ease;
}

.exam-content {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px;
    align-items: start;
}

.question-card {
    background: white;
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07);
    border: 1px solid #e5e7eb;
    animation: fadeIn 0.3s ease;
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
    background: #3b82f6; color: white;
    padding: 8px 16px; border-radius: 8px;
    font-weight: 600; font-size: 0.875rem;
}

.marks-badge {
    background: #fef3c7; color: #92400e;
    padding: 4px 12px; border-radius: 6px;
    font-size: 0.875rem; font-weight: 600;
}

.question-text {
    font-size: 1.25rem;
    line-height: 1.7;
    color: #1f2937;
    margin-bottom: 32px;
    font-weight: 500;
}

/* KaTeX display math */
.katex-display { margin: 0.5em 0; overflow-x: auto; overflow-y: hidden; }

.answer-options { display: flex; flex-direction: column; gap: 12px; }

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
    border-color: #3b82f6; background: #eff6ff;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59,130,246,0.15);
}

.answer-option.selected {
    border-color: #3b82f6; background: #dbeafe;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.option-indicator {
    width: 24px; height: 24px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease; flex-shrink: 0;
}
.answer-option.selected .option-indicator { border-color: #3b82f6; background: #3b82f6; }

.option-dot {
    width: 8px; height: 8px; background: white;
    border-radius: 50%; opacity: 0;
    transform: scale(0); transition: all 0.2s ease;
}
.answer-option.selected .option-dot { opacity: 1; transform: scale(1); }

.option-text { flex: 1; font-size: 1.1rem; color: #374151; font-weight: 500; }

.option-letter {
    background: #e5e7eb; color: #6b7280;
    padding: 4px 8px; border-radius: 4px;
    font-size: 0.875rem; font-weight: 600; margin-right: 12px;
}
.answer-option.selected .option-letter { background: #3b82f6; color: white; }

.navigation {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 32px; padding-top: 24px; border-top: 1px solid #e5e7eb;
}

.nav-btn {
    padding: 12px 24px; border-radius: 8px; font-weight: 600;
    text-decoration: none; transition: all 0.2s ease;
    border: none; cursor: pointer; font-size: 1rem;
}
.nav-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
.nav-btn.primary { background: #3b82f6; color: white; }
.nav-btn.primary:hover { background: #2563eb; }
.nav-btn.secondary { background: #f3f4f6; color: #6b7280; border: 1px solid #d1d5db; }
.nav-btn.secondary:hover { background: #e5e7eb; }
.nav-btn.success { background: #10b981; color: white; }
.nav-btn.success:hover { background: #059669; }
.nav-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

/* Sidebar */
.sidebar { display: flex; flex-direction: column; gap: 20px; }
.sidebar-card {
    background: white; border-radius: 12px; padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;
}
.sidebar-title {
    font-size: 1rem; font-weight: 700; color: #1f2937;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}
.sidebar-icon { width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; }

.timer-display {
    font-size: 2rem; font-weight: 700; color: #1f2937;
    text-align: center; font-family: 'Monaco', 'Courier New', monospace;
}
.timer-label { font-size: 0.875rem; color: #6b7280; text-align: center; margin-top: 4px; }

.question-nav { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }

.question-btn {
    aspect-ratio: 1; border: 2px solid #e5e7eb; background: white;
    border-radius: 8px; font-weight: 600; font-size: 0.875rem;
    color: #6b7280; cursor: pointer; transition: all 0.2s ease;
    display: flex; align-items: center; justify-content: center; position: relative;
}
.question-btn:hover { border-color: #3b82f6; color: #3b82f6; transform: translateY(-1px); }
.question-btn.current { background: #3b82f6; color: white; border-color: #3b82f6; }
.question-btn.answered { background: #10b981; color: white; border-color: #10b981; }
.question-btn.flagged { background: #f59e0b; color: white; border-color: #f59e0b; }
.question-btn.answered.flagged { background: linear-gradient(135deg, #10b981 50%, #f59e0b 50%); }
.question-btn.current.flagged  { background: linear-gradient(135deg, #3b82f6 50%, #f59e0b 50%); }

.flag-indicator {
    position: absolute; top: -4px; right: -4px;
    background: #f59e0b; color: white; border-radius: 50%;
    width: 16px; height: 16px; display: flex;
    align-items: center; justify-content: center;
    font-size: 10px; font-weight: bold;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.status-legend {
    display: flex; flex-wrap: wrap; gap: 12px;
    margin-top: 16px; padding-top: 16px; border-top: 1px solid #f3f4f6;
}
.legend-item { display: flex; align-items: center; gap: 6px; font-size: 0.75rem; color: #6b7280; }
.legend-box { width: 16px; height: 16px; border-radius: 4px; border: 2px solid; }
.legend-box.current  { background: #3b82f6; border-color: #3b82f6; }
.legend-box.answered { background: #10b981; border-color: #10b981; }
.legend-box.flagged  { background: #f59e0b; border-color: #f59e0b; }
.legend-box.unanswered { background: white; border-color: #e5e7eb; }

.info-list { list-style: none; padding: 0; margin: 0; }
.info-list li {
    display: flex; align-items: start; gap: 8px;
    padding: 8px 0; border-bottom: 1px solid #f3f4f6;
    font-size: 0.875rem; color: #6b7280;
}
.info-list li:last-child { border-bottom: none; }
.info-list strong { color: #1f2937; font-weight: 600; }
.info-icon { color: #3b82f6; margin-top: 2px; }

/* Submit Modal */
.modal-backdrop {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5);
    display: none; z-index: 9999;
    align-items: center; justify-content: center;
}
.modal-backdrop.show { display: flex; }
.modal-box {
    background: white; border-radius: 16px; padding: 32px;
    max-width: 400px; width: 90%; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
}

/* Responsive */
@media (max-width: 1024px) {
    .exam-content { grid-template-columns: 1fr; }
    .sidebar { order: -1; }
    .question-nav { grid-template-columns: repeat(8, 1fr); }
}
@media (max-width: 768px) {
    .exam-container { padding: 1rem; }
    .exam-header { padding: 1.5rem; }
    .exam-title { font-size: 1.5rem; }
    .question-text { font-size: 1.125rem; }
    .question-nav { grid-template-columns: repeat(5, 1fr); }
}
@media (max-width: 640px) {
    .exam-title { font-size: 1.25rem; }
    .exam-stats { flex-direction: column; gap: 16px; }
    .question-card { padding: 20px; }
    .question-nav { grid-template-columns: repeat(5, 1fr); }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
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
                <span class="stat-value">{{ $currentQuestionNumber ?? 1 }}</span>
                <span class="stat-label">Question</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $questions->count() ?? 0 }}</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">{{ $questions->count() > 0 ? round(($currentQuestionNumber / $questions->count()) * 100) : 0 }}%</span>
                <span class="stat-label">Progress</span>
            </div>
        </div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: {{ $questions->count() > 0 ? round(($currentQuestionNumber / $questions->count()) * 100) : 0 }}%"></div>
        </div>
    </div>

    <div class="exam-content">
        <!-- Main Question Area -->
        <div class="question-card">
            <div class="question-header">
                <div class="question-number">Question {{ $currentQuestionNumber ?? 1 }}</div>
                <div class="question-meta">
                    <div class="marks-badge">{{ $currentQuestion->points ?? $currentQuestion->marks ?? 5 }} marks</div>
                </div>
            </div>

            <div class="question-text">
                @if(isset($currentQuestion))
                    <div id="questionTextContent">{{ $currentQuestion->text }}</div>
                @else
                    No question available
                @endif
            </div>

            @if(isset($currentQuestion))
            <form method="POST" action="{{ route('exams.save-answer', [$exam, $session, $currentQuestionNumber]) }}">
                @csrf
                <div class="answer-options">
                    @foreach($currentQuestion->options ?? [] as $index => $option)
                        <label class="answer-option {{ isset($existingAnswer) && $existingAnswer->answer == $index ? 'selected' : '' }}">
                            <input type="radio" name="answer" value="{{ $index }}" class="sr-only"
                                   {{ isset($existingAnswer) && $existingAnswer->answer == $index ? 'checked' : '' }}>
                            <div class="option-indicator">
                                <div class="option-dot"></div>
                            </div>
                            <div class="option-text">
                                <span class="option-letter">{{ chr(65 + $index) }}</span>
                                <div class="option-content">{{ $option }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="navigation">
                    @if($currentQuestionNumber > 1)
                        <button type="submit" name="action" value="previous" class="nav-btn secondary">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="d-flex gap-2">
                        @if($currentQuestionNumber < $questions->count())
                            <button type="submit" name="action" value="next" class="nav-btn primary">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        @else
                            <button type="button" class="nav-btn success" onclick="openSubmitModal()">
                                <i class="fas fa-check-circle"></i> Submit Exam
                            </button>
                        @endif
                    </div>
                </div>
            </form>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Timer -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon"><i class="fas fa-clock"></i></div>
                    Time Remaining
                </div>
                <div class="timer-display" id="timer">
                    {{ sprintf('%02d', $exam->duration ?? 60) }}:00:00
                </div>
                <div class="timer-label">Hours:Minutes:Seconds</div>
            </div>

            <!-- Question Navigation -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <div class="sidebar-icon"><i class="fas fa-list-ol"></i></div>
                    Question Navigator
                </div>
                <div class="question-nav">
                    @for($i = 1; $i <= $questions->count(); $i++)
                        <a href="{{ route('exams.take-question', [$exam->id, $session]) }}/{{ $i }}"
                           id="nav-question-{{ $i }}"
                           class="question-btn {{ $i == ($currentQuestionNumber ?? 1) ? 'current' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
                <div class="status-legend">
                    <div class="legend-item"><div class="legend-box current"></div><span>Current</span></div>
                    <div class="legend-item"><div class="legend-box answered"></div><span>Answered</span></div>
                    <div class="legend-item"><div class="legend-box flagged"></div><span>Marked for Review</span></div>
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
                    <li><i class="fas fa-hourglass-half info-icon"></i><span><strong>Duration:</strong> {{ $exam->duration ?? 60 }} minutes</span></li>
                    <li><i class="fas fa-list-ul info-icon"></i><span><strong>Total Questions:</strong> {{ isset($questions) ? $questions->count() : 0 }}</span></li>
                    <li><i class="fas fa-star info-icon"></i><span><strong>Total Marks:</strong> {{ $exam->total_marks ?? 100 }}</span></li>
                    <li><i class="fas fa-coins info-icon"></i><span><strong>Tokens Used:</strong> {{ $exam->token_cost ?? 0 }}</span></li>
                </ul>
            </div>

            <!-- Instructions -->
            <div class="sidebar-card" style="background: #eff6ff; border-color: #bfdbfe;">
                <div class="sidebar-title" style="color: #1e40af;">
                    <div class="sidebar-icon"><i class="fas fa-lightbulb"></i></div>
                    Instructions
                </div>
                <ul class="info-list">
                    <li style="color: #1e40af;"><i class="fas fa-circle info-icon" style="font-size:6px; margin-top: 6px;"></i><span>Select one answer for each question</span></li>
                    <li style="color: #1e40af;"><i class="fas fa-circle info-icon" style="font-size:6px; margin-top: 6px;"></i><span>Use the navigator to jump between questions</span></li>
                    <li style="color: #1e40af;"><i class="fas fa-circle info-icon" style="font-size:6px; margin-top: 6px;"></i><span>Mark questions for review if unsure</span></li>
                    <li style="color: #1e40af;"><i class="fas fa-circle info-icon" style="font-size:6px; margin-top: 6px;"></i><span>Submit exam when you're finished</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Submit Confirmation Modal -->
<div id="submitModal" class="modal-backdrop">
    <div class="modal-box">
        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 12px;">Submit Exam?</h3>
        <p style="color: #6b7280; margin-bottom: 20px;">Are you sure you want to submit? You cannot change your answers after submission.</p>
        <div style="background: #fef3c7; border: 1px solid #fde68a; border-radius: 8px; padding: 12px; margin-bottom: 20px; display: flex; gap: 8px; align-items: center;">
            <i class="fas fa-exclamation-triangle" style="color: #d97706;"></i>
            <span style="font-size: 0.875rem; color: #92400e;">
                Unanswered questions: <strong id="unansweredCount">0</strong>
            </span>
        </div>
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button onclick="closeSubmitModal()" class="nav-btn secondary">Cancel</button>
            <button onclick="confirmSubmit()" class="nav-btn success">
                <i class="fas fa-check"></i> Submit Exam
            </button>
        </div>
    </div>
</div>

<script>
// ─────────────────────────────────────────────
// KaTeX Math Rendering
// KaTeX is loaded synchronously in @push('styles') above.
// Process $$...$$ (display) BEFORE $...$ (inline).
// ─────────────────────────────────────────────
function renderMathInText(text) {
    if (!text || typeof katex === 'undefined') return text || '';

    // 1. Display math $$...$$ — FIRST
    let result = text.replace(/\$\$([^$]+)\$\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), { throwOnError: false, displayMode: true });
        } catch (e) { return match; }
    });

    // 2. Inline math $...$
    result = result.replace(/\$([^$\n]+)\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), { throwOnError: false, displayMode: false });
        } catch (e) { return match; }
    });

    return result;
}

function applyMathRendering() {
    // Question text
    const qEl = document.getElementById('questionTextContent');
    if (qEl) qEl.innerHTML = renderMathInText(qEl.textContent);

    // Answer option contents
    document.querySelectorAll('.option-content').forEach(function(el) {
        el.innerHTML = renderMathInText(el.textContent);
    });
}

// ─────────────────────────────────────────────
// Timer (counts down from exam duration)
// ─────────────────────────────────────────────
let timeRemaining = {{ ($exam->duration ?? 60) * 60 }};

function updateTimer() {
    const h = Math.floor(timeRemaining / 3600);
    const m = Math.floor((timeRemaining % 3600) / 60);
    const s = timeRemaining % 60;

    const timerEl = document.getElementById('timer');
    timerEl.textContent =
        `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;

    if (timeRemaining <= 300) timerEl.style.color = '#dc2626';
    else if (timeRemaining <= 600) timerEl.style.color = '#f59e0b';
    else timerEl.style.color = '#1f2937';

    if (timeRemaining > 0) {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    } else {
        confirmSubmit();
    }
}

// ─────────────────────────────────────────────
// Answer state tracking
// ─────────────────────────────────────────────
let answers = {};

function updateQuestionGrid() {
    const totalQuestions = {{ isset($questions) ? $questions->count() : 0 }};
    const currentQ = {{ $currentQuestionNumber ?? 1 }};

    for (let i = 1; i <= totalQuestions; i++) {
        const btn = document.getElementById(`nav-question-${i}`);
        if (!btn) continue;

        btn.classList.remove('current', 'answered');
        if (i === currentQ) btn.classList.add('current');
        if (answers[i] !== undefined) btn.classList.add('answered');
    }
}

function saveAnswer(questionNumber, answer) {
    fetch(`/exams/{{ $exam->id }}/save-answer/{{ $session ?? 'current' }}/${questionNumber}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ answer: answer })
    }).catch(err => console.error('Error saving answer:', err));
}

// ─────────────────────────────────────────────
// Submit Modal
// ─────────────────────────────────────────────
function openSubmitModal() {
    const total     = {{ isset($questions) ? $questions->count() : 0 }};
    const answered  = Object.keys(answers).length;
    document.getElementById('unansweredCount').textContent = total - answered;
    document.getElementById('submitModal').classList.add('show');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.remove('show');
}

function confirmSubmit() {
    fetch(`{{ route('exams.submit', [$exam->id, ':session']) }}`.replace(':session', '{{ $session ?? 'current' }}'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ answers: answers })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) window.location.href = data.redirect;
    })
    .catch(err => console.error('Error submitting exam:', err));
}

// ─────────────────────────────────────────────
// DOMContentLoaded — wire everything up
// ─────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
    // Apply KaTeX math rendering immediately
    applyMathRendering();

    // Start timer
    updateTimer();

    // Answer click handlers
    document.querySelectorAll('.answer-option').forEach(function(option, index) {
        option.addEventListener('click', function() {
            document.querySelectorAll('.answer-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');

            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;

            answers[{{ $currentQuestionNumber ?? 1 }}] = index;
            updateQuestionGrid();
            saveAnswer({{ $currentQuestionNumber ?? 1 }}, index);
        });
    });

    // Restore existing answer highlight
    @if(isset($existingAnswer))
        const existingIdx = {{ $existingAnswer->answer ?? 'null' }};
        if (existingIdx !== null) {
            const opts = document.querySelectorAll('.answer-option');
            if (opts[existingIdx]) {
                opts[existingIdx].classList.add('selected');
                const r = opts[existingIdx].querySelector('input[type="radio"]');
                if (r) r.checked = true;
                answers[{{ $currentQuestionNumber ?? 1 }}] = existingIdx;
            }
        }
    @endif

    // Initial question grid state
    updateQuestionGrid();

    // Auto-save every 30 seconds
    setInterval(function() {
        const currentQ  = {{ $currentQuestionNumber ?? 1 }};
        if (answers[currentQ] !== undefined) {
            saveAnswer(currentQ, answers[currentQ]);
        }
    }, 30000);

    // Close modal on backdrop click
    document.getElementById('submitModal').addEventListener('click', function(e) {
        if (e.target === this) closeSubmitModal();
    });
});

// Warn before leaving if answers recorded
window.addEventListener('beforeunload', function(e) {
    if (Object.keys(answers).length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection