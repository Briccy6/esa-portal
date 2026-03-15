@extends('staff.partials.sidebar_dos')

@section('title', 'Student Reports')

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-file-bar-graph me-2"></i>
            Student Reports
        </h2>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-primary" onclick="exportToCSV()">
                <i class="fas fa-file-csv"></i> Export CSV
            </button>
            <button type="button" class="btn btn-outline-success" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
            <a href="{{ route('dos.students.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Students
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($totalStudents) }}</h4>
                            <p class="mb-0">Total Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($activeStudents) }}</h4>
                            <p class="mb-0">Active Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-check fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ number_format($inactiveStudents) }}</h4>
                            <p class="mb-0">Inactive Students</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-times fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Students by Branch</h5>
                </div>
                <div class="card-body">
                    @if($studentsByBranch->count() > 0)
                        <canvas id="branchChart" width="400" height="200"></canvas>
                        <div class="mt-3">
                            @foreach($studentsByBranch as $branch)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $branch->name }}</span>
                                    <span class="badge bg-primary">{{ $branch->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No data available</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Students by Course</h5>
                </div>
                <div class="card-body">
                    @if($studentsByCourse->count() > 0)
                        <canvas id="courseChart" width="400" height="200"></canvas>
                        <div class="mt-3">
                            @foreach($studentsByCourse as $course)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span>{{ $course->course_name }}</span>
                                    <span class="badge bg-info">{{ $course->count }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No data available</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Detailed Statistics</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Count</th>
                            <th>Percentage</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>Total Students</strong></td>
                            <td>{{ $totalStudents }}</td>
                            <td>100%</td>
                            <td><span class="badge bg-primary">All</span></td>
                        </tr>
                        <tr>
                            <td><strong>Active Students</strong></td>
                            <td>{{ $activeStudents }}</td>
                            <td>{{ $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0 }}%</td>
                            <td><span class="badge bg-success">Active</span></td>
                        </tr>
                        <tr>
                            <td><strong>Inactive Students</strong></td>
                            <td>{{ $inactiveStudents }}</td>
                            <td>{{ $totalStudents > 0 ? round(($inactiveStudents / $totalStudents) * 100, 1) : 0 }}%</td>
                            <td><span class="badge bg-warning">Inactive</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Branches</strong></td>
                            <td>{{ $studentsByBranch->count() }}</td>
                            <td>-</td>
                            <td><span class="badge bg-info">Branches</span></td>
                        </tr>
                        <tr>
                            <td><strong>Total Courses</strong></td>
                            <td>{{ $studentsByCourse->count() }}</td>
                            <td>-</td>
                            <td><span class="badge bg-secondary">Courses</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Report Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Key Insights</h6>
                            <ul>
                                <li>Total enrollment: <strong>{{ $totalStudents }}</strong> students</li>
                                <li>Active participation rate: <strong>{{ $totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0 }}%</strong></li>
                                <li>Students distributed across <strong>{{ $studentsByBranch->count() }}</strong> branches</li>
                                <li>Students enrolled in <strong>{{ $studentsByCourse->count() }}</strong> different courses</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Top Performing Branches</h6>
                            @if($studentsByBranch->count() > 0)
                                <ol>
                                    @foreach($studentsByBranch->sortByDesc('count')->take(3) as $branch)
                                        <li>{{ $branch->name }} ({{ $branch->count }} students)</li>
                                    @endforeach
                                </ol>
                            @else
                                <p class="text-muted">No branch data available</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Report generated on {{ now()->format('M d, Y H:i:s') }} | Data includes all registered students
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function exportToCSV() {
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add header
    csvContent += "Category,Count,Percentage,Status\n";
    
    // Add data
    csvContent += `Total Students,{{$totalStudents}},100%,All\n`;
    csvContent += `Active Students,{{$activeStudents}},{{$totalStudents > 0 ? round(($activeStudents / $totalStudents) * 100, 1) : 0}}%,Active\n`;
    csvContent += `Inactive Students,{{$inactiveStudents}},{{$totalStudents > 0 ? round(($inactiveStudents / $totalStudents) * 100, 1) : 0}}%,Inactive\n`;
    csvContent += `Total Branches,{{$studentsByBranch->count()}},-,Branches\n`;
    csvContent += `Total Courses,{{$studentsByCourse->count()}},-,Courses\n`;
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "student_report_{{ now()->format('Y-m-d') }}.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Simple charts using CSS (you can replace with Chart.js if needed)
document.addEventListener('DOMContentLoaded', function() {
    // Branch chart
    const branchCanvas = document.getElementById('branchChart');
    if (branchCanvas && {{ $studentsByBranch->count() }} > 0) {
        const ctx = branchCanvas.getContext('2d');
        // Simple bar chart implementation
        const data = @json($studentsByBranch->pluck('count'));
        const labels = @json($studentsByBranch->pluck('name'));
        
        // This is a placeholder - you can implement Chart.js here
        ctx.fillStyle = '#007bff';
        const maxValue = Math.max(...data);
        const barWidth = 50;
        const spacing = 20;
        
        data.forEach((value, index) => {
            const barHeight = (value / maxValue) * 150;
            const x = index * (barWidth + spacing) + 20;
            const y = 180 - barHeight;
            
            ctx.fillRect(x, y, barWidth, barHeight);
            ctx.fillStyle = '#333';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(value, x + barWidth/2, y - 5);
            ctx.fillText(labels[index].substring(0, 10), x + barWidth/2, 195);
            ctx.fillStyle = '#007bff';
        });
    }
    
    // Course chart
    const courseCanvas = document.getElementById('courseChart');
    if (courseCanvas && {{ $studentsByCourse->count() }} > 0) {
        const ctx = courseCanvas.getContext('2d');
        // Simple bar chart implementation
        const data = @json($studentsByCourse->pluck('count'));
        const labels = @json($studentsByCourse->pluck('course_name'));
        
        const maxValue = Math.max(...data);
        const barWidth = 50;
        const spacing = 20;
        
        data.forEach((value, index) => {
            const barHeight = (value / maxValue) * 150;
            const x = index * (barWidth + spacing) + 20;
            const y = 180 - barHeight;
            
            ctx.fillStyle = '#17a2b8';
            ctx.fillRect(x, y, barWidth, barHeight);
            ctx.fillStyle = '#333';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(value, x + barWidth/2, y - 5);
            ctx.fillText(labels[index].substring(0, 10), x + barWidth/2, 195);
            ctx.fillStyle = '#17a2b8';
        });
    }
});
</script>

<style media="print">
.btn-group {
    display: none !important;
}
.card {
    break-inside: avoid;
    page-break-inside: avoid;
}
</style>
@endsection
