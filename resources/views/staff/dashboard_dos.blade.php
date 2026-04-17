@extends('dos.layout')
@section('content')
@if(session('meet_link'))
    <div class="alert alert-success">
        Google Meet Link: <a href="{{ session('meet_link') }}" target="_blank">{{ session('meet_link') }}</a>
    </div>
@endif

<main class="flex-1 p-8 bg-indigo-50 min-h-screen">
    <h1 class="text-3xl font-bold text-indigo-900 mb-4">Welcome, DOS</h1>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Student Attendance (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Studied Hours (Last 7 Days)</div>
                <div class="card-body">
                    <canvas id="hoursChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Attendance Data
    const attendanceLabels = @json($attendanceStats->pluck('date'));
    const presentData = @json($attendanceStats->pluck('present'));
    const absentData = @json($attendanceStats->pluck('absent'));

    new Chart(document.getElementById('attendanceChart'), {
        type: 'bar',
        data: {
            labels: attendanceLabels,
            datasets: [
                {
                    label: 'Present',
                    data: presentData,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)'
                },
                {
                    label: 'Absent',
                    data: absentData,
                    backgroundColor: 'rgba(255, 99, 132, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } }
        }
    });

    // Studied Hours Data
    const hoursLabels = @json($studiedHours->pluck('date'));
    const hoursData = @json($studiedHours->pluck('hours'));

    new Chart(document.getElementById('hoursChart'), {
        type: 'line',
        data: {
            labels: hoursLabels,
            datasets: [{
                label: 'Studied Hours',
                data: hoursData,
                borderColor: 'rgba(40, 167, 69, 0.9)',
                backgroundColor: 'rgba(40, 167, 69, 0.3)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'top' } }
        }
    });
});
</script>
@endsection