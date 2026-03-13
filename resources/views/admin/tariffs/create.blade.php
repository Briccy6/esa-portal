@extends('admin.layout')

@section('title', 'Create Tariff')

@section('content')
<h1>Create Tariff</h1>

@if ($errors->any())
<div class="alert alert-danger">
    <ul>
    @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif

<form action="{{ route('admin.tariffs.store') }}" method="POST">
    @csrf

    <div class="mb-3">
        <label>Activity/Event</label>
        <input type="text" name="activity" class="form-control" value="{{ old('activity') }}" required>
    </div>

    <div class="mb-3">
        <label>Amount</label>
        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}" required>
    </div>

    <div class="mb-3 form-check">
        <input type="checkbox" name="has_discount" id="has_discount" class="form-check-input" {{ old('has_discount') ? 'checked' : '' }}>
        <label for="has_discount" class="form-check-label">Has Discount?</label>
    </div>

    <div id="discount_section" style="display:none;">
        <div class="mb-3">
            <label>Discount Amount</label>
            <input type="number" step="0.01" name="discount_amount" class="form-control" value="{{ old('discount_amount') }}">
        </div>

        <div class="mb-3">
            <label>Discount Type</label>
            <select name="discount_type" class="form-control">
                <option value="">Select discount type</option>
                <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Fixed</option>
                <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Percentage</option>
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label>Deadline Type</label>
        <select name="deadline_type" id="deadline_type" class="form-control" required>
            <option value="date" {{ old('deadline_type') == 'date' ? 'selected' : '' }}>Specific Date</option>
            <option value="hours" {{ old('deadline_type') == 'hours' ? 'selected' : '' }}>Hours From Now</option>
        </select>
    </div>

    <div class="mb-3" id="deadline_date_field" style="display:none;">
        <label>Deadline Date</label>
        <input type="datetime-local" name="deadline_date" class="form-control" value="{{ old('deadline_date') }}">
    </div>

    <div class="mb-3" id="deadline_hours_field" style="display:none;">
        <label>Deadline Hours</label>
        <input type="number" name="deadline_hours" class="form-control" value="{{ old('deadline_hours') }}">
    </div>

    <button type="submit" class="btn btn-success">Save Tariff</button>
</form>

<script>
    function toggleDiscount() {
        const checkbox = document.getElementById('has_discount');
        document.getElementById('discount_section').style.display = checkbox.checked ? 'block' : 'none';
    }

    function toggleDeadline() {
        const deadlineType = document.getElementById('deadline_type').value;
        document.getElementById('deadline_date_field').style.display = deadlineType === 'date' ? 'block' : 'none';
        document.getElementById('deadline_hours_field').style.display = deadlineType === 'hours' ? 'block' : 'none';
    }

    document.getElementById('has_discount').addEventListener('change', toggleDiscount);
    document.getElementById('deadline_type').addEventListener('change', toggleDeadline);

    // Initial toggle on page load
    toggleDiscount();
    toggleDeadline();
</script>
@endsection
