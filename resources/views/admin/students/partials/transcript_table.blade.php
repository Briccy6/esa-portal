@forelse($payments as $payment)
<tr>
    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : ($payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A') }}</td>
    <td>
        @php
            $termName = $payment->term ? $payment->term->name : 'No Term';
        @endphp
        <span class="badge bg-info">{{ $termName }}</span>
    </td>
    <td>
        @php
            $feeTypeMap = [
                'registration' => 'Registration Fee',
                'registration_fee' => 'Registration Fee',
                'school_fee' => 'School Fee',
                'tuition' => 'Tuition Fee',
                'practical' => 'Practical Fee',
                'trip' => 'Trip Fee',
                'student_card' => 'Student Card'
            ];
            $displayType = $feeTypeMap[$payment->type] ?? ucfirst(str_replace('_', ' ', $payment->type));
        @endphp
        <span class="badge bg-primary">{{ $displayType }}</span>
    </td>
    <td>
        <strong>{{ number_format($payment->amount, 2) }} RWF</strong>
    </td>
    <td>
        @php
            $meta = is_string($payment->meta) ? json_decode($payment->meta, true) : $payment->meta;
            $method = $meta['method'] ?? 'Online';
        @endphp
        <span class="badge {{ $method === 'cash' ? 'bg-success' : 'bg-info' }}">
            {{ ucfirst($method) }}
        </span>
        @if($payment->status)
            <span class="badge bg-{{ $payment->status === 'successful' || $payment->status === 'paid' ? 'success' : 'warning' }}">
                {{ ucfirst($payment->status) }}
            </span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="text-center text-muted py-4">
        <i class="bi bi-receipt display-4 d-block mb-2"></i>
        No payment records found for this student.
    </td>
</tr>
@endforelse

@if($payments->count() > 0)
<tr class="table-active">
    <td colspan="3" class="text-end"><strong>Total Paid:</strong></td>
    <td colspan="2"><strong class="text-success">{{ number_format($payments->sum('amount'), 2) }} RWF</strong></td>
</tr>
@endif