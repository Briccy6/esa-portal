@extends('students.app')

@section('title', 'My Subscriptions')

@section('content')
<div class="container-fluid">

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h4 mb-1">My Subscriptions</h2>
                    <p class="text-muted mb-0">Manage your subscription plans and tokens</p>
                </div>
                <div>
                    <a href="{{ route('student.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Subscription Status -->
    @if($activeSubscription)
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-success d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-check-circle-fill fs-3"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="alert-heading mb-1">Active Subscription</h6>
                    <p class="mb-1">
                        <strong>{{ $activeSubscription->subscription->name }}</strong> — 
                        {{ $activeSubscription->days_remaining }} days remaining
                    </p>
                    <small class="text-muted">Valid until: {{ $activeSubscription->ends_at->format('d M Y') }}</small>
                    <div class="mt-2">
                        <span class="badge bg-info">{{ $activeSubscription->subscription->tokens_included }} tokens included</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Subscription Plans -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-wrapper">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="h5 mb-0">
                        <i class="bi bi-star me-2"></i>Available Subscription Plans
                    </h4>
                    @if($activeSubscription)
                        <span class="badge bg-success">You have an active subscription</span>
                    @endif
                </div>

                <div class="row">
                    @foreach($subscriptions as $subscription)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card h-100 {{ $subscription->is_recommended ? 'border-primary' : '' }}">
                            @if($subscription->is_recommended)
                                <div class="card-header bg-primary text-white text-center py-2">
                                    <i class="bi bi-star-fill me-1"></i> Recommended
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="text-center mb-3">
                                    <h5 class="card-title">{{ $subscription->name }}</h5>
                                    <div class="h3 text-primary mb-2">
                                        ${{ number_format($subscription->price, 0) }}
                                    </div>
                                    <div class="text-muted">
                                        {{ $subscription->duration_days }} days
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <div class="text-center mb-3">
                                        <span class="badge bg-primary text-white">
                                            <i class="bi bi-coin me-1"></i>{{ $subscription->tokens_included }} Tokens Included
                                        </span>
                                    </div>
                                    
                                    @if($subscription->features)
                                    <ul class="list-unstyled">
                                        @foreach(array_slice($subscription->features, 0, 3) as $feature)
                                            <li class="d-flex align-items-center mb-2">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>{{ Str::limit($feature, 30) }}</span>
                                            </li>
                                        @endforeach
                                        @if(count($subscription->features) > 3)
                                            <li class="text-muted small">
                                                <i class="bi bi-three-dots me-1"></i>+{{ count($subscription->features) - 3 }} more features
                                            </li>
                                        @endif
                                    </ul>
                                    @endif
                                </div>

                                <div class="mt-auto">
                                    @if($activeSubscription)
                                        <button class="btn btn-secondary w-100" disabled>
                                            <i class="bi bi-check-circle me-1"></i>Already Active
                                        </button>
                                    @else
                                        <form action="{{ route('subscriptions.purchase') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="subscription_id" value="{{ $subscription->id }}">
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="bi bi-cart-plus me-1"></i>Choose {{ $subscription->name }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @if(!$activeSubscription)
                <div class="text-center mt-4">
                    <p class="text-muted mb-3">Need just tokens?</p>
                    <a href="{{ route('tokens.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-coin me-1"></i>Buy Individual Tokens
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Token Balance Overview -->
    <div class="row">
        <div class="col-12">
            <div class="content-wrapper">
                <h5 class="h5 mb-3">
                    <i class="bi bi-wallet2 me-2"></i>Your Token Balance
                </h5>
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="token-balance-display me-4">
                                <div class="h2 mb-0 text-primary">
                                    <i class="bi bi-coin me-2"></i>{{ $student->token_balance ?? 0 }}
                                </div>
                                <div class="text-muted">tokens available</div>
                            </div>
                            <div class="token-details">
                                <div class="small text-muted">
                                    <div class="mb-1">
                                        <i class="bi bi-arrow-down-circle text-success"></i>
                                        Total Earned: {{ $student->token ? $student->token->total_earned : 0 }}
                                    </div>
                                    <div>
                                        <i class="bi bi-arrow-up-circle text-danger"></i>
                                        Total Spent: {{ $student->token ? $student->token->total_spent : 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('tokens.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-gear me-1"></i>Manage Tokens
                        </a>
                    </div>
                </div>

                @if(($student->token_balance ?? 0) < 10 && !$activeSubscription)
                    <div class="alert alert-warning mt-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Low Token Balance!</strong>
                        You have less than 10 tokens. Consider purchasing a subscription for better value.
                    </div>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
