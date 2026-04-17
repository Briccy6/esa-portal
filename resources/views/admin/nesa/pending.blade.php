@extends('admin.layout')

@section('title', 'Pending NESA Registration')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock mr-2"></i>
                        Pending NESA Registration
                        <span class="badge badge-light ml-2">{{ $students->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $students->total() }}</h3>
                                    <p class="mb-0">Pending Registration</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ App\Models\Student::nesaRegistered()->count() }}</h3>
                                    <p class="mb-0">Already Registered</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ App\Models\Student::count() }}</h3>
                                    <p class="mb-0">Total Students</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $students->total() > 0 ? round(($students->total() / App\Models\Student::count()) * 100, 1) : 0 }}%</h3>
                                    <p class="mb-0">Pending Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.nesa.index') }}" class="btn btn-primary">
                                <i class="fas fa-arrow-left mr-2"></i>Back to All Students
                            </a>
                            <a href="{{ route('admin.nesa.registered') }}" class="btn btn-success ml-2">
                                <i class="fas fa-check-circle mr-2"></i>View Registered ({{ App\Models\Student::nesaRegistered()->count() }})
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <button class="btn btn-info" onclick="showBulkRegisterModal()">
                                <i class="fas fa-users mr-2"></i>Bulk Register Selected
                            </button>
                        </div>
                    </div>

                    <!-- Quick Registration Guide -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle mr-2"></i>Quick Registration Guide</h6>
                                <ol class="mb-0">
                                    <li>Select students you want to register for NESA examination</li>
                                    <li>Click "Bulk Register Selected" or use individual "Register" buttons</li>
                                    <li>Enter NESA SDMS code and registration numbers</li>
                                    <li>Upload supporting documents from NESA website</li>
                                    <li>Students will be marked as completed for national examination</li>
                                </ol>
                            </div>
                        </div>
                    </div>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                    </th>
                                    <th>Reg Number</th>
                                    <th>Full Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>Academic Year</th>
                                    <th>Documents</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox">
                                        </td>
                                        <td><strong>{{ $student->reg_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->phone ?? 'N/A' }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td>{{ $student->academicYear->year ?? 'N/A' }}</td>
                                        <td>
                                            @if($student->senior6_document)
                                                <span class="badge badge-success mr-1" title="Senior 6 Document">S6</span>
                                            @endif
                                            @if($student->senior3_document)
                                                <span class="badge badge-info mr-1" title="Senior 3 Document">S3</span>
                                            @endif
                                            @if($student->other_supporting_document)
                                                <span class="badge badge-secondary" title="Other Document">Other</span>
                                            @endif
                                            @if(!$student->senior6_document && !$student->senior3_document && !$student->other_supporting_document)
                                                <span class="text-muted">No docs</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-success" onclick="showQuickRegisterModal({{ $student->id }}, '{{ $student->full_name }}', '{{ $student->reg_number }}')" title="Quick Register">
                                                <i class="fas fa-rocket"></i>
                                            </button>
                                            <a href="{{ route('admin.nesa.edit', $student) }}" class="btn btn-sm btn-primary" title="Edit Details">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle mr-2"></i>
                                                Great! All students are registered for NESA examination.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                        </div>
                        <div>
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Register Modal -->
<div class="modal fade" id="bulkRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-users mr-2"></i>Bulk NESA Registration
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.nesa.bulk-register') }}" id="bulkRegisterForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span id="selectedCount">0</span> students selected for bulk registration.
                    </div>
                    
                    <div class="form-group">
                        <label for="bulk_sdms_code_modal" class="form-label">NESA SDMS Code *</label>
                        <input type="text" name="bulk_sdms_code" id="bulk_sdms_code_modal" class="form-control" required>
                        <small class="form-text text-muted">This SDMS code will be applied to all selected students</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="bulk_reg_number_prefix_modal" class="form-label">Registration Number Prefix *</label>
                        <input type="text" name="bulk_reg_number_prefix" id="bulk_reg_number_prefix_modal" class="form-control" required>
                        <small class="form-text text-muted">Example: NESA2025- (will be suffixed with 0001, 0002, etc.)</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="bulk_remarks_modal" class="form-label">Remarks</label>
                        <textarea name="bulk_remarks" id="bulk_remarks_modal" class="form-control" rows="2"></textarea>
                        <small class="form-text text-muted">These remarks will be applied to all selected students</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success" onclick="return confirm('This will register all selected students for NESA examination. Continue?')">
                        <i class="fas fa-check mr-2"></i>Register All Selected
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Quick Register Modal -->
<div class="modal fade" id="quickRegisterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-rocket mr-2"></i>Quick NESA Registration
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="POST" id="quickRegisterForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i>
                        Registering: <strong id="quickStudentName"></strong> (Reg: <strong id="quickStudentReg"></strong>)
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_sdms_code" class="form-label">NESA SDMS Code *</label>
                        <input type="text" name="nesa_sdms_code" id="quick_nesa_sdms_code" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_reg_number" class="form-label">NESA Registration Number *</label>
                        <input type="text" name="nesa_reg_number" id="quick_nesa_reg_number" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_document" class="form-label">NESA Document</label>
                        <input type="file" name="nesa_document" id="quick_nesa_document" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    </div>
                    
                    <div class="form-group">
                        <label for="quick_nesa_remarks" class="form-label">Remarks</label>
                        <textarea name="nesa_remarks" id="quick_nesa_remarks" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check mr-2"></i>Register Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
let currentStudentId = null;

function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    document.getElementById('selectedCount').textContent = checkboxes.length;
}

function showBulkRegisterModal() {
    const checkboxes = document.querySelectorAll('.student-checkbox:checked');
    
    if (checkboxes.length === 0) {
        alert('Please select at least one student to register.');
        return;
    }
    
    // Update form action with selected student IDs
    const form = document.getElementById('bulkRegisterForm');
    const formData = new FormData();
    
    checkboxes.forEach(checkbox => {
        formData.append('student_ids[]', checkbox.value);
    });
    
    // Store student IDs in hidden inputs
    const existingInputs = form.querySelectorAll('input[name="student_ids[]"]');
    existingInputs.forEach(input => input.remove());
    
    checkboxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'student_ids[]';
        input.value = checkbox.value;
        form.appendChild(input);
    });
    
    $('#bulkRegisterModal').modal('show');
}

function showQuickRegisterModal(studentId, studentName, studentReg) {
    currentStudentId = studentId;
    document.getElementById('quickStudentName').textContent = studentName;
    document.getElementById('quickStudentReg').textContent = studentReg;
    
    // Update form action
    document.getElementById('quickRegisterForm').action = `/admin/nesa/${studentId}/mark-registered`;
    
    // Clear previous values
    document.getElementById('quick_nesa_sdms_code').value = '';
    document.getElementById('quick_nesa_reg_number').value = '';
    document.getElementById('quick_nesa_document').value = '';
    document.getElementById('quick_nesa_remarks').value = '';
    
    $('#quickRegisterModal').modal('show');
}

// Update selected count when checkboxes are clicked
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
            
            updateSelectedCount();
        });
    });
});
</script>
@endsection
