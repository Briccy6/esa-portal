@extends('admin.layout')

@section('title', 'Exam Preview: ' . $exam->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>
            <i class="bi bi-eye"></i> Exam Preview: {{ $exam->title }}
            <small class="text-muted">- Student View Simulation</small>
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Exam
            </a>
            <button class="btn btn-info" onclick="togglePreviewMode()">
                <i class="bi bi-toggle-on"></i> Toggle Preview Mode
            </button>
        </div>
    </div>

    <!-- Preview Mode Indicator -->
    <div class="alert alert-info" id="previewAlert">
        <i class="bi bi-info-circle"></i>
        <strong>Preview Mode:</strong> This is how students will see the exam when taking it. 
        Questions are displayed in random order. <span id="adminInfo" class="badge bg-warning ms-2">Admin Info: Correct answers shown</span>
    </div>

    <!-- Exam Header (Student View) -->
    <div class="bg-white shadow-sm border rounded mb-4">
        <div class="container-fluid px-4 py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="mb-0">{{ $exam->title }}</h3>
                    <p class="text-muted mb-0">{{ $exam->description ?? 'No description provided' }}</p>
                </div>
                <div class="col-md-6">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 text-primary">02:00:00</div>
                            <small class="text-muted">Time Remaining</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-purple-600">{{ $preparedQuestions->count() }}</div>
                            <small class="text-muted">Questions</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-success">{{ $preparedQuestions->sum('points') }}</div>
                            <small class="text-muted">Total Points</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions Navigation -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-grid-3x3-gap"></i> Question Navigation
            </h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2" id="questionNav">
                @foreach($preparedQuestions as $index => $question)
                    <button class="btn btn-outline-primary question-nav-btn" 
                            onclick="showQuestion({{ $index }})"
                            data-question="{{ $index }}">
                        {{ $index + 1 }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Question Display -->
    <div class="row">
        <div class="col-lg-8">
            <div class="bg-white rounded shadow overflow-hidden">
                <!-- Question Header -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">Question <span id="currentQuestionNumber">1</span></h4>
                            <p class="mb-0 opacity-90">Select the best answer</p>
                        </div>
                        <div>
                            <span class="bg-white bg-opacity-20 px-3 py-1 rounded-full">
                                <span id="currentQuestionPoints">5</span> points
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="p-4">
                    @if($preparedQuestions->isNotEmpty())
                        <div id="questionContent">
                            <!-- Questions will be dynamically loaded here -->
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            <h5 class="mt-3">No Questions Available</h5>
                            <p class="text-muted">This exam doesn't have any questions yet.</p>
                            <a href="{{ route('exams.questions.index', $exam->id) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add Questions
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Navigation Buttons -->
                @if($preparedQuestions->isNotEmpty())
                <div class="border-top p-4">
                    <div class="d-flex justify-content-between">
                        <button class="btn btn-outline-secondary" onclick="previousQuestion()" id="prevBtn" disabled>
                            <i class="bi bi-arrow-left"></i> Previous
                        </button>
                        <button class="btn btn-primary" onclick="nextQuestion()" id="nextBtn">
                            Next <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Exam Info Panel -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-info-circle"></i> Exam Information
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Exam Type:</strong></td>
                            <td>{{ $exam->exam_type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Duration:</strong></td>
                            <td>{{ $exam->duration ?? 'N/A' }} minutes</td>
                        </tr>
                        <tr>
                            <td><strong>Total Questions:</strong></td>
                            <td>{{ $preparedQuestions->count() }}</td>
                        </tr>
                        <tr>
                            <td><strong>Total Points:</strong></td>
                            <td>{{ $preparedQuestions->sum('points') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge bg-{{ $exam->is_active ? 'success' : 'secondary' }}">
                                    {{ $exam->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Question Sources -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-database"></i> Question Sources
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $questionSources = $preparedQuestions->groupBy('question_bank_name');
                    @endphp
                    @foreach($questionSources as $source => $questions)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small">{{ $source }}</span>
                            <span class="badge bg-primary">{{ $questions->count() }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Admin Controls -->
            <div class="card" id="adminControls">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-gear"></i> Admin Controls
                    </h6>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="showAnswers" checked>
                        <label class="form-check-label" for="showAnswers">
                            Show Correct Answers
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="showSources" checked>
                        <label class="form-check-label" for="showSources">
                            Show Question Sources
                        </label>
                    </div>
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="printPreview()">
                        <i class="bi bi-printer"></i> Print Preview
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.question-nav-btn.active {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: white !important;
}

.answer-option {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.answer-option:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
}

.answer-option.selected {
    border-color: #0d6efd;
    background-color: #e7f3ff;
}

.answer-option.correct {
    border-color: #198754;
    background-color: #d1e7dd;
}

.answer-option.incorrect {
    border-color: #dc3545;
    background-color: #f8d7da;
}

.question-source {
    font-size: 0.875rem;
    color: #6c757d;
    margin-top: 8px;
    padding: 4px 8px;
    background-color: #f8f9fa;
    border-radius: 4px;
}

/* KaTeX display math centering */
.katex-display {
    margin: 0.5em 0;
    overflow-x: auto;
    overflow-y: hidden;
}

@media print {
    .no-print { display: none !important; }
    .answer-option { break-inside: avoid; }
}
</style>

<script>
let currentQuestionIndex = 0;
let questions = @json($preparedQuestions->toArray());
let showAnswers = true;
let showSources = true;

/**
 * Renders LaTeX mathematical expressions using KaTeX.
 * KaTeX is loaded synchronously in the layout <head>, so it is
 * always available here when this function is called.
 *
 * Process $$...$$ (display/block math) BEFORE $...$ (inline math)
 * to avoid the inner $ signs in display math being matched first.
 */
function renderMathInText(text) {
    if (!text) return '';

    // Safety check — KaTeX must be loaded from layout.blade.php
    if (typeof katex === 'undefined') {
        console.warn('KaTeX is not loaded. Mathematical expressions will appear as plain text.');
        return text;
    }

    // 1. Display math: $$...$$  (must come BEFORE inline to avoid double-matching)
    let result = text.replace(/\$\$([^$]+)\$\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), {
                throwOnError: false,
                displayMode: true
            });
        } catch (e) {
            console.warn('KaTeX display math error:', e);
            return match;
        }
    });

    // 2. Inline math: $...$
    result = result.replace(/\$([^$\n]+)\$/g, function(match, expr) {
        try {
            return katex.renderToString(expr.trim(), {
                throwOnError: false,
                displayMode: false
            });
        } catch (e) {
            console.warn('KaTeX inline math error:', e);
            return match;
        }
    });

    return result;
}

function showQuestion(index) {
    if (index < 0 || index >= questions.length) return;
    
    currentQuestionIndex = index;
    const question = questions[index];
    
    // Update question number and points
    document.getElementById('currentQuestionNumber').textContent = index + 1;
    document.getElementById('currentQuestionPoints').textContent = question.points || 5;
    
    // Update navigation buttons
    updateNavigationButtons();
    
    // Build question HTML
    const questionContent = document.getElementById('questionContent');
    
    const processedQuestionText = renderMathInText(question.question_text || question.text || '');
    
    let html = `
        <div class="mb-4">
            <div class="fs-5 text-dark mb-3">
                ${processedQuestionText}
            </div>
            
            ${question.question_image ? `
                <div class="text-center mb-4">
                    <img src="/storage/${question.question_image}" 
                         alt="Question Image" 
                         class="img-fluid rounded shadow-sm" style="max-height: 300px;">
                </div>
            ` : ''}
        </div>
        
        <div class="answer-options">
    `;
    
    question.options.forEach((option, optIndex) => {
        const isCorrect = showAnswers && String.fromCharCode(65 + optIndex) === question.correct_answer;
        const optionClass = isCorrect ? 'correct' : '';
        
        const processedOptionText = renderMathInText(option);
        
        html += `
            <div class="answer-option ${optionClass}" onclick="selectOption(${optIndex})">
                <div class="d-flex align-items-center">
                    <div class="me-3">
                        <strong>${String.fromCharCode(65 + optIndex)}.</strong>
                    </div>
                    <div class="flex-grow-1">
                        ${processedOptionText}
                    </div>
                    ${showAnswers && isCorrect ? '<i class="bi bi-check-circle-fill text-success ms-2"></i>' : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    if (showSources) {
        html += `
            <div class="question-source mt-3">
                <i class="bi bi-database"></i> Source: ${question.question_bank_name}
            </div>
        `;
    }
    
    questionContent.innerHTML = html;
}

function selectOption(index) {
    const options = document.querySelectorAll('.answer-option');
    options.forEach(opt => opt.classList.remove('selected'));
    options[index].classList.add('selected');
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    
    prevBtn.disabled = currentQuestionIndex === 0;
    nextBtn.disabled = currentQuestionIndex === questions.length - 1;
    
    const navButtons = document.querySelectorAll('.question-nav-btn');
    navButtons.forEach((btn, index) => {
        btn.classList.toggle('active', index === currentQuestionIndex);
    });
}

function nextQuestion() {
    if (currentQuestionIndex < questions.length - 1) {
        showQuestion(currentQuestionIndex + 1);
    }
}

function previousQuestion() {
    if (currentQuestionIndex > 0) {
        showQuestion(currentQuestionIndex - 1);
    }
}

function togglePreviewMode() {
    showAnswers = !showAnswers;
    showSources = !showSources;
    
    document.getElementById('showAnswers').checked = showAnswers;
    document.getElementById('showSources').checked = showSources;
    document.getElementById('adminInfo').style.display = showAnswers ? 'inline' : 'none';
    
    showQuestion(currentQuestionIndex);
}

function printPreview() {
    window.print();
}

// Event listeners for admin controls
document.getElementById('showAnswers').addEventListener('change', (e) => {
    showAnswers = e.target.checked;
    document.getElementById('adminInfo').style.display = showAnswers ? 'inline' : 'none';
    showQuestion(currentQuestionIndex);
});

document.getElementById('showSources').addEventListener('change', (e) => {
    showSources = e.target.checked;
    showQuestion(currentQuestionIndex);
});

// Initialize on DOM ready
// KaTeX is already loaded synchronously in layout.blade.php <head>,
// so it is guaranteed to be available here.
document.addEventListener('DOMContentLoaded', function() {
    if (questions.length > 0) {
        showQuestion(0);
    }
});
</script>
@endsection