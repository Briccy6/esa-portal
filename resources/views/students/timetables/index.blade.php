@extends('students.app')

@section('title', 'My Timetables')

@section('content')
<style>
    .student-timetable-page {
        --timetable-navy: #153b63;
        --timetable-blue: #245b91;
        --timetable-line: #d6dee8;
        --timetable-soft: #f3f6f9;
        --timetable-ink: #17212b;
    }

    .timetable-header {
        background: #153b63;
        color: white;
        padding: 1.35rem 1.5rem;
        border-radius: 0.5rem;
        margin-bottom: 2rem;
        border-left: 6px solid #e5b84a;
    }

    .timetable-header h2 {
        font-size: 1.75rem;
        font-weight: 800;
        letter-spacing: -0.02em;
    }

    .timetable-card {
        border: 1px solid var(--timetable-line);
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(27, 54, 79, 0.08);
        margin-bottom: 2rem;
    }

    .timetable-card .card-header {
        background: var(--timetable-navy) !important;
        border: 0;
        padding: 1rem 1.25rem;
    }

    .timetable-card .card-header h5 {
        font-size: 1.08rem;
        font-weight: 800;
    }

    .timetable-card .card-header small {
        color: #dfe8f2 !important;
        line-height: 1.7;
    }

    .timetable-table {
        background: white;
        border-radius: 0;
        overflow: hidden;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .timetable-table thead th {
        padding: 0.85rem 0.75rem;
        font-weight: 800;
        font-size: 0.78rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        border: 0;
        border-right: 1px solid rgba(255,255,255,0.16);
        white-space: nowrap;
        background: var(--timetable-blue);
        color: white !important;
        position: sticky;
        top: 0;
        z-index: 2;
    }

    .timetable-table thead {
        background: var(--timetable-blue);
    }

    .timetable-table tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border: 0;
        border-right: 1px solid var(--timetable-line);
        border-bottom: 1px solid var(--timetable-line);
        background: #ffffff;
        color: var(--timetable-ink);
        font-size: 0.91rem;
    }

    .timetable-table tbody tr:nth-child(even) {
        background: var(--timetable-soft);
    }

    .timetable-table tbody tr:hover {
        background: #edf4fa;
    }

    .timetable-time {
        min-width: 112px;
        color: #153b63;
        font-variant-numeric: tabular-nums;
    }

    .timetable-day {
        min-width: 96px;
    }

    .timetable-table .badge {
        border-radius: 0.3rem;
        padding: 0.38rem 0.48rem;
        font-weight: 800;
        font-size: 0.75rem;
    }

    .timetable-table .bg-primary {
        background: #dceaf6 !important;
        color: #153b63 !important;
        border: 1px solid #bfd2e4;
    }

    .timetable-table .bg-success {
        background: #dff1e7 !important;
        color: #155b38 !important;
        border: 1px solid #b9d8c7;
    }

    .timetable-table .bg-warning {
        background: #fff1c7 !important;
        color: #795700 !important;
        border: 1px solid #ead58c;
    }

    .timetable-table .bg-danger {
        background: #fde4e4 !important;
        color: #8b2323 !important;
        border: 1px solid #efc2c2;
    }

    .timetable-table .bg-info {
        background: #dcecf7 !important;
        color: #1a4f75 !important;
        border: 1px solid #bcd4e6;
    }

    .timetable-table .bg-light {
        background: #eef1f4 !important;
        color: #354250 !important;
        border: 1px solid #d4dae1;
    }

    .download-btn {
        background: #e5b84a;
        border: 1px solid #e5b84a;
        color: #17212b;
        padding: 0.65rem 1.1rem;
        border-radius: 0.35rem;
        font-weight: 800;
        transition: background 0.15s ease, border-color 0.15s ease;
    }

    .download-btn:hover {
        background: #f0cb67;
        border-color: #f0cb67;
        color: #17212b;
    }

    @media (max-width: 768px) {
        .timetable-header {
            padding: 1.5rem;
        }

        .timetable-table thead th,
        .timetable-table tbody td {
            padding: 0.75rem 0.5rem;
            font-size: 0.875rem;
        }

        .timetable-table {
            font-size: 0.875rem;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
    }

    @media (max-width: 576px) {
        .timetable-table thead th,
        .timetable-table tbody td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }

        .timetable-header h2 {
            font-size: 1.5rem;
        }

        .download-btn {
            padding: 0.4rem 1rem;
            font-size: 0.875rem;
        }
    }

    @media print {
        .timetable-header,
        .download-btn,
        .navbar,
        .sidebar,
        footer {
            display: none !important;
        }

        .timetable-card {
            box-shadow: none;
            border: 1px solid #ddd;
        }

        .timetable-table {
            box-shadow: none;
        }
    }
</style>

<div class="container py-4 student-timetable-page">
    <!-- Header -->
    <div class="timetable-header">
        <h2 class="mb-1"><i class="fas fa-calendar-alt me-2"></i>My Timetables</h2>
        <p class="mb-0 opacity-75">View your class schedule for the current academic year</p>
    </div>

    @if($timetables->isEmpty())
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            No active timetables were found for your branch and academic year.
        </div>
    @else
        @foreach($timetables as $timetable)
            <div class="card timetable-card student-timetable-page">
                <div class="card-header bg-primary text-white d-flex flex-wrap justify-content-between align-items-center">
                    <div class="mb-2 mb-md-0">
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
                        <table class="table table-bordered table-hover timetable-table" id="timetable-{{ $timetable->id }}">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Day</th>
                                    <th>Course</th>
                                    <th>Lesson/Subject</th>
                                    <th>Room/Location</th>
                                    <th>Teacher</th>
                                    <th>Session type / eligibility</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 0 => 'Sunday'];
                                @endphp
                                @foreach($days as $dayNum => $dayName)
                                    @php
                                        $daySessions = $timetable->sessions->where('day_of_week', $dayNum)->sortBy('start_time');
                                    @endphp
                                    @if(!$daySessions->isEmpty())
                                        @foreach($daySessions as $session)
                                        <tr>
                                            <td class="timetable-time" style="font-weight: 800;">
                                                @if($session->start_time && $session->end_time)
                                                    {{ \Carbon\Carbon::parse($session->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('H:i') }}
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                            <td class="timetable-day" style="font-weight: 800;">{{ $dayName }}</td>
                                            <td>
                                                @foreach($session->courses as $course)
                                                    <span class="badge bg-primary">{{ $course->abbreviation ?? $course->course_name }}</span>{{ !$loop->last ? ' ' : '' }}
                                                @endforeach
                                            </td>
                                            <td style="font-weight: 500;">{{ $session->lesson->title ?? 'N/A' }}</td>
                                            <td>{{ $session->classroom ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $displayTeacher = $session->lesson->branch_teacher ?? null;
                                                @endphp
                                                @if($displayTeacher)
                                                    <span class="badge bg-success">{{ $displayTeacher->teacher_name }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">TBA</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->session_type === 'test')
                                                    @if($session->test_eligibility['eligible'] ?? false)
                                                        <span class="badge bg-success">Test · eligible</span>
                                                    @else
                                                        <span class="badge bg-danger">Test · not eligible</span>
                                                        <small class="d-block mt-1 text-danger">
                                                            {{ $session->test_eligibility['reason'] ?? 'Fee payment is below the required percentage.' }}
                                                            Paid {{ number_format($session->test_eligibility['paid'] ?? 0, 1) }}%.
                                                            Required {{ number_format($session->test_eligibility['minimum'] ?? 0, 1) }}%.
                                                        </small>
                                                        <a class="small" href="{{ route('student.payment-schedule') }}">View fees</a>
                                                    @endif
                                                @elseif($session->session_type === 'self_study')
                                                    <span class="badge bg-info text-dark">Self study</span>
                                                @else
                                                    <span class="badge bg-light text-dark">Class</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($session->notes)
                                                    <small class="text-muted">{{ $session->notes }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
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
    const card = table.closest('.timetable-card');
    const cardHeader = card ? card.querySelector('.card-header') : null;
    const timetableTitle = cardHeader ? cardHeader.querySelector('h5')?.textContent : 'Timetable';
    const branchInfo = cardHeader ? cardHeader.querySelector('small')?.textContent : '';

    // Create a clean HTML for the timetable
    const html = `
        <html>
        <head>
            <title>${timetableTitle}</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    padding: 30px; 
                    margin: 0;
                    background: #f8f9fa;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    padding: 20px;
                    background: #153b63;
                    color: white;
                    border-left: 6px solid #e5b84a;
                }
                .header h1 {
                    margin: 0 0 10px 0;
                    font-size: 28px;
                }
                .header p {
                    margin: 0;
                    font-size: 14px;
                    opacity: 0.9;
                }
                table { 
                    width: 100%; 
                    border-collapse: collapse; 
                    margin-top: 20px; 
                    background: white;
                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                    border-radius: 8px;
                    overflow: hidden;
                }
                th { 
                    background: #245b91;
                    color: white; 
                    padding: 15px 12px;
                    text-align: left;
                    font-weight: 700;
                    font-size: 14px;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                    border: none;
                }
                td { 
                    border-bottom: 1px solid #e2e8f0;
                    padding: 12px;
                    text-align: left;
                    background: white;
                }
                tr:nth-child(even) { background-color: #f8fafc; }
                tr:hover { background-color: #f0f9ff; }
                .badge {
                    display: inline-block;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 12px;
                    font-weight: 600;
                    margin-right: 4px;
                }
                .badge-primary { background: #dbeafe; color: #1e40af; }
                .badge-success { background: #dcfce7; color: #166534; }
                .badge-warning { background: #fef3c7; color: #92400e; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>${timetableTitle}</h1>
                <p>${branchInfo}</p>
            </div>
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
