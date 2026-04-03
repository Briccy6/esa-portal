@extends('layouts.public')

@section('title', 'Public Exam - ' . $exam->title)

@push('styles')
<style>
/* Modern Moodle-inspired Exam Interface */
.exam-container {
    max-width: 1200px; margin: 0 auto; padding: 20px;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.exam-header {
    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    color: white; border-radius: 16px; padding: 32px; margin-bottom: 24px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); position: relative; overflow: hidden;
}

.exam-title { font-size: 2rem; font-weight: 700; margin-bottom: 8px; }
.exam-subtitle { color: rgba(255, 255, 255, 0.9); font-size: 1.1rem; margin-bottom: 24px; }
.exam-stats { display: flex; gap: 32px; align-items: center; }
.stat-item { text-align: center; }
.stat-value { font-size: 1.5rem; font-weight: 700; display: block; }
.stat-label { font-size: 0.875rem; color: rgba(255,255,255,0.8); text-transform: uppercase; }

.progress-bar { background: rgba(255,255,255,0.2); height: 8px; border-radius: 4px; overflow: hidden; margin-top: 16px; }
.progress-fill { background: #10b981; height: 100%; border-radius: 4px; transition: width 0.3s ease; }

.exam-content { display: grid; grid-template-columns: 1fr 320px; gap: 24px; align-items: start; }

.question-card {
    background: white; border-radius: 16px; padding: 32px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.07); border: 1px solid #e5e7eb;
}

.question-header {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #f3f4f6;
}

.question-number {
    background: #3b82f6; color: white; padding: 8px 16px;
    border-radius: 8px; font-weight: 600; font-size: 0.875rem;
}

.marks-badge {
    background: #fef3c7; color: #92400e; padding: 4px 12px;
    border-radius: 6px; font-size: 0.875rem; font-weight: 600;
}

.question-text {
    font-size: 1.25rem; line-height: 1.7; color: #1f2937;
    margin-bottom: 32px; font-weight: 500;
}

.answer-options { display: flex; flex-direction: column; gap: 12px; }

.answer-option {
    background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 12px;
    padding: 20px; cursor: pointer; transition: all 0.2s ease;
    position: relative; display: flex; align-items: center; gap: 16px;
}

.answer-option:hover {
    border-color: #3b82f6; background: #eff6ff;
    transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59,130,246,0.15);
}

.answer-option.selected {
    border-color: #3b82f6; background: #dbeafe;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
}

.option-indicator {
    width: 24px; height: 24px; border: 2px solid #d1d5db;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    transition: all 0.2s ease; flex-shrink: 0;
}

.answer-option.selected .option-indicator { border-color: #3b82f6; background: #3b82f6; }

.option-dot {
    width: 8px; height: 8px; background: white; border-radius: 50%;
    opacity: 0; transform: scale(0); transition: all 0.2s ease;
}

.answer-option.selected .option-dot { opacity: 1; transform: scale(1); }

.option-text { flex: 1; font-size: 1.1rem; color: #374151; font-weight: 500; }

.option-letter {
    background: #e5e7eb; color: #6b7280; padding: 4px 8px;
    border-radius: 4px; font-size: 0.875rem; font-weight: 600; margin-right: 12px;
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

.sidebar { display: flex; flex-direction: column; gap: 20px; }
.sidebar-card {
    background: white; border-radius: 12px; padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.06); border: 1px solid #e5e7eb;
}
.sidebar-title {
    font-size: 1rem; font-weight: 700; color: #1f2937;
    margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
}

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

.flag-btn {
    background: #f3f4f6; border: 1px solid #d1d5db; border-radius: 8px;
    padding: 8px 16px; font-size: 0.875rem; font-weight: 600;
    color: #6b7280; cursor: pointer; transition: all 0.2s ease;
}

.flag-btn:hover { background: #fef3c7; border-color: #f59e0b; color: #92400e; }
.flag-btn.flagged { background: #f59e0b; border-color: #f59e0b; color: white; }

@media (max-width: 1024px) {
    .exam-content { grid-template-columns: 1fr; }
    .sidebar { order: -1; }
    .question-nav { grid-template-columns: repeat(8, 1fr); }
}
</style>
@endpush

@section('content')
<div class="exam-container">
    <!-- Exam Header -->
    <div class="exam-header">
        <div>
            <h1 class="exam-title">{{ $exam->title }}</h1>
            <p class="exam-subtitle">{{ $exam->description ?? 'Public Examination' }}</p>
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
                    <button type="button" class="flag-btn" id="flagBtn" onclick="toggleFlag()">
                        <i class="fas fa-flag"></i> <span id="flagText">Flag</span>
                    </button>
                    <div class="marks-badge">{{ $currentQuestion->points ?? $currentQuestion->marks ?? 5 }} marks</div>
                </div>
            </div>

            <div class="question-text" id="questionText">
                {!! $currentQuestion->text !!}
            </div>

            <form id="answerForm" onsubmit="event.preventDefault(); event.stopPropagation(); return submitExam();">
                @csrf
                <div class="answer-options">
                    @foreach($currentQuestion->options ?? [] as $index => $option)
                        <label class="answer-option" data-option="{{ $index }}">
                            <input type="radio" name="answer" value="{{ $index }}" class="sr-only">
                            <div class="option-indicator">
                                <div class="option-dot"></div>
                            </div>
                            <div class="option-text">
                                <span class="option-letter">{{ chr(65 + $index) }}</span>
                                <div class="option-content">{!! $option !!}</div>
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="navigation">
                    @if($questionNumber > 1)
                        <button type="button" class="nav-btn secondary" onclick="goToQuestion({{ $questionNumber - 1 }})">
                            <i class="fas fa-arrow-left"></i> Previous
                        </button>
                    @else
                        <div></div>
                    @endif

                    <div class="d-flex gap-2">
                        @if($questionNumber < $totalQuestions)
                            <button type="button" id="nextBtn" class="nav-btn primary" disabled onclick="goToQuestion({{ $questionNumber + 1 }})">
                                Next <i class="fas fa-arrow-right"></i>
                            </button>
                        @else
                            <button type="submit" id="submitBtn" class="nav-btn success" disabled>
                                <i class="fas fa-check-circle"></i> Submit Exam
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Question Navigation -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <i class="fas fa-list-ol"></i> Question Navigator
                </div>
                <div class="question-nav">
                    @for($i = 1; $i <= $totalQuestions; $i++)
                        <button type="button" id="nav-question-{{ $i }}" class="question-btn {{ $i == $questionNumber ? 'current' : '' }}" onclick="goToQuestion({{ $i }})">
                            {{ $i }}
                        </button>
                    @endfor
                </div>
            </div>

            <!-- Exam Info -->
            <div class="sidebar-card">
                <div class="sidebar-title">
                    <i class="fas fa-info-circle"></i> Exam Information
                </div>
                <ul class="info-list">
                    <li><strong>Total Questions:</strong> {{ $totalQuestions }}</li>
                    <li><strong>Total Marks:</strong> {{ $exam->total_marks ?? 100 }}</li>
                    <li><strong>Session:</strong> {{ $sessionId }}</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
let answers = {};
let flaggedQuestions = new Set();

// Initialize from session data
const sessionData = @json($sessionData);
if (sessionData.answers) answers = sessionData.answers;
if (sessionData.flagged) flaggedQuestions = new Set(Object.keys(sessionData.flagged).filter(q => sessionData.flagged[q]));

// Restore existing answer if any
@if(isset($sessionData['answers'][$questionNumber]))
    const existingAnswer = '{{ $sessionData['answers'][$questionNumber] }}';
    const answerOption = document.querySelector(`.answer-option[data-option="${existingAnswer}"]`);
    if (answerOption) {
        const radio = answerOption.querySelector('input[type="radio"]');
        answerOption.classList.add('selected');
        radio.checked = true;
        document.getElementById('nextBtn').disabled = false;
        @if($questionNumber == $totalQuestions)
            document.getElementById('submitBtn').disabled = false;
        @endif
    }
@endif

// Restore flag state
@if(isset($sessionData['flagged'][$questionNumber]))
    const flagBtn = document.getElementById('flagBtn');
    const flagText = document.getElementById('flagText');
    flagBtn.classList.add('flagged');
    flagText.textContent = 'Unflag';
    
    const questionBtn = document.getElementById('nav-question-{{ $questionNumber }}');
    if (questionBtn) questionBtn.classList.add('flagged');
@endif

function saveAnswer(questionNumber, answer) {
    answers[questionNumber] = answer;
    
    fetch('{{ route('public.exams.save-answer', [$exam, $sessionId, $questionNumber]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ answer: answer })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Answer saved successfully');
        }
    })
    .catch(error => console.error('Error saving answer:', error));
}

function toggleFlag() {
    const flagBtn = document.getElementById('flagBtn');
    const flagText = document.getElementById('flagText');
    const questionBtn = document.getElementById('nav-question-{{ $questionNumber }}');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    const isFlagged = flaggedQuestions.has({{ $questionNumber }});
    
    if (isFlagged) {
        flaggedQuestions.delete({{ $questionNumber }});
        flagBtn.classList.remove('flagged');
        flagText.textContent = 'Flag';
        if (questionBtn) questionBtn.classList.remove('flagged');
    } else {
        flaggedQuestions.add({{ $questionNumber }});
        flagBtn.classList.add('flagged');
        flagText.textContent = 'Unflag';
        if (questionBtn) questionBtn.classList.add('flagged');
    }
    
    // Enable navigation when flagged
    if (nextBtn) nextBtn.disabled = false;
    if (submitBtn) submitBtn.disabled = false;
    
    // Save flag state
    fetch('{{ route('public.exams.save.session', [$exam, $sessionId]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            flagged: Array.from(flaggedQuestions).reduce((obj, key) => {
                obj[key] = true;
                return obj;
            }, {})
        })
    });
}

function goToQuestion(questionNum) {
    window.location.href = '{{ route('public.exams.take.question', [$exam, $sessionId, ':question']) }}'.replace(':question', questionNum);
}

function submitExam() {
    if (confirm('Are you sure you want to submit this exam? You cannot change your answers after submission.')) {
        // Prevent any default browser behavior
        event.preventDefault();
        
        // Create a form and submit it via POST
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route('public.exams.submit', [$exam, $sessionId]) }}';
        form.style.display = 'none'; // Hide the form
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add the form to body and submit
        document.body.appendChild(form);
        form.submit();
        
        // Prevent any further navigation
        return false;
    }
    return false;
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.answer-option').forEach(function(option, index) {
        option.addEventListener('click', function() {
            document.querySelectorAll('.answer-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            
            const radio = this.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
            
            const nextBtn = document.getElementById('nextBtn');
            const submitBtn = document.getElementById('submitBtn');
            
            if (nextBtn) nextBtn.disabled = false;
            if (submitBtn) submitBtn.disabled = false;
            
            saveAnswer({{ $questionNumber }}, index);
        });
    });
    
    // Keyboard shortcut for flag (F key)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'f' || e.key === 'F') {
            toggleFlag();
        }
    });
});
</script>

<!-- MathJax Configuration for Mathematical Expressions -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
window.MathJax = {
    tex: {
        inlineMath: [['$', '$'], ['\\(', '\\)']],
        displayMath: [['$$', '$$'], ['\\[', '\\]']],
        processEscapes: true,
        processEnvironments: true
    },
    options: {
        skipHtmlTags: ['script', 'noscript', 'style', 'textarea', 'pre']
    }
};

// Re-render MathJax when page loads
document.addEventListener('DOMContentLoaded', function() {
    if (window.MathJax && window.MathJax.typesetPromise) {
        window.MathJax.typesetPromise([document.getElementById('questionText')]);
    }
});

// Re-render MathJax when navigating to questions
function renderMathJax() {
    if (window.MathJax && window.MathJax.typesetPromise) {
        window.MathJax.typesetPromise([document.getElementById('questionText')]);
    }
}
</script>
@endsection
