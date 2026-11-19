{{-- filepath: d:\website\esa\resources\views\attendance\mark.blade.php --}}
@extends('dos.layout')

@section('content')
    <h2>Mark Attendance for {{ $today }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        $user = null;
        if(auth()->guard('teacher')->check()) $user = auth()->guard('teacher')->user();
        elseif(auth()->guard('staff')->check()) $user = auth()->guard('staff')->user();
        elseif(auth()->guard('admin')->check()) $user = auth()->guard('admin')->user();
    @endphp

    @foreach($lessons as $lesson)
        <h5>
            {{ $lesson->lesson->name ?? 'Lesson' }}
            ({{ $lesson->start_time }} - {{ $lesson->end_time }})
            @if($lesson->teacher)
                <span class="text-muted">| Teacher: {{ $lesson->teacher->full_name ?? $lesson->teacher->name ?? '' }}</span>
            @endif
        </h5>
        <form method="POST" action="{{ route('attendance.mark', $lesson->id) }}">
            @csrf
            <input type="hidden" name="date" value="{{ $today }}">
            <table class="table table-bordered">
                <tr>
                    <th>Student</th>
                    <th>Status</th>
                </tr>
                @foreach($students as $student)
                    <tr>
                        <td>{{ $student->full_name }}</td>
                        <td>
                            <select name="status[{{ $student->id }}]" class="form-select">
                                <option value="present">Present</option>
                                <option value="absent">Absent</option>
                                <option value="late">Late</option>
                            </select>
                        </td>
                    </tr>
                @endforeach
            </table>
            <button type="submit" class="btn btn-primary">Save Attendance</button>
        </form>
        <hr>
    @endforeach

    @if(empty($lessons) || $lessons->count() === 0)
        <div class="alert alert-info">No lessons scheduled for today.</div>
    @endif
@endsection