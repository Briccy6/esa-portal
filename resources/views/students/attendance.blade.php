@extends('students.app')

@section('content')
    <h2>My Attendance</h2>
    <div class="mb-3">
        <label>Approved Attendance Progress</label>
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $attendancePercent }}%;" aria-valuenow="{{ $attendancePercent }}" aria-valuemin="0" aria-valuemax="100">
                {{ $attendancePercent }}%
            </div>
        </div>
        <div class="mt-2">
            <strong>Approved days attended:</strong> {{ $attendedDays }} / {{ $totalDays }}
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Lesson</th>
                    <th>Status</th>
                    <th>Review</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendanceRecords as $record)
                    <tr>
                        <td>{{ $record->marked_at ? $record->marked_at->format('d M Y H:i') : ($record->date ? $record->date->format('d M Y') : '-') }}</td>
                        <td>{{ $record->lesson->title ?? '-' }}</td>
                        <td>
                            @if(in_array(strtoupper((string) $record->status), ['P', 'PRESENT'], true))
                                <span class="badge bg-success">Present</span>
                            @elseif(in_array(strtoupper((string) $record->status), ['L', 'LATE'], true))
                                <span class="badge bg-warning text-dark">Late</span>
                            @elseif(strtoupper((string) $record->status) === 'AR')
                                <span class="badge bg-danger">Absent with reason</span>
                            @elseif(in_array(strtoupper((string) $record->status), ['A', 'ABSENT'], true))
                                <span class="badge bg-danger">Absent</span>
                            @else
                                <span class="badge bg-secondary">{{ $record->status }}</span>
                            @endif
                        </td>
                        <td>
                            @if($record->approved)
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending DOS approval</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">No attendance has been recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
