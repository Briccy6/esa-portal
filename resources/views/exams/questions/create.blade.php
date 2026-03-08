@extends(
    auth('admin')->check() ? 'admin.layout' :
    (auth('teacher')->check() ? 'teacher.layout' :
    (auth('student')->check() ? 'student.layout' : 'staff.partials.sidebar_dos'))
)

@section('content')
<div class="container py-4">
    <h2>Add Question to Exam: {{ $exam->title ?? 'Exam' }}</h2>
    
    <!-- Fetch from Question Bank Button -->
    <div class="mb-3">
        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#questionBankModal">
            <i class="fas fa-database"></i> Fetch from Question Bank
        </button>
        <button type="button" class="btn btn-secondary ms-2" onclick="clearForm()">
            <i class="fas fa-eraser"></i> Clear Form
        </button>
    </div>

    <!-- Question Bank Modal -->
    <div class="modal fade" id="questionBankModal" tabindex="-1" aria-labelledby="questionBankModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="questionBankModalLabel">Select Question from Bank</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="questionSearch" class="form-control" placeholder="Search questions...">
                    </div>
                    <div class="mb-3">
                        <select id="courseFilter" class="form-select">
                            <option value="">All Courses</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="questionBankList" class="list-group" style="max-height: 400px; overflow-y: auto;">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Form -->
    <form method="POST" action="{{ route('exams.questions.store', $exam->id) }}" id="questionForm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <input type="text" name="text" class="form-control" id="questionText" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Option A</label>
            <input type="text" name="option_a" class="form-control" id="optionA">
        </div>
        <div class="mb-3">
            <label class="form-label">Option B</label>
            <input type="text" name="option_b" class="form-control" id="optionB">
        </div>
        <div class="mb-3">
            <label class="form-label">Option C</label>
            <input type="text" name="option_c" class="form-control" id="optionC">
        </div>
        <div class="mb-3">
            <label class="form-label">Option D</label>
            <input type="text" name="option_d" class="form-control" id="optionD">
        </div>
        <div class="mb-3">
            <label class="form-label">Correct Answer</label>
            <input type="text" name="correct_answer" class="form-control" id="correctAnswer">
        </div>
        <div class="mb-3">
            <label class="form-label">Marks</label>
            <input type="number" name="marks" class="form-control" id="marks" value="1" min="1">
        </div>
        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="{{ route('exams.questions', $exam->id) }}" class="btn btn-secondary">Back</a>
    </form>
</div>

<script>
let allQuestions = [];

// Load questions from bank
function loadQuestionsFromBank() {
    const courseId = document.getElementById('courseFilter').value;
    const searchTerm = document.getElementById('questionSearch').value.toLowerCase();
    
    fetch(`/api/question-bank/questions?course_id=${courseId}&search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            allQuestions = data;
            displayQuestions(data);
        })
        .catch(error => {
            console.error('Error loading questions:', error);
            document.getElementById('questionBankList').innerHTML = 
                '<div class="alert alert-danger">Error loading questions from bank</div>';
        });
}

// Display questions in modal
function displayQuestions(questions) {
    const listContainer = document.getElementById('questionBankList');
    
    if (questions.length === 0) {
        listContainer.innerHTML = '<div class="alert alert-info">No questions found</div>';
        return;
    }
    
    listContainer.innerHTML = questions.map(question => `
        <div class="list-group-item list-group-item-action">
            <div class="d-flex w-100 justify-content-between">
                <h6 class="mb-1">${question.text}</h6>
                <small>${question.marks} marks</small>
            </div>
            <div class="mb-1">
                <small class="text-muted">Type: ${question.question_type}</small>
            </div>
            ${question.option_a ? `<div><small>A: ${question.option_a}</small></div>` : ''}
            ${question.option_b ? `<div><small>B: ${question.option_b}</small></div>` : ''}
            ${question.option_c ? `<div><small>C: ${question.option_c}</small></div>` : ''}
            ${question.option_d ? `<div><small>D: ${question.option_d}</small></div>` : ''}
            ${question.correct_answer ? `<div><small><strong>Correct: ${question.correct_answer}</strong></small></div>` : ''}
            <button class="btn btn-sm btn-primary mt-2" onclick="selectQuestion(${question.id})">
                <i class="fas fa-plus"></i> Use This Question
            </button>
        </div>
    `).join('');
}

// Select a question and populate form
function selectQuestion(questionId) {
    const question = allQuestions.find(q => q.id === questionId);
    if (!question) return;
    
    document.getElementById('questionText').value = question.text || '';
    document.getElementById('optionA').value = question.option_a || '';
    document.getElementById('optionB').value = question.option_b || '';
    document.getElementById('optionC').value = question.option_c || '';
    document.getElementById('optionD').value = question.option_d || '';
    document.getElementById('correctAnswer').value = question.correct_answer || '';
    document.getElementById('marks').value = question.marks || 1;
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('questionBankModal'));
    modal.hide();
}

// Clear form
function clearForm() {
    document.getElementById('questionForm').reset();
}

// Event listeners
document.getElementById('questionSearch').addEventListener('input', loadQuestionsFromBank);
document.getElementById('courseFilter').addEventListener('change', loadQuestionsFromBank);

// Load questions when modal opens
document.getElementById('questionBankModal').addEventListener('shown.bs.modal', function () {
    loadQuestionsFromBank();
});
</script>
@endsection