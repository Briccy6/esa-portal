@if(isset($selectedAcademicYear) && $selectedAcademicYear)
<div class="alert alert-info d-flex align-items-center mb-4 gap-2">
    <i class="fas fa-calendar-alt"></i>
    <span class="flex-grow-1">
        <strong>{{ $selectedAcademicYear->year }}</strong>
        @php($status = $selectedAcademicYear->status ?? 'active')
        @if($status === 'active')
            <span class="badge bg-success ms-2">Active</span>
        @elseif($status === 'upcoming')
            <span class="badge bg-info ms-2">Upcoming</span>
        @else
            <span class="badge bg-secondary ms-2">Ended</span>
        @endif
    </span>
</div>
@endif
<style>
.alert {
    font-size: 0.875rem;
    padding: 0.75rem 1rem;
}

@media (max-width: 576px) {
    .alert {
        font-size: 0.8125rem;
        padding: 0.625rem 0.75rem;
    }

    .alert i {
        display: none;
    }
}
</style>
