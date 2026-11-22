@extends('accountant.partials.theme')

@section('title', 'Timetables')

@section('content')
<style>
    .timetable-header {
        background: #1e40af;
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .timetable-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .timetable-header p {
        opacity: 0.9;
        margin-bottom: 0;
        font-size: 0.95rem;
    }

    .timetable-table {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .timetable-table thead {
        background: #1e40af;
        color: white;
    }

    .timetable-table th {
        padding: 0.875rem 1rem;
        font-weight: 600;
        border: none;
        color: white !important;
        font-size: 0.875rem;
    }

    .timetable-table td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
    }

    .timetable-table tbody tr:hover {
        background: #f8fafc;
    }

    .download-btn {
        background: #1e40af;
        border: none;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }

    .download-btn:hover {
        background: #1e3a8a;
        transform: translateY(-1px);
    }
</style>

<div class="container py-4">
    <!-- Header -->
    <div class="timetable-header">
        <h2 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>Timetables</h2>
        <p class="mb-0 opacity-75">View timetables for your assigned branches</p>
    </div>

    @if($timetables->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No active timetables were found for your branches.
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
                        <br>
                        <small class="opacity-75">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Valid from: {{ $timetable->valid_from ? \Carbon\Carbon::parse($timetable->valid_from)->format('d M Y') : 'N/A' }}
                            @if($timetable->valid_until)
                                to {{ \Carbon\Carbon::parse($timetable->valid_until)->format('d M Y') }}
                            @endif
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
                        <table id="timetable-{{ $timetable->id }}" class="table table-bordered table-hover" style="background: white;">
                            <thead>
                                <tr style="background: #1e40af; color: white;">
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Time</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Day</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Course</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Lesson</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Room</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Shift</th>
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
                                                <span class="badge bg-{{ $session->shift === 'morning' ? 'success' : ($session->shift === 'afternoon' ? 'warning' : 'secondary') }} text-white">
                                                    {{ ucfirst($session->shift ?? 'N/A') }}
                                                </span>
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
            <h2>Timetable</h2>
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
