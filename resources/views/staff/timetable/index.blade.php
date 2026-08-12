@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Timetable Library</h2>
        <a href="{{ route('dos.timetable.create') }}" class="btn btn-primary">Create New Timetable</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($timetables->count() > 0)
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Branch</th>
                    <th>Academic Year</th>
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
                        <td>{{ $timetable->branch->name ?? 'N/A' }}</td>
                        <td>{{ $timetable->academicYear->year ?? 'N/A' }}</td>
                        <td>{{ $timetable->valid_from ? \Carbon\Carbon::parse($timetable->valid_from)->format('M d, Y') : 'N/A' }}</td>
                        <td>{{ $timetable->valid_until ? \Carbon\Carbon::parse($timetable->valid_until)->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            @if($timetable->status === 'published')
                                <span class="badge bg-success">Published</span>
                            @elseif($timetable->status === 'draft')
                                <span class="badge bg-secondary">Draft</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($timetable->status) }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('dos.timetable.edit', $timetable) }}" class="btn btn-warning btn-sm">Edit</a>
                            @if($timetable->status === 'draft')
                                <a href="{{ route('dos.timetable.publish', $timetable) }}" class="btn btn-success btn-sm ms-1">Publish</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="alert alert-info text-center">
            <h4><i class="bi bi-calendar-x"></i> No Timetables Found</h4>
            <p class="mb-3">There are currently no timetables created in the system.</p>
            <p class="text-muted mb-4">Use the weekly editor to build a branch timetable that students can view by course.</p>
            <a href="{{ route('dos.timetable.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create Your First Timetable
            </a>
        </div>
    @endif
</div>
@endsection