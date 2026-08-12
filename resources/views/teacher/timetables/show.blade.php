@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">{{ $timetable->title }}</h2>
            <p class="text-muted mb-0">Branch: {{ $timetable->branch->name ?? 'N/A' }} | Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }}</p>
            <p class="text-muted">Valid from {{ optional($timetable->valid_from)->format('M d, Y') }} to {{ optional($timetable->valid_until)->format('M d, Y') }}</p>
        </div>
        <a href="{{ route('teacher.timetables.index') }}" class="btn btn-secondary">Back to Timetables</a>
    </div>

    @if($teacherSessions->isEmpty())
        <div class="alert alert-info">No sessions found for this timetable.</div>
    @else
        @include('partials.timetable-weekly-grid', ['sessions' => $teacherSessions, 'timetable' => $timetable])
    @endif
</div>
@endsection