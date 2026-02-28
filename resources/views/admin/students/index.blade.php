@extends('admin.layout')

@section('title', 'Students Management')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="page-title">
                            <i class="bi bi-people-fill text-primary me-3"></i>
                            Students Management
                        </h1>
                        <p class="page-subtitle">Manage and view all registered students</p>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-lg">
                            <i class="bi bi-plus-circle me-2"></i>Add New Student
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    @if(session('generated_password'))
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-info-circle me-2"></i>Student password: <strong>{{ session('generated_password') }}</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-primary">
                <div class="stats-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $students->total() }}</h3>
                    <p class="stats-label">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-info">
                <div class="stats-icon">
                    <i class="bi bi-building"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $students->pluck('branch_id')->unique()->count() }}</h3>
                    <p class="stats-label">Branches</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="stats-card stats-warning">
                <div class="stats-icon">
                    <i class="bi bi-book"></i>
                </div>
                <div class="stats-content">
                    <h3 class="stats-number">{{ $students->pluck('course_id')->unique()->count() }}</h3>
                    <p class="stats-label">Courses</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="filters-card">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="search-box">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="name" id="nameSearch" class="form-control search-input" placeholder="Search by name..." value="{{ request('name') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="reg_number" id="regNumberSearch" class="form-control search-input" placeholder="Reg Number" value="{{ request('reg_number') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="email" id="emailSearch" class="form-control search-input" placeholder="Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="telephone" id="telephoneSearch" class="form-control search-input" placeholder="Phone" value="{{ request('telephone') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="course" id="courseSearch" class="form-control search-input" placeholder="Course" value="{{ request('course') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table Container -->
    <div class="row">
        <div class="col-12">
            <div id="studentsTableContainer" style="position: relative;">
               <div class="table-card">
    <div class="table-header">
        <h5 class="table-title">
            <i class="bi bi-table me-2"></i>Students List
        </h5>
        <div class="table-actions">
            <button class="btn btn-outline-primary btn-sm me-2" onclick="exportToCSV()">
                <i class="bi bi-download me-1"></i>Export CSV
            </button>
            <button class="btn btn-outline-success btn-sm" onclick="printTable()">
                <i class="bi bi-printer me-1"></i>Print
            </button>
        </div>
    </div>
    
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-header-custom">
                <tr>
                    <th>#</th>
                    <th>Student Name</th>
                    <th>Branch</th>
                    <th>Course</th>
                    <th>Reg Number</th>
                    <th>NESA Registered</th>
                    <th>Total to Pay</th>
                    <th>Discount & Final Payment</th>
                    <th>Total Paid</th>
                    <th>Remaining</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $index => $student)
                    @php
                        // Fetch the related course model
                        $course = \App\Models\Course::find($student->course_id);
                        $discount = $student->discount ?? 0;

                        // Calculate fees
                        $registrationFee = $course ? floatval($course->registration_fee) : 0;
                        $tuitionFee = $course ? floatval($course->tuition_fee) : 0;
                        $practicalFee = $course ? floatval($course->practical_fee) : 0;
                        $tripFee = $course ? floatval($course->trip_fee) : 0;

                        // Calculate discountable fees (exclude registration fee from discount)
                        $discountableFees = $tuitionFee + $practicalFee + $tripFee;

                        // Apply discount only to discountable fees
                        $discountAmount = ($discount > 0) ? ($discountableFees * ($discount / 100)) : 0;
                        $studentcard = 2000;
                        
                        // Total fees after discount (registration fee is not discounted)
                        $totalToPay = $studentcard + $registrationFee + ($discountableFees - $discountAmount);

                        // Get payments from student_transactions table
                        $studentTransactions = \App\Models\StudentTransaction::where('student_id', $student->id)
                            ->whereIn('status', ['successful', 'paid'])
                            ->get();

                        $totalPaid = $studentTransactions->sum('amount');
                        $remaining = $totalToPay - $totalPaid;
                        $hasPaidReg = $studentTransactions
                            ->where('type', 'registration')
                            ->sum('amount') > 0;
                    @endphp
                    <tr class="student-row" data-student-id="{{ $student->id }}">
                        <td class="fw-bold text-primary">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar">
                                    @if($student->id_photo)
                                        <img src="{{ asset('storage/' . $student->id_photo) }}" alt="Student Photo" class="avatar-img">
                                    @else
                                        <div class="avatar-placeholder">
                                            {{ substr($student->full_name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="student-details">
                                    <div class="student-name">{{ $student->full_name }}</div>
                                    <small class="text-muted d-block">Reg: {{ $student->reg_number }}</small>
                                    <div class="student-meta">
                                        <span class="meta-item">
                                            <i class="bi bi-building"></i>
                                            {{ $student->branch->name ?? 'N/A' }}
                                        </span>
                                        <span class="meta-item">
                                            <i class="bi bi-book"></i>
                                            {{ $student->course->course_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                    @if($student->nesa_registered)
                                        <span class="badge bg-success nesa-badge">
                                            <i class="bi bi-check-circle"></i>
                                            NESA Registered
                                        </span>
                                    @else
                                        <span class="badge bg-secondary nesa-badge">
                                            <i class="bi bi-x-circle"></i>
                                            Not Registered
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $student->branch->name ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $student->course->course_name ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $student->reg_number }}
                        </td>
                        <td>
                            @if($student->nesa_registered)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-danger">No</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">
                                {{ number_format($totalToPay, 2) }}
                            </span>
                            <br>
                            <small class="text-muted">
                                Reg: {{ number_format($registrationFee, 2) }} | 
                                Other: {{ number_format($discountableFees - $discountAmount, 2) }}
                            </small>
                        </td>
                        <td>
                            @if($discount > 0)
                                <span class="badge bg-success">{{ $discount }}% Discount</span>
                                <br>
                                <small class="text-muted">Applied to: {{ number_format($discountableFees, 2) }}</small>
                            @else
                                <span class="badge bg-secondary">No Discount</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-info">
                                {{ number_format($totalPaid, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-warning text-dark">
                                {{ number_format($remaining, 2) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('admin.students.show', $student->id) }}" 
                                   class="btn btn-sm btn-outline-primary" 
                                   data-bs-toggle="tooltip" 
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student->id) }}" 
                                   class="btn btn-sm btn-outline-warning" 
                                   data-bs-toggle="tooltip" 
                                   title="Edit Student">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" 
                                   class="btn btn-sm btn-outline-secondary" 
                                   data-bs-toggle="tooltip" 
                                   title="Show Password"
                                   onclick="showPasswordModal('{{ $student->reg_number }}', '{{ $student->password_plain ?? 'Not Available' }}')">
                                    <i class="bi bi-key"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger" 
                                        data-bs-toggle="tooltip" 
                                        title="Delete Student"
                                        onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-info"
                                        data-bs-toggle="tooltip"
                                        title="Set Discount"
                                        onclick="showDiscountModal({{ $student->id }}, '{{ $student->reg_number }}', '{{ addslashes($student->full_name) }}', {{ $student->discount ?? 0 }})">
                                    <i class="bi bi-percent"></i>
                                </button>
                                <button type="button"
                                        class="btn btn-sm btn-outline-dark"
                                        data-bs-toggle="tooltip"
                                        title="Generate Transcript"
                                        onclick="showTranscriptModal({{ $student->id }}, '{{ addslashes($student->full_name) }}', '{{ $student->reg_number }}')">
                                    <i class="bi bi-receipt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="no-data-row">
                        <td colspan="8" class="text-center py-5">
                            <div class="no-data-content">
                                <i class="bi bi-people display-1 text-muted mb-3"></i>
                                <h5 class="text-muted">No Students Found</h5>
                                <p class="text-muted">There are no students matching your search criteria.</p>
                                <button class="btn btn-primary" onclick="clearFilters()">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Clear Filters
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($students->hasPages())
    <div class="table-footer">
        <div class="d-flex justify-content-between align-items-center p-3">
            <div class="text-muted">
                Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
            </div>
            <div>
                {{ $students->links() }}
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .table-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.5rem 2rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .table-title {
        color: #2c3e50;
        font-weight: 600;
        margin-bottom: 0;
    }

    .table-actions {
        display: flex;
        gap: 0.5rem;
    }

    .table-header-custom {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .table-header-custom th {
        border: none;
        font-weight: 600;
        padding: 1rem;
        position: relative;
    }

    .student-row {
        transition: all 0.3s ease;
    }

    .student-row:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
    }

    .student-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .avatar-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .student-name {
        font-weight: 600;
        color: #2c3e50;
    }

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .student-name {
        font-weight: 600;
        color: #2c3e50;
    }
    
    .student-meta {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        margin-top: 0.25rem;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #6c757d;
    }
    
    .meta-item i {
        font-size: 0.9rem;
    }
    
    .nesa-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }
    
    .nesa-badge.bg-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    
    .nesa-badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        color: white;
    }
    
    .action-buttons .btn {
        transition: all 0.3s ease;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
    }

    .no-data-content {
        padding: 3rem 2rem;
    }

    .table-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .pagination {
        margin-bottom: 0;
    }

    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            gap: 1rem;
            align-items: stretch;
        }

        .table-actions {
            justify-content: center;
        }

        .action-buttons {
            flex-direction: column;
        }

        .student-info {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }
    }
</style>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="delete-icon mb-3">
                    <i class="bi bi-trash3 display-4 text-danger"></i>
                </div>
                <h6>Are you sure you want to delete this student?</h6>
                <p class="text-muted mb-0">
                    Student: <strong id="studentName"></strong><br>
                    This action cannot be undone.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Delete Student
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Show Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title"><i class="bi bi-key me-2"></i>Student Credentials</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center">
        <div class="mb-3">
          <label class="form-label fw-bold">Registration Number</label>
          <div id="modalRegNumber" class="form-control text-center mb-2" readonly></div>
        </div>
        <div class="mb-3">
          <label class="form-label fw-bold">Password</label>
          <div class="input-group">
            <input type="text" id="modalPassword" class="form-control text-center" readonly>
            <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility()">
              <i id="togglePasswordIcon" class="bi bi-eye"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Discount Modal -->
<div class="modal fade" id="discountModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <form id="discountForm" method="POST">
      @csrf
      @method('PATCH')
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title"><i class="bi bi-percent me-2"></i>Update Student Discount</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">Registration Number</label>
            <div id="discountRegNumber" class="form-control" readonly></div>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">Full Name</label>
            <div id="discountFullName" class="form-control" readonly></div>
          </div>
          <div class="mb-3">
            <label for="discountInput" class="form-label fw-bold">Discount (%)</label>
            <input type="number" min="0" max="100" step="0.01" class="form-control" id="discountInput" name="discount" required>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-info">Update Discount</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Transcript Modal -->
<div class="modal fade" id="transcriptModal" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title">
          <i class="bi bi-receipt me-2"></i>Student School Fees Transcript
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <strong>Name:</strong> <span id="transcriptStudentName"></span><br>
          <strong>Reg Number:</strong> <span id="transcriptRegNumber"></span>
        </div>
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Date</th>
                <th>Fee Type</th>
                <th>Amount Paid</th>
                <th>Payment Method</th>
              </tr>
            </thead>
            <tbody id="transcriptTableBody">
              <!-- Transcript rows will be injected here -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer justify-content-between">
        <div>
          <small class="text-muted">Generated by: <strong>{{ Auth::user()->name ?? 'Admin' }}</strong></small>
        </div>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }

    .page-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        font-size: 1.1rem;
        opacity: 0.9;
        margin-bottom: 0;
    }

    .header-actions .btn {
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }

    .header-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.3);
    }

    /* Statistics Cards */
    .stats-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        height: 100%;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }

    .stats-card.stats-primary {
        border-left: 5px solid #007bff;
    }

    .stats-card.stats-info {
        border-left: 5px solid #17a2b8;
    }

    .stats-card.stats-warning {
        border-left: 5px solid #ffc107;
    }

    .stats-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        opacity: 0.8;
    }

    .stats-primary .stats-icon {
        color: #007bff;
    }

    .stats-info .stats-icon {
        color: #17a2b8;
    }

    .stats-warning .stats-icon {
        color: #ffc107;
    }

    .stats-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        color: #2c3e50;
    }

    .stats-label {
        color: #6c757d;
        font-weight: 500;
        margin-bottom: 0;
    }

    /* Filters Card */
    .filters-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border: 1px solid #e9ecef;
    }

    .search-box {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 10;
    }

    .search-input {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        padding: 0.5rem 1rem;
    }

    .search-box .search-input {
        padding-left: 45px;
    }

    .search-input:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        outline: none;
    }

    /* Loading Overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        border-radius: 15px;
    }

    .delete-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(220, 53, 69, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .page-title {
            font-size: 1.8rem;
        }

        .stats-card {
            margin-bottom: 1rem;
        }
    }
</style>

<script>
let searchTimeout;
const searchDelay = 500; // 500ms delay for better UX

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('Live search initialized');
    
    // Add event listeners to all search inputs
    const searchInputs = ['nameSearch', 'regNumberSearch', 'emailSearch', 'telephoneSearch', 'courseSearch'];
    
    searchInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                console.log('Search input detected:', inputId, this.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(performSearch, searchDelay);
            });
            
            // Also trigger on keyup for immediate feedback
            input.addEventListener('keyup', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(searchTimeout);
                    performSearch();
                }
            });
        }
    });

    // Initialize tooltips
    initializeTooltips();

    // Handle pagination clicks (delegated event)
    document.addEventListener('click', function(e) {
        const paginationLink = e.target.closest('.pagination a');
        if (paginationLink) {
            e.preventDefault();
            const url = paginationLink.getAttribute('href');
            if (url && !paginationLink.classList.contains('disabled')) {
                console.log('Pagination clicked:', url);
                fetchStudents(url);
            }
        }
    });
});

function performSearch() {
    console.log('Performing search...');
    
    const formData = {
        name: document.getElementById('nameSearch').value.trim(),
        reg_number: document.getElementById('regNumberSearch').value.trim(),
        email: document.getElementById('emailSearch').value.trim(),
        telephone: document.getElementById('telephoneSearch').value.trim(),
        course: document.getElementById('courseSearch').value.trim()
    };

    console.log('Search data:', formData);

    const queryString = new URLSearchParams(formData).toString();
    const url = "{{ route('admin.students.index') }}" + (queryString ? '?' + queryString : '');
    
    console.log('Fetching URL:', url);
    fetchStudents(url);
}

function fetchStudents(url) {
    const container = document.getElementById('studentsTableContainer');
    
    console.log('Fetching students from:', url);
    
    // Show loading state
    showLoading(container);

    fetch(url, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'text/html',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        console.log('HTML received, updating table');
        
        // Parse the HTML and extract just the table container
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newContent = doc.querySelector('#studentsTableContainer');
        
        if (newContent) {
            container.innerHTML = newContent.innerHTML;
            console.log('Table updated successfully');
        } else {
            // If partial view, update directly
            container.innerHTML = html;
            console.log('Direct HTML update');
        }
        
        // Remove loading state
        hideLoading(container);

        // Re-initialize tooltips for new content
        initializeTooltips();
    })
    .catch(error => {
        console.error('Error fetching students:', error);
        hideLoading(container);
        
        // Show error message
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger m-3';
        errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>Error loading students. Please try again.';
        container.insertBefore(errorDiv, container.firstChild);
        
        setTimeout(() => errorDiv.remove(), 5000);
    });
}

function showLoading(container) {
    // Create loading overlay if it doesn't exist
    let overlay = container.querySelector('.loading-overlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
        container.appendChild(overlay);
    }
    container.style.opacity = '0.6';
    container.style.pointerEvents = 'none';
}

function hideLoading(container) {
    const overlay = container.querySelector('.loading-overlay');
    if (overlay) {
        overlay.remove();
    }
    container.style.opacity = '1';
    container.style.pointerEvents = 'auto';
}

function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        // Dispose old tooltip if exists
        const oldTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
        if (oldTooltip) {
            oldTooltip.dispose();
        }
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

function clearFilters() {
    console.log('Clearing filters');
    document.getElementById('nameSearch').value = '';
    document.getElementById('regNumberSearch').value = '';
    document.getElementById('emailSearch').value = '';
    document.getElementById('telephoneSearch').value = '';
    document.getElementById('courseSearch').value = '';
    performSearch();
}

// Modal Functions
function confirmDelete(studentId, studentName) {
    document.getElementById('studentName').textContent = studentName;
    document.getElementById('deleteForm').action = `/admin/students/${studentId}`;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function showPasswordModal(regNumber, password) {
    document.getElementById('modalRegNumber').textContent = regNumber;
    document.getElementById('modalPassword').value = password;
    document.getElementById('modalPassword').type = 'text';
    document.getElementById('togglePasswordIcon').className = 'bi bi-eye';
    const modal = new bootstrap.Modal(document.getElementById('passwordModal'));
    modal.show();
}

function togglePasswordVisibility() {
    const input = document.getElementById('modalPassword');
    const icon = document.getElementById('togglePasswordIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-slash';
    }
}

function showDiscountModal(studentId, regNumber, fullName, discount) {
    document.getElementById('discountRegNumber').textContent = regNumber;
    document.getElementById('discountFullName').textContent = fullName;
    document.getElementById('discountInput').value = discount;
    document.getElementById('discountForm').action = `/admin/students/${studentId}/discount`;
    const modal = new bootstrap.Modal(document.getElementById('discountModal'));
    modal.show();
}

function showTranscriptModal(studentId, fullName, regNumber) {
    document.getElementById('transcriptStudentName').textContent = fullName;
    document.getElementById('transcriptRegNumber').textContent = regNumber;
    
    const tbody = document.getElementById('transcriptTableBody');
    tbody.innerHTML = '<tr><td colspan="4" class="text-center"><span class="spinner-border spinner-border-sm"></span> Loading...</td></tr>';
    
    fetch(`/admin/students/${studentId}/transcript-ajax`)
        .then(response => response.text())
        .then(html => {
            tbody.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading transcript:', error);
            tbody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading transcript</td></tr>';
        });
    
    const modal = new bootstrap.Modal(document.getElementById('transcriptModal'));
    modal.show();
}

// Export and Print Functions
function exportToCSV() {
    const table = document.querySelector('#studentsTableContainer table');
    if (!table) return;
    
    const rows = Array.from(table.querySelectorAll('tr'));
    const csv = rows.map(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        return cells.slice(0, -1).map(cell => {
            return `"${cell.textContent.trim().replace(/"/g, '""')}"`;
        }).join(',');
    }).join('\n');

    const blob = new Blob([csv], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'students_list_' + new Date().toISOString().split('T')[0] + '.csv';
    a.click();
    window.URL.revokeObjectURL(url);
}

function printTable() {
    window.print();
}
</script>

<style media="print">
    .page-header,
    .filters-card,
    .stats-card,
    .header-actions,
    .action-buttons,
    .btn,
    .pagination {
        display: none !important;
    }

    .table-card {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    body {
        font-size: 12px;
    }
</style>
@endsection