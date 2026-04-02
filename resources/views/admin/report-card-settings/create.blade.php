@extends('admin.layout')

@section('title', 'Create Report Card Setting')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="page-title">Create Report Card Setting</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.report-card-settings.index') }}">Report Card Settings</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">
            <i class="fas fa-calendar-alt"></i>
            Create Report Card Setting
        </h5>
    </div>
    <div class="card-body">
                    <form action="{{ route('admin.report-card-settings.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="academic_year" class="form-label">Academic Year *</label>
                                    <input type="text" 
                                           class="form-control @error('academic_year') is-invalid @enderror" 
                                           id="academic_year" 
                                           name="academic_year" 
                                           value="{{ old('academic_year', config('app.academic_year', '2025-2026')) }}"
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
                                        <option value="Term 1" {{ old('term', 'Term 1') == 'Term 1' ? 'selected' : '' }}>Term 1</option>
                                        <option value="Term 2" {{ old('term') == 'Term 2' ? 'selected' : '' }}>Term 2</option>
                                        <option value="Term 3" {{ old('term') == 'Term 3' ? 'selected' : '' }}>Term 3</option>
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
                                           value="{{ old('report_card_available_at') }}"
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
                                               {{ old('is_active', true) ? 'checked' : '' }}>
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
                                      placeholder="Optional description for this report card setting">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
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
                                    <i class="fas fa-save"></i> Create Setting
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

@section('scripts')
<script>
// Set minimum datetime to current time
document.addEventListener('DOMContentLoaded', function() {
    const datetimeInput = document.getElementById('report_card_available_at');
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    
    datetimeInput.min = `${year}-${month}-${day}T${hours}:${minutes}`;
    
    // Set default to tomorrow at 9:00 AM if no value exists
    if (!datetimeInput.value) {
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(9, 0, 0, 0);
        
        const defaultYear = tomorrow.getFullYear();
        const defaultMonth = String(tomorrow.getMonth() + 1).padStart(2, '0');
        const defaultDay = String(tomorrow.getDate()).padStart(2, '0');
        const defaultHours = String(tomorrow.getHours()).padStart(2, '0');
        const defaultMinutes = String(tomorrow.getMinutes()).padStart(2, '0');
        
        datetimeInput.value = `${defaultYear}-${defaultMonth}-${defaultDay}T${defaultHours}:${defaultMinutes}`;
    }
});
</script>
@endsection
