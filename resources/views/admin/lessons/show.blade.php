@extends('admin.layout')

@section('title', 'Lesson Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lesson Details</h5>
                    <div>
                        <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Lessons
                        </a>
                        <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td>{{ $lesson->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Title:</strong></td>
                                    <td>{{ $lesson->title }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Course:</strong></td>
                                    <td>{{ $lesson->course ? $lesson->course->course_name : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Teacher:</strong></td>
                                    <td>{{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not Assigned' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Grading Information</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Max Test Marks:</strong></td>
                                    <td>{{ $lesson->max_test_marks ?? 100 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Max Exam Marks:</strong></td>
                                    <td>{{ $lesson->max_exam_marks ?? 100 }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Lesson Weight:</strong></td>
                                    <td>{{ $lesson->lesson_weight }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Academic Year:</strong></td>
                                    <td>{{ $lesson->academic_year ? $lesson->academic_year->name : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Terms</h6>
                            @if($lesson->terms->count() > 0)
                                <div class="row">
                                    @foreach($lesson->terms as $term)
                                        <div class="col-md-3">
                                            <span class="badge bg-primary">{{ $term->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No terms assigned</p>
                            @endif
                        </div>
                    </div>

                    @if($lesson->units && $lesson->units->count() > 0)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Lesson Units</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Unit Title</th>
                                            <th>Description</th>
                                            <th>Order</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lesson->units as $unit)
                                            <tr>
                                                <td>{{ $unit->title }}</td>
                                                <td>{{ $unit->description ?? 'N/A' }}</td>
                                                <td>{{ $unit->order ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('admin.lessons.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Back to Lessons
                                </a>
                                <div>
                                    <a href="{{ route('admin.lessons.edit', $lesson->id) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i> Edit Lesson
                                    </a>
                                    <a href="{{ route('admin.lessons.details.create', $lesson->id) }}" class="btn btn-info">
                                        <i class="fas fa-plus"></i> Add Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
