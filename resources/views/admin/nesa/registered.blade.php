@extends('admin.layout')

@section('title', 'NESA Registered Students')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle mr-2"></i>
                        NESA Registered Students
                        <span class="badge badge-light ml-2">{{ $students->total() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $students->total() }}</h3>
                                    <p class="mb-0">Total Registered</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $students->count() }}</h3>
                                    <p class="mb-0">This Page</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3>{{ App\Models\Student::notNesaRegistered()->count() }}</h3>
                                    <p class="mb-0">Still Pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3>{{ $students->total() > 0 ? round(($students->total() / App\Models\Student::count()) * 100, 1) : 0 }}%</h3>
                                    <p class="mb-0">Completion Rate</p>
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
                            <a href="{{ route('admin.nesa.pending') }}" class="btn btn-warning ml-2">
                                <i class="fas fa-clock mr-2"></i>View Pending ({{ App\Models\Student::notNesaRegistered()->count() }})
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.nesa.export') }}" class="btn btn-info">
                                <i class="fas fa-download mr-2"></i>Export to CSV
                            </a>
                        </div>
                    </div>

                    <!-- Bulk Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6><i class="fas fa-tools mr-2"></i>Bulk Actions</h6>
                                    <form method="POST" action="{{ route('admin.nesa.bulk-register') }}" id="bulkForm">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="text" name="bulk_sdms_code" class="form-control" placeholder="Bulk SDMS Code" required>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="text" name="bulk_reg_number_prefix" class="form-control" placeholder="Reg Number Prefix" required>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="bulk_remarks" class="form-control" placeholder="Bulk Remarks (Optional)">
                                            </div>
                                            <div class="col-md-2">
                                                <button type="submit" class="btn btn-success" onclick="return confirm('This will register selected students for NESA examination. Continue?')">
                                                    <i class="fas fa-users mr-2"></i>Register Selected
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
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
                                    <th>Branch</th>
                                    <th>Course</th>
                                    <th>NESA SDMS Code</th>
                                    <th>NESA Reg Number</th>
                                    <th>Registration Date</th>
                                    <th>Document</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" class="student-checkbox" form="bulkForm">
                                        </td>
                                        <td><strong>{{ $student->reg_number }}</strong></td>
                                        <td>{{ $student->full_name }}</td>
                                        <td>{{ $student->email }}</td>
                                        <td>{{ $student->branch->name ?? 'N/A' }}</td>
                                        <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                                        <td><code>{{ $student->nesa_sdms_code }}</code></td>
                                        <td><code>{{ $student->nesa_reg_number }}</code></td>
                                        <td>{{ $student->nesa_registration_date ? $student->nesa_registration_date->format('M d, Y') : 'N/A' }}</td>
                                        <td>
                                            @if($student->nesa_document)
                                                <a href="{{ route('admin.nesa.download', $student) }}" class="btn btn-sm btn-info" title="Download Document">
                                                    <i class="fas fa-file-download"></i>
                                                </a>
                                            @else
                                                <span class="text-muted">No file</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.nesa.edit', $student) }}" class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="11" class="text-center">
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle mr-2"></i>
                                                No students are currently registered for NESA examination.
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

<script>
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.student-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Update select all checkbox when individual checkboxes are clicked
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.student-checkbox');
    const selectAll = document.getElementById('selectAll');
    
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            
            selectAll.checked = allChecked;
            selectAll.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>
@endsection
