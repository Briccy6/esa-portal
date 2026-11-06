@extends('teacher.layout')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-calendar-check me-2"></i>My Dailies</h2>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Branch</th>
                <th>Course</th>
                <th>Lesson</th>
                <th>Unit</th>
                <th>Activity</th>
                <th>Hours Studied</th>
                <th>Total Students</th>
                <th>Absences</th>
                <th>Comment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailies as $daily)
                <tr>
                    <td>{{ $daily->date }}</td>
                    <td>{{ $daily->branch->name ?? '-' }}</td>
                    <td>{{ $daily->course->course_name ?? '-' }}</td>
                    <td>{{ $daily->lesson->title ?? '-' }}</td>
                    <td>{{ $daily->unit->unit_name ?? '-' }}</td>
                    <td>
                        @php
                            $activities = is_array($daily->activity) ? $daily->activity : json_decode($daily->activity, true);
                        @endphp
                        @if(!empty($activities))
                            <ul class="mb-0 ps-3">
                                @foreach($activities as $act)
                                    <li>{{ $act }}</li>
                                @endforeach
                            </ul>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ $daily->hours_studied }}</td>
                    <td>{{ $daily->total_students }}</td>
                    <td>{{ $daily->absences }}</td>
                    <td>{{ Str::limit($daily->comment, 40) }}</td>
                    <td>
                        @if($daily->approved)
                            <span class="badge bg-success">Approved</span>
                            @if($daily->approved_at)
                                <small class="text-muted d-block">{{ $daily->approved_at->format('M d, Y') }}</small>
                            @endif
                        @else
                            <span class="badge bg-warning text-dark">Pending Approval</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center text-muted">No dailies found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $dailies->links() }}
</div>
@endsection