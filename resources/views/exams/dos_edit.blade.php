@extends('staff.partials.sidebar_dos')

@section('title', 'Edit Exam')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-pencil-square"></i> Edit Exam</h2>
                <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Exams
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('dos.exams.update', $exam->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Exam Title *</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title', $exam->title) }}" required>
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
                                        <option value="Quiz" {{ old('exam_type', $exam->exam_type) == 'Quiz' ? 'selected' : '' }}>Quiz</option>
                                        <option value="Midterm" {{ old('exam_type', $exam->exam_type) == 'Midterm' ? 'selected' : '' }}>Midterm</option>
                                        <option value="Final" {{ old('exam_type', $exam->exam_type) == 'Final' ? 'selected' : '' }}>Final Exam</option>
                                        <option value="Assignment" {{ old('exam_type', $exam->exam_type) == 'Assignment' ? 'selected' : '' }}>Assignment</option>
                                    </select>
                                    @error('exam_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="class_date" class="form-label">Exam Date *</label>
                                    <input type="date" class="form-control @error('class_date') is-invalid @enderror" 
                                           id="class_date" name="class_date" value="{{ old('class_date', $exam->class_date->format('Y-m-d')) }}" required>
                                    @error('class_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="total_marks" class="form-label">Total Marks *</label>
                                    <input type="number" class="form-control @error('total_marks') is-invalid @enderror" 
                                           id="total_marks" name="total_marks" value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="exam_weight" class="form-label">Exam Weight (%) *</label>
                                    <input type="number" class="form-control @error('exam_weight') is-invalid @enderror" 
                                           id="exam_weight" name="exam_weight" value="{{ old('exam_weight', $exam->exam_weight) }}" min="1" max="100" required>
                                    @error('exam_weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="token_cost" class="form-label">Token Cost *</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control @error('token_cost') is-invalid @enderror" 
                                               id="token_cost" name="token_cost" value="{{ old('token_cost', $exam->token_cost ?? 10) }}" min="0" required>
                                        <span class="input-group-text">tokens</span>
                                    </div>
                                    <small class="form-text text-muted">
                                        Set to 10 if left empty (default)
                                    </small>
                                    @error('token_cost')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="branch_id" class="form-label">Branch *</label>
                                    <select class="form-select @error('branch_id') is-invalid @enderror" 
                                            id="branch_id" name="branch_id" required>
                                        <option value="">Select Branch</option>
                                        @foreach($branches as $branch)
                                            <option value="{{ $branch->id }}" {{ old('branch_id', $exam->branch_id) == $branch->id ? 'selected' : '' }}>
                                                {{ $branch->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('branch_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_year_id" class="form-label">Academic Year *</label>
                                    <select class="form-select @error('academic_year_id') is-invalid @enderror" 
                                            id="academic_year_id" name="academic_year_id" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id', $exam->academic_year_id) == $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
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
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $exam->teacher_id) == $teacher->id ? 'selected' : '' }}>
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
                                    <label for="term_id" class="form-label">Term *</label>
                                    <select class="form-select @error('term_id') is-invalid @enderror" 
                                            id="term_id" name="term_id" required>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ old('term_id', $exam->term_id) == $term->id ? 'selected' : '' }}>
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
                                                {{ in_array($course->id, old('course_ids', $exam->courses->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
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
                                                {{ in_array($lesson->id, old('lesson_ids', $exam->lessons->pluck('id')->toArray())) ? 'selected' : '' }}>
                                                {{ $lesson->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('lesson_ids')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $exam->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="2">{{ old('notes', $exam->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="exam_password" class="form-label">Exam Password</label>
                            <input type="text" class="form-control @error('exam_password') is-invalid @enderror" 
                                   id="exam_password" name="exam_password" value="{{ old('exam_password', $exam->exam_password) }}" 
                                   placeholder="Leave empty for no password">
                            @error('exam_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Exam
                            </button>
                            <a href="{{ route('dos.exams.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#course_ids, #lesson_ids').select2({
        theme: 'bootstrap-5',
        width: '100%',
        placeholder: 'Select options...'
    });

    // Set default token cost to 10 if empty
    $('#token_cost').on('blur', function() {
        if ($(this).val() === '' || $(this).val() === '0') {
            $(this).val(10);
        }
    });

    // Form validation
    $('form').on('submit', function(e) {
        var tokenCost = parseInt($('#token_cost').val());
        if (isNaN(tokenCost) || tokenCost < 0) {
            e.preventDefault();
            $('#token_cost').val(10);
            alert('Token cost has been set to 10 (default)');
            return false;
        }
    });
});
</script>
@endsection
