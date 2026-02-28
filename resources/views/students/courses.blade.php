@extends('students.app')

@section('content')
<div class="container py-4">
    <h2>My Course Details</h2>
    @forelse($courses as $course)
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $course->course_name ?? '-' }}</h4>
            </div>
            <div class="card-body">
                <h5>Lessons</h5>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead>
                            <tr>
                                <th>Lesson</th>
                                <th>Teacher</th>
                                <th>Terms</th>
                                <th>Daily Hours</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($course->lessons as $lesson)
                                <tr>
                                    <td>{{ $lesson->title }}</td>
                                    <td>{{ $lesson->teacher->teacher_name ?? '-' }}</td>
                                    <td>
                                        @foreach($lesson->terms as $term)
                                            <span class="badge bg-info text-dark">{{ $term->term ?? $term->title ?? '-' }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        {{-- Example: Show timetable/dailies for this lesson --}}
                                        @php
                                            $dailies = \App\Models\TeacherDaily::where('lesson_id', $lesson->id)->get();
                                        @endphp
                                        @if($dailies->count())
                                            @foreach($dailies as $daily)
                                                @php
                                                    $attendance = \App\Models\Attendance::where('student_id', auth()->id())
                                                        ->where('lesson_id', $lesson->id)
                                                        ->where('date', $daily->date)
                                                        ->first();
                                                @endphp
                                                <div>
                                                    <span class="badge bg-secondary">
                                                        {{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}:
                                                        @if($attendance)
                                                            @if($attendance->status == 'present')
                                                                <span class="badge bg-success">Present</span>
                                                            @elseif($attendance->status == 'absent')
                                                                <span class="badge bg-danger">Absent</span>
                                                            @elseif($attendance->status == 'late')
                                                                <span class="badge bg-warning text-dark">Late</span>
                                                            @else
                                                                <span class="badge bg-info text-dark">{{ ucfirst($attendance->status) }}</span>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-light text-dark">No record</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            @endforeach
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No lessons found for this course.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @empty
        <div class="alert alert-warning">No courses found.</div>
    @endforelse
</div>

<style>
/* Enhanced styling for courses page */
.container {
    max-width: 1200px;
}

h2 {
    color: #2c3e50;
    font-weight: 700;
    margin-bottom: 2rem;
    position: relative;
    padding-bottom: 0.5rem;
}

h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 2px;
}

.card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.12);
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none;
    padding: 1.5rem;
    position: relative;
    overflow: hidden;
}

.card-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 100px;
    height: 100px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    transform: rotate(45deg);
}

.card-header h4 {
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.card-body {
    padding: 2rem;
    background: #fafbfc;
}

.card-body h5 {
    color: #2c3e50;
    font-weight: 600;
    margin-bottom: 1.5rem;
    font-size: 1.1rem;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}

.table {
    margin-bottom: 0;
    background: white;
}

.table thead th {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f4fd 100%);
    border: none;
    color: #495057;
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1rem;
}

.table tbody tr {
    transition: all 0.2s ease;
    border: none;
}

.table tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    transform: translateX(2px);
}

.table tbody td {
    padding: 1rem;
    border: none;
    border-bottom: 1px solid #f1f3f4;
    vertical-align: middle;
}

.table tbody tr:last-child td {
    border-bottom: none;
}

.badge.bg-info {
    background-color: rgba(13, 202, 240, 0.15) !important;
    color: #0dcaf0 !important;
    border: 1px solid rgba(13, 202, 240, 0.3);
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    margin-right: 0.3rem;
    margin-bottom: 0.3rem;
    display: inline-block;
}

.badge.bg-secondary {
    background-color: rgba(108, 117, 125, 0.15) !important;
    color: #6c757d !important;
    border: 1px solid rgba(108, 117, 125, 0.3);
    font-weight: 500;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    margin-bottom: 0.5rem;
    display: inline-block;
    font-size: 0.75rem;
}

.text-muted {
    color: #8e9aaf !important;
    font-style: italic;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    border: none;
    border-radius: 12px;
    color: #856404;
    font-weight: 500;
    padding: 1.5rem;
    box-shadow: 0 2px 15px rgba(255, 193, 7, 0.2);
}

/* Empty state styling */
.table tbody tr td.text-center {
    padding: 3rem 1rem;
    color: #8e9aaf;
    font-style: italic;
    background: #f8f9fa;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .card:hover {
        transform: none;
    }
    
    .table tbody tr:hover {
        transform: none;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .table thead th,
    .table tbody td {
        padding: 0.75rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.3rem 0.6rem;
    }
    
    h2 {
        font-size: 1.5rem;
    }
}

/* Custom scrollbar */
.table-responsive::-webkit-scrollbar {
    height: 6px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection