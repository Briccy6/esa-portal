@extends('dos.layout')

@section('page_title', 'Overview')

@section('styles')
<style>
    .dos-dashboard {
        max-width: 1440px;
        margin: 0 auto;
    }

    .dashboard-heading {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-heading h1 {
        margin: 0;
        color: #172b4d;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 700;
        letter-spacing: -0.03em;
    }

    .dashboard-heading p {
        margin: 0.35rem 0 0;
        color: #64748b;
    }

    .dashboard-period {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.8rem;
        border: 1px solid #dbe3ee;
        border-radius: 0.65rem;
        background: #fff;
        color: #334155;
        font-size: 0.875rem;
        white-space: nowrap;
    }

    .dashboard-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-actions .btn {
        min-height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border-radius: 0.6rem;
        font-weight: 600;
    }

    .dashboard-actions .btn-primary {
        background: #2457a7;
    }

    .dashboard-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .dashboard-kpi {
        min-width: 0;
        padding: 1.15rem;
        border: 1px solid #e1e8f0;
        border-radius: 0.85rem;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .dashboard-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .dashboard-kpi-label {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .dashboard-kpi-value {
        display: block;
        margin-top: 0.65rem;
        color: #172b4d;
        font-size: clamp(1.45rem, 2vw, 1.9rem);
        font-weight: 700;
        line-height: 1.1;
    }

    .dashboard-kpi-note {
        display: block;
        margin-top: 0.4rem;
        color: #94a3b8;
        font-size: 0.78rem;
    }

    .dashboard-kpi-icon {
        display: inline-flex;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        border-radius: 0.7rem;
        background: #edf4ff;
        color: #2457a7;
        font-size: 1.1rem;
    }

    .dashboard-kpi-icon.is-green {
        background: #ecfdf3;
        color: #15803d;
    }

    .dashboard-kpi-icon.is-amber {
        background: #fff7e6;
        color: #b45309;
    }

    .dashboard-kpi-icon.is-red {
        background: #fff1f2;
        color: #be123c;
    }

    .dashboard-charts {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .dashboard-branches {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .dashboard-branch {
        min-width: 0;
        padding: 1.2rem;
        border: 1px solid #e1e8f0;
        border-top: 3px solid #2457a7;
        border-radius: 0.85rem;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .branch-heading {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .branch-heading h2 {
        margin: 0;
        color: #172b4d;
        font-size: 1.15rem;
        font-weight: 700;
    }

    .branch-heading p {
        margin: 0.25rem 0 0;
        color: #94a3b8;
        font-size: 0.82rem;
    }

    .branch-kpis {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    .branch-kpi {
        min-width: 0;
        padding: 0.85rem;
        border: 1px solid #edf1f6;
        border-radius: 0.7rem;
        background: #f8fafc;
    }

    .branch-kpi span {
        display: block;
        color: #64748b;
        font-size: 0.76rem;
        font-weight: 600;
    }

    .branch-kpi strong {
        display: block;
        margin-top: 0.35rem;
        color: #172b4d;
        font-size: 1.25rem;
        line-height: 1.1;
    }

    .branch-kpi small {
        display: block;
        margin-top: 0.3rem;
        color: #94a3b8;
        font-size: 0.72rem;
    }

    .branch-details {
        display: grid;
        grid-template-columns: minmax(220px, 0.85fr) minmax(280px, 1.2fr) minmax(200px, 0.85fr);
        gap: 0.85rem;
    }

    .branch-panel {
        min-width: 0;
        padding: 1rem;
        border: 1px solid #edf1f6;
        border-radius: 0.75rem;
    }

    .branch-panel h3 {
        margin: 0 0 0.8rem;
        color: #334155;
        font-size: 0.9rem;
        font-weight: 700;
    }

    .branch-gender-chart {
        position: relative;
        height: 220px;
    }

    .branch-table-wrap {
        overflow-x: auto;
    }

    .branch-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.82rem;
    }

    .branch-table th,
    .branch-table td {
        padding: 0.55rem 0.4rem;
        border-bottom: 1px solid #edf1f6;
        text-align: left;
    }

    .branch-table th {
        color: #64748b;
        font-size: 0.74rem;
        font-weight: 600;
    }

    .branch-table td:last-child,
    .branch-table th:last-child {
        text-align: right;
        white-space: nowrap;
    }

    .branch-teachers {
        display: grid;
        gap: 0.55rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .branch-teachers li {
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #edf1f6;
        color: #334155;
        font-size: 0.84rem;
    }

    .branch-empty {
        padding: 1rem 0.25rem;
        color: #94a3b8;
        font-size: 0.82rem;
        text-align: center;
    }

    .dashboard-chart {
        min-width: 0;
        margin: 0;
        border: 1px solid #e1e8f0;
        border-radius: 0.85rem;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .dashboard-chart-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1.1rem 1.2rem 0.85rem;
        border-bottom: 1px solid #edf1f6;
    }

    .dashboard-chart-header h2 {
        margin: 0;
        color: #172b4d;
        font-size: 1rem;
        font-weight: 700;
    }

    .dashboard-chart-header p {
        margin: 0.25rem 0 0;
        color: #94a3b8;
        font-size: 0.8rem;
    }

    .dashboard-chart-body {
        position: relative;
        height: 310px;
        padding: 1.1rem;
    }

    .dashboard-empty {
        display: flex;
        min-height: 230px;
        align-items: center;
        justify-content: center;
        padding: 1.5rem;
        color: #64748b;
        text-align: center;
    }

    .dashboard-empty i {
        display: block;
        margin-bottom: 0.65rem;
        color: #94a3b8;
        font-size: 1.5rem;
    }

    .spinner-border-sm {
        width: 0.75rem;
        height: 0.75rem;
        border-width: 0.15em;
    }

    @media (max-width: 1100px) {
        .dashboard-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .branch-kpis {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .branch-details {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .dashboard-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-charts {
            grid-template-columns: 1fr;
        }

        .branch-details {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .dashboard-kpis {
            gap: 0.7rem;
        }

        .dashboard-kpi {
            padding: 0.9rem;
        }

        .dashboard-kpi-icon {
            width: 34px;
            height: 34px;
            flex-basis: 34px;
            font-size: 1rem;
        }

        .dashboard-chart-header {
            padding: 1rem;
        }

        .dashboard-chart-body {
            height: 270px;
            padding: 0.75rem;
        }

        .dashboard-branch {
            padding: 0.9rem;
        }

        .branch-kpis {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.55rem;
        }

        .branch-kpi {
            padding: 0.7rem;
        }
    }
</style>
@endsection

@section('content')
@include('partials.academic-year-banner')

@if(session('meet_link'))
    <div class="alert alert-success" role="status">
        Google Meet link:
        <a href="{{ session('meet_link') }}" target="_blank" rel="noopener noreferrer">{{ session('meet_link') }}</a>
    </div>
@endif

@php
    $attendanceTotal = (int) $attendanceStats->sum('total');
    $presentTotal = (int) $attendanceStats->sum('present');
    $absentTotal = (int) $attendanceStats->sum('absent');
    $attendanceRate = $attendanceTotal > 0 ? round(($presentTotal / $attendanceTotal) * 100, 1) : null;
    $studiedHoursTotal = (float) $studiedHours->sum('hours');
@endphp

<main class="dos-dashboard">
    <header class="dashboard-heading">
        <div>
            <h1>Academic overview</h1>
            <p>Quick academic analytics for your assigned branches and recent activity.</p>
        </div>
        <div class="dashboard-period">
            <i class="bi bi-calendar3" aria-hidden="true"></i>
            <span>{{ $selectedAcademicYear->year ?? 'Current academic year' }}</span>
        </div>
    </header>

    <nav class="dashboard-actions" aria-label="Academic shortcuts">
        <a href="{{ route('dos.students.index') }}" class="btn btn-primary">
            <i class="bi bi-people" aria-hidden="true"></i>Students
        </a>
        <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-clipboard-check" aria-hidden="true"></i>Manage exams
        </a>
        <a href="{{ route('staff.lessons.create') }}" class="btn btn-outline-secondary">
            <i class="bi bi-journal-plus" aria-hidden="true"></i>Create lesson
        </a>
    </nav>

    <section class="dashboard-kpis" aria-label="Seven-day summary">
        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <div>
                    <span class="dashboard-kpi-label">Attendance rate</span>
                    <strong class="dashboard-kpi-value">{{ $attendanceRate !== null ? $attendanceRate . '%' : '—' }}</strong>
                </div>
                <span class="dashboard-kpi-icon"><i class="bi bi-percent" aria-hidden="true"></i></span>
            </div>
            <span class="dashboard-kpi-note">Across recorded attendance</span>
        </article>

        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <div>
                    <span class="dashboard-kpi-label">Present records</span>
                    <strong class="dashboard-kpi-value">{{ number_format($presentTotal) }}</strong>
                </div>
                <span class="dashboard-kpi-icon is-green"><i class="bi bi-person-check" aria-hidden="true"></i></span>
            </div>
            <span class="dashboard-kpi-note">Student attendance marked present</span>
        </article>

        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <div>
                    <span class="dashboard-kpi-label">Absent records</span>
                    <strong class="dashboard-kpi-value">{{ number_format($absentTotal) }}</strong>
                </div>
                <span class="dashboard-kpi-icon is-red"><i class="bi bi-person-x" aria-hidden="true"></i></span>
            </div>
            <span class="dashboard-kpi-note">Student attendance marked absent</span>
        </article>

        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <div>
                    <span class="dashboard-kpi-label">Teaching hours</span>
                    <strong class="dashboard-kpi-value">{{ number_format($studiedHoursTotal, 1) }}</strong>
                </div>
                <span class="dashboard-kpi-icon is-amber"><i class="bi bi-clock-history" aria-hidden="true"></i></span>
            </div>
            <span class="dashboard-kpi-note">Hours reported by teachers</span>
        </article>
    </section>

    <section class="dashboard-branches" aria-label="Branch analytics">
        @forelse($branchAnalytics as $analytics)
            <article class="dashboard-branch">
                <header class="branch-heading">
                    <div>
                        <h2>{{ $analytics['branch']->name }}</h2>
                        <p>{{ $selectedAcademicYear->year ?? 'All academic years' }} branch overview</p>
                    </div>
                </header>

                <div class="branch-kpis" aria-label="{{ $analytics['branch']->name }} summary">
                    <div class="branch-kpi">
                        <span>Students</span>
                        <strong>{{ number_format($analytics['total_students']) }}</strong>
                        <small>Enrolled in this academic year</small>
                    </div>
                    <div class="branch-kpi">
                        <span>Courses</span>
                        <strong>{{ number_format($analytics['courses']->count()) }}</strong>
                        <small>Assigned to this branch</small>
                    </div>
                    <div class="branch-kpi">
                        <span>Teachers</span>
                        <strong>{{ number_format($analytics['teachers']->count()) }}</strong>
                        <small>Assigned to this branch</small>
                    </div>
                    <div class="branch-kpi">
                        <span>Profiles complete</span>
                        <strong>{{ number_format($analytics['profile_completed']) }}</strong>
                        <small>{{ $analytics['profile_completed_rate'] !== null ? $analytics['profile_completed_rate'] . '% of students' : 'No students yet' }}</small>
                    </div>
                    <div class="branch-kpi">
                        <span>Email verified</span>
                        <strong>{{ number_format($analytics['verified_students']) }}</strong>
                        <small>{{ $analytics['verified_rate'] !== null ? $analytics['verified_rate'] . '% of students' : 'No students yet' }}</small>
                    </div>
                </div>

                <div class="branch-details">
                    <section class="branch-panel">
                        <h3>Students by gender</h3>
                        @if(array_sum($analytics['gender_counts']) > 0)
                            <div class="branch-gender-chart">
                                <canvas
                                    id="studentGenderChart{{ $analytics['branch']->id }}"
                                    role="img"
                                    aria-label="Student counts by gender for {{ $analytics['branch']->name }}"
                                ></canvas>
                            </div>
                        @else
                            <div class="branch-empty">No student gender data is available.</div>
                        @endif
                    </section>

                    <section class="branch-panel">
                        <h3>Students by course</h3>
                        @if($analytics['courses']->isNotEmpty())
                            <div class="branch-table-wrap">
                                <table class="branch-table">
                                    <thead>
                                        <tr><th>Course</th><th>Students</th></tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analytics['courses'] as $course)
                                            <tr>
                                                <td>{{ $course->course_name }}</td>
                                                <td>{{ number_format($course->student_count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="branch-empty">No courses are assigned to this branch.</div>
                        @endif
                    </section>

                    <section class="branch-panel">
                        <h3>Teachers</h3>
                        @if($analytics['teachers']->isNotEmpty())
                            <ul class="branch-teachers">
                                @foreach($analytics['teachers'] as $teacher)
                                    <li>{{ $teacher->teacher_name }}</li>
                                @endforeach
                            </ul>
                        @else
                            <div class="branch-empty">No teachers are assigned to this branch.</div>
                        @endif
                    </section>
                </div>
            </article>
        @empty
            <div class="dashboard-chart">
                <div class="dashboard-empty" role="status">No branches are assigned to your DOS account.</div>
            </div>
        @endforelse
    </section>

    <section class="dashboard-charts" aria-label="Academic activity charts">
        <article class="dashboard-chart">
            <header class="dashboard-chart-header">
                <div>
                    <h2>Student attendance</h2>
                    <p>Present and absent records by day</p>
                </div>
                <i class="bi bi-bar-chart-line text-primary" aria-hidden="true"></i>
            </header>
            <div class="dashboard-chart-body">
                @if($attendanceStats->isNotEmpty())
                    <canvas id="attendanceChart" role="img" aria-label="Student attendance chart for the last seven days"></canvas>
                @else
                    <div class="dashboard-empty">
                        <div><i class="bi bi-calendar2-x" aria-hidden="true"></i>No attendance records were found for this period.</div>
                    </div>
                @endif
            </div>
        </article>

        <article class="dashboard-chart">
            <header class="dashboard-chart-header">
                <div>
                    <h2>Teaching activity</h2>
                    <p>Hours reported by day</p>
                </div>
                <i class="bi bi-graph-up-arrow text-primary" aria-hidden="true"></i>
            </header>
            <div class="dashboard-chart-body">
                @if($studiedHours->isNotEmpty())
                    <canvas id="hoursChart" role="img" aria-label="Reported teaching hours for the last seven days"></canvas>
                @else
                    <div class="dashboard-empty">
                        <div><i class="bi bi-clock" aria-hidden="true"></i>No teaching activity was reported for this period.</div>
                    </div>
                @endif
            </div>
        </article>
    </section>

    <section class="dashboard-chart mt-4" aria-label="Live lesson hours tracking">
        <header class="dashboard-chart-header">
            <div>
                <h2>Total hours by lesson</h2>
                <p>Live tracking of teaching hours per lesson <span id="lastUpdated" class="text-muted small"></span></p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success rounded-pill d-flex align-items-center gap-1">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Live
                </span>
            </div>
        </header>
        <div class="dashboard-chart-body">
            <div class="branch-table-wrap">
                <table class="branch-table" id="lessonHoursTable">
                    <thead>
                        <tr>
                            <th>Lesson</th>
                            <th>Course</th>
                            <th>Teacher</th>
                            <th>Total Hours</th>
                            <th>Sessions</th>
                        </tr>
                    </thead>
                    <tbody id="lessonHoursBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Loading live data...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js" defer></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const attendanceCanvas = document.getElementById('attendanceChart');
    const lessonHoursUrl = "{{ route('dos.api.lesson-hours') }}";
    let lessonHoursInterval;

    // Live lesson hours tracking
    async function fetchLessonHours() {
        try {
            const response = await fetch(lessonHoursUrl);
            const data = await response.json();

            if (data.success) {
                updateLessonHoursTable(data.data);
                document.getElementById('lastUpdated').textContent = 'Updated: ' + new Date(data.last_updated).toLocaleTimeString();
            }
        } catch (error) {
            console.error('Failed to fetch lesson hours:', error);
        }
    }

    function updateLessonHoursTable(lessonHours) {
        const tbody = document.getElementById('lessonHoursBody');

        if (lessonHours.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No lesson hours data available yet.</td></tr>';
            return;
        }

        tbody.innerHTML = lessonHours.map(item => `
            <tr>
                <td>${item.lesson ? item.lesson.title : 'N/A'}</td>
                <td>${item.course ? item.course.course_name : 'N/A'}</td>
                <td>${item.teacher ? item.teacher.teacher_name : 'N/A'}</td>
                <td><strong>${parseFloat(item.total_hours).toFixed(1)} hrs</strong></td>
                <td>${item.sessions}</td>
            </tr>
        `).join('');
    }

    // Initial fetch
    fetchLessonHours();

    // Auto-refresh every 30 seconds
    lessonHoursInterval = setInterval(fetchLessonHours, 30000);

    const attendanceCanvas = document.getElementById('attendanceChart');
    if (attendanceCanvas) {
        new Chart(attendanceCanvas, {
            type: 'bar',
            data: {
                labels: @json($attendanceStats->pluck('date')->values()),
                datasets: [
                    {
                        label: 'Present',
                        data: @json($attendanceStats->pluck('present')->values()),
                        backgroundColor: '#2457a7',
                        borderRadius: 4
                    },
                    {
                        label: 'Absent',
                        data: @json($attendanceStats->pluck('absent')->values()),
                        backgroundColor: '#d97777',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
            }
        });
    }

    const hoursCanvas = document.getElementById('hoursChart');
    if (hoursCanvas) {
        new Chart(hoursCanvas, {
            type: 'line',
            data: {
                labels: @json($studiedHours->pluck('date')->values()),
                datasets: [{
                    label: 'Teaching hours',
                    data: @json($studiedHours->pluck('hours')->values()),
                    borderColor: '#16845b',
                    backgroundColor: '#d9f0e5',
                    pointBackgroundColor: '#16845b',
                    pointRadius: 4,
                    fill: true,
                    tension: 0.25
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

    @foreach($branchAnalytics as $analytics)
        const genderCanvas{{ $analytics['branch']->id }} = document.getElementById('studentGenderChart{{ $analytics['branch']->id }}');
        if (genderCanvas{{ $analytics['branch']->id }}) {
            new Chart(genderCanvas{{ $analytics['branch']->id }}, {
                type: 'doughnut',
                data: {
                    labels: @json(array_keys($analytics['gender_counts'])),
                    datasets: [{
                        data: @json(array_values($analytics['gender_counts'])),
                        backgroundColor: ['#2457a7', '#d97777', '#8b5cf6', '#cbd5e1'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }
    @endforeach
});
</script>
@endsection
