{{-- filepath: resources/views/teacher/dashboard.blade.php --}}
@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Welcome, {{ Auth::guard('teacher')->user()->teacher_name ?? 'Teacher' }}!</h1>
    <div class="row g-4">
        <!-- Marks Card -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-check fs-1 text-success"></i>
                    <h5 class="card-title mt-2">Marks</h5>
                    <p class="card-text">Insert or view student marks.</p>
                    <a href="{{ route('teacher.marks.index') }}" class="btn btn-outline-success btn-sm">Go to Marks</a>
                </div>
            </div>
        </div>
        <!-- Exams Card -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data fs-1 text-primary"></i>
                    <h5 class="card-title mt-2">Exams</h5>
                    <p class="card-text">View and manage exams.</p>
                    <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-primary btn-sm">Go to Exams</a>
                </div>
            </div>
        </div>
        <!-- Resources Card -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-upload fs-1 text-secondary"></i>
                    <h5 class="card-title mt-2">Study Resources</h5>
                    <p class="card-text">Upload YouTube links or documents for students.</p>
                    <a href="{{ route('teacher.resources.index') }}" class="btn btn-outline-secondary btn-sm">Upload Resource</a>
                </div>
            </div>
        </div>
        <!-- Live Classes Card -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-camera-video fs-1 text-danger"></i>
                    <h5 class="card-title mt-2">Live Classes</h5>
                    <p class="card-text">Manage and start your assigned live classes.</p>
                    <a href="{{ route('teacher.live-classes.index') }}" class="btn btn-outline-danger btn-sm">Manage Classes</a>
                </div>
            </div>
        </div>
        <!-- Salary Card -->
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-cash-coin fs-1 text-warning" style="cursor:pointer;" id="showSalaryIcon" title="Show Salary"></i>
                    <h5 class="card-title mt-2">Salary</h5>
                    <p class="card-text">
                        <span id="salaryAmount" style="display:none;">
                            {{ Auth::guard('teacher')->user()->salary ?? 'N/A' }}
                        </span>
                        <span id="salaryHidden">******</span>
                    </p>
                    <button class="btn btn-outline-warning btn-sm mb-2" onclick="requestAdvance()">Request Advance</button>
                </div>
            </div>
        </div>
        <!-- Notice Board Card -->
     
    </div>
</div>

<script>
document.getElementById('showSalaryIcon').addEventListener('click', function() {
    document.getElementById('salaryAmount').style.display = 'inline';
    document.getElementById('salaryHidden').style.display = 'none';
});
function requestAdvance() {
    alert('Advance request feature coming soon!');
    // You can replace this with a modal or redirect to an advance request form.
}
</script>
@endsection