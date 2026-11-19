@extends('teacher.layout')

@section('styles')
<style>
/* Professional Design System */
:root {
    --primary-color: #1e40af;
    --primary-light: #3b82f6;
    --primary-dark: #1e3a8a;
    --success-color: #059669;
    --info-color: #0891b2;
    --warning-color: #d97706;
    --card-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    --card-shadow-hover: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --border-radius: 12px;
    --transition: all 0.2s ease;
}

/* Page Header */
.question-banks-header {
    background: var(--primary-color);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: var(--border-radius);
}

.question-banks-header h1 {
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.question-banks-header p {
    opacity: 0.9;
    margin-bottom: 0;
    font-size: 1rem;
}

.question-banks-header .stats-card {
    background: rgba(255, 255, 255, 0.15);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    padding: 1rem;
    text-align: center;
}

.question-banks-header .stats-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

/* Import Section */
.import-section {
    background: var(--success-color);
    color: white;
    padding: 2rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    text-align: center;
}

.import-section h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.25rem;
}

.import-section p {
    margin-bottom: 1rem;
    opacity: 0.9;
    font-size: 0.95rem;
}

.import-section .btn-light {
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 8px;
    padding: 0.75rem;
    font-weight: 500;
    transition: var(--transition);
}

.import-section .btn-light:hover {
    background: white;
    transform: translateY(-1px);
}

/* Question Bank Cards */
.question-bank-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--card-shadow);
    border: 1px solid #e2e8f0;
    transition: var(--transition);
    height: 100%;
}

.question-bank-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--card-shadow-hover);
    border-color: var(--primary-light);
}

.question-bank-header {
    background: var(--primary-color);
    color: white;
    padding: 1.25rem;
}

.question-bank-header h5 {
    font-weight: 600;
    margin: 0;
    font-size: 1.125rem;
}

.question-bank-body {
    padding: 1.25rem;
}

.question-info {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    color: #64748b;
    font-size: 0.875rem;
    padding: 0.5rem;
    background: #f8fafc;
    border-radius: 6px;
}

.question-info i {
    margin-right: 0.75rem;
    color: var(--primary-color);
    font-size: 1rem;
}

.question-info span {
    font-weight: 500;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.35rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #f1f5f9;
    color: #64748b;
}

/* Action Buttons */
.question-bank-actions {
    padding: 1rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.btn-custom {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.625rem 1rem;
    transition: var(--transition);
    border: none;
}

.btn-custom:hover {
    transform: translateY(-1px);
}

.btn-primary {
    background: var(--info-color);
}

.btn-primary:hover {
    background: #0e7490;
}

.btn-success {
    background: var(--success-color);
}

.btn-success:hover {
    background: #047857;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    background: white;
    border-radius: var(--border-radius);
    border: 2px dashed #e2e8f0;
}

.empty-state i {
    color: #cbd5e1;
    margin-bottom: 1rem;
    font-size: 3rem;
}

.empty-state h3 {
    color: #64748b;
    margin-bottom: 1rem;
}

/* Alerts */
.alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
}

.alert-info {
    background: #dbeafe;
    color: #1e40af;
}

/* Pagination */
.pagination .page-link {
    border-radius: 6px;
    margin: 0 2px;
    border: 1px solid #e2e8f0;
    color: var(--primary-color);
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: #fff;
}

/* Responsive Design */
@media (max-width: 768px) {
    .question-banks-header h1 {
        font-size: 1.5rem;
    }

    .question-banks-header {
        padding: 1.5rem 0;
    }

    .import-section {
        padding: 1.5rem;
    }

    .question-bank-card {
        margin-bottom: 1rem;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="question-banks-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-database me-3"></i> My Question Banks</h1>
                    <p class="mb-0">Manage your question collections and import questions via CSV files</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats-card">
                        <div class="stats-number">{{ $questionBanks->total() }}</div>
                        <div class="small">Total Banks</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Section -->
    <div class="container">
        <div class="import-section">
            <h4><i class="bi bi-cloud-upload me-2"></i> Import Questions</h4>
            <p>Upload questions using CSV files to add them to your question banks</p>
            <div class="alert alert-light bg-white bg-opacity-10 border-white border-opacity-25">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Note:</strong> Question banks are created by administrators. Teachers can only import questions.
            </div>
            
            <div class="mt-4">
                <h5><i class="bi bi-download me-2"></i> Download CSV Templates:</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="{{ asset('mathematics_questions_template.csv') }}" class="btn btn-light w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-primary fs-4 d-block mb-2"></i>
                            <div class="fw-bold">Mathematics Template</div>
                            <small class="text-muted">For mathematical questions</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ asset('biology_questions_template.csv') }}" class="btn btn-light w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-success fs-4 d-block mb-2"></i>
                            <div class="fw-bold">Biology Template</div>
                            <small class="text-muted">For biology questions</small>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ asset('questions_template.csv') }}" class="btn btn-light w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-secondary fs-4 d-block mb-2"></i>
                            <div class="fw-bold">General Template</div>
                            <small class="text-muted">For general questions</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> 
                <strong>Import Errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    </div>

    <!-- Question Banks Grid -->
    <div class="container">
        @if($questionBanks->count() > 0)
            <div class="row g-4">
                @foreach($questionBanks as $questionBank)
                    <div class="col-lg-4 col-md-6">
                        <div class="question-bank-card">
                            <div class="question-bank-header">
                                <h5><i class="bi bi-collection me-2"></i>{{ $questionBank->name }}</h5>
                            </div>
                            <div class="question-bank-body">
                                @if($questionBank->description)
                                    <p class="text-muted mb-3">{{ Str::limit($questionBank->description, 80) }}</p>
                                @else
                                    <p class="text-muted mb-3"><em>No description available</em></p>
                                @endif
                                
                                <div class="question-info">
                                    <i class="bi bi-book"></i>
                                    <span>{{ $questionBank->course ? $questionBank->course->course_name : 'General' }}</span>
                                </div>
                                
                                <div class="question-info">
                                    <i class="bi bi-question-square"></i>
                                    <span><strong>{{ $questionBank->questions->count() }}</strong> questions</span>
                                </div>
                                
                                <div class="question-info">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $questionBank->teacher ? $questionBank->teacher->name : 'Unknown' }}</span>
                                </div>
                                
                                @if($questionBank->questions->count() > 0)
                                    <div class="mt-3">
                                        <div class="alert alert-info py-2">
                                            <i class="bi bi-calculator me-2"></i>
                                            <small>Contains mathematical expressions</small>
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="mt-3">
                                    <span class="status-badge {{ $questionBank->is_active ? 'status-active' : 'status-inactive' }}">
                                        <i class="bi bi-circle-fill me-1"></i>
                                        {{ $questionBank->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="question-bank-actions">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-primary btn-custom">
                                        <i class="bi bi-eye me-2"></i> View Questions
                                    </a>
                                    <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success btn-custom">
                                        <i class="bi bi-cloud-upload me-2"></i> Import CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="empty-state">
                <i class="bi bi-database display-1"></i>
                <h3 class="mt-4">No Question Banks Available</h3>
                <p class="text-muted mb-4">
                    Question banks are created by administrators. Once question banks are available, you can import questions using CSV files.
                </p>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> Teachers cannot create question banks. Please contact your administrator if you need new question banks created.
                </div>
                <div class="mt-4">
                    <a href="{{ route('teacher.dashboard') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    @if($questionBanks->hasPages())
        <div class="container mt-4">
            <div class="d-flex justify-content-center">
                {{ $questionBanks->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
