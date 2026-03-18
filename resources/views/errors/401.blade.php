@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-user-shield fa-4x text-info mb-3"></i>
                        <h1 class="display-4 fw-bold text-info">401</h1>
                    </div>
                    
                    <h2 class="h4 mb-3">Authentication Required</h2>
                    
                    <p class="text-muted mb-4">
                        Please log in to access this page.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">
                            <i class="fas fa-sign-in-alt me-2"></i>Log In
                        </a>
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
