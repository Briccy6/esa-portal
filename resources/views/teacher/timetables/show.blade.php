@extends('teacher.layout')

@section('content')
<style>
    .timetable-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }

    .timetable-table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timetable-table thead {
        background: #667eea;
        color: white;
    }

    .timetable-table th {
        padding: 1rem;
        font-weight: 600;
        border: none;
    }

    .timetable-table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    .timetable-table tbody tr:hover {
        background: #f8fafc;
    }

    .download-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 0.5rem 1.5rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="timetable-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1">{{ $timetable->title }}</h2>
                <p class="mb-0 opacity-75">
                    Branch: {{ $timetable->branch->name ?? 'N/A' }} | 
                    Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }} | 
                    Term: {{ $timetable->term->name ?? 'N/A' }}
                </p>
            </div>
            <div>
                <a href="{{ route('teacher.timetables.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <button onclick="downloadTimetable()" class="download-btn">
                    <i class="fas fa-download me-1"></i>Download
                </button>
            </div>
        </div>
    </div>

    @if($teacherSessions->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No sessions found for this timetable.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table timetable-table mb-0" id="timetable-detail">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Day</th>
                                <th>Course</th>
                                <th>Lesson</th>
                                <th>Room</th>
                                <th>Teacher</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                            @endphp
                            @foreach($days as $dayNum => $dayName)
                                @php
                                    $daySessions = $teacherSessions->where('day_of_week', $dayNum)->sortBy('start_time');
                                @endphp
                                @if($daySessions->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">
                                            <em>No sessions on {{ $dayName }}</em>
                                        </td>
                                    </tr>
                                @else
                                    @foreach($daySessions as $session)
                                    <tr>
                                        <td>{{ $session->start_time ?? '' }} - {{ $session->end_time ?? '' }}</td>
                                        <td>{{ $dayName }}</td>
                                        <td>
                                            @foreach($session->courses as $course)
                                                {{ $course->course_name }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        </td>
                                        <td>{{ $session->lesson->title ?? 'N/A' }}</td>
                                        <td>{{ optional($session->classroom) }}</td>
                                        <td>{{ $session->lesson->teacher->teacher_name ?? 'TBA' }}</td>
                                    </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function downloadTimetable() {
    const table = document.getElementById('timetable-detail');
    if (!table) return;

    const tableClone = table.cloneNode(true);
    
    // Create a clean HTML for the timetable
    const html = `
        <html>
        <head>
            <title>{{ $timetable->title }}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { color: #667eea; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #667eea; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h1>{{ $timetable->title }}</h1>
            <p>Branch: {{ $timetable->branch->name ?? 'N/A' }} | Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }} | Term: {{ $timetable->term->name ?? 'N/A' }}</p>
            ${tableClone.outerHTML}
        </body>
        </html>
    `;

    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = '{{ str_slug($timetable->title) }}_timetable.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection