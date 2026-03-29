@php
    $nowKigali = \Carbon\Carbon::now('Africa/Kigali');
    $validFrom = \Carbon\Carbon::parse($timetable->valid_from);
    $validUntil = \Carbon\Carbon::parse($timetable->valid_until);
@endphp

<div class="timetable-card">
    <div class="timetable-header">
        <h5 class="timetable-title">{{ $timetable->title }}</h5>
        <div class="timetable-status">
            @if($timetable->valid_from && $timetable->valid_until)
                @if($nowKigali->between($validFrom, $validUntil))
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Active
                    </span>
                @elseif($nowKigali->lt($validFrom))
                    <span class="badge bg-warning">
                        <i class="bi bi-clock me-1"></i>Upcoming
                    </span>
                @else
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>Expired
                    </span>
                @endif
            @else
                <span class="badge bg-primary">
                    <i class="bi bi-infinity me-1"></i>Always Valid
                </span>
            @endif
        </div>
    </div>
    <div class="timetable-body">
        @if($timetable->image)
            <div class="timetable-image">
                <img src="data:image/jpeg;base64,{{ $timetable->image }}" 
                     alt="Timetable: {{ $timetable->title }}" 
                     class="img-fluid rounded"
                     style="max-height: 200px; cursor: pointer;"
                     onclick="viewTimetableImage('{{ $timetable->title }}', 'data:image/jpeg;base64,{{ $timetable->image }}')">
            </div>
        @endif
        <div class="timetable-details">
            @if($timetable->valid_from && $timetable->valid_until)
                <div class="timetable-dates">
                    <i class="bi bi-calendar-range me-2"></i>
                    <strong>Valid Period:</strong>
                    {{ $validFrom->format('M d, Y') }} - 
                    {{ $validUntil->format('M d, Y') }}
                </div>
                <div class="timetable-time-remaining">
                    <i class="bi bi-clock me-2"></i>
                    <small class="text-muted">
                        @if($nowKigali->between($validFrom, $validUntil))
                            Expires in {{ $validUntil->diffForHumans($nowKigali, true) }}
                        @elseif($nowKigali->lt($validFrom))
                            Starts in {{ $validFrom->diffForHumans($nowKigali, true) }}
                        @else
                            Expired {{ $validUntil->diffForHumans($nowKigali, true) }} ago
                        @endif
                    </small>
                </div>
            @else
                <div class="timetable-dates">
                    <i class="bi bi-infinity me-2"></i>
                    <strong>No expiry date set</strong>
                </div>
            @endif
            <div class="timetable-actions">
                <button onclick="viewTimetableImage('{{ $timetable->title }}', 'data:image/jpeg;base64,{{ $timetable->image }}')" 
                        class="btn btn-primary btn-sm">
                    <i class="bi bi-eye me-1"></i>View Full Size
                </button>
                <button onclick="downloadTimetable('{{ $timetable->title }}', 'data:image/jpeg;base64,{{ $timetable->image }}')" 
                        class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-download me-1"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>
