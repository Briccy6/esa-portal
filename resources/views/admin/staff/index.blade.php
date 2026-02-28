@extends('admin.layout')

@section('title', 'Staff Members')

@section('content')
<div class="container">
    <h2>Staff Members 
        <a href="{{ route('admin.staff.create') }}" class="btn btn-primary float-end me-2">Add Staff</a>
        <button type="button" class="btn btn-success float-end me-2" data-bs-toggle="modal" data-bs-target="#nesaRegistrationModal">
            <i class="bi bi-award me-1"></i>Register NESA
        </button>
    </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Role</th>
                <th>Branch</th>
                <th>Photo</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($staffs as $staff)
            <tr>
                <td>{{ $staff->name }}</td>
                <td>{{ $staff->role }}</td>
                <td>
                    @if($staff->branches && $staff->branches->count())
                        {{ $staff->branches->pluck('name')->join(', ') }}
                    @else
                        N/A
                    @endif
                </td>
                <td>
                    @if($staff->photo)
                        <img src="data:image/jpeg;base64,{{ $staff->photo }}" alt="Photo" width="50" height="50">
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.staff.show', $staff->id) }}" class="btn btn-info btn-sm">View</a>
                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.staff.destroy', $staff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this staff?');">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
                    </form>
                    <form action="{{ route('admin.staff.reset_password', $staff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Reset password for this staff?');">
                        @csrf
                        <button class="btn btn-secondary btn-sm" type="submit">Reset Password</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- NESA Registration Modal -->
<div class="modal fade" id="nesaRegistrationModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="nesaRegistrationForm" method="POST" action="{{ route('admin.nesa.markAsRegistered', ['student' => ':studentId']) }}" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-award me-2"></i>NESA Registration
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Select Student</label>
                            <select name="student_id" id="studentSelect" class="form-select" required>
                                <option value="">Choose a student...</option>
                                @foreach($students as $student)
                                    @if(!$student->nesa_registered)
                                    <option value="{{ $student->id }}" 
                                            data-reg-number="{{ $student->reg_number }}"
                                            data-name="{{ $student->full_name }}"
                                            data-email="{{ $student->email }}"
                                            data-branch="{{ $student->branch->name ?? 'N/A' }}"
                                            data-course="{{ $student->course->abbreviation ?? $student->course->course_name ?? 'N/A' }}">
                                        {{ $student->reg_number }} - {{ $student->full_name }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Registration Status</label>
                            <div class="form-control bg-light">
                                <span id="studentStatus" class="badge bg-warning">Not Registered</span>
                            </div>
                        </div>
                    </div>

                    <div id="studentInfo" class="alert alert-info d-none">
                        <h6><i class="bi bi-person-fill me-2"></i>Student Information</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <strong>Name:</strong> <span id="infoName"></span><br>
                                <strong>Reg Number:</strong> <span id="infoRegNumber"></span><br>
                                <strong>Email:</strong> <span id="infoEmail"></span>
                            </div>
                            <div class="col-md-6">
                                <strong>Branch:</strong> <span id="infoBranch"></span><br>
                                <strong>Course:</strong> <span id="infoCourse"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="nesa_sdms_code" class="form-label fw-bold">NESA SDMS Code *</label>
                            <input type="text" name="nesa_sdms_code" id="nesa_sdms_code" class="form-control" required maxlength="50">
                        </div>
                        <div class="col-md-6">
                            <label for="nesa_reg_number" class="form-label fw-bold">NESA Registration Number *</label>
                            <input type="text" name="nesa_reg_number" id="nesa_reg_number" class="form-control" required maxlength="50">
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="nesa_document" class="form-label fw-bold">NESA Document</label>
                            <input type="file" name="nesa_document" id="nesa_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <div class="form-text">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max: 5MB)</div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="nesa_remarks" class="form-label fw-bold">Remarks</label>
                            <textarea name="nesa_remarks" id="nesa_remarks" class="form-control" rows="3" maxlength="500" placeholder="Any additional remarks..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Register for NESA
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const studentSelect = document.getElementById('studentSelect');
    const studentInfo = document.getElementById('studentInfo');
    const studentStatus = document.getElementById('studentStatus');
    
    studentSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (this.value) {
            // Show student information
            document.getElementById('infoName').textContent = selectedOption.dataset.name;
            document.getElementById('infoRegNumber').textContent = selectedOption.dataset.regNumber;
            document.getElementById('infoEmail').textContent = selectedOption.dataset.email;
            document.getElementById('infoBranch').textContent = selectedOption.dataset.branch;
            document.getElementById('infoCourse').textContent = selectedOption.dataset.course;
            
            studentInfo.classList.remove('d-none');
            studentStatus.className = 'badge bg-info';
            studentStatus.textContent = 'Ready for Registration';
            
            // Update form action
            const form = document.getElementById('nesaRegistrationForm');
            form.action = form.action.replace(':studentId', this.value);
        } else {
            studentInfo.classList.add('d-none');
            studentStatus.className = 'badge bg-warning';
            studentStatus.textContent = 'Not Registered';
        }
    });
});
</script>
@endsection
