@extends('admin.layout')

@section('title', $title ?? 'Token Transaction History')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ $title }}</h3>
                    @if($student)
                        <div class="card-tools">
                            <a href="{{ route('admin.tokens.index') }}" class="btn btn-sm btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Tokens
                            </a>
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        @if(!$student)
                                            <th>Student</th>
                                        @endif
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Balance</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->id }}</td>
                                            <td>{{ $transaction->created_at->format('M j, Y H:i') }}</td>
                                            @if(!$student)
                                                <td>
                                                    @if($transaction->token && $transaction->token->student)
                                                        {{ $transaction->token->student->full_name }}
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td>
                                                <span class="badge badge-{{ $transaction->type == 'credit' ? 'success' : 'danger' }}">
                                                    {{ ucfirst($transaction->type) }}
                                                </span>
                                            </td>
                                            <td class="{{ $transaction->type == 'credit' ? 'text-success' : 'text-danger' }}">
                                                {{ $transaction->type == 'credit' ? '+' : '-' }}{{ $transaction->amount }}
                                            </td>
                                            <td>{{ $transaction->balance_after ?? 'N/A' }}</td>
                                            <td>{{ $transaction->description ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No token transactions found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
