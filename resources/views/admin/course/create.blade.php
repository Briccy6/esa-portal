@extends('admin.layout') {{-- Assuming you use layouts.admin for sidebar and layout --}}

@section('title', 'Create Course')

@section('content')
<div class="container">
    <h2>Create Course</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Back to Courses Button --}}
    <a href="{{ route('admin.course.index') }}" class="btn btn-secondary mb-3">
        <i class="fas fa-arrow-left"></i> Back to Courses
    </a>

    <form action="{{ route('admin.course.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name</label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="{{ old('course_name') }}" required>
            @error('course_name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="abbreviation" class="form-label">Abbreviation</label>
            <input type="text" name="abbreviation" id="abbreviation" class="form-control" value="{{ old('abbreviation') }}" required>
            @error('abbreviation') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="course_type" class="form-label">Course Type</label>
            <select name="course_type" id="course_type" class="form-select" required>
                <option value="" disabled {{ old('course_type') ? '' : 'selected' }}>-- Select Course Type --</option>
                <option value="rtb" {{ old('course_type') == 'rtb trade' ? 'selected' : '' }}>RTB Trade</option>
                <option value="reb combination" {{ old('course_type') == 'reb combination' ? 'selected' : '' }}>REB Combination</option>
            </select>
            @error('course_type') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Create Course</button>
    </form>
</div>
@endsection
