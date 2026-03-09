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
                    {{ $currentQuestion->text }}
                @else
                    No question available
                @endif
            </div>

            @if(isset($currentQuestion))
            <!-- Answer Options -->
            <form method="POST" action="{{ route('exams.save-answer', [$exam, $session, $currentQuestionNumber]) }}">
                @csrf
                <div class="answer-options">
                    @foreach($currentQuestion->options ?? [] as $index => $option)
                        <label class="answer-option">
                            <input type="radio" name="answer" value="{{ $index }}" class="sr-only"
                                   {{ isset($existingAnswer) && $existingAnswer->answer == $index ? 'checked' : '' }}>
                            <div class="option-indicator">
                                <div class="option-dot"></div>
                            </div>
                            <div class="option-text">
                                <span class="option-letter">{{ chr(65 + $index) }}</span>
                                {{ $option }}
                            </div>
                        </label>
                    @endforeach
                </div>

                <!-- Navigation -->
                <div class="navigation">
                    @if($currentQuestionNumber > 1)
                        <button type="submit" name="action" value="previous" class="nav-btn secondary">
                            <i class="fas fa-arrow-left"></i>
                            Previous
                        </button>
                    @else
                        <div></div>
                    @endif
                    
                    <div class="d-flex gap-2">
                        @if($currentQuestionNumber < $questions->count())
                            <button type="submit" name="action" value="next" class="nav-btn primary">
                                Next
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        @else
                            <button type="submit" name="action" value="submit" class="nav-btn success">
                                <i class="fas fa-check-circle"></i>
                                Submit Exam
                            </button>
                        @endif
                            <div class="w-4 h-4 bg-blue-600 rounded mr-2"></div>
                            <span>Current</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-4 h-4 bg-green-600 rounded mr-2"></div>
                            <span>Answered</span>
                        </div>
                        <div class="flex items-center text-sm">
                            <div class="w-4 h-4 bg-yellow-500 rounded mr-2"></div>
                            <span>Marked for Review</span>
                        </div>
                    </div>
                </div>

                <!-- Exam Info -->
                <div class="bg-white rounded-2xl shadow-xl p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Exam Information</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Duration</span>
                            <span class="font-medium">{{$exam->duration ?? 60}} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Questions</span>
                            <span class="font-medium">{{isset($questions) ? $questions->count() : 0}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Marks</span>
                            <span class="font-medium">{{$exam->total_marks ?? 100}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tokens Used</span>
                            <span class="font-medium text-blue-600">{{$exam->token_cost ?? 0}}</span>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-blue-50 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-blue-900 mb-3">Instructions</h3>
                    <ul class="text-sm text-blue-800 space-y-2">
                        <li>• Select one answer for each question</li>
                        <li>• Use the navigator to jump between questions</li>
                        <li>• Mark questions for review if unsure</li>
                        <li>• Submit exam when you're finished</li>
                        <li>• You cannot change answers after submission</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div id="submitModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Submit Exam?</h3>
                <p class="text-gray-600 mb-6">Are you sure you want to submit your exam? You cannot change your answers after submission.</p>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-yellow-800">
                            Unanswered questions: <span id="unansweredCount">0</span>
                        </span>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <button onclick="closeSubmitModal()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        Cancel
                    </button>
                    <button onclick="confirmSubmit()" 
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                        Submit Exam
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentQuestion = {{$currentQuestionNumber ?? 1}};
let totalQuestions = {{isset($questions) ? $questions->count() : 0}};
let answers = {};
let markedForReview = new Set();
let timeRemaining = {{($exam->duration ?? 60) * 60}}; // in seconds

// Timer
function updateTimer() {
    const hours = Math.floor(timeRemaining / 3600);
    const minutes = Math.floor((timeRemaining % 3600) / 60);
    const seconds = timeRemaining % 60;
    
    document.getElementById('timer').textContent = 
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    if (timeRemaining > 0) {
        timeRemaining--;
        setTimeout(updateTimer, 1000);
    } else {
        submitExam();
    }
}

// Answer selection
function selectAnswer(optionIndex) {
    // Remove previous selection
    document.querySelectorAll('.answer-option').forEach(option => {
        option.classList.remove('border-blue-500', 'bg-blue-50');
    });
    
    // Add selection to current option
    const selectedOption = document.querySelectorAll('.answer-option')[optionIndex];
    selectedOption.classList.add('border-blue-500', 'bg-blue-50');
    
    // Update radio button
    document.querySelectorAll('[id^="option-dot-"]').forEach(dot => {
        dot.classList.add('hidden');
    });
    document.getElementById(`option-dot-${optionIndex}`).classList.remove('hidden');
    
    // Store answer
    answers[currentQuestion] = optionIndex;
    updateQuestionGrid();
}

// Navigation
function nextQuestion() {
    if (currentQuestion < totalQuestions) {
        jumpToQuestion(currentQuestion + 1);
    }
}

function previousQuestion() {
    if (currentQuestion > 1) {
        jumpToQuestion(currentQuestion - 1);
    }
}

function jumpToQuestion(questionNumber) {
    // Save current answer before moving
    if (answers[currentQuestion] !== undefined) {
        // Save to server via AJAX
        saveAnswer(currentQuestion, answers[currentQuestion]);
    }
    
    currentQuestion = questionNumber;
    // Load new question via AJAX or reload page
    window.location.href = `{{route('exams.take-question', [$exam->id, ':session'])}}`.replace(':session', '{{$session ?? 'current'}}') + '/' + questionNumber;
}

function markForReview() {
    if (markedForReview.has(currentQuestion)) {
        markedForReview.delete(currentQuestion);
    } else {
        markedForReview.add(currentQuestion);
    }
    updateQuestionGrid();
}

function updateQuestionGrid() {
    for (let i = 1; i <= totalQuestions; i++) {
        const button = document.getElementById(`nav-question-${i}`);
        button.classList.remove('bg-blue-600', 'bg-green-600', 'bg-yellow-500', 'text-white');
        button.classList.add('border-gray-300');
        
        if (i === currentQuestion) {
            button.classList.add('bg-blue-600', 'text-white');
            button.classList.remove('border-gray-300');
        } else if (answers[i] !== undefined) {
            if (markedForReview.has(i)) {
                button.classList.add('bg-yellow-500', 'text-white');
            } else {
                button.classList.add('bg-green-600', 'text-white');
            }
            button.classList.remove('border-gray-300');
        }
    }
}

// Submit
function submitExam() {
    const unanswered = Object.keys(answers).length;
    document.getElementById('unansweredCount').textContent = totalQuestions - unanswered;
    document.getElementById('submitModal').classList.remove('hidden');
}

function closeSubmitModal() {
    document.getElementById('submitModal').classList.add('hidden');
}

function confirmSubmit() {
    // Submit all answers via AJAX
    fetch(`{{route('exams.submit', [$exam->id, ':session'])}}`.replace(':session', '{{$session ?? 'current'}}'), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        },
        body: JSON.stringify({
            answers: answers,
            marked_for_review: Array.from(markedForReview)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        }
    })
    .catch(error => {
        console.error('Error submitting exam:', error);
    });
}

function saveAnswer(questionNumber, answer) {
    // Save answer to server via AJAX
    fetch(`/exams/{{$exam->id}}/save-answer/{{$session ?? 'current'}}/${questionNumber}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{csrf_token()}}'
        },
        body: JSON.stringify({ answer: answer })
    })
    .catch(error => {
        console.error('Error saving answer:', error);
    });
}

// Initialize
updateTimer();
updateQuestionGrid();

// Auto-save every 30 seconds
setInterval(() => {
    if (answers[currentQuestion] !== undefined) {
        saveAnswer(currentQuestion, answers[currentQuestion]);
    }
}, 30000);

// Prevent leaving the page
window.addEventListener('beforeunload', (e) => {
    if (Object.keys(answers).length > 0) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endsection
