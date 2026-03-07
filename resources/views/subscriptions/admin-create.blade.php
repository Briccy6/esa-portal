@extends('admin.layout')

@section('title', 'Create Subscription')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-gray-800">Create New Subscription</h1>
        <a href="{{ route('subscriptions.admin-index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to Subscriptions
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Subscription Details</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('subscriptions.admin-store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Subscription Name *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tier" class="form-label">Tier *</label>
                            <select class="form-control @error('tier') is-invalid @enderror" 
                                    id="tier" name="tier" required>
                                <option value="">Select Tier</option>
                                <option value="basic" {{ old('tier') === 'basic' ? 'selected' : '' }}>Basic</option>
                                <option value="standard" {{ old('tier') === 'standard' ? 'selected' : '' }}>Standard</option>
                                <option value="premium" {{ old('tier') === 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                            @error('tier')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="tokens_included" class="form-label">Tokens Included *</label>
                            <input type="number" class="form-control @error('tokens_included') is-invalid @enderror" 
                                   id="tokens_included" name="tokens_included" value="{{ old('tokens_included') }}" 
                                   min="1" required>
                            @error('tokens_included')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="price" class="form-label">Price ($) *</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price') }}" 
                                   min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="duration_days" class="form-label">Duration (Days) *</label>
                            <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                                   id="duration_days" name="duration_days" value="{{ old('duration_days') }}" 
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
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label">Features</label>
                            <small class="text-muted d-block mb-2">Add one feature per line</small>
                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                      id="features" name="features" rows="6" placeholder="e.g.&#10;Access to all exams&#10;Priority support&#10;Study materials">{{ old('features') }}</textarea>
                            @error('features')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input @error('is_active') is-invalid @enderror" 
                                       id="is_active" name="is_active" value="1" checked>
                                <label class="custom-control-label" for="is_active">Active</label>
                                @error('is_active')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input @error('is_recommended') is-invalid @enderror" 
                                       id="is_recommended" name="is_recommended" value="1">
                                <label class="custom-control-label" for="is_recommended">Recommended</label>
                                @error('is_recommended')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Preview Card -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="mb-3">Preview</h5>
                        <div class="card border-primary">
                            <div class="card-body">
                                <div id="subscriptionPreview" class="row">
                                    <div class="col-md-8">
                                        <h6 id="previewName" class="card-title">Subscription Name</h6>
                                        <p id="previewDescription" class="text-muted">Description will appear here...</p>
                                        <div id="previewFeatures" class="mt-3">
                                            <!-- Features will be added here -->
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <h4 id="previewPrice" class="text-primary">$0.00</h4>
                                        <p class="text-muted"><span id="previewTokens">0</span> tokens</p>
                                        <p class="text-muted"><span id="previewDuration">0</span> days</p>
                                        <span id="previewTier" class="badge badge-secondary">Tier</span>
                                        <span id="previewRecommended" class="badge badge-warning d-none">Recommended</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Subscription
                        </button>
                        <a href="{{ route('subscriptions.admin-index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    updatePreview();
    
    // Add event listeners for real-time preview
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('description').addEventListener('input', updatePreview);
    document.getElementById('tokens_included').addEventListener('input', updatePreview);
    document.getElementById('price').addEventListener('input', updatePreview);
    document.getElementById('duration_days').addEventListener('input', updatePreview);
    document.getElementById('tier').addEventListener('change', updatePreview);
    document.getElementById('features').addEventListener('input', updatePreview);
    document.getElementById('is_recommended').addEventListener('change', updatePreview);
});

function updatePreview() {
    const name = document.getElementById('name').value || 'Subscription Name';
    const description = document.getElementById('description').value || 'Description will appear here...';
    const tokens = document.getElementById('tokens_included').value || '0';
    const price = document.getElementById('price').value || '0.00';
    const duration = document.getElementById('duration_days').value || '0';
    const tier = document.getElementById('tier').value || 'basic';
    const features = document.getElementById('features').value;
    const recommended = document.getElementById('is_recommended').checked;
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewDescription').textContent = description;
    document.getElementById('previewTokens').textContent = tokens;
    document.getElementById('previewPrice').textContent = '$' + parseFloat(price).toFixed(2);
    document.getElementById('previewDuration').textContent = duration;
    
    // Update tier badge
    const tierBadge = document.getElementById('previewTier');
    tierBadge.textContent = tier.charAt(0).toUpperCase() + tier.slice(1);
    tierBadge.className = 'badge badge-' + (tier === 'premium' ? 'warning' : (tier === 'standard' ? 'info' : 'secondary'));
    
    // Update recommended badge
    const recommendedBadge = document.getElementById('previewRecommended');
    if (recommended) {
        recommendedBadge.classList.remove('d-none');
    } else {
        recommendedBadge.classList.add('d-none');
    }
    
    // Update features
    const featuresContainer = document.getElementById('previewFeatures');
    if (features.trim()) {
        const featuresList = features.split('\n').filter(f => f.trim());
        featuresContainer.innerHTML = featuresList.map(f => 
            `<div class="d-flex align-items-center mb-1">
                <i class="fas fa-check text-success mr-2"></i>
                <small>${f.trim()}</small>
            </div>`
        ).join('');
    } else {
        featuresContainer.innerHTML = '';
    }
}
</script>
@endsection
