@if(isset($selectedAcademicYear) && $selectedAcademicYear)
<div class="alert alert-info d-flex align-items-center justify-content-between mb-4">
    <div>
        <i class="fas fa-calendar-alt me-2"></i>
        Showing data for academic year <strong>{{ $selectedAcademicYear->year }}</strong>
        @php($status = $selectedAcademicYear->status ?? 'active')
        @if($status === 'active')
            <span class="badge bg-success ms-2">Active</span>
        @elseif($status === 'upcoming')
            <span class="badge bg-info ms-2">Upcoming</span>
        @else
            <span class="badge bg-secondary ms-2">Ended</span>
        @endif
    </div>
    <small class="text-muted">Use the academic year selector in the top bar to switch years.</small>
</div>
@endif
