@if($payments->count())
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Method</th>
                <th>Reference</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d') : '-' }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $payment->type)) }}</td>
                    <td>{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</td>
                    <td>
                        <span class="badge bg-{{ $payment->status === 'successful' ? 'success' : ($payment->status === 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </td>
                    <td>
                        @php
                            $meta = is_array($payment->meta) ? $payment->meta : (json_decode($payment->meta, true) ?? []);
                        @endphp
                        {{ $meta['method'] ?? '-' }}
                    </td>
                    <td>{{ $payment->tx_ref }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info mb-0">No payments found for this student.</div>
@endif