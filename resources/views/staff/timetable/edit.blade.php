@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <h2>Edit Timetable</h2>
    <form action="{{ route('dos.timetable.update', $timetable->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title (optional)</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $timetable->title) }}">
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Timetable Image (leave blank to keep current)</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">
            @if($timetable->image)
                <img src="data:image/jpeg;base64,{{ $timetable->image }}" alt="Timetable" width="100" class="mt-2">
            @endif
        </div>
        <div class="mb-3">
            <label for="valid_from" class="form-label">Valid From</label>
            <input type="date" name="valid_from" id="valid_from" class="form-control" value="{{ old('valid_from', $timetable->valid_from) }}" required>
        </div>
        <div class="mb-3">
            <label for="valid_until" class="form-label">Valid Until</label>
            <input type="date" name="valid_until" id="valid_until" class="form-control" value="{{ old('valid_until', $timetable->valid_until) }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Timetable</button>
    </form>
</div>
@endsection