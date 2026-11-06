@extends('teacher.layout')

@section('styles')
<style>
.marks-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.marks-header h4 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.marks-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.marks-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
}

.marks-card .card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.marks-table {
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.marks-table .table {
    margin-bottom: 0;
}

.marks-table .table-dark {
    background: linear-gradient(135deg, #4c51bf 0%, #667eea 100%);
}

.marks-table .table-dark th {
    border: none;
    padding: 1rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.marks-table .table td {
    padding: 0.875rem;
    vertical-align: middle;
    border-bottom: 1px solid #e5e7eb;
}

.marks-table .table tbody tr:hover {
    background-color: #f8fafc;
}

.mark-display {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    display: inline-block;
    min-width: 60px;
    text-align: center;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stats-number.primary { color: #667eea; }
.stats-number.info { color: #0ea5e9; }
.stats-number.success { color: #10b981; }
.stats-number.warning { color: #f59e0b; }

.stats-label {
    color: #6b7280;
    font-size: 0.875rem;
    font-weight: 500;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    border: 2px dashed #e5e7eb;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.btn-primary-custom {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.2s ease;
}

.btn-primary-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(102, 126, 234, 0.3);
    color: white;
}

.table-responsive {
    border-radius: 10px;
    overflow: hidden;
}

@media (max-width: 768px) {
    .marks-table .table th {
        font-size: 0.75rem;
        padding: 0.5rem;
    }
    
    .marks-table .table td {
        font-size: 0.8rem;
        padding: 0.5rem;
    }
    
    .stats-number {
        font-size: 1.5rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="marks-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4><i class="bi bi-journal-text"></i> Recorded Marks</h4>
                    <p class="mb-0">View and manage student marks across different lessons and assessments</p>
                </div>
                <a href="{{ route('teacher.marks.index') }}" class="btn btn-light">
                    <i class="bi bi-plus-circle"></i> Record New Marks
                </a>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="marks-card">
            <div class="card-body p-0">
                @if(count($groupedMarks) > 0 && count($students) > 0)
                    @foreach($groupedMarks as $termId => $termData)
                        <!-- Term Section -->
                        <div class="mb-4">
                            <h4 class="text-primary mb-3">
                                <i class="bi bi-calendar3 me-2"></i>{{ $termData['name'] }}
                            </h4>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover marks-table">
                                    <thead class="table-dark">
                                        <tr>
                                            <th rowspan="2" class="align-middle">Student Name</th>
                                            <th rowspan="2" class="align-middle">Course</th>
                                            <th rowspan="2" class="align-middle">Branch</th>
                                            @foreach($termData['lessons'] as $lessonId => $lessonData)
                                                <th colspan="{{ count($lessonData['dates']) }}" class="text-center">
                                                    {{ $lessonData['title'] }}
                                                </th>
                                            @endforeach
                                        </tr>
                                        <tr>
                                            @foreach($termData['lessons'] as $lessonId => $lessonData)
                                                @foreach($lessonData['dates'] as $date => $dateMarks)
                                                    <th class="text-center" style="min-width: 120px;">
                                                        @if($date === 'no-date')
                                                            <small>No Date</small>
                                                        @else
                                                            <small>{{ \Carbon\Carbon::parse($date)->format('M d, Y') }}</small>
                                                        @endif
                                                    </th>
                                                @endforeach
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($students as $studentId => $studentInfo)
                                            <tr>
                                                <td class="align-middle">
                                                    <strong>{{ $studentInfo['name'] ?? $studentInfo['reg_number'] ?? $studentId }}</strong>
                                                </td>
                                                <td class="align-middle">{{ $studentInfo['course'] }}</td>
                                                <td class="align-middle">{{ $studentInfo['branch'] }}</td>
                                                
                                                @foreach($termData['lessons'] as $lessonId => $lessonData)
                                                    @foreach($lessonData['dates'] as $date => $dateMarks)
                                                        <td class="text-center align-middle">
                                                            @if(isset($lessonData['students'][$studentId][$date]))
                                                                @php
                                                                    $mark = $lessonData['students'][$studentId][$date];
                                                                    $originalMark = $mark->value . '/' . ($mark->assessment_type === 'test' ? ($mark->marks_out_of ?? 100) : 100);
                                                                    $percentage = $mark->normalized_percentage;
                                                                    $status = $mark->status;
                                                                @endphp
                                                            
                                                                <div class="mark-display">
                                                                    {{ $originalMark }}
                                                                </div>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    @endforeach
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        @if(!$loop->last)<hr class="my-4">@endif
                    @endforeach
                    
                    <!-- Summary Statistics -->
                    <div class="p-4">
                        <h5 class="mb-4"><i class="bi bi-graph-up"></i> Summary Statistics</h5>
                        <div class="row">
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="text-center">
                                        <div class="stats-number primary">{{ count($students) }}</div>
                                        <div class="stats-label">Total Students</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="text-center">
                                        <div class="stats-number info">
                                            @php
                                                $totalLessons = 0;
                                                foreach($groupedMarks as $termData) {
                                                    $totalLessons += count($termData['lessons']);
                                                }
                                            @endphp
                                            {{ $totalLessons }}
                                        </div>
                                        <div class="stats-label">Total Lessons</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="text-center">
                                        <div class="stats-number success">
                                            @php
                                                $totalDates = 0;
                                                foreach($groupedMarks as $termData) {
                                                    foreach($termData['lessons'] as $lessonData) {
                                                        $totalDates += count($lessonData['dates']);
                                                    }
                                                }
                                            @endphp
                                            {{ $totalDates }}
                                        </div>
                                        <div class="stats-label">Total Test Dates</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="stats-card">
                                    <div class="text-center">
                                        <div class="stats-number warning">
                                            @php
                                                $totalMarks = 0;
                                                foreach($students as $studentId => $studentInfo) {
                                                    foreach($groupedMarks as $termData) {
                                                        foreach($termData['lessons'] as $lessonData) {
                                                            foreach($lessonData['dates'] as $date => $dateMarks) {
                                                                if(isset($lessonData['students'][$studentId][$date])) {
                                                                    $totalMarks++;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            {{ $totalMarks }}
                                        </div>
                                        <div class="stats-label">Total Marks Recorded</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="empty-state m-4">
                        <i class="bi bi-journal-text display-1"></i>
                        <h4 class="mt-4">No marks recorded yet</h4>
                        <p class="text-muted mb-4">Start recording marks for your students to see them here.</p>
                        <a href="{{ route('teacher.marks.index') }}" class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle"></i> Record Marks
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
