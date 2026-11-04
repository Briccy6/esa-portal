@php
    $student = $student ?? null;
    $payments = $payments ?? collect();
@endphp

<div class="transcript-container">
    <style>
        .transcript-container {
            font-size: 14px;
        }
        .transcript-container table {
            margin-bottom: 0;
        }
        .transcript-container .badge {
            font-size: 11px;
            padding: 4px 8px;
        }
    </style>

    @if($student)
        <div class="mb-3">
            <strong>Student:</strong> {{ $student->full_name ?? 'N/A' }}<br>
            <strong>Registration Number:</strong> {{ $student->reg_number ?? 'N/A' }}<br>
            <strong>Course:</strong> {{ $student->course->course_name ?? 'N/A' }}<br>
            <strong>Branch:</strong> {{ $student->branch->name ?? 'N/A' }}
        </div>
    @endif

    @if($payments->count() > 0)
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Term</th>
                        <th>Fee Type</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $index => $payment)
                        @php
                            $meta = is_array($payment->meta) ? $payment->meta : (json_decode($payment->meta, true) ?: []);
                            $method = $meta['method'] ?? 'online';

                            $feeTypeMap = [
                                'registration_fee' => 'Registration',
                                'registration' => 'Registration',
                                'school_fee' => 'School Fee',
                                'tuition_fee' => 'School Fee',
                                'tuition' => 'School Fee',
                                'practical_fee' => 'Practical',
                                'practical' => 'Practical',
                                'trip_fee' => 'Trip',
                                'trip' => 'Trip',
                                'student_card' => 'Student Card',
                                'studentcard' => 'Student Card'
                            ];
                            $displayType = $feeTypeMap[$payment->type] ?? ucfirst(str_replace('_', ' ', $payment->type));
                            $displayDate = $payment->paid_at ? \Carbon\Carbon::parse($payment->paid_at)->format('Y-m-d H:i') : ($payment->created_at ? $payment->created_at->format('Y-m-d H:i') : 'N/A');
                            $termName = $payment->term ? $payment->term->name : 'No Term';
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $displayDate }}</td>
                            <td><span class="badge bg-info">{{ $termName }}</span></td>
                            <td><span class="badge bg-primary">{{ $displayType }}</span></td>
                            <td><strong>{{ number_format($payment->amount, 2) }} RWF</strong></td>
                            <td>
                                @if(strtolower($method) === 'cash')
                                    <span class="badge bg-success"><i class="fas fa-money-bill-wave"></i> Cash</span>
                                @else
                                    <span class="badge bg-primary"><i class="fas fa-credit-card"></i> Online</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($payment->status, ['successful', 'paid']))
                                    <span class="badge bg-success">Paid</span>
                                @elseif($payment->status === 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">{{ ucfirst($payment->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-active">
                        <td colspan="4" class="text-end"><strong>Total Paid:</strong></td>
                        <td colspan="3"><strong class="text-success">{{ number_format($payments->sum('amount'), 2) }} RWF</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    @else
        <div class="text-center py-4 text-muted">
            <i class="fas fa-receipt fa-2x mb-2"></i>
            <div>No payment records found for this student.</div>
        </div>
    @endif
</div>
