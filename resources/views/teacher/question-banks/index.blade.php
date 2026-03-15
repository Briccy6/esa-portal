@extends('teacher.layout')

@section('styles')
<style>
.question-banks-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    border-radius: 15px;
}

.question-banks-header h1 {
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.question-banks-header p {
    opacity: 0.9;
    margin-bottom: 0;
}

.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
}

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: #667eea;
}

.question-bank-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    border: 1px solid #e5e7eb;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}

.question-bank-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 20px rgba(0, 0, 0, 0.12);
}

.question-bank-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1rem 1.5rem;
    position: relative;
}

.question-bank-header h5 {
    font-weight: 600;
    margin: 0;
}

.question-bank-body {
    padding: 1.5rem;
}

.question-info {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
    color: #6b7280;
    font-size: 0.9rem;
}

.question-info i {
    margin-right: 0.5rem;
    color: #667eea;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-active {
    background: #d1fae5;
    color: #065f46;
}

.status-inactive {
    background: #f3f4f6;
    color: #6b7280;
}

.question-bank-actions {
    padding: 1rem 1.5rem;
    background: #f9fafb;
    border-top: 1px solid #e5e7eb;
}

.btn-custom {
    border-radius: 8px;
    font-weight: 500;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
}

.btn-custom:hover {
    transform: translateY(-1px);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    border: 2px dashed #e5e7eb;
}

.empty-state i {
    color: #d1d5db;
    margin-bottom: 1rem;
}

.import-section {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 15px;
    margin-bottom: 2rem;
    text-align: center;
}

.import-section h4 {
    margin-bottom: 0.5rem;
    font-weight: 600;
}

.import-section p {
    margin-bottom: 1rem;
    opacity: 0.9;
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
                    <h1><i class="bi bi-database"></i> My Question Banks</h1>
                    <p class="mb-0">Manage your question collections and import questions via CSV files</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <div class="stats-card bg-white bg-opacity-10 text-white">
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
            <h4><i class="bi bi-cloud-upload"></i> Import Questions</h4>
            <p>Upload questions using CSV files to add them to your question banks</p>
            <small class="d-block mb-3">Note: Question banks are created by administrators. Teachers can only import questions.</small>
            
            <div class="mt-3">
                <h6><i class="bi bi-download"></i> Download CSV Templates:</h6>
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ asset('mathematics_questions_template.csv') }}" class="btn btn-light btn-sm w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-primary"></i>
                            <div class="small">Mathematics Template</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ asset('biology_questions_template.csv') }}" class="btn btn-light btn-sm w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                            <div class="small">Biology Template</div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ asset('questions_template.csv') }}" class="btn btn-light btn-sm w-100" download>
                            <i class="bi bi-file-earmark-spreadsheet text-secondary"></i>
                            <div class="small">General Template</div>
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
            <div class="row">
                @foreach($questionBanks as $questionBank)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="question-bank-card">
                            <div class="question-bank-header">
                                <h5>{{ $questionBank->name }}</h5>
                            </div>
                            <div class="question-bank-body">
                                @if($questionBank->description)
                                    <p class="text-muted mb-3">{{ Str::limit($questionBank->description, 80) }}</p>
                                @else
                                    <p class="text-muted mb-3">No description available</p>
                                @endif
                                
                                <div class="question-info">
                                    <i class="bi bi-book"></i>
                                    <span>{{ $questionBank->course ? $questionBank->course->course_name : 'General' }}</span>
                                </div>
                                
                                <div class="question-info">
                                    <i class="bi bi-question-square"></i>
                                    <span>{{ $questionBank->questions->count() }} questions</span>
                                </div>
                                
                                <div class="question-info">
                                    <i class="bi bi-person"></i>
                                    <span>{{ $questionBank->teacher ? $questionBank->teacher->name : 'Unknown' }}</span>
                                </div>
                                
                                @if($questionBank->questions->count() > 0)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="bi bi-calculator"></i> 
                                            Contains mathematical expressions
                                        </small>
                                    </div>
                                @endif
                                
                                <div class="mt-3">
                                    <span class="status-badge {{ $questionBank->is_active ? 'status-active' : 'status-inactive' }}">
                                        {{ $questionBank->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            <div class="question-bank-actions">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('teacher.question-banks.show', $questionBank) }}" class="btn btn-primary btn-custom">
                                        <i class="bi bi-eye"></i> View Questions
                                    </a>
                                    <a href="{{ route('teacher.question-banks.add-question', $questionBank) }}" class="btn btn-success btn-custom">
                                        <i class="bi bi-cloud-upload"></i> Import CSV
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
                    <i class="bi bi-info-circle"></i>
                    <strong>Note:</strong> Teachers cannot create question banks. Please contact your administrator if you need new question banks created.
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
