@extends('dos.layout')

@section('title', 'Exams Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-clipboard-data-fill"></i> Exams Management</h2>
                <a href="{{ route('dos.exams.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New Exam
                </a>
            </div>

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h4>{{ $exams->count() }}</h4>
                            <p>Total Exams</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h4>{{ $exams->where('is_active', true)->count() }}</h4>
                            <p>Active Exams</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h4>{{ \App\Models\ExamAttempt::count() }}</h4>
                            <p>Total Attempts</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h4>{{ \App\Models\QuestionBank::count() }}</h4>
                            <p>Question Banks</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dos.exams.index') }}">
                        <div class="row">
                            <div class="col-md-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search exams...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Course</label>
                                <select name="course" class="form-select">
                                    <option value="">All Courses</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-outline-primary">Filter</button>
                                    <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">Clear</a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Exams Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course(s)</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exams as $exam)
                                    <tr>
                                        <td>
                                            <strong>{{ $exam->title }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $exam->exam_type }}</small>
                                        </td>
                                        <td>
                                            @foreach($exam->courses->take(2) as $course)
                                                <span class="badge bg-info">{{ $course->course_name }}</span>
                                            @endforeach
                                            @if($exam->courses->count() > 2)
                                                <span class="badge bg-secondary">+{{ $exam->courses->count() - 2 }} more</span>
                                            @endif
                                        </td>
                                        <td>{{ $exam->class_date->format('M d, Y') }}</td>
                                        <td>
                                            @if($exam->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('dos.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('dos.exams.edit', $exam->id) }}" class="btn btn-sm btn-outline-warning">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('dos.exams.destroy', $exam->id) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No exams found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($exams->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $exams->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
