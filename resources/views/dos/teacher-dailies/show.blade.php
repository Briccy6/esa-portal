@extends('dos.layout')

@section('title', 'Teacher Daily Activity Details')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar-check me-2"></i>Daily Activity Details</h2>
        <a href="{{ route('dos.teacher-dailies.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Daily Activity Info -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Activity Information</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Date:</strong> {{ $daily->date }}</p>
                    <p><strong>Teacher:</strong> {{ $daily->teacher->teacher_name ?? '-' }}</p>
                    <p><strong>Branch:</strong> {{ $daily->branch->name ?? '-' }}</p>
                    <p><strong>Course:</strong> {{ $daily->course->course_name ?? '-' }}</p>
                    <p><strong>Lesson:</strong> {{ $daily->lesson->title ?? '-' }}</p>
                    <p><strong>Unit:</strong> {{ $daily->unit->unit_name ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Total Students:</strong> {{ $daily->total_students }}</p>
                    <p><strong>Absences:</strong> {{ $daily->absences }}</p>
                    <p><strong>Hours Studied:</strong> {{ $daily->hours_studied }}</p>
                    <p><strong>Status:</strong>
                        @if($daily->approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </p>
                    @if($daily->approved_at)
                        <p><strong>Approved At:</strong> {{ $daily->approved_at }}</p>
                    @endif
                </div>
            </div>
            
            <hr>
            
            <div class="row">
                <div class="col-md-12">
                    <h6>Activities Done:</h6>
                    @php
                        $activities = is_array($daily->activity) ? $daily->activity : json_decode($daily->activity, true);
                    @endphp
                    @if(!empty($activities))
                        <ul>
                            @foreach($activities as $act)
                                <li>{{ $act }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No activities recorded.</p>
                    @endif
                </div>
            </div>

            @if($daily->comment)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Comments:</h6>
                        <p>{{ $daily->comment }}</p>
                    </div>
                </div>
            @endif

            @if($daily->challenges)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Challenges Faced:</h6>
                        <p>{{ $daily->challenges }}</p>
                    </div>
                </div>
            @endif

            @if($daily->quiz_link)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h6>Quiz/Test Link:</h6>
                        <a href="{{ $daily->quiz_link }}" target="_blank">{{ $daily->quiz_link }}</a>
                    </div>
                </div>
            @endif
        </div>
        <div class="card-footer">
            @if(!$daily->approved)
                <button type="button" class="btn btn-success" onclick="approveDaily({{ $daily->id }})">
                    <i class="bi bi-check-circle me-2"></i>Approve Activity
                </button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle me-2"></i>Reject Activity
                </button>
            @else
                <span class="text-success"><i class="bi bi-check-circle me-2"></i>This activity has been approved</span>
            @endif
        </div>
    </div>

    <!-- Attendance Records -->
    <div class="card">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0">Attendance Records</h5>
        </div>
        <div class="card-body">
            @if($attendances->isEmpty())
                <p class="text-muted">No attendance records found for this activity.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Reg Number</th>
                                <th>Status</th>
                                <th>Marked At</th>
                                <th>Approved</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attendances as $attendance)
                                <tr>
                                    <td>{{ $attendance->student->full_name ?? '-' }}</td>
                                    <td>{{ $attendance->student->reg_number ?? '-' }}</td>
                                    <td>
                                        @if($attendance->status == 'P')
                                            <span class="badge bg-success">Present</span>
                                        @elseif($attendance->status == 'A')
                                            <span class="badge bg-danger">Absent</span>
                                        @elseif($attendance->status == 'AR')
                                            <span class="badge bg-warning text-dark">Absent with Reason</span>
                                        @elseif($attendance->status == 'L')
                                            <span class="badge bg-info">Late</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->marked_at ? $attendance->marked_at->format('Y-m-d H:i') : '-' }}</td>
                                    <td>
                                        @if($attendance->approved)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-warning text-dark">No</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Daily Activity</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="rejectForm" method="POST" action="{{ route('dos.teacher-dailies.reject', $daily->id) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">Rejection Reason</label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required placeholder="Please provide a reason for rejection..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="submitReject()">Reject</button>
            </div>
        </div>
    </div>
</div>

<script>
function approveDaily(id) {
    if (confirm('Are you sure you want to approve this daily activity and all related attendance records?')) {
        fetch('{{ route('dos.teacher-dailies.approve', ':id') }}'.replace(':id', id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error: ' + error.message);
        });
    }
}

function submitReject() {
    const reason = document.getElementById('rejection_reason').value;
    if (!reason.trim()) {
        alert('Please provide a rejection reason.');
        return;
    }
    
    document.getElementById('rejectForm').submit();
}
</script>
@endsection
