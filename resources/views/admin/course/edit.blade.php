@extends('admin.layout')
@section('content')
<h1>Edit Course</h1>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('admin.course.update', $course->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" value="{{ old('course_name', $course->course_name) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="abbreviation">Abbreviation</label>
        <input type="text" name="abbreviation" value="{{ old('abbreviation', $course->abbreviation) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="course_type">Course Type</label>
        <input type="text" name="course_type" value="{{ old('course_type', $course->course_type) }}" class="form-control" required>
    </div>

    <div class="form-group">
        <label for="academic_year_id">Academic Year</label>
        <select name="academic_year_id" class="form-control" required>
            @foreach($academicYears as $year)
                <option value="{{ $year->id }}" {{ (old('academic_year_id', $course->academic_year_id) == $year->id) ? 'selected' : '' }}>
                    {{ $year->year }}
                </option>
            @endforeach
        </select>
    </div>
<div class="form-group">
    <label for="registration_fee">Registration Fee</label>
    <input type="number" step="0.01" min="0" name="registration_fee" class="form-control"
        value="{{ old('registration_fee', $course->registration_fee) }}">
</div>

<div class="form-group">
    <label for="tuition_fee">Tuition Fee</label>
    <input type="number" step="0.01" min="0" name="tuition_fee" class="form-control"
        value="{{ old('tuition_fee', $course->tuition_fee) }}">
</div>

<div class="form-group">
    <label for="practical_fee">Practical Fee</label>
    <input type="number" step="0.01" min="0" name="practical_fee" class="form-control"
        value="{{ old('practical_fee', $course->practical_fee) }}">
</div>

<div class="form-group">
    <label for="trip_fee">Trip Fee</label>
    <input type="number" step="0.01" min="0" name="trip_fee" class="form-control"
        value="{{ old('trip_fee', $course->trip_fee) }}">
</div>

<div class="form-group">
    <label for="discount_fee">Discount</label>
    <input type="number" step="0.01" min="0" name="discount_fee" class="form-control"
        value="{{ old('discount_fee', $course->discount_fee) }}">
</div>
    <div class="form-group">
        <label>Branches</label>
        <div>
            @foreach($branches as $branch)
                <label>
                    <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                        {{ (in_array($branch->id, old('branches', $course->branches->pluck('id')->toArray() ?? []))) ? 'checked' : '' }}>
                    {{ $branch->name }}
                </label><br>
            @endforeach
        </div>
    </div>

    <div class="form-group">
        <label for="total_cost">Total Cost (Excluding Registration Fee & After Discount)</label>
        <input type="text" name="total_cost" id="total_cost" class="form-control" 
            value="{{ old('total_cost', $course->total_cost) }}" readonly>
        <small class="form-text text-muted">
            This is the sum of Tuition, Practical, and Trip fees minus discount (%), registration fee is not included.
        </small>
    </div>

    <button type="submit" class="btn btn-primary">Update Course</button>
</form>

<script>
function calculateTotalCost() {
    const tuition = parseFloat(document.querySelector('input[name="tuition_fee"]')?.value) || 0;
    const practical = parseFloat(document.querySelector('input[name="practical_fee"]')?.value) || 0;
    const trip = parseFloat(document.querySelector('input[name="trip_fee"]')?.value) || 0;
    const discount = parseFloat(document.querySelector('input[name="discount_fee"]')?.value) || 0;

    let subtotal = tuition + practical + trip;
    let total = subtotal;

    if (discount > 0 && discount <= 100) {
        total = subtotal - (subtotal * (discount / 100));
    }

    document.getElementById('total_cost').value = total.toFixed(2);
}

['tuition_fee', 'practical_fee', 'trip_fee', 'discount_fee'].forEach(function(name) {
    document.querySelector('input[name="' + name + '"]').addEventListener('input', calculateTotalCost);
});

document.addEventListener('DOMContentLoaded', calculateTotalCost);
</script>
@endsection
