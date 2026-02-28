@extends('staff.partials.sidebar_sec')

@section('content')
    <h2>Add New Student</h2>
    <form action="{{ route('students.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Course</label>
            <select name="course_id" class="form-control" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                @endforeach
            </select>
        </div>
        <!-- Optionally add other student fields here -->
        <div class="mb-3">
            <label>Registration Fee Paid</label>
            <input type="number" name="registration_fee_paid" class="form-control" min="0" step="0.01" required>
        </div>
        <div class="mb-3">
            <label>Tuition Fee Paid</label>
            <input type="number" name="tuition_fee_paid" class="form-control" min="0" step="0.01" required>
        </div>
        <div class="mb-3">
            <label>Trip Fee Paid</label>
            <input type="number" name="trip_fee_paid" class="form-control" min="0" step="0.01" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Student</button>
    </form>
@endsection