@extends('students.app')

@section('content')
<div class="container">
    <h3 class="mb-4">Your Course Fees</h3>

    @if(session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>Fee Type</th>
                <th>Total Amount</th>
                <th>Paid</th>
                <th>Remaining</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $fees = [
                    ['type' => 'registration', 'label' => 'Registration Fee', 'amount' => $registrationFee],
                    ['type' => 'tuition', 'label' => 'Tuition Fee', 'amount' => $tuitionFee],
                    ['type' => 'trip', 'label' => 'Trip Fee', 'amount' => $tripFee],
                   ['type' => 'practical','label'=>'Pratical Fee', 'amount' => $practicalFee],

                ];
            @endphp
            @foreach($fees as $fee)
                @php
                    $paid = \App\Models\StudentTransaction::where('student_id', $student->id)
                        ->where('type', $fee['type'])
                        ->where('status', 'successful')
                        ->sum('amount');
                    $remaining = max(0, $fee['amount'] - $paid);
                @endphp
                <tr>
                    <td>{{ $fee['label'] }}</td>
                    <td>{{ number_format($fee['amount']) }} RWF</td>
                    <td>{{ number_format($paid) }} RWF</td>
                    <td>{{ number_format($remaining) }} RWF</td>
                    <td>
                        @if($remaining == 0)
                            <span class="text-primary fw-bold"><i class="fas fa-check-circle"></i> Paid</span>
                        @else
                            <span class="text-danger fw-bold"><i class="fas fa-times-circle"></i> Not Paid</span>
                        @endif
                    </td>
                    <td>
                        @if($remaining > 0)
                            <form action="{{ route('student.pay.fee') }}" method="POST" class="d-inline-flex gap-2 flex-column">
                                @csrf
                                <input type="hidden" name="fee_type" value="{{ $fee['type'] }}">
                                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                                    <input type="number" name="pay_amount" class="form-control form-control-sm" placeholder="Amount" value="{{ $remaining }}" min="1" max="{{ $remaining }}" required style="width: 100px;">
                                    <input type="number" name="pay_number" class="form-control form-control-sm" placeholder="Phone" required style="width: 120px;">
                                    <button type="submit" class="btn btn-success btn-sm">Pay</button>
                                </div>
                            </form>
                        @else
                            <button class="btn btn-outline-primary btn-sm" disabled>
                                <i class="fas fa-check"></i> Paid
                            </button>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

<script>
document.querySelectorAll('form[action="{{ route('student.pay.fee') }}"]').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const fd = new FormData(form);
        fetch(form.action, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: fd
        })
        .then(res => res.json())
        .then(data => {
            if (data.payment_link) {
                window.location.href = data.payment_link;
            } else {
                alert(data.message || 'Payment failed.');
            }
        })
        .catch(() => alert('An error occurred.'));
    });
});
</script>
