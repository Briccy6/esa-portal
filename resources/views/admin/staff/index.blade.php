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

    @if(session('error'))
        <div class="alert alert-danger">
            <h5><i class="bi bi-exclamation-triangle me-2"></i>Deletion Error</h5>
            <p class="mb-2">{{ session('error') }}</p>
            
            @if(session('dependency_details'))
                <div class="mt-3">
                    <h6>Dependency Details:</h6>
                    <ul class="mb-0">
                        @foreach(session('dependency_details') as $table => $count)
                            <li><strong>{{ $table }}:</strong> {{ $count }} record(s)</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            @if(session('technical_error'))
                <div class="mt-2">
                    <small class="text-muted">
                        <strong>Technical Details:</strong> {{ session('technical_error') }}
                    </small>
                </div>
            @endif
        </div>
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
                            <label class="form-label fw-bold">Search & Select Student</label>
                            <input type="text" 
                                   id="studentSearchInput" 
                                   class="form-control" 
                                   placeholder="Type to search students..."
                                   autocomplete="off"
                                   list="studentList">
                            <datalist id="studentList">
                                @foreach($allStudents as $student)
                                    <option value="{{ $student->id }}" 
                                            data-reg-number="{{ $student->reg_number }}"
                                            data-name="{{ $student->full_name }}"
                                            data-email="{{ $student->email }}"
                                            data-branch="{{ $student->branch->name ?? 'N/A' }}"
                                            data-course="{{ $student->course->abbreviation ?? $student->course->course_name ?? 'N/A' }}"
                                            data-nesa-registered="{{ $student->nesa_registered ? 'true' : 'false' }}"
                                            data-search-text="{{ strtolower($student->full_name . ' ' . $student->reg_number . ' ' . ($student->branch->name ?? '') . ' ' . ($student->course->abbreviation ?? $student->course->course_name ?? '')) }}">
                                        {{ $student->reg_number }} - {{ $student->full_name }} {{ $student->nesa_registered ? '(✓ Registered)' : '' }}
                                    </option>
                                @endforeach
                            </datalist>
                            <input type="hidden" name="student_id" id="studentSelect" value="">
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
    const studentSearchInput = document.getElementById('studentSearchInput');
    const studentSelect = document.getElementById('studentSelect');
    const studentInfo = document.getElementById('studentInfo');
    const studentStatus = document.getElementById('studentStatus');
    const dataList = document.getElementById('studentList');
    const allOptions = Array.from(dataList.options);
    
    // Debug: Log available students
    console.log('Total students loaded:', allOptions.length);
    console.log('Datalist options:', allOptions);
    
    // Show student info function
    function showStudentInfo() {
        // Get the selected option by finding the one with matching value
        let selectedOption = null;
        for (let i = 0; i < allOptions.length; i++) {
            if (allOptions[i].value === studentSelect.value) {
                selectedOption = allOptions[i];
                break;
            }
        }
        
        // Only show info if we have both a value and a valid option
        if (studentSelect.value && selectedOption && studentSelect.value !== '') {
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
            form.action = form.action.replace(':studentId', studentSelect.value);
            console.log('Form action updated to:', form.action);
            console.log('Student selected:', selectedOption.dataset.name, 'ID:', studentSelect.value);
        } else {
            hideStudentInfo();
        }
    }
    
    // Hide student info function
    function hideStudentInfo() {
        studentInfo.classList.add('d-none');
        studentStatus.className = 'badge bg-warning';
        studentStatus.textContent = 'Not Registered';
    }
    
    // Event listeners
    studentSearchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        console.log('Searching for:', searchTerm);
        
        // Find matching option
        let matchedOption = null;
        for (let i = 0; i < allOptions.length; i++) {
            const option = allOptions[i];
            const searchText = option.dataset.searchText || '';
            const isNesaRegistered = option.dataset.nesaRegistered === 'true';
            
            // Only show non-NESA registered students in search results
            if (searchText.includes(searchTerm) && !isNesaRegistered) {
                matchedOption = option;
                break;
            }
        }
        
        // Update hidden input and show info if match found
        if (matchedOption) {
            studentSelect.value = matchedOption.value;
            showStudentInfo();
            console.log('Match found:', matchedOption.dataset.name);
        } else {
            studentSelect.value = '';
            hideStudentInfo();
        }
    });
    
    // Handle selection from datalist - use multiple events for better compatibility
    studentSearchInput.addEventListener('change', function() {
        console.log('Input changed, value:', this.value);
        showStudentInfo();
    });
    
    // Also handle blur event for when user clicks away
    studentSearchInput.addEventListener('blur', function() {
        console.log('Input blurred, value:', this.value);
        setTimeout(() => showStudentInfo(), 100); // Small delay to ensure datalist selection is processed
    });
    
    // Handle keyup for Enter key
    studentSearchInput.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') {
            console.log('Enter pressed, showing student info');
            showStudentInfo();
        }
    });
    
    // Initialize
    console.log('Searchable input initialized');
});
</script>
@endsection

<style>
/* Searchable Select Styles */
.searchable-select-container {
    position: relative;
}

.searchable-select-container .select-wrapper {
    position: relative;
}

.searchable-select-container .select-arrow {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    pointer-events: none;
    color: #6c757d;
    z-index: 1;
}

.searchable-select-container select {
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    padding-right: 35px;
}

.searchable-select-container select:focus + .select-arrow {
    color: #0d6efd;
}
</style>
