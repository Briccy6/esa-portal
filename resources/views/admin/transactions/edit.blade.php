@extends('admin.layout')

@section('content')
<div class="container">
    <h4>Edit Transaction #{{ $transaction->id }}</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Amount</label>
            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $transaction->amount) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Type</label>
            <select name="type" class="form-select" required>
                <option value="registration_fee" {{ $transaction->type === 'registration_fee' ? 'selected' : '' }}>Registration Fee</option>
                <option value="school_fee" {{ $transaction->type === 'school_fee' ? 'selected' : '' }}>School Fee</option>
                <option value="practical_fee" {{ $transaction->type === 'practical_fee' ? 'selected' : '' }}>Practical Fee</option>
                <option value="trip_fee" {{ $transaction->type === 'trip_fee' ? 'selected' : '' }}>Trip Fee</option>
                <option value="student_card" {{ $transaction->type === 'student_card' ? 'selected' : '' }}>Student Card</option>
                <option value="other" {{ $transaction->type === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="successful" {{ $transaction->status === 'successful' ? 'selected' : '' }}>Successful</option>
                <option value="paid" {{ $transaction->status === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ $transaction->status === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ $transaction->status === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Paid At</label>
            <input type="datetime-local" name="paid_at" class="form-control" value="{{ old('paid_at', $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d\TH:i') : '') }}">
        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-primary" type="submit">Save changes</button>
            <a href="#" class="btn btn-secondary" onclick="window.history.back(); return false;">Cancel</a>
        </div>
    </form>
</div>
@endsection