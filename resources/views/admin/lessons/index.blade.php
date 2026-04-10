@extends('admin.layout')

@section('title', 'Lessons Management')

@section('content')
<div class="content">
    <div class="container-fluid px-4 py-4">

        {{-- ── PAGE HEADER ── --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0">
                    <i class="fas fa-book me-2"></i>Lessons Management
                </h1>
                <p class="text-muted mb-0">Manage lesson assignments across branches and courses</p>
            </div>
            <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Lesson Assignment
            </a>
        </div>

        {{-- ── ALERTS ── --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error!</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ── FILTERS ── --}}
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.lessons.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Course</label>
                        <select name="course_id" class="form-select">
                            <option value="">All Courses</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Teacher</label>
                        <select name="teacher_id" class="form-select">
                            <option value="">All Teachers</option>
                            @foreach ($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->first_name }} {{ $teacher->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Search Lesson</label>
                        <input type="text" name="search" class="form-control" placeholder="Search lesson title..."
                            value="{{ request('search') }}">
                    </div>

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-2"></i>Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── LESSONS TABLE ── --}}
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0">
                    <i class="fas fa-list me-2"></i>Lesson Assignments
                    <span class="badge bg-light text-dark ms-2">{{ $lessonAssignments->total() }}</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @if ($lessonAssignments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="22%">Lesson</th>
                                    <th width="17%">Course</th>
                                    <th width="17%">Branch</th>
                                    <th width="22%">Teacher Name</th>
                                    <th width="12%">Max Marks</th>
                                    <th width="10%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lessonAssignments as $assignment)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $assignment->lesson->title ?? 'N/A' }}</div>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-percentage me-1"></i>Weight: {{ $assignment->lesson->lesson_weight ?? 0 }}%
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-white">
                                                {{ $assignment->course->course_name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary text-white">
                                                {{ $assignment->branch->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-success">
                                                <i class="fas fa-user-tie me-1"></i>
                                                @if($assignment->teacher)
                                                    {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}
                                                @else
                                                    <span class="text-danger">No Teacher Assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-nowrap badge bg-light text-dark">
                                                {{ $assignment->lesson->max_test_marks ?? '0' }}/{{ $assignment->lesson->max_exam_marks ?? '0' }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.lessons.edit', $assignment->id) }}" 
                                                class="btn btn-sm btn-warning" title="Edit Assignment">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.lessons.destroy', $assignment->id) }}" 
                                                method="POST" class="d-inline" 
                                                onsubmit="return confirm('Delete this assignment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete Assignment">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div class="d-flex justify-content-between align-items-center p-3 border-top">
                        <div class="text-muted">
                            Showing {{ $lessonAssignments->firstItem() }} to {{ $lessonAssignments->lastItem() }} 
                            of {{ $lessonAssignments->total() }} results
                        </div>
                        {{ $lessonAssignments->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="alert alert-info m-0">
                        <i class="fas fa-info-circle me-2"></i>
                        No lesson assignments found. 
                        <a href="{{ route('admin.lessons.create') }}" class="alert-link">Create one now</a>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }

    .btn-sm {
        padding: 0.25rem 0.5rem;
        margin: 0 2px;
    }
</style>
@endsection
