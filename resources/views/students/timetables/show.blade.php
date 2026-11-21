@extends('students.app')

@section('title', 'Timetable Details')

@section('content')
<style>
    .timetable-header {
        background: #153b63;
        color: white;
        padding: 1.35rem 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        border-left: 6px solid #e5b84a;
    }

    .timetable-table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timetable-table thead {
        background: #245b91;
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
        background: #e5b84a;
        border: 1px solid #e5b84a;
        color: #17212b;
        padding: 0.55rem 1.1rem;
        border-radius: 0.35rem;
        font-weight: 800;
        transition: background 0.15s ease;
    }

    .download-btn:hover {
        background: #f0cb67;
        color: #17212b;
    }

    @media (max-width: 767.98px) {
        .timetable-header > div {
            flex-direction: column;
            align-items: stretch !important;
            gap: 0.75rem;
        }

        .timetable-header > div > div {
            width: 100%;
        }

        .timetable-header p {
            overflow-wrap: anywhere;
        }

        .timetable-header .btn,
        .timetable-header .download-btn {
            width: 100%;
            margin-right: 0 !important;
        }

        .timetable-header .download-btn {
            margin-bottom: 0;
        }

        .card-body {
            padding: 0.75rem;
        }

        .table-responsive {
            overflow: visible;
        }

        .timetable-table {
            display: block;
            width: 100%;
            overflow: visible;
            border: 0;
            box-shadow: none;
        }

        .timetable-table thead {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .timetable-table tbody {
            display: block;
        }

        .timetable-table tbody tr {
            display: block;
            margin-bottom: 0.75rem;
            overflow: hidden;
            border: 1px solid #d6dee8;
            border-radius: 0.45rem;
            background: #fff;
            box-shadow: 0 2px 6px rgba(27, 54, 79, 0.06);
        }

        .timetable-table tbody tr:last-child {
            margin-bottom: 0;
        }

        .timetable-table tbody td {
            display: grid;
            grid-template-columns: minmax(5.25rem, 34%) minmax(0, 1fr);
            gap: 0.65rem;
            align-items: start;
            width: 100%;
            min-width: 0;
            padding: 0.65rem 0.75rem;
            border: 0;
            border-bottom: 1px solid #d6dee8;
            text-align: left;
            font-size: 0.875rem;
        }

        .timetable-table.table-bordered tbody td {
            border: 0;
            border-bottom: 1px solid #d6dee8;
        }

        .timetable-table tbody td:last-child {
            border-bottom: 0;
        }

        .timetable-table tbody td::before {
            content: attr(data-label);
            color: #43566a;
            font-size: 0.68rem;
            font-weight: 800;
            letter-spacing: 0.035em;
            line-height: 1.4;
            text-transform: uppercase;
        }

        .timetable-cell-value {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .timetable-table .badge {
            max-width: 100%;
            white-space: normal;
            text-align: left;
        }
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
                <a href="{{ route('student.timetables.index') }}" class="btn btn-outline-light me-2">
                    <i class="fas fa-arrow-left me-1"></i>Back
                </a>
                <button onclick="downloadTimetable()" class="download-btn">
                    <i class="fas fa-download me-1"></i>Download
                </button>
            </div>
        </div>
    </div>

    @if($studentSessions->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No sessions found for your course in this timetable.
        </div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover timetable-table" id="timetable-detail">
                        <thead>
                            <tr>
                                <th>Time</th>
                                <th>Day</th>
                                <th>Course</th>
                                <th>Lesson</th>
                                <th>Room</th>
                                <th>Teacher</th>
                                <th>Session type / eligibility</th>
                            </tr>
                        </thead>
                        <tbody style="background: white;">
                            @php
                                $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                            @endphp
                            @foreach($days as $dayNum => $dayName)
                                @php
                                    $daySessions = $studentSessions->where('day_of_week', $dayNum)->sortBy('start_time');
                                @endphp
                                @if(!$daySessions->isEmpty())
                                    @foreach($daySessions as $session)
                                    <tr style="transition: all 0.3s ease;">
                                        <td data-label="Time" style="font-weight: 800;">
                                            <span class="timetable-cell-value">{{ $session->start_time ?? '' }} - {{ $session->end_time ?? '' }}</span>
                                        </td>
                                        <td data-label="Day" style="font-weight: 800;">
                                            <span class="timetable-cell-value">{{ $dayName }}</span>
                                        </td>
                                        <td data-label="Course">
                                            <span class="timetable-cell-value">
                                                @foreach($session->courses as $course)
                                                    <span class="badge bg-info text-white">{{ $course->course_name }}</span>{{ !$loop->last ? ' ' : '' }}
                                                @endforeach
                                            </span>
                                        </td>
                                        <td data-label="Lesson">
                                            <span class="timetable-cell-value">{{ $session->lesson->title ?? 'N/A' }}</span>
                                        </td>
                                        <td data-label="Room">
                                            <span class="timetable-cell-value">{{ $session->classroom ?? '' }}</span>
                                        </td>
                                        <td data-label="Teacher">
                                            <span class="timetable-cell-value">
                                                @if($session->lesson->branch_teacher)
                                                    <span class="badge bg-success text-white">{{ $session->lesson->branch_teacher->teacher_name }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">TBA</span>
                                                @endif
                                            </span>
                                        </td>
                                        <td data-label="Session type / eligibility">
                                            <span class="timetable-cell-value">
                                                @if($session->session_type === 'test')
                                                    @if($session->test_eligibility['eligible'] ?? false)
                                                        <span class="badge bg-success">Eligible</span>
                                                    @else
                                                        <span class="badge bg-danger">Not eligible</span>
                                                        <small class="d-block text-danger">{{ $session->test_eligibility['reason'] ?? 'Fee payment is below the required percentage.' }} Paid {{ number_format($session->test_eligibility['paid'] ?? 0, 1) }}%. Required {{ number_format($session->test_eligibility['minimum'] ?? 0, 1) }}%.</small>
                                                        <a class="small" href="{{ route('student.payment-schedule') }}">View fees</a>
                                                    @endif
                                                @elseif($session->session_type === 'self_study')
                                                    <span class="badge bg-info text-dark">Self study</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Class</span>
                                                @endif
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
