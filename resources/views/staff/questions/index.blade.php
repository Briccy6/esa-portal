@extends('dos.layout')

@section('title', 'Exam Questions')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .question-text {
        font-family: 'Times New Roman', serif;
        font-size: 1.1em;
        line-height: 1.6;
        margin-bottom: 1rem;
    }
    
    .math-expression {
        display: block;
        margin: 1rem 0;
        padding: 1rem;
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        border-radius: 4px;
        font-family: 'Courier New', monospace;
        font-size: 1.1em;
        color: #333;
    }
    
    .options-container {
        display: grid;
        grid-template-columns: auto;
        gap: 1rem;
        margin: 1rem 0;
    }
    
    .form-check {
        padding: 1rem;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        background: #ffffff;
        transition: all 0.3s ease;
    }
    
    .form-check:hover {
        border-color: #007bff;
        background: #f8f9fa;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    
    .form-check-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        font-weight: 500;
    }
    
    .text-success {
        color: #155724 !important;
        font-weight: 700;
    }
    
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 1rem;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .card {
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.2);
    }
    
    @media print {
        .card {
            break-inside: avoid;
            page-break-inside: avoid;
            box-shadow: none;
            border: 1px solid #ddd;
        }
        
        .btn {
            display: none;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .options-container {
            page-break-inside: avoid;
        }
        
        .form-check {
            page-break-inside: avoid;
            margin: 0.5rem 0;
        }
    }
</style>

<!-- MathJax Configuration -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<script>
window.MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\(', '\)']],
    displayMath: [['$$', '$$'], ['\\[', '\\]']],
    processEscapes: false
  },
  options: {
    skipHtmlTags: true,
    processHtmlClass: 'math'
  }
};
</script>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Questions for Exam: {{ $exam->title ?? 'Exam' }}</h2>
        <div class="badge bg-info">Total: {{ $exam->questions->count() }} questions</div>
    </div>

    <!-- Exam Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <strong>Exam Type:</strong> {{ $exam->exam_type ?? 'N/A' }}<br>
                    <strong>Total Marks:</strong> {{ $exam->total_marks ?? 'N/A' }}<br>
                    <strong>Exam Date:</strong> {{ $exam->class_date ? $exam->class_date->format('M d, Y') : 'N/A' }}
                </div>
                <div class="col-md-6">
                    <strong>Questions from Banks:</strong> {{ $exam->questions->whereNotNull('question_bank_id')->count() }}<br>
                    <strong>Manually Added:</strong> {{ $exam->questions->whereNull('question_bank_id')->count() }}<br>
                    <strong>Status:</strong> <span class="badge bg-{{ $exam->is_active ? 'success' : 'secondary' }}">{{ $exam->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Add & Import Buttons -->
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('exams.questions.create', $exam->id) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Question Manually
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload"></i> Import Questions (CSV)
        </button>
        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#questionBankImportModal">
            <i class="bi bi-database"></i> Import from Question Banks
        </button>
        @if($exam->questions->count() > 0)
            <button class="btn btn-info" onclick="window.print()">
                <i class="bi bi-printer"></i> Print Questions
            </button>
        @endif
    </div>

    @if($exam->questions->count() > 0)
        <div class="row">
            @foreach($exam->questions as $index => $question)
                <div class="col-lg-6 mb-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Question #{{ $index + 1 }}</h6>
                            <div>
                                @if($question->question_bank_id)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-database"></i> From Bank
                                    </span>
                                @else
                                    <span class="badge bg-primary">
                                        <i class="bi bi-pencil"></i> Manual
                                    </span>
                                @endif
                                <span class="badge bg-secondary">{{ $question->marks }} marks</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="mb-3 question-text">{!! $question->text !!}</p>
                            
                            @if($question->option_a || $question->option_b || $question->option_c || $question->option_d)
                                <div class="options-container mb-3">
                                    @if($question->option_a)
                                        <div class="form-check {{ $question->correct_answer === 'A' ? 'text-success fw-bold' : '' }}">
                                            <label class="form-check-label">
                                                A. <span class="option-text math">{!! $question->option_a !!}</span>
                                                @if($question->correct_answer === 'A') <i class="bi bi-check-circle-fill ms-2"></i> @endif
                                            </label>
                                        </div>
                                    @endif
                                    @if($question->option_b)
                                        <div class="form-check {{ $question->correct_answer === 'B' ? 'text-success fw-bold' : '' }}">
                                            <label class="form-check-label">
                                                B. <span class="option-text math">{!! $question->option_b !!}</span>
                                                @if($question->correct_answer === 'B') <i class="bi bi-check-circle-fill ms-2"></i> @endif
                                            </label>
                                        </div>
                                    @endif
                                    @if($question->option_c)
                                        <div class="form-check {{ $question->correct_answer === 'C' ? 'text-success fw-bold' : '' }}">
                                            <label class="form-check-label">
                                                C. <span class="option-text math">{!! $question->option_c !!}</span>
                                                @if($question->correct_answer === 'C') <i class="bi bi-check-circle-fill ms-2"></i> @endif
                                            </label>
                                        </div>
                                    @endif
                                    @if($question->option_d)
                                        <div class="form-check {{ $question->correct_answer === 'D' ? 'text-success fw-bold' : '' }}">
                                            <label class="form-check-label">
                                                D. <span class="option-text math">{!! $question->option_d !!}</span>
                                                @if($question->correct_answer === 'D') <i class="bi bi-check-circle-fill ms-2"></i> @endif
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            
                            @if($question->question_bank_id && $question->questionBank)
                                <div class="mt-2 pt-2 border-top">
                                    <small class="text-muted">
                                        <i class="bi bi-database"></i> 
                                        Source: {{ $question->questionBank->name }}
                                    </small>
                                </div>
                            @endif
                            
                            <div class="mt-3 d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="try { editQuestion({{ $question->id }}); } catch(e) { console.error('Edit error:', e); alert('Unable to edit question. Please try again.'); }">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="try { deleteQuestion({{ $question->id }}); } catch(e) { console.error('Delete error:', e); alert('Unable to delete question. Please try again.'); }">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Summary Section -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="mb-0">Questions Summary</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <h4 class="text-primary">{{ $exam->questions->count() }}</h4>
                        <p class="mb-0">Total Questions</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-success">{{ $exam->questions->sum('marks') }}</h4>
                        <p class="mb-0">Total Marks</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-warning">{{ $exam->questions->whereNotNull('question_bank_id')->count() }}</h4>
                        <p class="mb-0">From Banks</p>
                    </div>
                    <div class="col-md-3">
                        <h4 class="text-info">{{ $exam->questions->whereNull('question_bank_id')->count() }}</h4>
                        <p class="mb-0">Manual</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <div class="text-center">
                <i class="bi bi-inbox fa-3x text-muted mb-3"></i>
                <h5>No Questions Found</h5>
                <p class="mb-3">This exam doesn't have any questions yet. You can:</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('exams.questions.create', $exam->id) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Add Question Manually
                    </a>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
                        <i class="bi bi-upload"></i> Import from CSV
                    </button>
                    <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#questionBankImportModal">
                        <i class="bi bi-database"></i> Import from Question Banks
                    </button>
                </div>
            </div>
        </div>
    @endif
    
    <div class="mt-4">
        <a href="{{ route('dos.exams.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Exams
        </a>
    </div>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('exams.questions.import', $exam->id) }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">Import Questions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="questionsFile" class="form-label">Upload CSV or Excel File</label>
          <input type="file" class="form-control" id="questionsFile" name="questions_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
          <div class="form-text">
              Download a <a href="{{ asset('questions_template.csv') }}" download>sample template</a>.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>

<!-- Question Bank Import Modal -->
<div class="modal fade" id="questionBankImportModal" tabindex="-1" aria-labelledby="questionBankImportModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="questionBankImportModalLabel">
            <i class="bi bi-database"></i> Import Questions from Question Banks
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
            <div class="col-md-8">
                <label for="questionBankSelect" class="form-label">Select Question Banks</label>
                <select id="questionBankSelect" class="form-select" multiple size="6">
                    @php
                        $questionBanks = \App\Models\QuestionBank::with('questions')->get();
                    @endphp
                    @foreach($questionBanks as $bank)
                        <option value="{{ $bank->id }}">
                            {{ $bank->name }} ({{ $bank->questions->count() }} questions)
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple banks</small>
            </div>
            <div class="col-md-4">
                <label for="questionsPerBank" class="form-label">Questions per Bank</label>
                <input type="number" id="questionsPerBank" class="form-control" min="1" max="50" value="5">
                <small class="form-text text-muted">Random questions per bank</small>
                
                <div class="mt-2">
                    <label class="form-label">
                        <input type="radio" name="importType" value="perBank" checked> Per Bank
                    </label>
                    <label class="form-label">
                        <input type="radio" name="importType" value="total"> Total Questions
                    </label>
                </div>
                
                <div class="mt-3">
                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="try { previewQuestionBankQuestions(); } catch(e) { console.error('Preview error:', e); alert('Unable to preview questions. Please try again.'); }">
                        <i class="bi bi-eye"></i> Preview
                    </button>
                </div>
            </div>
        </div>

        <div id="questionBankPreview" class="mt-3" style="display: none;">
            <h6>Questions Preview:</h6>
            <div id="questionBankPreviewList" style="max-height: 300px; overflow-y: auto;"></div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-warning" onclick="try { importFromQuestionBanks(); } catch(e) { console.error('Import error:', e); alert('Unable to import questions. Please try again.'); }">
            <i class="bi bi-database"></i> Import Questions
        </button>
      </div>
    </div>
  </div>
</div>

<style>
.options-container .form-check {
    margin-bottom: 0.5rem;
}
@media print {
    .no-print { display: none !important; }
    .card { break-inside: avoid; }
}
</style>

<script>
function editQuestion(questionId) {
    // Implement edit functionality or redirect to edit page
    alert('Edit functionality not yet implemented for question ID: ' + questionId);
}

function deleteQuestion(questionId) {
    if (confirm('Are you sure you want to delete this question?')) {
        // Implement delete functionality
        fetch('/exams/questions/' + questionId, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error deleting question: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting question');
        });
    }
}

function previewQuestionBankQuestions() {
    const selectedBanks = Array.from(document.getElementById('questionBankSelect').selectedOptions).map(option => option.value);
    const questionsPerBank = document.getElementById('questionsPerBank').value;
    const importType = document.querySelector('input[name="importType"]:checked').value;
    
    if (!selectedBanks || selectedBanks.length === 0) {
        alert('Please select at least one question bank');
        return;
    }
    
    if (!questionsPerBank || questionsPerBank < 1) {
        alert('Please enter a valid number of questions');
        return;
    }
    
    // Calculate expected total
    const expectedTotal = importType === 'perBank' ? questionsPerBank * selectedBanks.length : parseInt(questionsPerBank);
    
    // Show loading state
    const previewBtn = document.querySelector('button[onclick="previewQuestionBankQuestions()"]');
    const originalText = previewBtn.innerHTML;
    previewBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Loading...';
    previewBtn.disabled = true;
    
    fetch('/exams/question-banks/preview-questions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_bank_ids: selectedBanks,
            questions_per_bank: parseInt(questionsPerBank),
            import_type: importType
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        const previewDiv = document.getElementById('questionBankPreview');
        const previewList = document.getElementById('questionBankPreviewList');
        
        if (data.error) {
            previewList.innerHTML = '<div class="alert alert-danger"><strong>Error:</strong> ' + data.error + '</div>';
            previewDiv.style.display = 'block';
        } else if (data.questions && data.questions.length > 0) {
            let html = '<div class="alert alert-info"><strong>Preview:</strong> ' + data.questions.length + ' questions will be imported (expected: ' + expectedTotal + ')</div>';
            data.questions.forEach((question, index) => {
                html += `
                    <div class="card mb-2">
                        <div class="card-body p-3">
                            <h6 class="card-title mb-2">Question ${index + 1}</h6>
                            <p class="card-text mb-2">${question.text}</p>
                            ${question.options ? '<small class="text-muted"><strong>Options:</strong> ' + question.options + '</small><br>' : ''}
                            <small class="text-muted"><strong>Bank:</strong> ${question.question_bank_name}</small>
                        </div>
                    </div>
                `;
            });
            previewList.innerHTML = html;
            previewDiv.style.display = 'block';
        } else {
            previewList.innerHTML = '<p class="text-muted">No questions found in selected question banks.</p>';
            previewDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const previewDiv = document.getElementById('questionBankPreview');
        const previewList = document.getElementById('questionBankPreviewList');
        previewList.innerHTML = '<div class="alert alert-danger"><strong>Error:</strong> ' + error.message + '</div>';
        previewDiv.style.display = 'block';
        alert('An error occurred while previewing questions: ' + error.message);
    })
    .finally(() => {
        // Restore button state
        previewBtn.innerHTML = originalText;
        previewBtn.disabled = false;
    });
}

function importFromQuestionBanks() {
    const selectedBanks = Array.from(document.getElementById('questionBankSelect').selectedOptions).map(option => option.value);
    const questionsPerBank = document.getElementById('questionsPerBank').value;
    const importType = document.querySelector('input[name="importType"]:checked').value;
    
    if (!selectedBanks || selectedBanks.length === 0) {
        alert('Please select at least one question bank');
        return;
    }
    
    if (!questionsPerBank || questionsPerBank < 1) {
        alert('Please enter a valid number of questions');
        return;
    }
    
    // Calculate expected total
    const expectedTotal = importType === 'perBank' ? questionsPerBank * selectedBanks.length : parseInt(questionsPerBank);
    
    if (!confirm('Are you sure you want to import ' + expectedTotal + ' questions? (' + importType + ' mode)')) {
        return;
    }
    
    // Show loading state
    const importBtn = event.target;
    const originalText = importBtn.innerHTML;
    importBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Importing...';
    importBtn.disabled = true;
    
    fetch('/exams/' + {{ $exam->id }} + '/import-from-banks', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_bank_ids: selectedBanks,
            questions_per_bank: parseInt(questionsPerBank),
            import_type: importType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Successfully imported ' + data.imported_count + ' questions!');
            location.reload();
        } else {
            alert('Error importing questions: ' + data.message);
            importBtn.innerHTML = originalText;
            importBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while importing questions');
        importBtn.innerHTML = originalText;
        importBtn.disabled = false;
    });
}
</script>
@endsection
