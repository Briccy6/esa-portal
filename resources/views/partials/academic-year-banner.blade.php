@if(isset($selectedAcademicYear) && $selectedAcademicYear)
<div class="alert alert-info d-flex flex-column flex-md-row align-items-center justify-content-between mb-4 gap-2">
    <div class="text-center text-md-start">
        <i class="fas fa-calendar-alt me-2"></i>
        <span class="d-inline-block">Showing data for academic year <strong>{{ $selectedAcademicYear->year }}</strong></span>
        @php($status = $selectedAcademicYear->status ?? 'active')
        @if($status === 'active')
            <span class="badge bg-success ms-2 d-inline-block">Active</span>
        @elseif($status === 'upcoming')
            <span class="badge bg-info ms-2 d-inline-block">Upcoming</span>
        @else
            <span class="badge bg-secondary ms-2 d-inline-block">Ended</span>
        @endif
    </div>
    <small class="text-muted text-center text-md-end">Use the academic year selector in the top bar to switch years.</small>
</div>
@endif
<style>
@media (max-width: 768px) {
    .alert {
        font-size: 0.875rem !important;
        padding: 0.75rem !important;
    }

    .alert strong {
        word-break: break-word;
    }

    .alert .badge {
        font-size: 0.7rem !important;
        padding: 0.25rem 0.5rem !important;
    }
}

@media (max-width: 576px) {
    .alert {
        font-size: 0.8125rem !important;
        padding: 0.625rem !important;
    }

    .alert i {
        display: none;
    }

    .alert small {
        font-size: 0.75rem !important;
    }
}
</style>
