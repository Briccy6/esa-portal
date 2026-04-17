@extends('students.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Token Management</h4>
                <a href="{{ route('student.dashboard') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>

            <!-- Current Balance -->
            @if($token)
            <div class="card bg-gradient bg-primary text-white mb-4">
                <div class="card-body text-center">
                    <h2 class="display-4 fw-bold">{{ $token->formatted_balance }}</h2>
                    <p class="mb-0">Current Token Balance</p>
                </div>
            </div>
            @else
            <div class="card border-warning mb-4">
                <div class="card-body text-center">
                    <h5 class="text-warning">No Token Account Found</h5>
                    <p class="text-muted">Your token account will be created automatically when you make your first purchase or when you're granted tokens.</p>
                </div>
            </div>
            @endif

            <!-- Purchase Tokens -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Purchase Tokens</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('tokens.purchase') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Number of Tokens</label>
                            <div class="row g-2 mb-3">
                                <div class="col-6 col-md-3">
                                    <button type="button" onclick="setTokenAmount(10)" class="btn btn-outline-primary w-100 token-amount-btn">
                                        <div class="h5 mb-1">10</div>
                                        <small>RWF 5.00</small>
                                    </button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" onclick="setTokenAmount(25)" class="btn btn-outline-primary w-100 token-amount-btn">
                                        <div class="h5 mb-1">25</div>
                                        <small>RWF 10.00</small>
                                    </button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" onclick="setTokenAmount(50)" class="btn btn-outline-primary w-100 token-amount-btn">
                                        <div class="h5 mb-1">50</div>
                                        <small>RWF 18.00</small>
                                    </button>
                                </div>
                                <div class="col-6 col-md-3">
                                    <button type="button" onclick="setTokenAmount(100)" class="btn btn-outline-primary w-100 token-amount-btn">
                                        <div class="h5 mb-1">100</div>
                                        <small>RWF 30.00</small>
                                    </button>
                                </div>
                            </div>
                            <input type="number" name="amount" id="tokenAmount" min="1" max="1000" required 
                                   class="form-control" placeholder="Enter custom amount (1-1000 tokens)">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Reference (Optional)</label>
                            <input type="text" name="payment_reference" class="form-control" 
                                   placeholder="Transaction ID or payment reference">
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> Token pricing: 1 token = RWF 0.50 (bulk purchases get discounts)
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-cart-plus"></i> Purchase Tokens
                        </button>
                    </form>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Transaction History</h5>
                    <a href="{{ route('tokens.dashboard') }}" class="btn btn-sm btn-outline-primary">
                        Dashboard <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    @if($transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions as $transaction)
                                <tr>
                                    <td>
                                        @if($transaction->type === 'credit')
                                            <span class="badge bg-success">
                                                <i class="bi bi-arrow-up"></i> Credit
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-arrow-down"></i> Debit
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $transaction->description }}</div>
                                        <small class="text-muted">{{ $transaction->source }}</small>
                                    </td>
                                    <td class="{{ $transaction->type === 'credit' ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $transaction->formatted_amount }}
                                    </td>
                                    <td>{{ $transaction->balance_after }}</td>
                                    <td>{{ $transaction->created_at->format('M d, Y g:i A') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-3">
                        {{ $transactions->links() }}
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="bi bi-receipt display-4 text-muted"></i>
                        <p class="text-muted mt-2">No transactions yet</p>
                        <small class="text-muted">Purchase tokens or get a subscription to see your transaction history.</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setTokenAmount(amount) {
    document.getElementById('tokenAmount').value = amount;
    
    // Update button styles
    document.querySelectorAll('.token-amount-btn').forEach(btn => {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    event.target.classList.remove('btn-outline-primary');
    event.target.classList.add('btn-primary');
}
</script>
@endsection
