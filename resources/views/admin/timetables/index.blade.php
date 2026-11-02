@extends('admin.layout')

@section('title', 'Manage Timetables')

@section('content')
<div class="page-header">
    <h1 class="page-title">Manage Timetables</h1>
    <div class="header-actions">
        <a href="{{ route('dos.timetable.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Timetable
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if($timetables->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5>No Timetables Found</h5>
                <p class="text-muted">No timetables have been created yet.</p>
                <a href="{{ route('dos.timetable.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create Timetable
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Branch</th>
                            <th>Academic Year</th>
                            <th>Term</th>
                            <th>Status</th>
                            <th>Valid From</th>
                            <th>Valid Until</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timetables as $timetable)
                        <tr>
                            <td>{{ $timetable->title }}</td>
                            <td>{{ $timetable->branch->name ?? 'N/A' }}</td>
                            <td>{{ $timetable->academicYear->year ?? 'N/A' }}</td>
                            <td>{{ $timetable->term->name ?? 'N/A' }}</td>
                            <td>
                                @if($timetable->status === 'published')
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                            <td>{{ $timetable->valid_from ? $timetable->valid_from->format('M d, Y') : '' }}</td>
                            <td>{{ $timetable->valid_until ? $timetable->valid_until->format('M d, Y') : '' }}</td>
                            <td>
                                <a href="{{ route('admin.timetables.show', $timetable) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('dos.timetable.edit', $timetable) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
