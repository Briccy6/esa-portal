@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-tools fa-4x text-info mb-3"></i>
                        <h1 class="display-4 fw-bold text-info">503</h1>
                    </div>
                    
                    <h2 class="h4 mb-3">Maintenance Mode</h2>
                    
                    <p class="text-muted mb-4">
                        We're currently performing maintenance. Please check back soon.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <button onclick="location.reload()" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Check Again
                        </button>
                        <button onclick="history.back()" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
