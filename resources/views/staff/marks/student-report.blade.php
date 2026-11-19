@extends('dos.layout')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <!-- School Information Header -->
            <div class="card shadow-sm mb-4 border-2 border-primary">
                <div class="card-body">
                    <div class="row align-items-center">
                        <!-- Left Column - School Info -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                    <i class="fas fa-school fs-2 text-primary"></i>
                                </div>
                                <div>
                                    <h3 class="text-primary fw-bold mb-1">Excellent Servant Academy</h3>
                                    <h5 class="text-muted mb-0">TRAINEE'S ASSESSMENT REPORT</h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <p class="mb-1"><strong>Address:</strong> Kigali City, Down Town, Ndamage House 2nd Floor</p>
                                    <p class="mb-1"><strong>Phone:</strong> 0788525247</p>
                                </div>
                                <div class="col-6">
                                    <p class="mb-1"><strong>E-mail:</strong> excellentservantacademy@gmail.com</p>
                                    <p class="mb-1"><strong>Website:</strong> https://excellentservantacademy.com</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Right Column - Student Info -->
                        <div class="col-md-6">
                            <div class="bg-light rounded p-3">
                                <div class="row align-items-center">
                                    <div class="col-7">
                                        <p class="mb-1"><strong>NAME:</strong> {{ $student->full_name }}</p>
                                        <p class="mb-1"><strong>REG NO:</strong> {{ $student->reg_number ?? 'ESAHEG022025-2026' }}</p>
                                        <p class="mb-1"><strong>CLASS:</strong> {{ $student->course->course_name ?? 'N/A' }}</p>
                                        <p class="mb-1"><strong>YEAR:</strong> {{ $student->academicYear->year ?? '2025-2026' }}</p>
                                    </div>
                                    <div class="flex-shrink-0 ms-2">
                                        <div class="bg-secondary bg-opacity-10 rounded p-2 text-center" style="width: 120px; height: 140px; display: flex; align-items: center; justify-content: center;">
                                            @if($student->passport_photo)
                                                @php
                                                    $photoPath = $student->passport_photo;
                                                    // Add extension if missing
                                                    if (!str_contains($photoPath, '.') && str_contains($photoPath, 'wzwnc4T6aiZMyuxDMySNfg17XGDd3ChdouadyuT3')) {
                                                        $photoPath .= '.png';
                                                    } elseif (!str_contains($photoPath, '.') && str_contains($photoPath, 'aKc2vKdlKFsMyFJ3m2N39HFU1uVIV6GJZMTuWsbj')) {
                                                        $photoPath .= '.jpg';
                                                    }
                                                @endphp
                                                @if(str_contains($photoPath, 'http'))
                                                    <img src="{{ $photoPath }}" alt="Student Photo" style="height: 110px; width: 100%; max-width: 110px; object-fit: cover; border-radius: 6px; border: 2px solid #dee2e6;">
                                                @else
                                                    <img src="{{ asset($photoPath) }}" alt="Student Photo" style="height: 110px; width: 100%; max-width: 110px; object-fit: cover; border-radius: 6px; border: 2px solid #dee2e6;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div style="display: none; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                                        <i class="fas fa-user-circle fs-1 text-secondary"></i>
                                                        <p class="mb-0 small text-muted mt-2" style="font-size: 0.65rem;">No Photo</p>
                                                    </div>
                                                @endif
                                            @else
                                                <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                                    <i class="fas fa-user-circle fs-1 text-secondary"></i>
                                                    <p class="mb-0 small text-muted mt-2" style="font-size: 0.65rem;">No Photo</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Header Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="text-dark fw-bold mb-1">
                        <i class="fas fa-id-card me-2 text-success"></i>
                        Student Report Card
                    </h2>
                    <p class="text-muted mb-0">
                        {{ $student->full_name }} (ID: {{ $student->id }})
                        @if($student->course)
                            - {{ $student->course->course_name }}
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('dos.marks.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Marks
                    </a>
                    <button onclick="try { window.print(); } catch(e) { console.error('Print error:', e); alert('Unable to print. Please try again.'); }" class="btn btn-success">
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
                                <h5 class="fw-bold">{{ $student->full_name }}</h5>
                                <p class="mb-0">Student ID: {{ $student->id }}</p>
                                <p class="mb-0">{{ $student->reg_number ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="py-3">
                                <h6 class="text-muted">ACADEMIC YEAR</h6>
                                <h4 class="fw-bold">{{ $student->academicYear->year ?? date('Y') . '-' . (date('Y') + 1) }}</h4>
                                <h6 class="text-muted mt-2">
                                    @if(request('term'))
                                        {{ request('term') }}
                                    @else
                                        @php
                                            // Get the term from the student's marks
                                            $studentTerm = null;
                                            if ($marks->isNotEmpty()) {
                                                $firstMark = $marks->first();
                                                if ($firstMark->exam && $firstMark->exam->term) {
                                                    $studentTerm = $firstMark->exam->term->term;
                                                }
                                            }
                                            
                                            // Fallback to most recent term if no marks found
                                            if (!$studentTerm) {
                                                $recentTerm = \App\Models\Term::orderBy('id', 'desc')->first();
                                                $studentTerm = $recentTerm ? $recentTerm->term : 'Term 1';
                                            }
                                            
                                            echo $studentTerm;
                                        @endphp
                                    @endif
                                </h6>
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
                                <h5 class="fw-bold">{{ $stats['total_assessments'] }}</h5>
                                <small>Total Assessments</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-primary bg-opacity-10 rounded p-3">
                                <i class="fas fa-percentage fs-2 text-primary mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($stats['average_score'], 1) }}%</h5>
                                <small>Average Score</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-info bg-opacity-10 rounded p-3">
                                <i class="fas fa-file-alt fs-2 text-info mb-2"></i>
                                <h5 class="fw-bold">{{ $stats['exam_count'] }}</h5>
                                <small>Exams Taken</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-warning bg-opacity-10 rounded p-3">
                                <i class="fas fa-pencil-alt fs-2 text-warning mb-2"></i>
                                <h5 class="fw-bold">{{ $stats['test_count'] }}</h5>
                                <small>Tests Taken</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-success bg-opacity-10 rounded p-3">
                                <i class="fas fa-trophy fs-2 text-success mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($stats['highest_score'], 1) }}</h5>
                                <small>Best Score</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="bg-danger bg-opacity-10 rounded p-3">
                                <i class="fas fa-exclamation-triangle fs-2 text-danger mb-2"></i>
                                <h5 class="fw-bold">{{ number_format($stats['lowest_score'], 1) }}</h5>
                                <small>Lowest Score</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Annual Summary with Term Averages -->
            @if(isset($stats['term_summaries']) && count($stats['term_summaries']) > 0)
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-gradient-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2"></i>Annual Summary - Term Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Annual Average Highlight -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="bg-warning bg-opacity-10 rounded p-4 text-center" style="border-left: 5px solid #ffc107;">
                                <h6 class="text-muted mb-2">ANNUAL AVERAGE (All Terms)</h6>
                                <h2 class="fw-bold text-dark" style="font-size: 2.5rem;">{{ number_format($stats['annual_average'], 1) }}%</h2>
                                <p class="mb-0 text-muted">
                                    Based on {{ count($stats['term_summaries']) }} term(s)
                                    @php
                                        $annualGrade = 'F';
                                        if ($stats['annual_average'] >= 80) $annualGrade = 'A';
                                        elseif ($stats['annual_average'] >= 70) $annualGrade = 'B';
                                        elseif ($stats['annual_average'] >= 60) $annualGrade = 'C';
                                        elseif ($stats['annual_average'] >= 50) $annualGrade = 'D';
                                    @endphp
                                    <span class="badge bg-{{ $annualGrade == 'A' ? 'success' : ($annualGrade == 'B' ? 'info' : ($annualGrade == 'C' ? 'warning' : ($annualGrade == 'D' ? 'secondary' : 'danger'))) }} ms-2" style="font-size: 0.85rem;">Grade {{ $annualGrade }}</span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Term-wise Breakdown -->
                    <div class="row">
                        @foreach($stats['term_summaries'] as $termName => $termData)
                            <div class="col-md-6 mb-3">
                                <div class="card border-1 h-100">
                                    <div class="card-header bg-light border-bottom">
                                        <h6 class="mb-0 fw-semibold">{{ $termName }}</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row text-center">
                                            <div class="col-6 border-end">
                                                <h6 class="text-muted small">Term Average</h6>
                                                <h4 class="fw-bold text-primary">{{ number_format($termData['average'], 1) }}%</h4>
                                            </div>
                                            <div class="col-6">
                                                <h6 class="text-muted small">Assessments</h6>
                                                <h4 class="fw-bold text-info">{{ $termData['count'] }}</h4>
                                            </div>
                                        </div>
                                        <div class="row text-center mt-3 pt-3 border-top">
                                            <div class="col-4">
                                                <small class="text-muted d-block">Exams</small>
                                                <span class="fw-semibold">{{ $termData['exam_count'] }}</span>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Tests</small>
                                                <span class="fw-semibold">{{ $termData['test_count'] }}</span>
                                            </div>
                                            <div class="col-4">
                                                <small class="text-muted d-block">Range</small>
                                                <span class="fw-semibold">{{ number_format($termData['lowest'], 1) }}-{{ number_format($termData['highest'], 1) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Subject-wise Performance -->
            @foreach($marksByCourse as $courseId => $courseMarks)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-gradient-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-book me-2"></i>
                            {{ $courseMarks->first()->course->course_name ?? 'Course ' . $courseId }} - Subject Performance
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Assessment Name</th>
                                        <th>Date</th>
                                        <th class="text-center">Type</th>
                                        <th class="text-center">Score</th>
                                        <th class="text-center">Out Of</th>
                                        <th class="text-center">Percentage</th>
                                        <th class="text-center">Grade</th>
                                        <th class="text-center">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($courseMarks as $mark)
                                        <tr>
                                            <td class="fw-semibold">{{ $mark->exam->exam_name ?? 'Test' }}</td>
                                            <td>{{ $mark->created_at ? $mark->created_at->format('M d, Y') : '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $mark->assessment_type == 'exam' ? 'primary' : 'secondary' }} px-2 py-1">
                                                    {{ ucfirst($mark->assessment_type) }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="fw-bold {{ $mark->value >= ($mark->marks_out_of * 0.5) ? 'text-success' : 'text-danger' }}">
                                                    {{ $mark->value ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $mark->marks_out_of ?? '-' }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-{{ $mark->value >= ($mark->marks_out_of * 0.8) ? 'success' : ($mark->value >= ($mark->marks_out_of * 0.5) ? 'warning' : 'danger') }}">
                                                    {{ $mark->marks_out_of ? number_format(($mark->value / $mark->marks_out_of) * 100, 1) : '0.0' }}%
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($mark->grade)
                                                    <span class="badge bg-light text-dark">{{ $mark->grade->name }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($mark->value >= ($mark->marks_out_of * 0.8))
                                                    <span class="badge bg-success">Excellent</span>
                                                @elseif($mark->value >= ($mark->marks_out_of * 0.6))
                                                    <span class="badge bg-info">Good</span>
                                                @elseif($mark->value >= ($mark->marks_out_of * 0.5))
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
                        
                        <!-- Subject Summary -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="bg-light rounded p-3">
                                    <div class="row text-center">
                                        <div class="col-md-3">
                                            <h6 class="text-muted">Subject Average</h6>
                                            <h4 class="fw-bold text-primary">
                                                {{ number_format($courseMarks->whereNotNull('value')->avg('value'), 1) }}
                                            </h4>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="text-muted">Total Assessments</h6>
                                            <h4 class="fw-bold text-info">{{ $courseMarks->count() }}</h4>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="text-muted">Pass Rate</h6>
                                            <h4 class="fw-bold text-success">
                                                {{ $courseMarks->count() > 0 ? number_format(($courseMarks->filter(function($mark) { return $mark->value >= ($mark->marks_out_of * 0.5); })->count() / $courseMarks->count()) * 100, 1) : '0.0' }}%
                                            </h4>
                                        </div>
                                        <div class="col-md-3">
                                            <h6 class="text-muted">Subject Grade</h6>
                                            @php
                                                $subjectAvg = $courseMarks->whereNotNull('value')->avg('value');
                                                $subjectGrade = 'F';
                                                if ($subjectAvg >= 80) $subjectGrade = 'A';
                                                elseif ($subjectAvg >= 70) $subjectGrade = 'B';
                                                elseif ($subjectAvg >= 60) $subjectGrade = 'C';
                                                elseif ($subjectAvg >= 50) $subjectGrade = 'D';
                                            @endphp
                                            <h4 class="fw-bold">
                                                <span class="badge bg-{{ $subjectGrade == 'A' ? 'success' : ($subjectGrade == 'B' ? 'info' : ($subjectGrade == 'C' ? 'warning' : ($subjectGrade == 'D' ? 'secondary' : 'danger'))) }}">
                                                    {{ $subjectGrade }}
                                                </span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

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
                                    @if($stats['average_score'] >= 80)
                                        Excellent academic performance
                                    @elseif($stats['average_score'] >= 60)
                                        Good academic performance
                                    @else
                                        Consistent effort in studies
                                    @endif
                                </li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>
                                    @if($stats['total_assessments'] >= 10)
                                        Regular participation in assessments
                                    @else
                                        Active participation in class activities
                                    @endif
                                </li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>
                                    @if($stats['highest_score'] >= 70)
                                        Strong performance in best subjects
                                    @else
                                        Shows improvement potential
                                    @endif
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-semibold text-warning mb-3">Areas for Improvement</h6>
                            <ul class="list-unstyled">
                                <li><i class="fas fa-arrow-right text-warning me-2"></i>
                                    @if($stats['average_score'] < 60)
                                        Focus on improving overall academic performance
                                    @else
                                        Continue maintaining good performance
                                    @endif
                                </li>
                                <li><i class="fas fa-arrow-right text-warning me-2"></i>
                                    @if($stats['lowest_score'] < 50)
                                        Additional support needed in weaker areas
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

            <!-- Grade Scale Reference -->
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Grade Scale Reference
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
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #17a2b8, #117a8b);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
    }
    
    .bg-gradient-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
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
