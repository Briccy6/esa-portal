@extends('teacher.layout')

@section('styles')
<style>
.exams-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.exams-header h1 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.exams-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.exams-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.exams-card .card-header {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.filters-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}

.filters-card .card-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.exams-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.exams-table .table {
    margin-bottom: 0;
}

.exams-table .table thead {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.exams-table .table thead th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.exams-table .table td {
    padding: 0.875rem;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
}

.exams-table .table tbody tr:hover {
    background-color: #eff6ff;
}

.badge-custom {
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.8rem;
}

.badge-info {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

.badge-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.badge-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
}

.badge-secondary {
    background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    color: white;
}

.btn-action {
    padding: 0.5rem 0.75rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s ease;
    border: none;
    text-decoration: none;
    display: inline-block;
}

.btn-view {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
}

.btn-view:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
    color: white;
}

.btn-edit {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white;
}

.btn-edit:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    color: white;
}

.btn-questions {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    color: white;
}

.btn-questions:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(6, 182, 212, 0.3);
    color: white;
}

.btn-delete {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-delete:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
    color: white;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(59, 130, 246, 0.3);
    color: white;
}

.btn-outline-custom {
    border: 2px solid #3b82f6;
    color: #3b82f6;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-outline-custom:hover {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border-color: transparent;
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    border: 2px dashed #e5e7eb;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

@media (max-width: 768px) {
    .exams-table .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .exams-table .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .btn-action {
        padding: 0.375rem 0.5rem;
        font-size: 0.75rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="exams-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1><i class="bi bi-clipboard-data"></i> My Exams</h1>
                    <p class="mb-0">Create and manage examinations for your students</p>
                </div>
                <a href="{{ route('teacher.exams.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle me-2"></i>Create New Exam
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="container">
        <div class="filters-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filter & Search Exams</h5>
            </div>
            <div class="card-body p-4">
                <form method="GET" action="{{ route('teacher.exams.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="course_filter" class="form-label">Filter by Course</label>
                            <select name="course_filter" id="course_filter" class="form-select">
                                <option value="">All Courses</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course_filter') == $course->id ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status_filter" class="form-label">Filter by Status</label>
                            <select name="status_filter" id="status_filter" class="form-select">
                                <option value="">All Status</option>
                                <option value="draft" {{ request('status_filter') == 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ request('status_filter') == 'published' ? 'selected' : '' }}>Published</option>
                                <option value="completed" {{ request('status_filter') == 'completed' ? 'selected' : '' }}>Completed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="search" class="form-label">Search</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   value="{{ request('search') }}" placeholder="Search exams...">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-custom me-2">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise"></i> Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="container">
        <div class="exams-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-ul"></i> All Exams</h5>
            </div>
            <div class="card-body p-0">
                @if($exams->count() > 0)
                    <div class="table-responsive exams-table">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Total Marks</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exams as $exam)
                                    <tr>
                                        <td>
                                            <strong>{{ $exam->title }}</strong>
                                            @if($exam->description)
                                                <br><small class="text-muted">{{ Str::limit($exam->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($exam->courses->count() > 0)
                                                @foreach($exam->courses->take(2) as $course)
                                                    <span class="badge badge-custom badge-secondary">{{ $course->course_name }}</span>
                                                @endforeach
                                                @if($exam->courses->count() > 2)
                                                    <span class="badge badge-custom badge-secondary">+{{ $exam->courses->count() - 2 }}</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No course</span>
                                            @endif
                                        </td>
                                        <td>{{ $exam->class_date->format('M d, Y') }}</td>
                                        <td><span class="badge badge-custom badge-info">{{ $exam->exam_type }}</span></td>
                                        <td><strong>{{ $exam->total_marks }}</strong></td>
                                        <td>
                                            @if($exam->is_public)
                                                <span class="badge badge-custom badge-success">Public</span>
                                            @else
                                                <span class="badge badge-custom badge-warning">Private</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('teacher.exams.show', $exam) }}" class="btn btn-action btn-view" title="View">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-action btn-edit" title="Edit">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="{{ route('teacher.exams.questions', $exam) }}" class="btn btn-action btn-questions" title="Questions">
                                                    <i class="bi bi-question-circle"></i> Questions
                                                </a>
                                                <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-action btn-delete" title="Delete" 
                                                            onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone and will remove all associated data.')">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="pagination-info">
                            Showing {{ $exams->firstItem() }} to {{ $exams->lastItem() }} of {{ $exams->total() }} exams
                        </div>
                        <div>
                            {{ $exams->links() }}
                        </div>
                    </div>
                @else
                    <div class="empty-state m-4">
                        <i class="bi bi-clipboard-x display-1"></i>
                        <h4 class="mt-4">No Exams Found</h4>
                        <p class="text-muted mb-4">You haven't created any exams yet.</p>
                        <a href="{{ route('teacher.exams.create') }}" class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Exam
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
