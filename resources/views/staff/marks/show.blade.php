@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-eye me-2 text-primary"></i>
                        Mark Details
                    </h2>
                    <p class="text-muted mb-0">View detailed information about this mark entry</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Marks
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i> Print
                    </button>
                </div>
            </div>

            <!-- Mark Details Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-check me-2"></i>Mark Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Student Information -->
                        <div class="col-md-6">
                            <h6 class="text-primary fw-semibold mb-3">
                                <i class="fas fa-user-graduate me-2"></i>Student Details
                            </h6>
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="bg-primary bg-opacity-20 rounded-circle p-3 d-inline-block">
                                            <i class="fas fa-user fs-1 text-primary"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <p class="mb-2"><strong>Name:</strong> {{ $mark->student->name ?? 'Unknown' }}</p>
                                        <p class="mb-2"><strong>Student ID:</strong> {{ $mark->student_id }}</p>
                                        <p class="mb-2"><strong>Course:</strong> {{ $mark->student->course->course_name ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Branch:</strong> {{ $mark->student->branch->branch_name ?? 'N/A' }}</p>
                                        <p class="mb-0"><strong>Class:</strong> {{ $mark->student->class ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Assessment Information -->
                        <div class="col-md-6">
                            <h6 class="text-info fw-semibold mb-3">
                                <i class="fas fa-clipboard me-2"></i>Assessment Details
                            </h6>
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <div class="bg-info bg-opacity-20 rounded-circle p-3 d-inline-block">
                                            <i class="fas fa-{{ $mark->assessment_type == 'exam' ? 'file-alt' : 'pencil-alt' }} fs-1 text-info"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <p class="mb-2"><strong>Type:</strong> 
                                            <span class="badge bg-{{ $mark->assessment_type == 'exam' ? 'primary' : 'secondary' }}">
                                                {{ ucfirst($mark->assessment_type) }}
                                            </span>
                                        </p>
                                        <p class="mb-2"><strong>Course:</strong> {{ $mark->course->course_name ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Branch:</strong> {{ $mark->branch->branch_name ?? 'N/A' }}</p>
                                        @if($mark->exam)
                                            <p class="mb-2"><strong>Exam:</strong> {{ $mark->exam->exam_name }}</p>
                                        @endif
                                        @if($mark->lesson)
                                            <p class="mb-0"><strong>Lesson:</strong> {{ $mark->lesson->title }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Performance Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bg-success bg-opacity-10 rounded p-3 text-center">
                                        <h3 class="fw-bold text-success">{{ $mark->value ?? 0 }}</h3>
                                        <p class="mb-0">Score Obtained</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bg-info bg-opacity-10 rounded p-3 text-center">
                                        <h3 class="fw-bold text-info">{{ $mark->marks_out_of ?? '-' }}</h3>
                                        <p class="mb-0">Total Marks</p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($mark->marks_out_of)
                                <div class="mt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-semibold">Percentage</span>
                                        <span class="badge bg-{{ $mark->value >= ($mark->marks_out_of * 0.8) ? 'success' : ($mark->value >= ($mark->marks_out_of * 0.5) ? 'warning' : 'danger') }} fs-6">
                                            {{ number_format(($mark->value / $mark->marks_out_of) * 100, 1) }}%
                                        </span>
                                    </div>
                                    <div class="progress" style="height: 25px;">
                                        <div class="progress-bar bg-{{ $mark->value >= ($mark->marks_out_of * 0.8) ? 'success' : ($mark->value >= ($mark->marks_out_of * 0.5) ? 'warning' : 'danger') }}" 
                                             role="progressbar" 
                                             style="width: {{ min(($mark->value / $mark->marks_out_of) * 100, 100) }}%">
                                            {{ number_format(($mark->value / $mark->marks_out_of) * 100, 1) }}%
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <h6 class="text-warning fw-semibold mb-3">Performance Status</h6>
                                <div class="text-center">
                                    @if($mark->value >= ($mark->marks_out_of * 0.8))
                                        <i class="fas fa-trophy fs-1 text-success mb-2"></i>
                                        <h5 class="text-success fw-bold">Excellent</h5>
                                        <p class="text-muted mb-0">Outstanding performance!</p>
                                    @elseif($mark->value >= ($mark->marks_out_of * 0.6))
                                        <i class="fas fa-thumbs-up fs-1 text-info mb-2"></i>
                                        <h5 class="text-info fw-bold">Good</h5>
                                        <p class="text-muted mb-0">Above average performance</p>
                                    @elseif($mark->value >= ($mark->marks_out_of * 0.5))
                                        <i class="fas fa-check-circle fs-1 text-warning mb-2"></i>
                                        <h5 class="text-warning fw-bold">Average</h5>
                                        <p class="text-muted mb-0">Satisfactory performance</p>
                                    @else
                                        <i class="fas fa-exclamation-triangle fs-1 text-danger mb-2"></i>
                                        <h5 class="text-danger fw-bold">Needs Improvement</h5>
                                        <p class="text-muted mb-0">Below average performance</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Information -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-medal me-2"></i>Grade Information
                    </h5>
                </div>
                <div class="card-body">
                    @if($mark->grade)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="bg-warning bg-opacity-10 rounded p-3 text-center">
                                    <h2 class="fw-bold text-warning">{{ $mark->grade->name }}</h2>
                                    <p class="mb-0">Grade Achieved</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6 class="fw-semibold mb-3">Grade Details</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Grade Name:</strong> {{ $mark->grade->name }}</p>
                                        <p class="mb-2"><strong>Description:</strong> {{ $mark->grade->description ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-2"><strong>Min Score:</strong> {{ $mark->grade->min_score ?? 'N/A' }}</p>
                                        <p class="mb-2"><strong>Max Score:</strong> {{ $mark->grade->max_score ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle text-muted fs-1 mb-3"></i>
                            <h5 class="text-muted">No Grade Assigned</h5>
                            <p class="text-muted mb-0">This mark entry does not have a grade assigned yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Additional Information -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Additional Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">Timestamp Information</h6>
                            <div class="bg-secondary bg-opacity-10 rounded p-3">
                                <p class="mb-2"><strong>Created:</strong> {{ $mark->created_at ? $mark->created_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                <p class="mb-2"><strong>Last Updated:</strong> {{ $mark->updated_at ? $mark->updated_at->format('M d, Y h:i A') : 'N/A' }}</p>
                                <p class="mb-2"><strong>Status:</strong> 
                                    <span class="badge bg-success">{{ $mark->status ?? 'Recorded' }}</span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold mb-3">System Information</h6>
                            <div class="bg-secondary bg-opacity-10 rounded p-3">
                                <p class="mb-2"><strong>Mark ID:</strong> {{ $mark->id }}</p>
                                <p class="mb-2"><strong>Assessment Type:</strong> {{ ucfirst($mark->assessment_type) }}</p>
                                <p class="mb-0"><strong>Record Status:</strong> 
                                    <span class="badge bg-info">Active</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="card shadow-sm mt-4">
                <div class="card-body text-center">
                    <div class="d-flex gap-2 justify-content-center">
                        <a href="{{ route('dos.marks.student-report', ['student_id' => $mark->student_id]) }}" 
                           class="btn btn-primary">
                            <i class="fas fa-user-graduate me-1"></i> View Student Report
                        </a>
                        <button onclick="window.print()" class="btn btn-success">
                            <i class="fas fa-print me-1"></i> Print Details
                        </button>
                        <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list me-1"></i> Back to All Marks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d, #545b62);
    }
    
    @media print {
        .btn, .d-flex.gap-2, .d-flex.gap-2.justify-content-center {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
        }
        
        .progress {
            border: 1px solid #dee2e6;
        }
    }
</style>
@endsection
