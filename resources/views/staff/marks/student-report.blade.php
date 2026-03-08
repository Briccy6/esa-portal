@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-user-graduate me-2 text-primary"></i>
                        Student Academic Report
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $student->name }} (ID: {{ $student->id }})
                        @if($student->course)
                            - {{ $student->course->course_name }}
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Marks
                    </a>
                    <button onclick="window.print()" class="btn btn-primary">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>

            <!-- Student Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Student Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="bg-primary bg-opacity-10 rounded p-3 text-center">
                                <i class="fas fa-user-circle fs-1 text-primary mb-2"></i>
                                <h6 class="fw-bold">{{ $student->name }}</h6>
                                <small class="text-muted">Student ID: {{ $student->id }}</small>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Branch:</strong> {{ $student->branch->branch_name ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Class:</strong> {{ $student->class ?? 'N/A' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2"><strong>Gender:</strong> {{ $student->gender ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Phone:</strong> {{ $student->phone ?? 'N/A' }}</p>
                                    <p class="mb-2"><strong>Email:</strong> {{ $student->email ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Summary -->
            <div class="row mb-4">
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-info text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['total_assessments'] }}</h5>
                            <small>Total Assessments</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ number_format($stats['average_score'], 1) }}</h5>
                            <small>Average Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['highest_score'] }}</h5>
                            <small>Highest Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['lowest_score'] }}</h5>
                            <small>Lowest Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-file-alt fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['exam_count'] }}</h5>
                            <small>Exams</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 bg-gradient-secondary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-pencil-alt fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['test_count'] }}</h5>
                            <small>Tests</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marks by Course -->
            @foreach($marksByCourse as $courseId => $courseMarks)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>
                            {{ $courseMarks->first()->course->course_name ?? 'Course ' . $courseId }}
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Assessment</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Grade</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseMarks as $mark)
                                        <tr>
                                            <td>{{ $mark->created_at ? $mark->created_at->format('M d, Y') : '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $mark->assessment_type == 'exam' ? 'primary' : 'secondary' }}">
                                                    {{ ucfirst($mark->assessment_type) }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $mark->exam->exam_name ?? 'Test' }}
                                                @if($mark->lesson)
                                                    <br><small class="text-muted">{{ $mark->lesson->title }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold {{ $mark->value >= ($mark->marks_out_of * 0.5) ? 'text-success' : 'text-danger' }}">
                                                    {{ $mark->value ?? '-' }}
                                                </span>
                                                @if($mark->marks_out_of)
                                                    <br><small class="text-muted">/ {{ $mark->marks_out_of }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($mark->grade)
                                                    <span class="badge bg-light text-dark">{{ $mark->grade->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($mark->value >= ($mark->marks_out_of * 0.5))
                                                    <span class="badge bg-success">Pass</span>
                                                @else
                                                    <span class="badge bg-danger">Fail</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Performance Chart -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Performance Overview
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="performanceChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #28a745, #1e7e34);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
    }
    
    .bg-gradient-secondary {
        background: linear-gradient(135deg, #6c757d, #545b62);
    }
    
    @media print {
        .btn, .d-flex.gap-2 {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('performanceChart').getContext('2d');
    
    const labels = @json($marks->pluck(function($mark) {
        return $mark->exam ? $mark->exam->exam_name : 'Test - ' . $mark->created_at->format('M d');
    }));
    
    const data = @json($marks->pluck('value'));
    const colors = data.map(score => score >= 50 ? 'rgba(40, 167, 69, 0.8)' : 'rgba(220, 53, 69, 0.8)');
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Score',
                data: data,
                backgroundColor: colors,
                borderColor: colors.map(color => color.replace('0.8', '1')),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
