@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Manage Lessons</h1>
                    <p class="text-muted mb-0">View, edit, and manage all lessons</p>
                </div>
                <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Create New Lesson
                </a>
            </div>
        </div>
    </div>

    @if($lessonDetails->count())
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">All Lessons ({{ $lessonDetails->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Units</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lessonDetails as $lesson)
                                        <tr>
                                            <td>{{ $lesson->id }}</td>
                                            <td>{{ $lesson->title }}</td>
                                            <td>{{ $lesson->course->course_name ?? 'N/A' }}</td>
                                            <td>{{ $lesson->teacher->teacher_name ?? 'N/A' }}</td>
                                            <td>{{ $lesson->units->count() }}</td>
                                            <td>
                                                <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="{{ route('staff.lesson-units.create', $lesson->id) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="bi bi-diagram-3"></i> Units
                                                </a>
                                                <form action="{{ route('staff.lessons.destroy', $lesson->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this lesson? This will permanently delete the lesson and ALL related data including units, marks, attendance, and exam associations. This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm text-center py-5">
                    <div class="card-body">
                        <h3 class="text-muted">No Lessons Found</h3>
                        <p class="text-muted mb-4">There are no lessons created yet.</p>
                        <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Create Your First Lesson
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
