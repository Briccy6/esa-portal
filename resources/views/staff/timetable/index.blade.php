@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <h2>All Uploaded Timetables</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($timetables->count() > 0)
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Image</th>
                    <th>Valid From</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($timetables as $timetable)
                <tr>
                    <td>{{ $timetable->title }}</td>
                    <td>
                        @if($timetable->image)
                            <img src="data:image/jpeg;base64,{{ $timetable->image }}" alt="Timetable" width="100">
                        @endif
                    </td>
                    <td>{{ $timetable->valid_from ? \Carbon\Carbon::parse($timetable->valid_from)->format('M d, Y') : 'N/A' }}</td>
                    <td>{{ $timetable->valid_until ? \Carbon\Carbon::parse($timetable->valid_until)->format('M d, Y') : 'N/A' }}</td>
                    <td>
                        @if($timetable->valid_from && $timetable->valid_until)
                            @if(\Carbon\Carbon::now()->between($timetable->valid_from, $timetable->valid_until))
                                <span class="badge bg-success">Valid</span>
                                <small class="text-muted d-block">Valid until {{ \Carbon\Carbon::parse($timetable->valid_until)->diffForHumans() }}</small>
                            @else
                                <span class="badge bg-danger">Expired</span>
                                <small class="text-muted d-block">Expired {{ \Carbon\Carbon::parse($timetable->valid_until)->diffForHumans() }}</small>
                            @endif
                        @else
                            <span class="badge bg-secondary">No Dates Set</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('dos.timetable.edit', $timetable->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info text-center">
            <h4><i class="bi bi-calendar-x"></i> No Timetables Found</h4>
            <p class="mb-3">There are currently no timetables uploaded in the system.</p>
            <p class="text-muted mb-4">Timetables help students and staff understand class schedules and timing.</p>
            <a href="{{ route('dos.timetable.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Upload Your First Timetable
            </a>
        </div>
    @endif
</div>
@endsection