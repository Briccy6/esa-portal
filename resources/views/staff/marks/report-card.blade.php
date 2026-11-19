@extends('dos.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-id-card me-2 text-success"></i>
                        Student Report Card
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
                    <button onclick="window.print()" class="btn btn-success">
                        <i class="fas fa-print me-1"></i> Print Report Card
                    </button>
                </div>
            </div>

            <!-- Report Card Header -->
            <div class="card shadow-sm mb-4 border-2 border-success">
                <div class="card-body text-center">
                    <h3 class="text-success fw-bold mb-3">ACADEMIC REPORT CARD</h3>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-user-circle fs-1 text-success mb-2"></i>
                                <h5 class="fw-bold">{{ $student->name }}</h5>
                                <p class="mb-0">Student ID: {{ $student->id }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="py-3">
                                <h6 class="text-muted">ACADEMIC YEAR</h6>
                                <h4 class="fw-bold">{{ request('academic_year') ?? date('Y') . '-' . (date('Y') + 1) }}</h4>
                                <h6 class="text-muted mt-2">{{ request('term') ?? 'Current Term' }}</h6>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <h6 class="text-muted">CLASS INFORMATION</h6>
                                <p class="mb-1"><strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                                <p class="mb-1"><strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}</p>
                                <p class="mb-0"><strong>Academic Year:</strong> {{ $student->academicYear->year ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Overall Performance Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Overall Performance Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-clipboard-check fs-2 text-success mb-2"></i>
                                <h5 class="fw-bold">{{ $overallStats['total_assessments'] }}</h5>
                                <small>Total Assessments</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i class="fas fa-percentage fs-2 text-primary mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($overallStats['average_score'], 1) }}%</h5>
                                <small>Average Score</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <i class="fas fa-file-alt fs-2 text-info mb-2"></i>
                                <h5 class="fw-bold">{{ $overallStats['total_exams'] }}</h5>
                                <small>Exams Taken</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i class="fas fa-pencil-alt fs-2 text-warning mb-2"></i>
                                <h5 class="fw-bold">{{ $overallStats['total_tests'] }}</h5>
                                <small>Tests Taken</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-trophy fs-2 text-success mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($overallStats['best_subject'], 1) }}%</h5>
                                <small>Best Subject</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-danger bg-opacity-10 rounded p-3">
                                <i class="fas fa-exclamation-triangle fs-2 text-danger mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($overallStats['needs_improvement'], 1) }}%</h5>
                                <small>Needs Improvement</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Examination Results -->
            @if($examMarks->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Examination Results
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($examMarks as $courseId => $exams)
                            <div class="mb-4">
                                <h6 class="text-primary fw-semibold mb-3">
                                    {{ $exams->first()->course->course_name ?? 'Course ' . $courseId }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Exam Name</th>
                                                <th>Date</th>
                                                <th class="text-center">Score</th>
                                                <th class="text-center">Out Of</th>
                                                <th class="text-center">Percentage</th>
                                                <th class="text-center">Grade</th>
                                                <th class="text-center">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($exams as $exam)
                                                <tr>
                                                    <td class="fw-semibold">{{ $exam->exam->exam_name ?? 'Exam' }}</td>
                                                    <td>{{ $exam->created_at ? $exam->created_at->format('M d, Y') : '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="fw-bold {{ $exam->value >= ($exam->marks_out_of * 0.5) ? 'text-success' : 'text-danger' }}">
                                                            {{ $exam->value ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $exam->marks_out_of ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $exam->value >= ($exam->marks_out_of * 0.8) ? 'success' : ($exam->value >= ($exam->marks_out_of * 0.5) ? 'warning' : 'danger') }}">
                                                            {{ $exam->marks_out_of ? number_format(($exam->value / $exam->marks_out_of) * 100, 1) : '0.0' }}%
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($exam->grade)
                                                            <span class="badge bg-light text-dark">{{ $exam->grade->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($exam->value >= ($exam->marks_out_of * 0.8))
                                                            <span class="badge bg-success">Excellent</span>
                                                        @elseif($exam->value >= ($exam->marks_out_of * 0.6))
                                                            <span class="badge bg-info">Good</span>
                                                        @elseif($exam->value >= ($exam->marks_out_of * 0.5))
                                                            <span class="badge bg-warning">Average</span>
                                                        @else
                                                            <span class="badge bg-danger">Needs Improvement</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Test Results -->
            @if($testMarks->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-warning text-dark">
                        <h5 class="mb-0">
                            <i class="fas fa-pencil-alt me-2"></i>Test Results
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($testMarks as $courseId => $tests)
                            <div class="mb-4">
                                <h6 class="text-warning fw-semibold mb-3">
                                    {{ $tests->first()->course->course_name ?? 'Course ' . $courseId }}
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Test/Lesson</th>
                                                <th>Date</th>
                                                <th class="text-center">Score</th>
                                                <th class="text-center">Out Of</th>
                                                <th class="text-center">Percentage</th>
                                                <th class="text-center">Grade</th>
                                                <th class="text-center">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($tests as $test)
                                                <tr>
                                                    <td class="fw-semibold">{{ $test->lesson->title ?? 'Test' }}</td>
                                                    <td>{{ $test->created_at ? $test->created_at->format('M d, Y') : '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="fw-bold {{ $test->value >= ($test->marks_out_of * 0.5) ? 'text-success' : 'text-danger' }}">
                                                            {{ $test->value ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">{{ $test->marks_out_of ?? '-' }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-{{ $test->value >= ($test->marks_out_of * 0.8) ? 'success' : ($test->value >= ($test->marks_out_of * 0.5) ? 'warning' : 'danger') }}">
                                                            {{ $test->marks_out_of ? number_format(($test->value / $test->marks_out_of) * 100, 1) : '0.0' }}%
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($test->grade)
                                                            <span class="badge bg-light text-dark">{{ $test->grade->name }}</span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        @if($test->value >= ($test->marks_out_of * 0.8))
                                                            <span class="badge bg-success">Excellent</span>
                                                        @elseif($test->value >= ($test->marks_out_of * 0.6))
                                                            <span class="badge bg-info">Good</span>
                                                        @elseif($test->value >= ($test->marks_out_of * 0.5))
                                                            <span class="badge bg-warning">Average</span>
                                                        @else
                                                            <span class="badge bg-danger">Needs Improvement</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Teacher's Comments -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-comments me-2"></i>Academic Performance Comments
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-info mb-3">Strengths</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check-circle text-success me-2"></i>
                                    @if($overallStats['average_score'] >= 80)
                                        Excellent academic performance
                                    @elseif($overallStats['average_score'] >= 60)
                                        Good academic performance
                                    @else
                                        Consistent effort in studies
                                    @endif
                                </li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>
                                    @if($overallStats['total_assessments'] >= 10)
                                        Regular participation in assessments
                                    @else
                                        Active participation in class activities
                                    @endif
                                </li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>
                                    @if($overallStats['best_subject'] >= 70)
                                        Strong performance in best subject
                                    @else
                                    </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-warning mb-3">Areas for Improvement</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right text-warning me-2"></i>
                                    @if($overallStats['average_score'] < 60)
                                        Focus on improving overall academic performance
                                    @else
                                        Continue maintaining good performance
                                    @endif
                                </li>
                                <li><i class="fas fa-arrow-right text-warning me-2"></i>
                                    @if($overallStats['needs_improvement'] < 50)
                                        Additional support needed in weaker subjects
                                    @else
                                        Practice regularly to strengthen understanding
                                    @endif
                                </li>
                                <li><i class="fas fa-arrow-right text-warning me-2"></i>
                                    Develop better study habits and time management
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grade Scale -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Grade Scale
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">Percentage Range</th>
                                            <th class="text-center">Grade</th>
                                            <th>Performance Level</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">80% - 100%</td>
                                            <td class="text-center"><span class="badge bg-success">A</span></td>
                                            <td>Excellent - Outstanding performance</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">70% - 79%</td>
                                            <td class="text-center"><span class="badge bg-info">B</span></td>
                                            <td>Good - Above average performance</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">60% - 69%</td>
                                            <td class="text-center"><span class="badge bg-warning">C</span></td>
                                            <td>Average - Satisfactory performance</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">50% - 59%</td>
                                            <td class="text-center"><span class="badge bg-secondary">D</span></td>
                                            <td>Below Average - Needs improvement</td>
                                        </tr>
                                        <tr>
                                            <td class="text-center">Below 50%</td>
                                            <td class="text-center"><span class="badge bg-danger">F</span></td>
                                            <td>Fail - Significant improvement needed</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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
    
    .border-2 {
        border-width: 2px !important;
    }
    
    @media print {
        .btn, .d-flex.gap-2 {
            display: none !important;
        }
        
        .card {
            break-inside: avoid;
            page-break-inside: avoid;
        }
        
        body {
            font-size: 12px;
        }
        
        .badge {
            border: 1px solid #dee2e6;
        }
    }
</style>
@endsection
