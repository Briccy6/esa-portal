@extends('students.app')

@section('content')
    <h2>My Attendance</h2>
    <div class="mb-3">
        <label>Attendance Progress</label>
        <div class="progress" style="height: 30px;">
            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100">
                {{ $progress }}%
            </div>
        </div>
        <div class="mt-2">
            <strong>Days Attended:</strong> {{ $attendedDays }} / {{ $totalDays }}
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendanceRecords as $record)
                <tr>
                    <td>{{ $record->date }}</td>
                    <td>
                        @if($record->status == 'present')
                            <span class="badge bg-success">Present</span>
                        @else
                            <span class="badge bg-danger">Absent</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection