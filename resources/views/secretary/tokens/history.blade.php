@extends('secretary.layout')

@section('page_title', 'Token history')
@section('title', $title ?? 'Token Transaction History')

@section('content')
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>{{ $title }}</h1>
            <p>Review token credits and deductions.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('secretary.tokens.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to tokens
            </a>
        </div>
    </div>

    <section class="sec-card">
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        @if(!$student)
                            <th>Student</th>
                        @endif
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Balance after</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at?->format('M j, Y H:i') }}</td>
                            @if(!$student)
                                <td>{{ $transaction->token->student->full_name ?? 'N/A' }}</td>
                            @endif
                            <td>
                                @if(in_array($transaction->type, ['credit', 'add']))
                                    <span class="badge text-bg-success">{{ ucfirst($transaction->type) }}</span>
                                @else
                                    <span class="badge text-bg-danger">{{ ucfirst($transaction->type) }}</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($transaction->type, ['credit', 'add']))
                                    +{{ $transaction->amount }}
                                @else
                                    -{{ $transaction->amount }}
                                @endif
                            </td>
                            <td>{{ $transaction->balance_after ?? 'N/A' }}</td>
                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $student ? 5 : 6 }}" class="sec-empty">No token transactions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($transactions, 'hasPages') && $transactions->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $transactions->links() }}</div>
        @endif
    </section>
</div>
@endsection
