@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-users me-2 text-info"></i>
                        Class Academic Report
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $course->course_name }} - {{ $branch->name }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Marks
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                </div>
            </div>

            <!-- Class Information Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-school me-2"></i>Class Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <h6 class="fw-bold text-info">{{ $course->course_name }}</h6>
                                <p class="mb-1"><strong>Branch:</strong> {{ $branch->name }}</p>
                                <p class="mb-0"><strong>Total Students:</strong> {{ $stats['total_students'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-primary">{{ $stats['students_with_marks'] }}</h4>
                                        <small class="text-muted">Students with Marks</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center">
                                        <h4 class="fw-bold text-success">{{ number_format($stats['pass_rate'], 1) }}%</h4>
                                        <small class="text-muted">Pass Rate</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-primary text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ number_format($stats['average_score'], 1) }}</h5>
                            <small>Class Average</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-success text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-up fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['highest_score'] }}</h5>
                            <small>Highest Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-danger text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-arrow-down fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ $stats['lowest_score'] }}</h5>
                            <small>Lowest Score</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 bg-gradient-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-percentage fs-2 mb-2"></i>
                            <h5 class="fw-bold">{{ number_format($stats['pass_rate'], 1) }}%</h5>
                            <small>Pass Rate</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Distribution -->
            @if($gradeDistribution->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-pie me-2"></i>Grade Distribution
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <canvas id="gradeChart" height="80"></canvas>
                            </div>
                            <div class="col-md-4">
                                @foreach($gradeDistribution as $grade => $count)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-light text-dark">{{ $grade }}</span>
                                        <span class="fw-bold">{{ $count }} students</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Class Marks Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Student Performance Rankings
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Rank</th>
                                    <th>Student Name</th>
                                    <th>Student ID</th>
                                    <th class="text-center">Total Score</th>
                                    <th class="text-center">Average</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $rank = 1;
                                @endphp
                                @foreach($studentPerformance as $performance)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">#{{ $rank }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <span class="fw-semibold">{{ $performance->student->full_name ?? 'Unknown' }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $performance->student->reg_number ?? $performance->student_id }}</td>
                                        <td class="text-center">
                                            <span class="fw-bold {{ $performance->total_marks >= 50 ? 'text-success' : ($performance->total_marks > 0 ? 'text-danger' : 'text-muted') }}">
                                                {{ number_format($performance->total_marks, 1) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info text-white">
                                                {{ number_format($performance->average_marks, 1) }}%
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-dark">{{ $performance->grade }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if($performance->pass_status === 'Pass')
                                                <span class="badge bg-success">Pass</span>
                                            @elseif($performance->pass_status === 'Fail')
                                                <span class="badge bg-danger">Fail</span>
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @php
                                        $rank++;
                                    @endphp
                                @endforeach
                                
                                @if(empty($studentPerformance))
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-info-circle text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No marks found for this class</p>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Students Without Marks -->
            @php
                $studentsWithoutMarks = array_filter($studentPerformance, fn($p) => $p->marks_count == 0);
            @endphp
            @if(!empty($studentsWithoutMarks))
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>Students Without Marks
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($studentsWithoutMarks as $performance)
                                <div class="col-md-4 mb-2">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user text-muted me-2"></i>
                                        <span>{{ $performance->student->full_name }} ({{ $performance->student->reg_number ?? 'N/A' }})</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
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
    @if($gradeDistribution->isNotEmpty())
        const ctx = document.getElementById('gradeChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: @json($gradeDistribution->keys()->toArray()),
                datasets: [{
                    data: @json($gradeDistribution->values()->toArray()),
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(153, 102, 255, 0.8)',
                        'rgba(255, 159, 64, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)',
                        'rgba(255, 159, 64, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    @endif
});
</script>
@endsection
