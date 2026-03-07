@extends('admin.layout')

@section('title', 'Edit Subscription')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Edit Subscription: {{ $subscription->name }}</h1>
        <a href="{{ route('subscriptions.admin-index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Subscriptions
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('subscriptions.admin-update', $subscription->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Subscription Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $subscription->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tier" class="form-label">Tier *</label>
                            <select class="form-control @error('tier') is-invalid @enderror" 
                                    id="tier" name="tier" required>
                                <option value="">Select Tier</option>
                                <option value="basic" {{ old('tier', $subscription->tier) === 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="standard" {{ old('tier', $subscription->tier) === 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="premium" {{ old('tier', $subscription->tier) === 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('tier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tokens_included" class="form-label">Tokens Included *</label>
                            <input type="number" class="form-control @error('tokens_included') is-invalid @enderror" 
                                   id="tokens_included" name="tokens_included" value="{{ old('tokens_included', $subscription->tokens_included) }}" 
                                   min="1" required>
                            @error('tokens_included')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="price" class="form-label">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $subscription->price) }}" 
                                   min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="duration_days" class="form-label">Duration (Days) *</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                                   id="duration_days" name="duration_days" value="{{ old('duration_days', $subscription->duration_days) }}" 
                                   min="1" required>
                            @error('duration_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description', $subscription->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Features</label>
                            <small class="text-muted d-block mb-2">Add one feature per line</small>
                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                      id="features" name="features" rows="6" placeholder="e.g.&#10;Access to all exams&#10;Priority support&#10;Study materials">{{ old('features', is_array($subscription->features) ? implode("\n", $subscription->features) : $subscription->features) }}</textarea>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input @error('is_active') is-invalid @enderror" 
                                       id="is_active" name="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Active</label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input @error('is_recommended') is-invalid @enderror" 
                                       id="is_recommended" name="is_recommended" value="1" {{ old('is_recommended', $subscription->is_recommended) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_recommended">Recommended</label>
                                @error('is_recommended')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Subscription Statistics</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6>Total Purchases</h6>
                                        <h3>{{ $subscription->studentSubscriptions()->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Active Purchases</h6>
                                        <h3>{{ $subscription->studentSubscriptions()->active()->count() }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Total Revenue</h6>
                                        <h3>${{ number_format($subscription->studentSubscriptions()->sum('paid_amount'), 2) }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6>Tokens Distributed</h6>
                                        <h3>{{ $subscription->studentSubscriptions()->sum(function($sub) { return $sub->subscription->tokens_included; }) }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Subscription
                        </button>
                        <a href="{{ route('subscriptions.admin-index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        @if($subscription->studentSubscriptions()->count() === 0)
                            <form action="{{ route('subscriptions.admin-destroy', $subscription->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this subscription?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
