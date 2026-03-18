@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle fa-4x text-danger mb-3"></i>
                        <h1 class="display-4 fw-bold text-danger">422</h1>
                    </div>
                    
                    <h2 class="h4 mb-3">Validation Error</h2>
                    
                    <p class="text-muted mb-4">
                        Please check your input and try again.
                    </p>
                    
                    @if($errors->any())
                        <div class="alert alert-danger text-start mb-4">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <button onclick="history.back()" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Go Back & Fix
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
