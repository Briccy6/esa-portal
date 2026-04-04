@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-book me-2 text-primary"></i>
                        {{ $course->course_name }}
                    </h2>
                    <p class="text-muted mb-0">Course Details & Lesson Management</p>
                </div>
                <div>
                    <a href="{{ route('dos.courses.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to Courses
                    </a>
                    <a href="{{ route('dos.courses.edit', $course) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>Edit Course
                    </a>
                </div>
            </div>

            <!-- Course Information Card -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Course Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold pe-3">Course Name:</td>
                                    <td>{{ $course->course_name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Course Code:</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $course->course_code }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Abbreviation:</td>
                                    <td>
                                        <span class="badge bg-info">{{ $course->abbreviation }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Course Type:</td>
                                    <td>{{ $course->course_type }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Academic Year:</td>
                                    <td>
                                        @if($course->academicYear)
                                            {{ $course->academicYear->year }}
                                        @else
                                            <span class="text-muted">Not assigned</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-gradient-success text-white">
                            <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Course Fees</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless mb-0">
                                <tr>
                                    <td class="fw-semibold pe-3">Registration Fee:</td>
                                    <td class="text-end">
                                        <span class="badge bg-success">₦{{ number_format($course->registration_fee, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Tuition Fee:</td>
                                    <td class="text-end">
                                        <span class="badge bg-info">₦{{ number_format($course->tuition_fee, 2) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-semibold pe-3">Practical Fee:</td>
                                    <td class="text-end">
                                        <span class="badge bg-warning">₦{{ number_format($course->practical_fee, 2) }}</span>
                                    </td>
                                </tr>
                                @if($course->trip_fee)
                                    <tr>
                                        <td class="fw-semibold pe-3">Trip Fee:</td>
                                        <td class="text-end">
                                            <span class="badge bg-danger">₦{{ number_format($course->trip_fee, 2) }}</span>
                                        </td>
                                    </tr>
                                @endif
                                @if($course->discount_fee)
                                    <tr>
                                        <td class="fw-semibold pe-3">Discount:</td>
                                        <td class="text-end">
                                            <span class="badge bg-secondary">₦{{ number_format($course->discount_fee, 2) }}</span>
                                        </td>
                                    </tr>
                                @endif
                                <tr class="border-top">
                                    <td class="fw-bold pe-3">Total Cost:</td>
                                    <td class="text-end fw-bold">
                                        <span class="badge bg-dark fs-6">₦{{ number_format($course->total_cost ?? ($course->registration_fee + $course->tuition_fee + $course->practical_fee + ($course->trip_fee ?? 0) - ($course->discount_fee ?? 0)), 2) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lessons Section -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-list me-2"></i>Lessons ({{ count($course->lessons) }})</h5>
                        <span class="badge bg-light text-dark">{{ count($course->lessons) }} total</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($course->lessons) > 0)
                        <div class="row">
                            @foreach($course->lessons as $lesson)
                                <div class="col-md-6 mb-3">
                                    <div class="lesson-card p-3 border rounded h-100" style="border-left: 4px solid #0d6efd;">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 fw-semibold">{{ $lesson->title }}</h6>
                                            @if($lesson->teacher)
                                                <span class="badge bg-success">Assigned</span>
                                            @else
                                                <span class="badge bg-warning">Unassigned</span>
                                            @endif
                                        </div>
                                        
                                        @if($lesson->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($lesson->description, 100) }}</p>
                                        @endif
                                        
                                        <div class="d-flex align-items-center mt-3">
                                            <i class="fas fa-user-tie text-primary me-2"></i>
                                            @if($lesson->teacher)
                                                <span class="fw-semibold">{{ $lesson->teacher->teacher_name }}</span>
                                            @else
                                                <span class="text-muted fst-italic">No teacher assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Lessons Found</h5>
                            <p class="text-muted mb-0">This course doesn't have any lessons yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Teachers Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-warning text-dark">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-chalkboard-user me-2"></i>Teachers ({{ count($course->teachers) }})</h5>
                        <span class="badge bg-light text-dark">{{ count($course->teachers) }} teachers</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(count($course->teachers) > 0)
                        <div class="row">
                            @foreach($course->teachers as $teacher)
                                <div class="col-md-4 mb-3">
                                    <div class="teacher-card p-3 border rounded h-100" style="border-left: 4px solid #ffc107;">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-2">
                                                <i class="fas fa-user text-warning"></i>
                                            </div>
                                            <h6 class="mb-0 fw-semibold">{{ $teacher->teacher_name }}</h6>
                                        </div>
                                        
                                        <div class="small mb-2">
                                            <strong>Lessons:</strong>
                                        </div>
                                        <div class="mb-0">
                                            @php
                                                $teacherLessons = $course->lessons->where('teacher_id', $teacher->id);
                                            @endphp
                                            @if(count($teacherLessons) > 0)
                                                @foreach($teacherLessons as $lesson)
                                                    <span class="badge bg-light text-dark me-1 mb-1">
                                                        <i class="fas fa-book-open me-1"></i>{{ $lesson->title }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted fst-italic small">No lessons assigned</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-user-slash fs-1 text-muted mb-3"></i>
                            <h5 class="text-muted">No Teachers Assigned</h5>
                            <p class="text-muted mb-0">No teachers have been assigned to this course yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #0c5460);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .lesson-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #f8f9fa;
    }
    
    .lesson-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    
    .teacher-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #fffbf0;
    }
    
    .teacher-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
</style>
@endsection
