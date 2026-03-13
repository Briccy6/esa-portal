@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title)

@section('styles')
<style>
.results-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.results-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
    text-align: center;
}

.score-display {
    font-size: 48px;
    font-weight: bold;
    margin: 20px 0;
}

.pass-fail-badge {
    font-size: 24px;
    padding: 15px 30px;
    border-radius: 50px;
    margin: 20px 0;
}

.pass-fail-badge.pass {
    background: #28a745;
    color: white;
}

.pass-fail-badge.fail {
    background: #dc3545;
    color: white;
}

.result-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.question-result {
    border-left: 4px solid #dee2e6;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 0 8px 8px 0;
    transition: all 0.3s ease;
}

.question-result:hover {
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.question-result.correct {
    border-left-color: #28a745;
    background: #f8fff9;
}

.question-result.incorrect {
    border-left-color: #dc3545;
    background: #fff8f8;
}

.question-result.partial {
    border-left-color: #ffc107;
    background: #fffdf7;
}

.option-display {
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
}

.option-display.selected {
    background: #e3f2fd;
    border-left: 3px solid #2196f3;
}

.option-display.correct {
    background: #e8f5e8;
    border-left: 3px solid #4caf50;
}

.option-display.incorrect {
    background: #ffebee;
    border-left: 3px solid #f44336;
}

.feedback-box {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.explanation-box {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 15px;
    margin-top: 15px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin: 20px 0;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.stat-value {
    font-size: 32px;
    font-weight: bold;
    color: #007bff;
}

.stat-label {
    color: #6c757d;
    margin-top: 5px;
}

@media (max-width: 768px) {
    .results-container {
        padding: 10px;
    }
    
    .score-display {
        font-size: 36px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
}
</style>
@endsection

@section('content')
<div class="results-container">
    <!-- Results Header -->
    <div class="results-header">
        <h1>{{ $exam->title }}</h1>
        <p class="mb-0">{{ $exam->subject }} | Completed on {{ $attempt->finished_at ? $attempt->finished_at->format('M d, Y h:i A') : 'N/A' }}</p>
        
        @if($attempt->total_score !== null)
            <div class="score-display">
                {{ number_format($attempt->percentage, 1) }}%
            </div>
            
            <div class="pass-fail-badge {{ $attempt->isPassed() ? 'pass' : 'fail' }}">
                {{ $attempt->isPassed() ? 'PASSED' : 'FAILED' }}
            </div>
            
            <div class="row mt-4">
                <div class="col-md-4">
                    <h5>Score</h5>
                    <p>{{ $attempt->total_score }} / {{ $exam->questions->sum('marks') }}</p>
                </div>
                <div class="col-md-4">
                    <h5>Duration</h5>
                    <p>{{ $attempt->duration_minutes }} minutes</p>
                </div>
                <div class="col-md-4">
                    <h5>Attempt</h5>
                    <p>#{{ $attempt->attempt_number }}</p>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                <h4><i class="fas fa-clock"></i> Exam Being Graded</h4>
                <p>Your exam has been submitted and is currently being graded. Some questions may require manual grading.</p>
                <p>Please check back later for your final results.</p>
            </div>
        @endif
    </div>

    @if($attempt->total_score !== null && $exam->settings->show_correct_answers)
        <!-- Performance Statistics -->
        <div class="result-card">
            <h3><i class="fas fa-chart-bar"></i> Performance Overview</h3>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-value">{{ $attempt->studentAnswers->count() }}</div>
                    <div class="stat-label">Questions Attempted</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $attempt->studentAnswers->where('is_correct', true)->count() }}</div>
                    <div class="stat-label">Correct Answers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $attempt->studentAnswers->where('is_correct', false)->count() }}</div>
                    <div class="stat-label">Incorrect Answers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $attempt->studentAnswers->where('needs_manual_grading', true)->count() }}</div>
                    <div class="stat-label">Pending Grading</div>
                </div>
            </div>
        </div>

        <!-- Question by Question Results -->
        <div class="result-card">
            <h3><i class="fas fa-list-check"></i> Question Review</h3>
            
            @foreach($attempt->studentAnswers->sortBy('question_id') as $answer)
                @php
                    $question = $answer->question;
                    $isCorrect = $answer->is_correct;
                    $resultClass = $isCorrect === true ? 'correct' : ($isCorrect === false ? 'incorrect' : 'partial');
                @endphp
                
                <div class="question-result {{ $resultClass }}">
                    <div class="row">
                        <div class="col-md-8">
                            <h5>Question {{ $loop->index + 1 }}</h5>
                            <p class="mb-2">{{ $question->text }}</p>
                            <p class="text-muted">
                                Type: {{ ucfirst(str_replace('_', ' ', $question->question_type)) }} | 
                                Marks: {{ $question->marks }} |
                                Your Score: {{ $answer->score ?? 0 }}/{{ $question->marks }}
                            </p>
                            
                            <!-- Show answer based on question type -->
                            @if($question->question_type === 'multiple_choice')
                                @php
                                    $correctOption = 'option_' . strtolower($question->correct_answer);
                                    $correctText = $correctOption ?? '';
                                @endphp
                                <div class="option-display correct">
                                    <strong>Correct Answer:</strong> {{ $question->correct_answer }}. {{ $correctText }}
                                </div>
                                @if($answer->answer)
                                    @php
                                        $answerOption = 'option_' . strtolower($answer->answer);
                                        $answerText = $answerOption ?? '';
                                    @endphp
                                    <div class="option-display selected">
                                        <strong>Your Answer:</strong> {{ $answer->answer }}. {{ $answerText }}
                                    </div>
                                @endif
                                
                            @elseif($question->question_type === 'multiple_select')
                                <div class="option-display correct">
                                    <strong>Correct Answers:</strong> {{ implode(', ', $question->correct_answers ?? []) }}
                                </div>
                                @if($answer->selected_options)
                                    <div class="option-display selected">
                                        <strong>Your Answers:</strong> {{ implode(', ', $answer->selected_options) }}
                                    </div>
                                @endif
                                
                            @elseif(in_array($question->question_type, ['true_false', 'short_answer']))
                                <div class="option-display correct">
                                    <strong>Correct Answer:</strong> {{ $question->correct_answer }}
                                </div>
                                @if($answer->answer)
                                    <div class="option-display selected">
                                        <strong>Your Answer:</strong> {{ $answer->answer }}
                                    </div>
                                @endif
                                
                            @elseif($question->question_type === 'essay')
                                @if($answer->answer)
                                    <div class="option-display selected">
                                        <strong>Your Answer:</strong>
                                        <p class="mt-2">{{ $answer->answer }}</p>
                                    </div>
                                @endif
                            @endif
                            
                            <!-- Teacher Feedback -->
                            @if($answer->feedback)
                                <div class="feedback-box">
                                    <h6><i class="fas fa-comment"></i> Teacher Feedback</h6>
                                    <p>{{ $answer->feedback }}</p>
                                </div>
                            @endif
                            
                            <!-- Explanation -->
                            @if($exam->settings->show_explanations && $question->explanation)
                                <div class="explanation-box">
                                    <h6><i class="fas fa-lightbulb"></i> Explanation</h6>
                                    <p>{{ $question->explanation }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4 text-right">
                            <div class="result-badge">
                                @if($isCorrect === true)
                                    <span class="badge badge-success">Correct</span>
                                @elseif($isCorrect === false)
                                    <span class="badge badge-danger">Incorrect</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted">
                                    {{ $answer->answered_at ? $answer->answered_at->format('h:i A') : 'Not answered' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Exam Information -->
    <div class="result-card">
        <h3><i class="fas fa-info-circle"></i> Exam Information</h3>
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Exam Title:</strong></td>
                        <td>{{ $exam->title }}</td>
                    </tr>
                    <tr>
                        <td><strong>Subject:</strong></td>
                        <td>{{ $exam->subject }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Questions:</strong></td>
                        <td>{{ $exam->questions->count() }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total Marks:</strong></td>
                        <td>{{ $exam->questions->sum('marks') }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <td><strong>Duration:</strong></td>
                        <td>{{ $exam->settings->duration_minutes }} minutes</td>
                    </tr>
                    <tr>
                        <td><strong>Passing Score:</strong></td>
                        <td>{{ $exam->settings->passing_score ?? 'Not set' }}%</td>
                    </tr>
                    <tr>
                        <td><strong>Started:</strong></td>
                        <td>{{ $attempt->started_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    <tr>
                        <td><strong>Finished:</strong></td>
                        <td>{{ $attempt->finished_at ? $attempt->finished_at->format('M d, Y h:i A') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="text-center mt-4">
        @if($exam->settings->allow_retakes && $attempt->attempt_number < $exam->settings->max_attempts)
            <a href="{{ route('exams.start', $exam) }}" class="btn btn-warning btn-lg">
                <i class="fas fa-redo"></i> Retake Exam
            </a>
        @endif
        
        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg ml-2">
            <i class="fas fa-home"></i> Back to Dashboard
        </a>
        
        @if($attempt->total_score !== null)
            <button type="button" class="btn btn-info btn-lg ml-2" onclick="window.print()">
                <i class="fas fa-print"></i> Print Results
            </button>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
// Print-friendly view
function beforePrint() {
    document.querySelectorAll('.btn').forEach(btn => btn.style.display = 'none');
}

function afterPrint() {
    document.querySelectorAll('.btn').forEach(btn => btn.style.display = '');
}

window.addEventListener('beforeprint', beforePrint);
window.addEventListener('afterprint', afterPrint);

// Smooth scroll to questions
document.querySelectorAll('.question-result').forEach((element, index) => {
    element.style.cursor = 'pointer';
    element.addEventListener('click', function() {
        this.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
});

// Highlight correct/incorrect answers
document.addEventListener('DOMContentLoaded', function() {
    // Add tooltips for better UX
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
