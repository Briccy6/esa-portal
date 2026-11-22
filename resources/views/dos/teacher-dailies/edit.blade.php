@extends('dos.layout')

@section('title', 'Edit Teacher Daily Activity')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-pencil me-2"></i>Edit Daily Activity</h2>
        <a href="{{ route('dos.teacher-dailies.show', $daily->id) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Details
        </a>
    </div>

    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">Edit Daily Activity</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('dos.teacher-dailies.update', $daily->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="course_id" class="form-label">Course</label>
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option value="">Select Course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ $daily->course_id == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="branch_id" class="form-label">Branch</label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ $daily->branch_id == $branch->id ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="lesson_id" class="form-label">Lesson</label>
                            <input type="text" class="form-control" id="lesson_id" name="lesson_id" 
                                   value="{{ $daily->lesson_id }}" readonly>
                            <small class="text-muted">Lesson ID cannot be changed</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="unit_id" class="form-label">Unit ID</label>
                            <input type="text" class="form-control" id="unit_id" name="unit_id" 
                                   value="{{ $daily->unit_id ?? '' }}" placeholder="Leave blank if not applicable">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="hours_studied" class="form-label">Hours Studied</label>
                            <input type="number" class="form-control" id="hours_studied" name="hours_studied" 
                                   value="{{ $daily->hours_studied }}" min="0" max="24" step="0.5" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="total_students" class="form-label">Total Students</label>
                            <input type="number" class="form-control" id="total_students" name="total_students" 
                                   value="{{ $daily->total_students }}" min="0" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="absences" class="form-label">Absences</label>
                            <input type="text" class="form-control" id="absences" name="absences" 
                                   value="{{ $daily->absences ?? '' }}" placeholder="e.g., Student1, Student2">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="activity" class="form-label">Activities Done</label>
                    <textarea class="form-control" id="activity" name="activity[]" rows="3" 
                              placeholder="Enter each activity on a new line">{{ old('activity', is_array($daily->activity) ? implode("\n", $daily->activity) : $daily->activity) }}</textarea>
                    <small class="text-muted">Enter each activity on a new line</small>
                </div>

                <div class="mb-3">
                    <label for="comment" class="form-label">Comments</label>
                    <textarea class="form-control" id="comment" name="comment" rows="3" 
                              placeholder="Add any comments or notes">{{ $daily->comment ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="challenges" class="form-label">Challenges Faced</label>
                    <textarea class="form-control" id="challenges" name="challenges" rows="3" 
                              placeholder="Describe any challenges faced during the session">{{ $daily->challenges ?? '' }}</textarea>
                </div>

                <div class="mb-3">
                    <label for="quiz_link" class="form-label">Quiz/Test Link</label>
                    <input type="url" class="form-control" id="quiz_link" name="quiz_link" 
                           value="{{ $daily->quiz_link ?? '' }}" placeholder="https://example.com/quiz">
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> You can only edit daily activities that have not yet been approved.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i> Save Changes
                    </button>
                    <a href="{{ route('dos.teacher-dailies.show', $daily->id) }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-2"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
