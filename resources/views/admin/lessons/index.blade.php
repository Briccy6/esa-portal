@extends('admin.layout')

@section('title', 'Lessons Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lessons Management</h5>
                    <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Lesson
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($lessonDetails->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Test Marks</th>
                                        <th>Exam Marks</th>
                                        <th>Weight</th>
                                        <th>Terms</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lessonDetails as $lesson)
                                        <tr>
                                            <td>{{ $lesson->id }}</td>
                                            <td>{{ $lesson->title }}</td>
                                            <td>{{ $lesson->course ? $lesson->course->course_name : 'N/A' }}</td>
                                            <td>{{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not Assigned' }}</td>
                                            <td>{{ $lesson->max_test_marks ?? 100 }}</td>
                                            <td>{{ $lesson->max_exam_marks ?? 100 }}</td>
                                            <td>{{ $lesson->lesson_weight }}%</td>
                                            <td>
                                                @if($lesson->terms->count() > 0)
                                                    {{ $lesson->terms->pluck('name')->implode(', ') }}
                                                @else
                                                    <span class="text-muted">No terms</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.lessons.show', $lesson->id) }}" class="btn btn-sm btn-info" title="View">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('admin.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this lesson?')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-chalkboard fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Lessons Found</h5>
                            <p class="text-muted">Start by creating your first lesson.</p>
                            <a href="{{ route('admin.lessons.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create New Lesson
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
