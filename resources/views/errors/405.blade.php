@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-ban fa-4x text-warning mb-3"></i>
                        <h1 class="display-4 fw-bold text-warning">405</h1>
                    </div>
                    
                    <h2 class="h4 mb-3">Method Not Allowed</h2>
                    
                    <p class="text-muted mb-4">
                        This action is not allowed. Please use the correct method.
                    </p>
                    
                    <div class="d-grid gap-2">
                        <button onclick="history.back()" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home me-2"></i>Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
