@extends('admin.layout')

@section('title', 'Lesson Assignment Details')

@section('content')
<div class="content">
    <div class="container-fluid px-4 py-4">

        {{-- ── PAGE HEADER ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Lesson Assignment Details
                </h1>
                <p class="text-muted mb-0">View lesson assignment information</p>
            </div>
            <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Lessons
            </a>
        </div>

        {{-- ── DETAILS CARD ── --}}
        <div class="row">
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Assignment Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Lesson</h6>
                                <p class="h5 mb-0">{{ $lesson->lesson->title ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Course</h6>
                                <p class="h5 mb-0">{{ $lesson->course->course_name ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Branch</h6>
                                <p class="h5 mb-0">{{ $lesson->branch->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted mb-2">Teacher</h6>
                                <p class="h5 mb-0">{{ $lesson->teacher->first_name ?? '' }} {{ $lesson->teacher->last_name ?? '' }}</p>
                            </div>
                        </div>

                        <hr>

                        <h6 class="text-muted mb-3">Lesson Details</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">Lesson Weight</small>
                                <p class="h6 mb-2">{{ $lesson->lesson->lesson_weight ?? '0' }}%</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Max Test Marks</small>
                                <p class="h6 mb-2">{{ $lesson->lesson->max_test_marks ?? '0' }}</p>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">Max Exam Marks</small>
                                <p class="h6 mb-2">{{ $lesson->lesson->max_exam_marks ?? '0' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h6>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-warning btn-sm w-100 mb-2">
                            <i class="fas fa-edit me-2"></i>Edit Assignment
                        </a>
                        <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm w-100">
                                <i class="fas fa-trash me-2"></i>Delete Assignment
                            </button>
                        </form>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Metadata</h6>
                    </div>
                    <div class="card-body">
                        <small class="text-muted">Created</small>
                        <p class="mb-2">{{ $lesson->created_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                        
                        <small class="text-muted">Last Updated</small>
                        <p class="mb-0">{{ $lesson->updated_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
