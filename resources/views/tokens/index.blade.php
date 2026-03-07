@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Token Management</h1>
            <p class="text-gray-600">Manage your exam tokens and view transaction history</p>
        </div>

        <!-- Current Balance -->
        @if($token)
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white mb-8">
            <div class="text-center">
                <div class="text-5xl font-bold mb-2">{{$token->formatted_balance}}</div>
                <div class="text-blue-100">Current Token Balance</div>
            </div>
        </div>
        @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8 text-center">
            <h3 class="text-lg font-semibold text-yellow-800 mb-2">No Token Account Found</h3>
            <p class="text-yellow-600 mb-4">Your token account will be created automatically when you make your first purchase or when you're granted tokens.</p>
        </div>
        @endif

        <!-- Purchase Tokens -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">Purchase Tokens</h2>
            
            <form action="{{route('tokens.purchase')}}" method="POST" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Number of Tokens</label>
                    <div class="grid grid-cols-4 gap-4 mb-4">
                        <button type="button" onclick="setTokenAmount(10)" class="token-amount-btn bg-gray-100 hover:bg-blue-100 border-2 border-transparent hover:border-blue-500 rounded-lg p-4 text-center transition">
                            <div class="text-2xl font-bold">10</div>
                            <div class="text-sm text-gray-600">$5.00</div>
                        </button>
                        <button type="button" onclick="setTokenAmount(25)" class="token-amount-btn bg-gray-100 hover:bg-blue-100 border-2 border-transparent hover:border-blue-500 rounded-lg p-4 text-center transition">
                            <div class="text-2xl font-bold">25</div>
                            <div class="text-sm text-gray-600">$10.00</div>
                        </button>
                        <button type="button" onclick="setTokenAmount(50)" class="token-amount-btn bg-gray-100 hover:bg-blue-100 border-2 border-transparent hover:border-blue-500 rounded-lg p-4 text-center transition">
                            <div class="text-2xl font-bold">50</div>
                            <div class="text-sm text-gray-600">$18.00</div>
                        </button>
                        <button type="button" onclick="setTokenAmount(100)" class="token-amount-btn bg-gray-100 hover:bg-blue-100 border-2 border-transparent hover:border-blue-500 rounded-lg p-4 text-center transition">
                            <div class="text-2xl font-bold">100</div>
                            <div class="text-sm text-gray-600">$30.00</div>
                        </button>
                    </div>
                    <input type="number" name="amount" id="tokenAmount" min="1" max="1000" required 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Enter custom amount (1-1000 tokens)">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Reference (Optional)</label>
                    <input type="text" name="payment_reference" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Transaction ID or payment reference">
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-blue-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="text-sm text-blue-800">Token pricing: 1 token = $0.50 (bulk purchases get discounts)</span>
                    </div>
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-200">
                    Purchase Tokens
                </button>
            </form>
        </div>

        <!-- Transaction History -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Transaction History</h2>
                <a href="{{route('tokens.dashboard')}}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    Dashboard →
                </a>
            </div>

            @if($transactions->count() > 0)
            <div class="space-y-4">
                @foreach($transactions as $transaction)
                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <div class="rounded-full p-3 mr-4 {{$transaction->type === 'credit' ? 'bg-green-100' : 'bg-red-100'}}">
                            @if($transaction->type === 'credit')
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            @else
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                            @endif
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{$transaction->description}}</div>
                            <div class="text-sm text-gray-500">{{$transaction->created_at->format('M d, Y g:i A')}}</div>
                            <div class="text-xs text-gray-400">Source: {{$transaction->source}}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-lg font-bold {{$transaction->type === 'credit' ? 'text-green-600' : 'text-red-600'}}">
                            {{$transaction->formatted_amount}}
                        </div>
                        <div class="text-sm text-gray-500">Balance: {{$transaction->balance_after}}</div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{$transactions->links()}}
            </div>
            @else
            <div class="text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h2a1 1 0 100-2 2 2 0 00-2 2v11a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v11z" clip-rule="evenodd"></path>
                </svg>
                <p class="text-lg font-medium mb-2">No transactions yet</p>
                <p class="text-sm">Purchase tokens or get a subscription to see your transaction history.</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function setTokenAmount(amount) {
    document.getElementById('tokenAmount').value = amount;
    
    // Update button styles
    document.querySelectorAll('.token-amount-btn').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'border-blue-500');
        btn.classList.add('bg-gray-100', 'border-transparent');
    });
    
    event.target.classList.remove('bg-gray-100', 'border-transparent');
    event.target.classList.add('bg-blue-100', 'border-blue-500');
}
</script>
@endsection
