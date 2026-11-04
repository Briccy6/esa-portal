@extends('students.app')

@section('title', 'My Timetables')

@section('content')
<style>
    .timetable-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 1rem;
        margin-bottom: 2rem;
    }

    .timetable-card {
        border-radius: 1rem;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .timetable-table {
        background: white;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .timetable-table thead th {
        padding: 1.25rem 1rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        white-space: nowrap;
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        color: white !important;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .timetable-table thead {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
    }

    .timetable-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid #e2e8f0;
        background: #ffffff;
    }

    .timetable-table tbody tr:hover {
        background: #f0f9ff;
    }

    .download-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 0.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
    }

    .download-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
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

<div class="container py-4">
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
            <div class="card timetable-card">
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
                        <table class="table table-bordered table-hover" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; overflow: hidden;">
                            <thead>
                                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Time</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Day</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Course</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Lesson</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Room</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Teacher</th>
                                    <th style="color: white; border: 1px solid rgba(255,255,255,0.2);">Notes</th>
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
                                                @php
                                                    $displayTeacher = $session->lesson->branch_teacher ?? ($session->lesson->teacher ?? null);
                                                @endphp
                                                @if($displayTeacher)
                                                    <span class="badge bg-success text-white">{{ $displayTeacher->teacher_name }}</span>
                                                @else
                                                    <span class="badge bg-warning text-dark">TBA</span>
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
    const table = document.querySelector('.card .table');
    if (!table) return;

    const tableClone = table.cloneNode(true);
    const cardHeader = document.querySelector('.card-header');
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
                    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
                    color: white;
                    border-radius: 10px;
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
                    background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
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
                .badge-info { background: #e0f2fe; color: #0369a1; }
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
