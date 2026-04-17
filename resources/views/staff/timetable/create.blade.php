@extends('staff.partials.sidebar_dos')

@section('content')
<div class="container mt-4">
    <h2>Upload Timetable</h2>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <form action="{{ route('dos.timetable.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="title" class="form-label">Title (optional)</label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}">
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Timetable Image</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*" required>
        </div>
        <div class="mb-3">
            <label for="valid_from" class="form-label">Valid From</label>
            <input type="date" name="valid_from" id="valid_from" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="valid_until" class="form-label">Valid Until</label>
            <input type="date" name="valid_until" id="valid_until" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Timetable</button>
    </form>
</div>
@endsection