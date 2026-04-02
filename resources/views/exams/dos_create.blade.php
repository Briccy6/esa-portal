@extends('dos.layout')

@section('title', 'Create Exam')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-plus-circle"></i> Create New Exam</h2>
                <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Exams
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dos.exams.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Exam Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exam_type" class="form-label">Exam Type *</label>
                                    <select class="form-select @error('exam_type') is-invalid @enderror" 
                                            id="exam_type" name="exam_type" required>
                                        <option value="">Select Exam Type</option>
                                        <option value="Quiz" {{ old('exam_type') == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                                        <option value="Midterm" {{ old('exam_type') == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                                        <option value="Final" {{ old('exam_type') == 'Final' ? 'selected' : '' }}>Final Exam</option>
                                        <option value="Assignment" {{ old('exam_type') == 'Assignment' ? 'selected' : '' }}>Assignment</option>
                                    </select>
                                    @error('exam_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="exam_weight" class="form-label">Exam Weight (%) *</label>
                                    <input type="number" class="form-control @error('exam_weight') is-invalid @enderror" 
                                           id="exam_weight" name="exam_weight" value="{{ old('exam_weight', 100) }}" 
                                           min="1" max="100" required>
                                    @error('exam_weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="total_marks" class="form-label">Total Marks *</label>
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror" 
                                           id="total_marks" name="total_marks" value="{{ old('total_marks', 100) }}" 
                                           min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="class_date" class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control @error('class_date') is-invalid @enderror" 
                                           id="class_date" name="class_date" value="{{ old('class_date') }}" required>
                                    @error('class_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label">Branch *</label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" 
                                            id="branch_id" name="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->branch_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                                    <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                            id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="term_id" class="form-label">Term *</label>
                                    <select class="form-select @error('term_id') is-invalid @enderror" 
                                            id="term_id" name="term_id" required>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                                {{ $term->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="course_ids" class="form-label">Courses *</label>
                                    <select class="form-select @error('course_ids') is-invalid @enderror" 
                                            id="course_ids" name="course_ids[]" multiple required>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" 
                                                    {{ in_array($course->id, old('course_ids', [])) ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple courses</small>
                                    @error('course_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="lesson_ids" class="form-label">Lessons *</label>
                                    <select class="form-select @error('lesson_ids') is-invalid @enderror" 
                                            id="lesson_ids" name="lesson_ids[]" multiple required>
                                        @foreach($lessons as $lesson)
                                            <option value="{{ $lesson->id }}" 
                                                    {{ in_array($lesson->id, old('lesson_ids', [])) ? 'selected' : '' }}>
                                                {{ $lesson->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple lessons</small>
                                    @error('lesson_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="teacher_id" class="form-label">Teacher *</label>
                                    <select class="form-select @error('teacher_id') is-invalid @enderror" 
                                            id="teacher_id" name="teacher_id" required>
                                        <option value="">Select Teacher</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->teacher_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exam_password" class="form-label">Exam Password</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('exam_password') is-invalid @enderror" 
                                               id="exam_password" name="exam_password" value="{{ old('exam_password', $examPassword) }}" readonly>
                                        <button class="btn btn-outline-secondary" type="button" onclick="generatePassword()">Generate</button>
                                    </div>
                                    <small class="form-text text-muted">Auto-generated password for exam access</small>
                                    @error('exam_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Question Bank Selection -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="bi bi-question-circle"></i>
                                    Questions from Question Banks
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($questionBanks->count() > 0)
                                    <div class="row">
                                        <div class="col-md-8">
                                            <label for="question_bank_ids" class="form-label">Select Question Banks</label>
                                            <select name="question_bank_ids[]" id="question_bank_ids" class="form-select" multiple>
                                                @foreach($questionBanks as $questionBank)
                                                    <option value="{{ $questionBank->id }}">
                                                        {{ $questionBank->name }} ({{ $questionBank->questions->count() }} questions)
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple banks</small>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="questions_per_bank" class="form-label">Questions per Bank</label>
                                            <input type="number" name="questions_per_bank" id="questions_per_bank" class="form-control" 
                                                   min="1" max="50" value="10">
                                            <small class="form-text text-muted">Random questions per bank</small>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="previewQuestions()">
                                            <i class="bi bi-eye"></i> Preview Questions
                                        </button>
                                        <div id="questionsPreview" class="mt-3" style="display: none;">
                                            <h6>Questions Preview:</h6>
                                            <div id="questionsList"></div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        <strong>No Question Banks Available</strong>
                                        <p class="mb-0">There are no question banks in the system. You need to create question banks first.</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create Exam
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function generatePassword() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    let password = '';
    for (let i = 0; i < 8; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('exam_password').value = password;
}

function previewQuestions() {
    const selectedBanks = Array.from(document.getElementById('question_bank_ids').selectedOptions).map(option => option.value);
    const questionsPerBank = document.getElementById('questions_per_bank').value;
    
    if (!selectedBanks || selectedBanks.length === 0) {
        alert('Please select at least one question bank');
        return;
    }
    
    if (!questionsPerBank || questionsPerBank < 1) {
        alert('Please enter a valid number of questions per bank');
        return;
    }
    
    fetch('/exams/preview-questions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            question_bank_ids: selectedBanks,
            questions_per_bank: parseInt(questionsPerBank)
        })
    })
    .then(response => response.json())
    .then(data => {
        const previewDiv = document.getElementById('questionsPreview');
        const questionsList = document.getElementById('questionsList');
        
        if (data.questions && data.questions.length > 0) {
            let html = '<div class="row">';
            data.questions.forEach((question, index) => {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Question ${index + 1}</h6>
                                <p class="card-text">${question.text}</p>
                                ${question.options ? '<small class="text-muted"><strong>Options:</strong> ' + question.options + '</small>' : ''}
                                <br><small class="text-muted"><strong>Bank:</strong> ${question.question_bank_name}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            questionsList.innerHTML = html;
            previewDiv.style.display = 'block';
        } else {
            questionsList.innerHTML = '<p class="text-muted">No questions found in the selected question banks.</p>';
            previewDiv.style.display = 'block';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while previewing questions');
    });
}
</script>
@endsection
