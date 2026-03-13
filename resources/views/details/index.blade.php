@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Manage Units</h1>
                    <p class="text-muted mb-0">View and manage all lesson units</p>
                </div>
            </div>
        </div>
    </div>

    @if($lessonDetails->count())
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0">All Units ({{ $lessonDetails->count() }})</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Unit Name</th>
                                        <th>Lesson</th>
                                        <th>Course</th>
                                        <th>Teacher</th>
                                        <th>Hours</th>
                                        <th>Term</th>
                                        <th>Level</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lessonDetails as $unit)
                                        <tr>
                                            <td>{{ $unit->id }}</td>
                                            <td>{{ $unit->unit_name }}</td>
                                            <td>
                                                @if($unit->lesson)
                                                    <a href="{{ route('staff.lessons.edit', $unit->lesson->id) }}" class="text-decoration-none">
                                                        {{ $unit->lesson->title }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">No lesson</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($unit->lesson && $unit->lesson->course)
                                                    {{ $unit->lesson->course->course_name ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($unit->lesson && $unit->lesson->teacher)
                                                    {{ $unit->lesson->teacher->teacher_name ?? 'N/A' }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $unit->hours }}</td>
                                            <td>
                                                @if($unit->term)
                                                    {{ $unit->term->name }}
                                                    @if($unit->term->academicYear)
                                                        <small class="text-muted">({{ $unit->term->academicYear->year }})</small>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No term</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ 
                                                    $unit->course_level == 'beginner' ? 'success' : 
                                                    ($unit->course_level == 'intermediate' ? 'warning' : 'danger') 
                                                }}">
                                                    {{ ucfirst($unit->course_level) }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('staff.lesson-units.edit', $unit->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="{{ route('staff.lesson-units.destroy', $unit->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this unit?')">
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
                        <div class="mb-4">
                            <i class="bi bi-diagram-3 display-1 text-muted"></i>
                        </div>
                        <h3 class="text-muted">No Units Found</h3>
                        <p class="text-muted mb-4">There are no units created yet.</p>
                        <a href="{{ route('staff.lessons.index') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Manage Lessons to Add Units
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
