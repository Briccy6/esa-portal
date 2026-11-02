@extends('students.app')

@section('title', 'My Timetables')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">My Timetables</h2>
    </div>
    @if($timetables->isEmpty())
        <div class="alert alert-info">No active timetables were found for your branch.</div>
    @else
        @foreach($timetables as $timetable)
            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="card-title mb-1">{{ $timetable->title }}</h4>
                            <p class="text-muted mb-0">Branch: {{ $timetable->branch->name ?? 'N/A' }} | Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }} | Term: {{ $timetable->term->name ?? 'N/A' }}</p>
                            <p class="text-muted">Valid from {{ optional($timetable->valid_from)->format('M d, Y') }} to {{ optional($timetable->valid_until)->format('M d, Y') }}</p>
                        </div>
                        <a href="{{ route('student.timetables.show', $timetable) }}" class="btn btn-primary">View Details</a>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            @include('partials.timetable-weekly-grid', ['sessions' => $timetable->sessions, 'timetable' => $timetable])
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
