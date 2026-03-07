@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-2xl p-8 text-white mb-8">
            <div class="text-center">
                <h1 class="text-3xl font-bold mb-2">Token Dashboard</h1>
                <p class="text-blue-100">Manage your exam tokens and subscriptions</p>
            </div>
        </div>

        <!-- Token Balance Card -->
        <div class="bg-white rounded-2xl shadow-lg p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Current Balance</h2>
                    <p class="text-gray-600">Available tokens for exams</p>
                </div>
                <div class="text-right">
                    <div class="text-4xl font-bold text-blue-600">{{$stats['total_tokens']}}</div>
                    <div class="text-sm text-gray-500">tokens</div>
                </div>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-green-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-green-100 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{$stats['total_earned']}}</div>
                            <div class="text-sm text-gray-600">Total Earned</div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-red-100 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-red-600">{{$stats['total_spent']}}</div>
                            <div class="text-sm text-gray-600">Total Spent</div>
                        </div>
                    </div>
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 rounded-full p-3 mr-4">
                            <svg class="w-6 h-6 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h2a1 1 0 100-2 2 2 0 00-2 2v11a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v11z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{$stats['total_tokens']}}</div>
                            <div class="text-sm text-gray-600">Available</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Buy More Tokens</h3>
                <p class="text-gray-600 mb-4">Purchase additional tokens for exam attempts</p>
                <a href="{{route('tokens.index')}}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white py-2 px-6 rounded-lg font-semibold transition duration-200">
                    Purchase Tokens
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Upgrade Subscription</h3>
                <p class="text-gray-600 mb-4">Get more tokens with a subscription plan</p>
                <a href="{{route('subscriptions.index')}}" class="inline-block bg-purple-600 hover:bg-purple-700 text-white py-2 px-6 rounded-lg font-semibold transition duration-200">
                    View Plans
                </a>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-lg p-8">
            <h3 class="text-xl font-bold text-gray-900 mb-6">Recent Transactions</h3>
            
            @if($stats['recent_transactions']->count() > 0)
            <div class="space-y-4">
                @foreach($stats['recent_transactions'] as $transaction)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="rounded-full p-2 mr-4 {{$transaction->type === 'credit' ? 'bg-green-100' : 'bg-red-100'}}">
                            <svg class="w-5 h-5 {{$transaction->type === 'credit' ? 'text-green-600' : 'text-red-600'}}" fill="currentColor" viewBox="0 0 20 20">
                                @if($transaction->type === 'credit')
                                <path fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L11 5.414V17a1 1 0 11-2 0V5.414L4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                @else
                                <path fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L9 14.586V3a1 1 0 012 0v11.586l4.293-4.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                @endif
                            </svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900">{{$transaction->description}}</div>
                            <div class="text-sm text-gray-500">{{$transaction->created_at->format('M d, Y H:i')}}</div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-bold {{$transaction->type === 'credit' ? 'text-green-600' : 'text-red-600'}}">
                            {{$transaction->formatted_amount}}
                        </div>
                        <div class="text-sm text-gray-500">Balance: {{$transaction->balance_after}}</div>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100-4h2a1 1 0 100-2 2 2 0 00-2 2v11a2 2 0 002 2h6a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v11z" clip-rule="evenodd"></path>
                </svg>
                <p>No transactions yet</p>
            </div>
            @endif

            @if($stats['recent_transactions']->count() > 0)
            <div class="text-center mt-6">
                <a href="{{route('tokens.index')}}" class="text-blue-600 hover:text-blue-700 font-semibold">
                    View All Transactions →
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
