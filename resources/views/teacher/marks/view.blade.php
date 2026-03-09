@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Recorded Marks</h4>
                    <a href="{{ route('teacher.marks.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-plus-circle"></i> Record New Marks
                    </a>
                </div>
                <div class="card-body">
                    @if(count($groupedMarks) > 0 && count($students) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th rowspan="2" class="align-middle">Student Reg No</th>
                                        <th rowspan="2" class="align-middle">Course</th>
                                        <th rowspan="2" class="align-middle">Branch</th>
                                        @foreach($groupedMarks as $lessonId => $lessonData)
                                            <th colspan="{{ count($lessonData['dates']) }}" class="text-center">
                                                {{ $lessonData['title'] }}
                                            </th>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach($groupedMarks as $lessonId => $lessonData)
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
                                                <strong>{{ $studentInfo['reg_number'] ?? $studentId }}</strong>
                                            </td>
                                            <td class="align-middle">{{ $studentInfo['course'] }}</td>
                                            <td class="align-middle">{{ $studentInfo['branch'] }}</td>
                                            
                                            @foreach($groupedMarks as $lessonId => $lessonData)
                                                @foreach($lessonData['dates'] as $date => $dateMarks)
                                                    <td class="text-center align-middle">
                                                        @if(isset($lessonData['students'][$studentId][$date]))
                                                            @php
                                                                $mark = $lessonData['students'][$studentId][$date];
                                                                $originalMark = $mark->value . '/' . $mark->marks_out_of;
                                                                $percentage = $mark->normalized_percentage;
                                                                $status = $mark->status;
                                                            @endphp
                                                            
                                                            <div class="mark-display">
                                                                <div class="fw-bold {{ $percentage >= 50 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $percentage }}%
                                                                </div>
                                                                <small class="text-muted">{{ $originalMark }}</small>
                                                                <div>
                                                                    <span class="badge bg-{{ $status === 'recorded' ? 'success' : ($status === 'updated' ? 'info' : 'warning') }} badge-sm">
                                                                {{ ucfirst($status) }}
                                                            </span>
                                                                </div>
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
                        
                        <!-- Summary Statistics -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title mb-3">Summary Statistics</h6>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 class="text-primary">{{ count($students) }}</h5>
                                                    <small class="text-muted">Total Students</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 class="text-info">{{ count($groupedMarks) }}</h5>
                                                    <small class="text-muted">Total Lessons</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 class="text-success">
                                                        @php
                                                            $totalDates = 0;
                                                            foreach($groupedMarks as $lessonData) {
                                                                $totalDates += count($lessonData['dates']);
                                                            }
                                                        @endphp
                                                        {{ $totalDates }}
                                                    </h5>
                                                    <small class="text-muted">Total Test Dates</small>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="text-center">
                                                    <h5 class="text-warning">
                                                        @php
                                                            $totalMarks = 0;
                                                            foreach($students as $studentId => $studentInfo) {
                                                                foreach($groupedMarks as $lessonId => $lessonData) {
                                                                    foreach($lessonData['dates'] as $date => $dateMarks) {
                                                                        if(isset($lessonData['students'][$studentId][$date])) {
                                                                            $totalMarks++;
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        {{ $totalMarks }}
                                                    </h5>
                                                    <small class="text-muted">Total Marks Recorded</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-journal-text display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">No marks recorded yet</h4>
                            <p class="text-muted">Start recording marks for your students to see them here.</p>
                            <a href="{{ route('teacher.marks.index') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Record Marks
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.mark-display {
    font-size: 0.85rem;
}

.badge-sm {
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
}

.table th {
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}
</style>
@endsection
