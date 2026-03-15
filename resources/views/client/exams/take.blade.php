@extends('layouts.client')

@section('title', 'Taking Exam - ESA Portal')

@push('styles')
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

    /* Exam Container */
    .exam-container {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    @media (max-width: 900px) {
        .exam-container {
            grid-template-columns: 1fr;
            gap: 20px;
        }
    }

    /* Question Card */
    .question-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 32px;
        position: relative;
        overflow: hidden;
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border);
    }

    .question-number {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .question-progress {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--text-muted);
    }

    .progress-text {
        font-weight: 500;
    }

    .progress-badge {
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 11px;
    }

    .question-content {
        margin-bottom: 32px;
    }

    .question-text {
        font-size: 18px;
        line-height: 1.6;
        color: var(--text-primary);
        margin-bottom: 24px;
        font-weight: 500;
    }

    /* Answer Options */
    .answers-container {
        display: grid;
        gap: 12px;
    }

    .answer-option {
        display: flex;
        align-items: flex-start;
        gap: 16px;
        padding: 20px;
        background: var(--surface-2);
        border: 2px solid var(--border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .answer-option::before {
        content: '';
        position: absolute;
        top: 0; left: 0;
        right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(79,142,247,0.05), transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
        pointer-events: none;
    }

    .answer-option:hover {
        border-color: rgba(79,142,247,0.4);
        background: var(--surface);
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    }

    .answer-option:hover::before {
        opacity: 1;
    }

    .answer-option.selected {
        border-color: var(--accent);
        background: rgba(79,142,247,0.1);
        box-shadow: 0 4px 12px rgba(79,142,247,0.2);
    }

    .answer-option.selected::before {
        opacity: 1;
        background: linear-gradient(135deg, rgba(79,142,247,0.1), transparent);
    }

    .answer-radio {
        width: 20px;
        height: 20px;
        border: 2px solid var(--border);
        border-radius: 50%;
        background: var(--surface);
        position: relative;
        cursor: pointer;
        transition: all 0.2s ease;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .answer-radio::after {
        content: '';
        position: absolute;
        top: 50%; left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--text-muted);
        transition: all 0.2s ease;
    }

    .answer-option.selected .answer-radio {
        border-color: var(--accent);
        background: var(--accent);
    }

    .answer-option.selected .answer-radio::after {
        background: #fff;
        width: 6px;
        height: 6px;
    }

    .answer-label {
        flex: 1;
        font-size: 16px;
        line-height: 1.5;
        color: var(--text-primary);
        font-weight: 500;
    }

    .answer-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        background: rgba(79,142,247,0.15);
        color: var(--accent);
        border-radius: 8px;
        font-weight: 700;
        font-size: 12px;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .answer-option.selected .answer-letter {
        background: var(--accent);
        color: #fff;
    }

    /* Navigation */
    .navigation {
        margin-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .nav-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 14px 24px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        min-width: 120px;
    }

    .nav-btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    .nav-btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .btn-prev {
        background: var(--surface-2);
        color: var(--text-muted);
        border: 1px solid var(--border);
    }

    .btn-prev:hover:not(:disabled) {
        background: var(--surface);
        border-color: rgba(79,142,247,0.3);
        color: var(--text-primary);
        transform: translateY(-2px);
    }

    .btn-next {
        background: linear-gradient(135deg, var(--accent), var(--accent-2));
        color: #fff;
        box-shadow: 0 4px 12px rgba(79,142,247,0.3);
    }

    .btn-next:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(79,142,247,0.4);
    }

    .btn-submit {
        background: linear-gradient(135deg, var(--accent-3), var(--accent));
        color: #fff;
        box-shadow: 0 4px 12px rgba(52,211,153,0.3);
    }

    .btn-submit:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(52,211,153,0.4);
    }

    /* Question Navigator */
    .question-navigator {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 20px;
        height: fit-content;
    }

    .navigator-title {
        font-family: 'Syne', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 16px;
        text-align: center;
    }

    .navigator-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-bottom: 16px;
    }

    @media (max-width: 400px) {
        .navigator-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .question-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        border: 2px solid var(--border);
        border-radius: 8px;
        background: var(--surface-2);
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        position: relative;
    }

    .question-btn:hover {
        border-color: rgba(79,142,247,0.3);
        background: rgba(79,142,247,0.1);
        color: var(--accent);
        transform: translateY(-1px);
    }

    .question-btn.current {
        border-color: var(--accent);
        background: var(--accent);
        color: #fff;
        box-shadow: 0 2px 8px rgba(79,142,247,0.3);
    }

    .question-btn.answered {
        border-color: var(--accent-3);
        background: rgba(52,211,153,0.1);
        color: var(--accent-3);
    }

    .question-btn.flagged {
        border-color: var(--accent-warm);
        background: rgba(251,191,36,0.1);
        color: var(--accent-warm);
    }

    .question-btn.flagged::after {
        content: '🚩';
        position: absolute;
        top: -6px; right: -6px;
        font-size: 10px;
    }

    /* Timer */
    .timer-display {
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 12px 16px;
        text-align: center;
        margin-bottom: 16px;
    }

    .timer-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 4px;
    }

    .timer-value {
        font-family: 'Syne', sans-serif;
        font-size: 24px;
        font-weight: 800;
        color: var(--accent);
        font-variant-numeric: tabular-nums;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-content {
            padding: 20px;
        }
        
        .question-card {
            padding: 24px;
        }
        
        .question-text {
            font-size: 16px;
        }
        
        .answer-option {
            padding: 16px;
            gap: 12px;
        }
        
        .navigator-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>
@endpush

@section('content')
<!-- Page Header -->
<div class="topbar">
    <div class="topbar-left">
        <h1>{{ $exam->title }}</h1>
        <p>Question {{ $questionNumber }} of {{ $questions->count() }}</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.exams.show', $exam->id) }}" class="btn-prev">
            <i class="fas fa-times"></i>
            Exit Exam
        </a>
    </div>
</div>

<!-- Exam Container -->
<div class="exam-container">
    <!-- Question Card -->
    <div class="question-card">
        <!-- Question Header -->
        <div class="question-header">
            <div class="question-number">Question {{ $questionNumber }}</div>
            <div class="question-progress">
                <span class="progress-text">Progress:</span>
                <span class="progress-badge">{{ $questionNumber }}/{{ $questions->count() }}</span>
            </div>
        </div>
        
        <!-- Question Content -->
        <div class="question-content">
            <div class="question-text">
                {!! $currentQuestion->text !!}
            </div>
            
            <!-- Answer Options -->
            <div class="answers-container">
                @foreach($currentQuestion->options as $option)
                    <label class="answer-option" data-option="{{ $option->label }}" onclick="selectAnswer('{{ $option->label }}')">
                        <input type="radio" 
                               name="answer" 
                               value="{{ $option->label }}" 
                               class="answer-radio"
                               @if(isset($answers[$questionNumber]) && $answers[$questionNumber] === $option->label) checked @endif>
                        <span class="answer-letter">{{ $option->label }}</span>
                        <div class="answer-label">
                            <div>{{ $option->text }}</div>
                            @if($option->explanation)
                                <div style="font-size: 13px; color: var(--text-muted); margin-top: 8px;">
                                    💡 {{ $option->explanation }}
                                </div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="navigation">
            @if($questionNumber > 1)
                <a href="{{ route('client.exams.take.question', [$exam->id, $session, $questionNumber - 1]) }}" 
                   class="nav-btn btn-prev">
                    <i class="fas fa-arrow-left"></i>
                    Previous
                </a>
            @endif
            
            @if($questionNumber < $questions->count())
                <button id="nextBtn" 
                        class="nav-btn btn-next" 
                        onclick="proceedToNext()"
                        @if(!isset($answers[$questionNumber])) disabled @endif>
                    <i class="fas fa-arrow-right"></i>
                    Next
                </button>
            @else
                <button id="submitBtn" 
                        class="nav-btn btn-submit" 
                        onclick="submitExam()"
                        @if(!isset($answers[$questionNumber])) disabled @endif>
                    <i class="fas fa-check"></i>
                    Submit Exam
                </button>
            @endif
        </div>
    </div>
    
    <!-- Question Navigator -->
    <div>
        <!-- Timer Display -->
        <div class="timer-display">
            <div class="timer-label">Time Remaining</div>
            <div class="timer-value" id="timer">00:00:00</div>
        </div>
        
        <!-- Question Navigator -->
        <div class="question-navigator">
            <div class="navigator-title">Question Navigator</div>
            <div class="navigator-grid">
                @for($i = 1; $i <= $questions->count(); $i++)
                    <a href="{{ route('client.exams.take.question', [$exam->id, $session, $i]) }}" 
                       class="question-btn 
                           @if($i == $questionNumber) current @endif
                           @if(isset($answers[$i])) answered @endif
                           @if(isset($flagged[$i])) flagged @endif">
                        {{ $i }}
                    </a>
                @endfor
            </div>
        </div>
    </div>
</div>

<script>
    let currentAnswers = @json($answers);
    let currentFlagged = @json($flagged);

    function selectAnswer(option) {
        // Remove previous selection
        document.querySelectorAll('.answer-option').forEach(el => el.classList.remove('selected'));
        document.querySelectorAll('.answer-radio').forEach(el => el.checked = false);
        
        // Add new selection
        const selectedOption = document.querySelector(`[data-option="${option}"]`);
        selectedOption.classList.add('selected');
        selectedOption.querySelector('.answer-radio').checked = true;
        
        // Save answer
        saveAnswer(option);
        
        // Enable next button
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        if (nextBtn) nextBtn.disabled = false;
        if (submitBtn) submitBtn.disabled = false;
    }

    function saveAnswer(answer) {
        fetch('{{ route('client.exams.save-answer', [$exam->id, $session, $questionNumber]) }}', {
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
                currentAnswers[{{ $questionNumber }}] = answer;
                updateNavigator();
            }
        })
        .catch(error => console.error('Error saving answer:', error));
    }

    function proceedToNext() {
        window.location.href = '{{ route('client.exams.take.question', [$exam->id, $session, $questionNumber + 1]) }}';
    }

    function submitExam() {
        if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
            window.location.href = '{{ route('client.exams.submit', [$exam->id, $session]) }}';
        }
    }

    function updateNavigator() {
        // Update answered and flagged states
        document.querySelectorAll('.question-btn').forEach((btn, index) => {
            const questionNum = index + 1;
            btn.classList.remove('answered', 'flagged');
            
            if (currentAnswers[questionNum]) {
                btn.classList.add('answered');
            }
            
            if (currentFlagged[questionNum]) {
                btn.classList.add('flagged');
            }
        });
    }

    // Initialize navigator on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateNavigator();
    });
</script>
@endsection
