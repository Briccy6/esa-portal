@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Subscription Plan</h1>
            <p class="text-xl text-gray-600">Select the perfect plan for your learning journey</p>
        </div>

        <!-- Current Subscription Status -->
        @if($activeSubscription)
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-green-800">Active Subscription</h3>
                    <p class="text-green-600">{{$activeSubscription->subscription->name}} - {{$activeSubscription->days_remaining}} days remaining</p>
                </div>
                <div class="text-green-600">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
        @endif

        <!-- Subscription Plans Grid -->
        <div class="grid md:grid-cols-3 gap-8 mb-12">
            @foreach($subscriptions as $subscription)
            <div class="relative bg-white rounded-2xl shadow-lg overflow-hidden {{ $subscription->is_recommended ? 'ring-2 ring-blue-500 transform scale-105' : '' }}">
                @if($subscription->is_recommended)
                <div class="absolute top-0 right-0 bg-blue-500 text-white px-3 py-1 rounded-bl-lg text-sm font-semibold">
                    Recommended
                </div>
                @endif
                
                <div class="p-8">
                    <div class="text-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{$subscription->name}}</h3>
                        <div class="text-4xl font-bold text-blue-600 mb-2">
                            ${{number_format($subscription->price, 2)}}
                        </div>
                        <div class="text-gray-500">
                            {{$subscription->duration_days}} days
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="text-center mb-4">
                            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm font-semibold">
                                {{$subscription->tokens_included}} Tokens Included
                            </span>
                        </div>
                        
                        @if($subscription->features)
                        <ul class="space-y-3">
                            @foreach($subscription->features as $feature)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                {{$feature}}
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <div class="text-center">
                        @if($activeSubscription)
                        <button class="w-full bg-gray-300 text-gray-500 py-3 px-6 rounded-lg font-semibold cursor-not-allowed" disabled>
                            Already Subscribed
                        </button>
                        @else
                        <form action="{{route('subscriptions.purchase')}}" method="POST">
                            @csrf
                            <input type="hidden" name="subscription_id" value="{{$subscription->id}}">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-semibold transition duration-200">
                                Choose {{$subscription->name}}
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Token Purchase Option -->
        <div class="bg-gray-50 rounded-2xl p-8 text-center">
            <h3 class="text-2xl font-bold text-gray-900 mb-4">Need Just Tokens?</h3>
            <p class="text-gray-600 mb-6">Purchase tokens individually without a subscription</p>
            <a href="{{route('tokens.index')}}" class="inline-block bg-gray-800 hover:bg-gray-900 text-white py-3 px-8 rounded-lg font-semibold transition duration-200">
                Buy Tokens
            </a>
        </div>
    </div>
</div>
@endsection
