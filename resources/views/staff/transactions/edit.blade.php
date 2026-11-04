@if(request()->ajax())
    {{-- For AJAX requests, return just the form content --}}
    <form method="POST" action="{{ route('transactions.update', $transaction->id) }}" class="edit-transaction-form">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Amount <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="amount" class="form-control form-control-lg" value="{{ old('amount', $transaction->amount) }}" required>
                @error('amount')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Fee Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select form-select-lg" required>
                    <option value="">-- Select Fee Type --</option>
                    <option value="registration_fee" {{ $transaction->type === 'registration_fee' || $transaction->type === 'registration' ? 'selected' : '' }}>Registration Fee</option>
                    <option value="school_fee" {{ $transaction->type === 'school_fee' || $transaction->type === 'tuition_fee' || $transaction->type === 'tuition' ? 'selected' : '' }}>School Fee</option>
                    <option value="practical_fee" {{ $transaction->type === 'practical_fee' || $transaction->type === 'practical' ? 'selected' : '' }}>Practical Fee</option>
                    <option value="trip_fee" {{ $transaction->type === 'trip_fee' || $transaction->type === 'trip' ? 'selected' : '' }}>Trip Fee</option>
                    <option value="student_card" {{ $transaction->type === 'student_card' || $transaction->type === 'studentcard' ? 'selected' : '' }}>Student Card</option>
                    <option value="other" {{ $transaction->type === 'other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('type')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Term (Optional)</label>
            <select name="term_id" class="form-select">
                <option value="">-- No Term --</option>
                @php
                    $terms = \App\Models\Term::orderBy('start_date', 'desc')->get();
                @endphp
                @foreach($terms as $term)
                    <option value="{{ $term->id }}" {{ $transaction->term_id == $term->id ? 'selected' : '' }}>
                        {{ $term->name }} ({{ $term->academicYear->year ?? '' }})
                    </option>
                @endforeach
            </select>
            @error('term_id')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Notes (Optional)</label>
            <textarea name="notes" class="form-control" rows="3" placeholder="Add any notes about this transaction">{{ (is_array($transaction->meta) ? $transaction->meta : json_decode($transaction->meta, true) ?? [])['notes'] ?? '' }}</textarea>
            @error('notes')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-grow-1">
                <i class="fas fa-save me-2"></i>Save Changes
            </button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="fas fa-times me-2"></i>Cancel
            </button>
        </div>
    </form>
@else
    {{-- For regular page requests, extend the staff layout --}}
    @extends('staff.partials.sidebar_sec')

    @section('content')
    <div class="container py-4">
        <div class="card">
            <div class="card-header">
                <strong>Edit Transaction #{{ $transaction->id }}</strong>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('transactions.update', $transaction->id) }}">
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
@endif