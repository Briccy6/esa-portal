@extends('students.app')

@section('title', 'Timetable Details')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $timetable->title }}</h2>
            <p class="text-muted mb-0">Branch: {{ $timetable->branch->name ?? 'N/A' }} | Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }}</p>
            <p class="text-muted">Term: {{ $timetable->term->name ?? 'N/A' }}</p>
            <p class="text-muted">Valid from {{ optional($timetable->valid_from)->format('M d, Y') }} to {{ optional($timetable->valid_until)->format('M d, Y') }}</p>
        </div>
        <div>
            <a href="{{ route('student.timetables.index') }}" class="btn btn-secondary">Back to Timetables</a>
        </div>
    </div>

    @if($timetable->sessions->isEmpty())
        <div class="alert alert-info">No sessions found for this timetable.</div>
    @else
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">TEACHING TIMETABLE FOR {{ strtoupper($timetable->term->name ?? 'SEMESTER') }} {{ $timetable->academicYear->year ?? '' }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>Date</th>
                                <th>Day</th>
                                <th>Room(s)</th>
                                <th>Time</th>
                                <th>Module Code</th>
                                <th>Lecturer</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($timetable->sessions as $session)
                            @php
                                $dayName = \Carbon\Carbon::now()->setISOFormat(1)->startOfWeek()->addDays($session->day_of_week - 1)->format('l');
                            @endphp
                            <tr>
                                <td>{{ optional($session->classroom) }}</td>
                                <td>{{ $dayName }}</td>
                                <td>{{ optional($session->classroom) }}</td>
                                <td>{{ optional($session->start_time) }} - {{ optional($session->end_time) }}</td>
                                <td>{{ $session->lesson->title ?? 'N/A' }}</td>
                                <td>{{ $session->lesson->teacher->teacher_name ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <a href="{{ route('student.timetables.index') }}" class="btn btn-success">Download Timetable</a>
                    <a href="{{ route('student.timetables.index') }}" class="btn btn-info">Timetable Report</a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
