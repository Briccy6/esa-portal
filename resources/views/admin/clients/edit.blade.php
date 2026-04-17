@extends('admin.layout')

@section('title', 'Edit Client - ESA Admin')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Edit Client</h1>
            <p class="text-muted mb-0">Update client information for {{ $client->name }}</p>
        </div>
        <div>
            <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-outline-info me-2">
                <i class="fas fa-eye"></i>
                View Client
            </a>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left"></i>
                Back to Clients
            </a>
        </div>
    </div>

    <!-- Edit Client Form -->
    <div class="card">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Client Information</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.clients.update', $client) }}">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $client->name) }}" 
                                   placeholder="Enter client's full name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $client->email) }}" 
                                   placeholder="Enter client's email address"
                                   required>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   value="{{ old('phone', $client->phone) }}" 
                                   placeholder="Enter client's phone number">
                            @error('phone')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" 
                                      name="address" 
                                      class="form-control @error('address') is-invalid @enderror" 
                                      rows="1" 
                                      placeholder="Enter client's address">{{ old('address', $client->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1"
                                       {{ old('is_active', $client->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    <strong>Active</strong>
                                    <small class="text-muted d-block">Client can access the system and make purchases</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Client Status Information -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="alert alert-info">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Client Status Information</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>Current Tokens:</strong> {{ $client->getCurrentTokens() }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Registration Date:</strong> {{ $client->created_at->format('M j, Y') }}
                                </div>
                                <div class="col-md-4">
                                    <strong>Last Updated:</strong> {{ $client->updated_at->format('M j, Y g:i A') }}
                                </div>
                            </div>
                            @if($client->hasActiveSubscription())
                                <div class="mt-2">
                                    <strong>Active Subscription:</strong> 
                                    <span class="badge bg-success">{{ $client->activeSubscription->subscription->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="form-group mb-0 mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Client
                    </button>
                    <a href="{{ route('admin.clients.show', $client) }}" class="btn btn-info ms-2">
                        <i class="fas fa-eye me-2"></i>View Client
                    </a>
                    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary ms-2">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
