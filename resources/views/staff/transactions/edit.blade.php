@extends('staff.partials.sidebar_sec') {{-- or your staff layout --}}

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header">
            <strong>Edit Transaction #{{ $transaction->id }}</strong>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('staff.transactions.update', $transaction->id) }}">
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
                        <option value="practical_fee" {{ $transaction->type === 'practical_fee' ? 'selected' : '' }}>Practical</option>
                        <option value="trip_fee" {{ $transaction->type === 'trip_fee' ? 'selected' : '' }}>Trip</option>
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
                    <input type="datetime-local" name="paid_at" class="form-control"
                           value="{{ optional($transaction->paid_at)->format('Y-m-d\TH:i') ?? '' }}">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Save changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection