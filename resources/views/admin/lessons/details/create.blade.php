@extends('admin.layout')

@section('title', 'Create Lesson Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create Lesson Details - {{ $lesson->title }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lessons.details.store', $lesson->id) }}">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Lesson:</strong> {{ $lesson->title }}<br>
                                    <strong>Course:</strong> {{ $lesson->course ? $lesson->course->course_name : 'N/A' }}<br>
                                    <strong>Terms:</strong> {{ $terms->pluck('name')->implode(', ') }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="outcome_title" class="form-label">Outcome Title *</label>
                                    <input type="text" class="form-control @error('outcome_title') is-invalid @enderror" 
                                           id="outcome_title" name="outcome_title" value="{{ old('outcome_title') }}" required>
                                    @error('outcome_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="outcome_code" class="form-label">Outcome Code</label>
                                    <input type="text" class="form-control @error('outcome_code') is-invalid @enderror" 
                                           id="outcome_code" name="outcome_code" value="{{ old('outcome_code') }}">
                                    @error('outcome_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="outcome_description" class="form-label">Outcome Description</label>
                            <textarea class="form-control @error('outcome_description') is-invalid @enderror" 
                                      id="outcome_description" name="outcome_description" rows="4">{{ old('outcome_description') }}</textarea>
                            @error('outcome_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="max_marks" class="form-label">Maximum Marks</label>
                                    <input type="number" class="form-control @error('max_marks') is-invalid @enderror" 
                                           id="max_marks" name="max_marks" value="{{ old('max_marks', 100) }}" min="1">
                                    @error('max_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="pass_marks" class="form-label">Passing Marks</label>
                                    <input type="number" class="form-control @error('pass_marks') is-invalid @enderror" 
                                           id="pass_marks" name="pass_marks" value="{{ old('pass_marks', 50) }}" min="0">
                                    @error('pass_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="assessment_type" class="form-label">Assessment Type</label>
                                    <select class="form-select @error('assessment_type') is-invalid @enderror" 
                                            id="assessment_type" name="assessment_type">
                                        <option value="">Select assessment type</option>
                                        <option value="test" {{ old('assessment_type') == 'test' ? 'selected' : '' }}>Test</option>
                                        <option value="exam" {{ old('assessment_type') == 'exam' ? 'selected' : '' }}>Exam</option>
                                        <option value="assignment" {{ old('assessment_type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                                        <option value="practical" {{ old('assessment_type') == 'practical' ? 'selected' : '' }}>Practical</option>
                                        <option value="project" {{ old('assessment_type') == 'project' ? 'selected' : '' }}>Project</option>
                                    </select>
                                    @error('assessment_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="learning_objectives" class="form-label">Learning Objectives</label>
                            <textarea class="form-control @error('learning_objectives') is-invalid @enderror" 
                                      id="learning_objectives" name="learning_objectives" rows="3" 
                                      placeholder="Enter learning objectives (one per line)">{{ old('learning_objectives') }}</textarea>
                                    @error('learning_objectives')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                        </div>

                        <input type="hidden" name="terms" value="{{ $terms->pluck('id')->implode(',') }}">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.lessons.show', $lesson->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Lesson
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Lesson Details
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
