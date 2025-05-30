@extends('admin.layout')

@section('title', 'Create Academic Year')

@section('content')
<div class="container">
    <h2>Create Academic Year</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.academic_year.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="year" class="form-label">Academic Year (e.g. 2024-2025)</label>
            <input type="text" name="year" id="year" class="form-control" value="{{ old('year') }}" required>
        </div>

        <div class="mb-3">
            <label for="starting_date" class="form-label">Starting Date</label>
            <input type="date" name="starting_date" id="starting_date" class="form-control" value="{{ old('starting_date') }}" required>
        </div>

        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" id="expiry_date" class="form-control" value="{{ old('expiry_date') }}" required>
        </div>

        <div class="mb-3">
            <label for="is_ongoing" class="form-label">Status</label>
            <select name="is_ongoing" id="is_ongoing" class="form-select" required>
                <option value="1" {{ old('is_ongoing') == '1' ? 'selected' : '' }}>Active</option>
                <option value="0" {{ old('is_ongoing') == '0' ? 'selected' : '' }}>Ended</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Create Academic Year</button>
        <a href="{{ route('admin.academic_year.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
