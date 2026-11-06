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
        <h2 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>My Timetables</h2>
        <p class="mb-0 opacity-75">View timetables for your assigned lessons</p>
    </div>

    @if($timetables->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No active timetables were found for your assigned classes.
        </div>
    @else
        @foreach($timetables as $timetable)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-1">{{ $timetable->title }}</h5>
                        <small class="opacity-75">
                            Branch: {{ $timetable->branch->name ?? 'N/A' }} | 
                            Academic Year: {{ $timetable->academicYear->year ?? 'N/A' }} | 
                            Term: {{ $timetable->term->name ?? 'N/A' }}
                        </small>
                    </div>
                    <div>
                        <button onclick="downloadTimetable({{ $timetable->id }})" class="download-btn">
                            <i class="fas fa-download me-1"></i>Download
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="timetable-{{ $timetable->id }}" class="table table-bordered table-hover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; overflow: hidden;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Time</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Day</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Course</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Lesson</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Room</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Teacher</th>
                                </tr>
                            </thead>
                            <tbody style="background: white;">
                                @php
                                    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                                @endphp
                                @foreach($days as $dayNum => $dayName)
                                    @php
                                        $daySessions = $timetable->sessions->where('day_of_week', $dayNum)->sortBy('start_time');
                                    @endphp
                                    @if(!$daySessions->isEmpty())
                                        @foreach($daySessions as $session)
                                        <tr style="transition: all 0.3s ease;">
                                            <td style="font-weight: 500;">{{ $session->start_time ?? '' }} - {{ $session->end_time ?? '' }}</td>
                                            <td style="font-weight: 500;">{{ $dayName }}</td>
                                            <td>
                                                @foreach($session->courses as $course)
                                                    <span class="badge bg-info text-white">{{ $course->course_name }}</span>{{ !$loop->last ? ' ' : '' }}
                                                @endforeach
                                            </td>
                                            <td>{{ $session->lesson->title ?? 'N/A' }}</td>
                                            <td>{{ $session->classroom ?? '' }}</td>
                                            <td>
                                                @if($session->lesson->teacher)
                                                    <span class="badge bg-success text-white">{{ $session->lesson->teacher->teacher_name }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">TBA</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<script>
function downloadTimetable(timetableId) {
    const table = document.getElementById('timetable-' + timetableId);
    if (!table) return;

    const tableClone = table.cloneNode(true);
    
    // Create a clean HTML for the timetable
    const html = `
        <html>
        <head>
            <title>Timetable</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                th { background-color: #667eea; color: white; }
                tr:nth-child(even) { background-color: #f9f9f9; }
            </style>
        </head>
        <body>
            <h2>Teaching Timetable</h2>
            ${tableClone.outerHTML}
        </body>
        </html>
    `;

    const blob = new Blob([html], { type: 'text/html' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'timetable.html';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}
</script>
@endsection
