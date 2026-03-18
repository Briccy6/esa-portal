@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-clock fa-4x text-orange mb-3"></i>
                        <h1 class="display-4 fw-bold text-warning">419</h1>
                    </div>
                    
                    <h2 class="h4 mb-3">Session Expired</h2>
                    
                    <p class="text-muted mb-4">
                        Your session has expired. Please refresh the page and try again.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ url()->current() }}" class="btn btn-primary">
                            <i class="fas fa-sync me-2"></i>Refresh Page
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-sign-in-alt me-2"></i>Log In Again
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
