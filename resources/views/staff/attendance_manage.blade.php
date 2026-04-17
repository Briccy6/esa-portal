{{-- filepath: d:\website\esa\resources\views\staff\attendance_manage.blade.php --}}
@extends('staff.partials.sidebar_dos')

@section('content')
    <h2>Mark Attendance by Course & Lesson</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Course and Branch Selection --}}
    <form method="GET" action="{{ route('attendance.mark.form') }}" class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Course</label>
            <select name="course_id" class="form-select" required>
                <option value="">Select Course</option>
                @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                        {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Branch</label>
            <select name="branch_id" class="form-select" required>
                <option value="">Select Branch</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                        {{ $branch->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary">Show Lessons</button>
        </div>
    </form>

    @if(isset($lessons) && count($lessons))
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
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
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
                    </tbody>
                </table>
                <button type="submit" class="btn btn-success">Save Attendance</button>
            </form>
            <hr>
        @endforeach
    @elseif(request('course_id') && request('branch_id'))
        <div class="alert alert-info">No lessons scheduled for today for this course and branch.</div>
    @endif
@endsection