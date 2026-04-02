@extends('admin.layout')

@section('title', 'Edit Report Card Setting')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Edit Report Card Setting</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.report-card-settings.index') }}">Report Card Settings</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-calendar-alt"></i>
            Edit Report Card Setting
        </h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.report-card-settings.update', $reportCardSetting) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="academic_year" class="form-label">Academic Year *</label>
                        <input type="text" 
                               class="form-control @error('academic_year') is-invalid @enderror" 
                               id="academic_year" 
                               name="academic_year" 
                               value="{{ old('academic_year', $reportCardSetting->academic_year) }}"
                               placeholder="e.g., 2025-2026"
                               required>
                        @error('academic_year')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="term" class="form-label">Term *</label>
                        <select class="form-select @error('term') is-invalid @enderror" 
                                id="term" 
                                name="term" 
                                required>
                            <option value="">Select Term</option>
                            <option value="Term 1" {{ old('term', $reportCardSetting->term) == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                            <option value="Term 2" {{ old('term', $reportCardSetting->term) == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                            <option value="Term 3" {{ old('term', $reportCardSetting->term) == 'Term 3' ? 'selected' : '' }}>Term 3</option>
                        </select>
                        @error('term')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="report_card_available_at" class="form-label">Report Cards Available From *</label>
                        <input type="datetime-local" 
                               class="form-control @error('report_card_available_at') is-invalid @enderror" 
                               id="report_card_available_at" 
                               name="report_card_available_at" 
                               value="{{ old('report_card_available_at', $reportCardSetting->report_card_available_at->format('Y-m-d\TH:i')) }}"
                               required>
                        @error('report_card_available_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Students will be able to access their report cards from this date and time, provided they have paid all fees.
                        </small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <div class="form-check mt-4">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   {{ old('is_active', $reportCardSetting->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                            <small class="form-text text-muted d-block">
                                Enable this setting for students
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" 
                          id="description" 
                          name="description" 
                          rows="3"
                          placeholder="Optional description for this report card setting">{{ old('description', $reportCardSetting->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Status Information -->
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> Current Status</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>Setting Status:</strong>
                        @if($reportCardSetting->is_active)
                            @if($reportCardSetting->isAvailable())
                                <span class="badge bg-success">Available</span>
                            @else
                                <span class="badge bg-warning">Scheduled</span>
                            @endif
                        @else
                            <span class="badge bg-secondary">Inactive</span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <strong>Time Until Available:</strong>
                        @if($reportCardSetting->isAvailable())
                            <span class="text-success">Available now</span>
                        @else
                            <span class="text-warning">{{ $reportCardSetting->getTimeRemaining() }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="{{ route('admin.report-card-settings.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Settings
                </a>
                <div>
                    <button type="reset" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Setting
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const datetimeInput = document.getElementById('report_card_available_at');
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    // Allow past dates for editing but show warning if date is in the past
    datetimeInput.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        if (selectedDate < now) {
            if (!confirm('You have selected a date/time in the past. This will make report cards immediately available. Continue?')) {
                // Reset to original value
                this.value = '{{ $reportCardSetting->report_card_available_at->format('Y-m-d\TH:i') }}';
            }
        }
    });
});
</script>
@endsection
