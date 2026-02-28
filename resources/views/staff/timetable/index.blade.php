@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <h2>All Uploaded Timetables</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
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
                <td>{{ $timetable->valid_from }}</td>
                <td>{{ $timetable->valid_until }}</td>
                <td>
                    @if(\Carbon\Carbon::now()->between($timetable->valid_from, $timetable->valid_until))
                        <span class="badge bg-success">Valid</span>
                    @else
                        <span class="badge bg-danger">Expired</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('dos.timetable.edit', $timetable->id) }}" class="btn btn-warning btn-sm">Edit</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection