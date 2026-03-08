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

<!-- Floating Message Icon -->
<a href="#" 
   class="btn btn-primary rounded-circle shadow"
   style="position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px; display: flex; align-items: center; justify-content: center; z-index: 1050;"
   title="Create Message"
   data-bs-toggle="modal" data-bs-target="#messageModal">
    <i class="bi bi-chat-dots fs-2"></i>
</a>

<!-- Message Modal -->
<div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('teacher.messages.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="messageModalLabel">New Message</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="recipient" class="form-label">To</label>
            <input type="text" class="form-control" id="recipient" name="recipient" required placeholder="Recipient (e.g. student name or ID)">
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" class="form-control" id="subject" name="subject" required>
          </div>
          <div class="mb-3">
            <label for="body" class="form-label">Message</label>
            <textarea class="form-control" id="body" name="body" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
      </div>
    </form>
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