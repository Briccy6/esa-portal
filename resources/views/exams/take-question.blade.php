@extends('layouts.app')

@section('title', 'Take Exam - ' . $exam->title)

@section('styles')
<style>
.exam-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.exam-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
}

.question-card {
    background: white;
    border-radius: 10px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.question-palette {
    position: sticky;
    top: 20px;
    background: white;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.question-number {
    display: inline-block;
    width: 40px;
    height: 40px;
    line-height: 40px;
    text-align: center;
    border-radius: 50%;
    margin: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s ease;
}

.question-number.answered {
    background: #28a745;
    color: white;
}

.question-number.current {
    background: #007bff;
    color: white;
    transform: scale(1.1);
}

.question-number.unanswered {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
    color: #495057;
}

.question-number:hover {
    transform: scale(1.05);
}

.timer {
    font-size: 24px;
    font-weight: bold;
    color: #dc3545;
}

.violation-warning {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
    padding: 10px;
    border-radius: 5px;
    margin-bottom: 20px;
    display: none;
}

.fullscreen-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    display: none;
}

.navigation-buttons {
    margin-top: 30px;
    text-align: center;
}

.btn-exam {
    padding: 12px 30px;
    font-size: 16px;
    border-radius: 25px;
    transition: all 0.3s ease;
}

.btn-exam:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.option-label {
    display: block;
    padding: 15px;
    margin: 10px 0;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.option-label:hover {
    border-color: #007bff;
    background: #f8f9ff;
}

.option-label.selected {
    border-color: #007bff;
    background: #e3f2fd;
}

input[type="radio"]:checked + .option-label,
input[type="checkbox"]:checked + .option-label {
    border-color: #28a745;
    background: #d4edda;
}

.essay-answer {
    width: 100%;
    min-height: 150px;
    padding: 15px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 16px;
    resize: vertical;
}

.essay-answer:focus {
    border-color: #007bff;
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

@media (max-width: 768px) {
    .exam-container {
        padding: 10px;
    }
    
    .question-palette {
        position: relative;
        margin-bottom: 20px;
    }
    
    .question-number {
        width: 35px;
        height: 35px;
        line-height: 35px;
        margin: 3px;
    }
}
</style>
@endsection

@section('content')
<div class="exam-container">
    <!-- Exam Header -->
    <div class="exam-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2>{{ $exam->title }}</h2>
                <p class="mb-0">{{ $exam->subject }} | Question {{ $questionNumber }} of {{ $totalQuestions }}</p>
            </div>
            <div class="col-md-4 text-right">
                <div class="timer" id="examTimer">
                    <i class="fas fa-clock"></i> 
                    <span id="timeDisplay">{{ gmdate('H:i:s', $session->time_remaining_seconds) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Violation Warning -->
    <div class="violation-warning" id="violationWarning">
        <strong>Warning:</strong> <span id="violationMessage"></span>
        <div class="mt-2">
            <small>Violations remaining: <span id="violationsRemaining">{{ $exam->settings->max_tab_switches - $session->tab_switches }}</span></small>
        </div>
    </div>

    <div class="row">
        <!-- Question Content -->
        <div class="col-lg-8">
            <div class="question-card">
                <form id="examForm" method="POST" action="{{ route('exams.save-answer', [$exam, $session, $questionNumber]) }}">
                    @csrf
                    
                    <div class="question-text mb-4">
                        <h4>Question {{ $questionNumber }}</h4>
                        <p class="lead">{{ $question->text }}</p>
                        <p class="text-muted">Marks: {{ $question->marks }} {{ $question->weight > 1 ? '(Weight: ' . $question->weight . ')' : '' }}</p>
                    </div>

                    <!-- Multiple Choice -->
                    @if($question->question_type === 'multiple_choice')
                        <div class="options">
                            @foreach($options as $key => $option)
                                <div class="form-check">
                                    <input type="radio" name="answer" value="{{ $key }}" id="option_{{ $key }}" 
                                           class="form-check-input" {{ $existingAnswer->answer === $key ? 'checked' : '' }}>
                                    <label class="option-label" for="option_{{ $key }}">
                                        <strong>{{ $key }}.</strong> {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                    <!-- Multiple Select -->
                    @elseif($question->question_type === 'multiple_select')
                        <div class="options">
                            @foreach($options as $key => $option)
                                <div class="form-check">
                                    <input type="checkbox" name="selected_options[]" value="{{ $key }}" id="option_{{ $key }}" 
                                           class="form-check-input" 
                                           @if($existingAnswer && in_array($key, $existingAnswer->selected_options ?? [])) checked @endif>
                                    <label class="option-label" for="option_{{ $key }}">
                                        <strong>{{ $key }}.</strong> {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                    <!-- True/False -->
                    @elseif($question->question_type === 'true_false')
                        <div class="options">
                            <div class="form-check">
                                <input type="radio" name="answer" value="True" id="option_true" 
                                       class="form-check-input" {{ $existingAnswer->answer === 'True' ? 'checked' : '' }}>
                                <label class="option-label" for="option_true">
                                    <strong>True</strong>
                                </label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="answer" value="False" id="option_false" 
                                       class="form-check-input" {{ $existingAnswer->answer === 'False' ? 'checked' : '' }}>
                                <label class="option-label" for="option_false">
                                    <strong>False</strong>
                                </label>
                            </div>
                        </div>

                    <!-- Short Answer -->
                    @elseif($question->question_type === 'short_answer')
                        <div class="form-group">
                            <label for="shortAnswer" class="form-label">Your Answer:</label>
                            <textarea name="answer" id="shortAnswer" class="form-control essay-answer" 
                                      rows="3" placeholder="Enter your answer here...">{{ $existingAnswer->answer ?? '' }}</textarea>
                            <small class="form-text text-muted">Provide a brief answer (1-2 sentences).</small>
                        </div>

                    <!-- Essay -->
                    @elseif($question->question_type === 'essay')
                        <div class="form-group">
                            <label for="essayAnswer" class="form-label">Your Answer:</label>
                            <textarea name="answer" id="essayAnswer" class="form-control essay-answer" 
                                      rows="8" placeholder="Write your detailed answer here...">{{ $existingAnswer->answer ?? '' }}</textarea>
                            <small class="form-text text-muted">Provide a comprehensive answer. This will be graded manually.</small>
                        </div>
                    @endif

                    <!-- Navigation Buttons -->
                    <div class="navigation-buttons">
                        <div class="row">
                            <div class="col-md-4">
                                @if($questionNumber > 1)
                                    <button type="submit" name="action" value="previous" class="btn btn-secondary btn-exam btn-block">
                                        <i class="fas fa-arrow-left"></i> Previous
                                    </button>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-warning btn-exam btn-block" onclick="saveAndContinue()">
                                    <i class="fas fa-save"></i> Save & Continue
                                </button>
                            </div>
                            <div class="col-md-4">
                                @if($questionNumber < $totalQuestions)
                                    <button type="submit" name="action" value="next" class="btn btn-primary btn-exam btn-block">
                                        Next <i class="fas fa-arrow-right"></i>
                                    </button>
                                @else
                                    <button type="submit" name="action" value="submit" class="btn btn-success btn-exam btn-block" 
                                            onclick="return confirm('Are you sure you want to submit your exam?')">
                                        <i class="fas fa-paper-plane"></i> Submit Exam
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Question Palette -->
        <div class="col-lg-4">
            <div class="question-palette">
                <h5 class="mb-3">Question Palette</h5>
                <div class="mb-3">
                    <small class="text-muted">
                        <span class="question-number answered"></span> Answered
                        <span class="question-number current"></span> Current
                        <span class="question-number unanswered"></span> Unanswered
                    </small>
                </div>
                
                <div class="question-numbers">
                    @for($i = 1; $i <= $totalQuestions; $i++)
                        @php
                            $questionId = $session->question_order[$i - 1] ?? null;
                            $isAnswered = $questionId && \App\Models\StudentAnswer::where('attempt_id', $session->attempt_id)
                                ->where('question_id', $questionId)
                                ->whereNotNull('answer')
                                ->exists();
                            $isCurrent = $i == $questionNumber;
                        @endphp
                        
                        <a href="{{ route('exams.take-question', [$exam, $session, $i]) }}" 
                           class="question-number 
                           @if($isAnswered) answered 
                           @elseif($isCurrent) current 
                           @else unanswered @endif">
                            {{ $i }}
                        </a>
                    @endfor
                </div>

                <div class="mt-4">
                    <h6>Exam Info</h6>
                    <ul class="list-unstyled">
                        <li><strong>Duration:</strong> {{ $exam->settings->duration_minutes }} minutes</li>
                        <li><strong>Total Marks:</strong> {{ $exam->questions->sum('marks') }}</li>
                        <li><strong>Passing Score:</strong> {{ $exam->settings->passing_score ?? 'Not set' }}%</li>
                    </ul>
                </div>

                <div class="mt-3">
                    <button type="button" class="btn btn-danger btn-sm btn-block" onclick="submitExam()">
                        <i class="fas fa-paper-plane"></i> Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Fullscreen Overlay -->
<div class="fullscreen-overlay" id="fullscreenOverlay">
    <div class="text-center">
        <h2>Fullscreen Required</h2>
        <p>Please enter fullscreen mode to continue with the exam.</p>
        <button type="button" class="btn btn-primary btn-lg" onclick="enterFullscreen()">
            <i class="fas fa-expand"></i> Enter Fullscreen
        </button>
    </div>
</div>
@endsection

@section('scripts')
<script>
let examTimer;
let timeRemaining = {{ $session->time_remaining_seconds }};
let violations = {{ $session->tab_switches }};
let maxViolations = {{ $exam->settings->max_tab_switches }};

// Timer functionality
function startTimer() {
    examTimer = setInterval(function() {
        timeRemaining--;
        
        if (timeRemaining <= 0) {
            clearInterval(examTimer);
            autoSubmitExam('expired');
            return;
        }
        
        updateTimerDisplay();
        
        // Update server every 30 seconds
        if (timeRemaining % 30 === 0) {
            updateTimeServer();
        }
    }, 1000);
}

function updateTimerDisplay() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;
    
    const display = hours.toString().padStart(2, '0') + ':' + 
                   minutes.toString().padStart(2, '0') + ':' + 
                   seconds.toString().padStart(2, '0');
    
    document.getElementById('timeDisplay').textContent = display;
    
    // Change color when time is running out
    const timerElement = document.getElementById('examTimer');
    if (timeRemaining < 300) { // Less than 5 minutes
        timerElement.style.color = '#dc3545';
    } else if (timeRemaining < 600) { // Less than 10 minutes
        timerElement.style.color = '#ffc107';
    }
}

function updateTimeServer() {
    fetch('{{ route("exams.update-time", [$exam, $session]) ?? "#" }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ time_remaining: timeRemaining })
    });
}

// Security features
function setupSecurity() {
    // Disable right click
    if ({{ $exam->settings->disable_right_click ? 'true' : 'false' }}) {
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            logViolation('right_click');
        });
    }
    
    // Disable copy/paste
    if ({{ $exam->settings->disable_copy_paste ? 'true' : 'false' }}) {
        document.addEventListener('copy', function(e) {
            e.preventDefault();
            logViolation('copy_attempt');
        });
        
        document.addEventListener('paste', function(e) {
            e.preventDefault();
            logViolation('paste_attempt');
        });
    }
    
    // Fullscreen requirement
    if ({{ $exam->settings->require_fullscreen ? 'true' : 'false' }}) {
        checkFullscreen();
        document.addEventListener('fullscreenchange', handleFullscreenChange);
    }
    
    // Tab switching detection
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            logViolation('tab_switch');
        }
    });
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Disable F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+U
        if (e.key === 'F12' || 
            (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J')) ||
            (e.ctrlKey && e.key === 'U')) {
            e.preventDefault();
            logViolation('suspicious_activity');
        }
    });
}

function logViolation(type) {
    violations++;
    
    fetch('{{ route('exams.log-violation', [$exam, $session]) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            violation_type: type,
            description: 'Security violation detected'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.action === 'auto_submit') {
            autoSubmitExam('violations');
        } else {
            updateViolationWarning();
        }
    });
    
    updateViolationWarning();
}

function updateViolationWarning() {
    const warning = document.getElementById('violationWarning');
    const message = document.getElementById('violationMessage');
    const remaining = document.getElementById('violationsRemaining');
    
    if (violations > 0) {
        warning.style.display = 'block';
        message.textContent = 'Security violation detected! Multiple violations will result in automatic submission.';
        remaining.textContent = maxViolations - violations;
        
        if (violations >= maxViolations) {
            message.textContent = 'Maximum violations reached. Exam will be submitted automatically.';
        }
    }
}

function checkFullscreen() {
    if (!document.fullscreenElement) {
        document.getElementById('fullscreenOverlay').style.display = 'flex';
    }
}

function handleFullscreenChange() {
    if (!document.fullscreenElement) {
        logViolation('fullscreen_exit');
    } else {
        document.getElementById('fullscreenOverlay').style.display = 'none';
    }
}

function enterFullscreen() {
    document.documentElement.requestFullscreen().catch(function(err) {
        console.error('Error attempting to enable fullscreen:', err);
    });
}

// Form submission
function saveAndContinue() {
    const form = document.getElementById('examForm');
    const formData = new FormData(form);
    
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.text())
    .then(() => {
        // Move to next question or stay on current
        @if($questionNumber < $totalQuestions)
            window.location.href = '{{ route("exams.take-question", [$exam, $session, $questionNumber + 1]) }}';
        @else
            alert('You are on the last question. Use the Submit Exam button when finished.');
        @endif
    });
}

function submitExam() {
    if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
        autoSubmitExam('manual');
    }
}

function autoSubmitExam(reason) {
    clearInterval(examTimer);
    
    const form = document.getElementById('examForm');
    const formData = new FormData(form);
    formData.append('action', 'submit');
    formData.append('auto_submit_reason', reason);
    
    fetch('{{ route('exams.submit', [$exam, $session]) }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.text())
    .then(() => {
        window.location.href = '{{ route('exams.results', $exam) }}';
    });
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    startTimer();
    setupSecurity();
    
    // Auto-save every 2 minutes
    setInterval(function() {
        saveAndContinue();
    }, 120000);
});

// Prevent leaving the page
window.addEventListener('beforeunload', function(e) {
    if (timeRemaining > 0) {
        e.preventDefault();
        e.returnValue = 'You have an ongoing exam. Are you sure you want to leave?';
        return e.returnValue;
    }
});
</script>
@endsection
