@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil"></i> Edit Question Bank</h2>
        <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Question Bank
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Question Bank Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.question-banks.update', $questionBank) }}">
                        @csrf
                        @method('PUT')

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Question Bank Name *</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="{{ old('name', $questionBank->name) }}" required>
                            <div class="form-text">Give your question bank a descriptive name.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $questionBank->description) }}</textarea>
                            <div class="form-text">Optional description of what this question bank contains.</div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="course_id" class="form-label">Course</label>
                                <select class="form-select" id="course_id" name="course_id">
                                    <option value="">Select Course (Optional)</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $questionBank->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate with a specific course.</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="academic_year_id" class="form-label">Academic Year</label>
                                <select class="form-select" id="academic_year_id" name="academic_year_id">
                                    <option value="">Select Academic Year (Optional)</option>
                                    @foreach($academicYears as $academicYear)
                                        <option value="{{ $academicYear->id }}" {{ old('academic_year_id', $questionBank->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                            {{ $academicYear->year }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Associate with an academic year.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                       {{ $questionBank->is_active ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Active Question Bank
                                </label>
                                <div class="form-text">Uncheck to make this question bank inactive.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Teacher</label>
                            <input type="text" class="form-control" value="{{ $questionBank->teacher->teacher_name }}" readonly>
                            <div class="form-text">Question banks cannot be transferred to other teachers.</div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-secondary me-md-2">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Update Question Bank
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
