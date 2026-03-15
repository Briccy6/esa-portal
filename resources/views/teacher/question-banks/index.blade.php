@extends('teacher.layout')

@section('styles')
<style>
/* Modern Design System */
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --info-gradient: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    --card-shadow-hover: 0 12px 20px rgba(0, 0, 0, 0.12);
    --border-radius: 15px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Page Header */
.question-banks-header {
    background: var(--primary-gradient);
    color: white;
    padding: 3rem 0;
    margin-bottom: 2rem;
    border-radius: var(--border-radius);
    position: relative;
    overflow: hidden;
}

.question-banks-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

.question-banks-header h1 {
    font-weight: 700;
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 1;
}

.question-banks-header p {
    opacity: 0.9;
    margin-bottom: 0;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

.question-banks-header .stats-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: var(--transition);
}

.question-banks-header .stats-card:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-2px);
}

.question-banks-header .stats-number {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

/* Import Section */
.import-section {
    background: var(--success-gradient);
    color: white;
    padding: 2.5rem;
    border-radius: var(--border-radius);
    margin-bottom: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.import-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="import-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M10 0 L20 10 L10 20 L0 10 Z" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23import-pattern)"/></svg>');
    pointer-events: none;
}

.import-section h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
    font-size: 1.5rem;
    position: relative;
    z-index: 1;
}

.import-section p {
    margin-bottom: 1rem;
    opacity: 0.9;
    font-size: 1.1rem;
    position: relative;
    z-index: 1;
}

.import-section .btn-light {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 10px;
    padding: 1rem;
    font-weight: 500;
    transition: var(--transition);
    position: relative;
    z-index: 1;
}

.import-section .btn-light:hover {
    background: rgba(255, 255, 255, 1);
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

/* Question Bank Cards */
.question-bank-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--card-shadow);
    border: 1px solid #e5e7eb;
    transition: var(--transition);
    height: 100%;
    position: relative;
}

.question-bank-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition);
}

.question-bank-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--card-shadow-hover);
}

.question-bank-card:hover::before {
    opacity: 1;
}

.question-bank-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.5rem;
    position: relative;
}

.question-bank-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
}

.question-bank-header h5 {
    font-weight: 600;
    margin: 0;
    font-size: 1.25rem;
}

.question-bank-body {
    padding: 1.5rem;
}

.question-info {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
    color: #6b7280;
    font-size: 0.9rem;
    padding: 0.5rem;
    background: #f9fafb;
    border-radius: 8px;
    transition: var(--transition);
}

.question-info:hover {
    background: #f3f4f6;
    transform: translateX(2px);
}

.question-info i {
    margin-right: 0.75rem;
    color: #667eea;
    font-size: 1rem;
}

.question-info span {
    font-weight: 500;
}

/* Status Badges */
.status-badge {
    display: inline-block;
    padding: 0.4rem 1rem;
    border-radius: 25px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: var(--transition);
}

.status-active {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.status-inactive {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    color: #6b7280;
    border: 1px solid #d1d5db;
}

/* Action Buttons */
.question-bank-actions {
    padding: 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.btn-custom {
    border-radius: 10px;
    font-weight: 500;
    padding: 0.75rem 1rem;
    transition: var(--transition);
    border: none;
    position: relative;
    overflow: hidden;
}

.btn-custom::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-custom:hover::before {
    left: 100%;
}

.btn-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.btn-primary {
    background: var(--info-gradient);
}

.btn-success {
    background: var(--success-gradient);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 5rem 2rem;
    background: white;
    border-radius: var(--border-radius);
    border: 2px dashed #e5e7eb;
    position: relative;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1.5rem;
    font-size: 4rem;
}

.empty-state h3 {
    color: #6b7280;
    margin-bottom: 1rem;
}

/* Alerts */
.alert {
    border-radius: 12px;
    border: none;
    padding: 1rem 1.5rem;
    margin-bottom: 1rem;
    position: relative;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    color: #065f46;
}

.alert-danger {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    color: #991b1b;
}

.alert-warning {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
}

.alert-info {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
}

/* Pagination */
.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: none;
    color: #667eea;
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background: var(--primary-gradient);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.question-bank-card {
    animation: fadeInUp 0.6s ease-out;
}

.question-bank-card:nth-child(1) { animation-delay: 0.1s; }
.question-bank-card:nth-child(2) { animation-delay: 0.2s; }
.question-bank-card:nth-child(3) { animation-delay: 0.3s; }

/* Responsive Design */
@media (max-width: 768px) {
    .question-banks-header h1 {
        font-size: 2rem;
    }
    
    .question-banks-header {
        padding: 2rem 0;
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
