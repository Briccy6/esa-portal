@extends('teacher.layout')

@section('styles')
<style>
.math-content {
    font-family: 'Times New Roman', serif;
    line-height: 1.6;
}

.math-content p {
    margin-bottom: 1rem;
}

.math-content .MathJax {
    font-size: 1.1em;
    color: #1a1a1a;
}

.question-detail {
    max-height: 60vh;
    overflow-y: auto;
}

.option-item {
    transition: all 0.2s ease;
}

.option-item:hover {
    background-color: #f8f9fa;
}

.options-list {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.question-meta {
    border-top: 1px solid #e9ecef;
    padding-top: 1rem;
    margin-top: 1rem;
}

/* Enhanced table styling */
.table .math-content {
    font-size: 0.9em;
}

.table td {
    vertical-align: middle;
}

/* Modal enhancements */
.modal-lg {
    max-width: 800px;
}

.modal-body {
    padding: 2rem;
}

/* Loading indicator for math rendering */
.math-rendering {
    opacity: 0.6;
    position: relative;
}

.math-rendering::after {
    content: "Rendering mathematics...";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8em;
    color: #6c757d;
}
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-question-circle"></i> {{ $questionBank->name }}</h2>
            @if($questionBank->course)
                <p class="text-muted mb-0">Course: {{ $questionBank->course->course_name }}</p>
            @endif
        </div>
        <div>
            <a href="{{ route('teacher.question-banks.edit', $questionBank) }}" class="btn btn-outline-primary me-2">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success me-2">
                <i class="bi bi-plus-circle"></i> Add Question
            </a>
            <a href="{{ route('teacher.question-banks.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('errors'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> 
            @if(is_array(session('errors')))
                {{ count(session('errors')) }} errors occurred during import.
                <ul class="mb-0 mt-2">
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @else
                {{ session('errors') }}
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row mb-4">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Question Bank Details</h5>
                </div>
                <div class="card-body">
                    @if($questionBank->description)
                        <p><strong>Description:</strong> {{ $questionBank->description }}</p>
                    @endif
                    
                    <p><strong>Status:</strong> 
                        <span class="badge {{ $questionBank->is_active ? 'bg-success' : 'bg-secondary' }}">
                            {{ $questionBank->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </p>
                    
                    <p><strong>Total Questions:</strong> {{ $questionBank->questions->count() }}</p>
                    
                    @if($questionBank->academicYear)
                        <p><strong>Academic Year:</strong> {{ $questionBank->academicYear->year }}</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success">
                            <i class="bi bi-plus-circle"></i> Add Question
                        </a>
                        <a href="{{ route('teacher.question-banks.import', $questionBank) }}" class="btn btn-info">
                            <i class="bi bi-upload"></i> Import Questions
                        </a>
                        <a href="{{ route('teacher.question-banks.export', $questionBank) }}" class="btn btn-outline-primary">
                            <i class="bi bi-download"></i> Export Questions
                        </a>
                        <a href="{{ route('teacher.question-banks.edit', $questionBank) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-pencil"></i> Edit Bank
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Questions ({{ $questionBank->questions->count() }})</h5>
            <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle"></i> Add Question
            </a>
        </div>
        <div class="card-body">
            @if($questionBank->questions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Question</th>
                                <th>Type</th>
                                <th>Marks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($questionBank->questions as $question)
                                <tr>
                                    <td>
                                        <div class="math-content">
                                            <strong>{{ Str::limit($question->text, 80) }}</strong>
                                        </div>
                                        @if($question->question_image)
                                            <br><small class="text-muted"><i class="bi bi-image"></i> Has image</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $question->question_type)) }}</span>
                                    </td>
                                    <td>{{ $question->marks }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-outline-primary" title="View Question" onclick="viewQuestion({{ $question->id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" title="Edit Question">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger" title="Delete Question">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-question-circle display-4 text-muted"></i>
                    <h5 class="mt-3">No Questions Yet</h5>
                    <p class="text-muted">Start adding questions to this question bank.</p>
                    <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i> Add Your First Question
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Question View Modal -->
<div class="modal fade" id="questionViewModal" tabindex="-1" aria-labelledby="questionViewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="questionViewModalLabel">
                    <i class="bi bi-question-circle me-2"></i>Question Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="questionContent">
                    <!-- Question content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
// Question data (passed from controller)
const questions = @json($questionBank->questions);

function viewQuestion(questionId) {
    const question = questions.find(q => q.id === questionId);
    if (!question) return;
    
    const modal = document.getElementById('questionViewModal');
    const content = document.getElementById('questionContent');
    
    // Build question HTML with mathematical expressions
    let html = `
        <div class="question-detail">
            <div class="math-content mb-3">
                <h6 class="text-primary">Question:</h6>
                <p class="fs-5">${question.text}</p>
            </div>
    `;
    
    // Add options if they exist
    if (question.option_a || question.option_b || question.option_c || question.option_d) {
        html += '<h6 class="text-primary">Options:</h6><div class="options-list">';
        
        const options = [
            { key: 'option_a', label: 'A' },
            { key: 'option_b', label: 'B' },
            { key: 'option_c', label: 'C' },
            { key: 'option_d', label: 'D' }
        ];
        
        options.forEach(option => {
            if (question[option.key]) {
                const isCorrect = question.correct_answer === option.label || 
                                question.correct_answer === `option_${option.label.toLowerCase()}`;
                html += `
                    <div class="option-item p-2 mb-2 border rounded ${isCorrect ? 'border-success bg-success-subtle' : ''}">
                        <strong>${option.label}.</strong> 
                        <span class="math-content">${question[option.key]}</span>
                        ${isCorrect ? '<span class="badge bg-success ms-2">Correct</span>' : ''}
                    </div>
                `;
            }
        });
        
        html += '</div>';
    }
    
    // Add explanation if it exists
    if (question.explanation) {
        html += `
            <div class="math-content mt-3">
                <h6 class="text-primary">Explanation:</h6>
                <p>${question.explanation}</p>
            </div>
        `;
    }
    
    // Add metadata
    html += `
        <div class="question-meta mt-3">
            <div class="row">
                <div class="col-md-4">
                    <small class="text-muted"><strong>Type:</strong> ${question.question_type}</small>
                </div>
                <div class="col-md-4">
                    <small class="text-muted"><strong>Marks:</strong> ${question.marks}</small>
                </div>
                <div class="col-md-4">
                    <small class="text-muted"><strong>Difficulty:</strong> ${question.difficulty_level || 'N/A'}</small>
                </div>
            </div>
        </div>
    `;
    
    html += '</div>';
    
    content.innerHTML = html;
    
    // Show modal
    const modalInstance = new bootstrap.Modal(modal);
    modalInstance.show();
    
    // Re-render MathJax after modal content is loaded
    setTimeout(() => {
        if (typeof reRenderMathJax === 'function') {
            reRenderMathJax();
        }
    }, 100);
}
</script>
@endsection
