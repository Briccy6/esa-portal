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
                        <a href="{{ route('admin.students.create') }}" class="btn btn-primary btn-lg me-2">
                            <i class="bi bi-plus-circle me-2"></i>Add New Student
                        </a>
                        <button type="button" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#nesaRegistrationModal">
                            <i class="bi bi-award me-2"></i>Register NESA
                        </button>
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
                    @endphp
                    <tr class="student-row" data-student-id="{{ $student->id }}">
                        <td class="fw-bold text-primary">{{ $students->firstItem() + $index }}</td>
                        <td>
                            <div class="student-info">
                                <div class="student-avatar">
                                    @if($student->id_photo)
                                    @php
                                        // support both raw base64 and storage paths
                                        $photoSrc = '';
                                        if(str_starts_with($student->id_photo, 'data:image')) {
                                            $photoSrc = $student->id_photo;
                                        } elseif(preg_match('/^[A-Za-z0-9\/\r\n+]+={0,2}$/', $student->id_photo)) {
                                            // looks like base64 without prefix
                                            $photoSrc = 'data:image/jpeg;base64,' . $student->id_photo;
                                        } else {
                                            $photoSrc = asset('storage/' . $student->id_photo);
                                        }
                                    @endphp
                                    <img src="{{ $photoSrc }}" alt="Student Photo" class="avatar-img">
                                @else
                                    <div class="avatar-placeholder">
                                        {{ substr($student->full_name, 0, 2) }}
                                    </div>
                                @endif
                                </div>
                                <div class="student-details">
                                    <div class="student-name">{{ $student->full_name }}</div>
                                    <div class="student-meta">
                                        <span class="meta-item">
                                            <i class="bi bi-building"></i>
                                            {{ $student->branch->name ?? 'N/A' }}
                                        </span>
                                        <span class="meta-item">
                                            <i class="bi bi-book"></i>
                                            {{ $student->course->abbreviation ?? $student->course->course_name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $student->branch->name ?? 'N/A' }}
                        </td>
                        <td>
                            {{ $student->course->abbreviation ?? $student->course->course_name ?? 'N/A' }}
                        </td>
                        <td class="text-center">
                            <div class="action-buttons">
                                <a href="{{ route('admin.students.show', $student->id) }}" 
                                   class="btn btn-sm btn-outline-primary action-btn" 
                                   data-bs-toggle="tooltip" 
                                   title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('admin.students.edit', $student->id) }}" 
                                   class="btn btn-sm btn-outline-warning action-btn" 
                                   data-bs-toggle="tooltip" 
                                   title="Edit Student">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="javascript:void(0);" 
                                   class="btn btn-sm btn-outline-secondary action-btn" 
                                   data-bs-toggle="tooltip" 
                                   title="Show Password"
                                   onclick="showPasswordModal('{{ $student->reg_number }}', '{{ $student->password_plain ?? 'Not Available' }}')">
                                    <i class="bi bi-key"></i>
                                </a>
                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger action-btn" 
                                        data-bs-toggle="tooltip" 
                                        title="Delete Student"
                                        onclick="confirmDelete({{ $student->id }}, '{{ addslashes($student->full_name) }}')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr class="no-data-row">
                        <td colspan="6" class="text-center py-5">
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
            <div class="pagination-info">
                Showing <strong>{{ $students->firstItem() }}</strong> to <strong>{{ $students->lastItem() }}</strong> of <strong>{{ $students->total() }}</strong> students
            </div>
            <div class="pagination-wrapper">
                {{ $students->links('') }}
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
    
    
    .action-buttons .btn {
        transition: all 0.3s ease;
        transform: translateY(0);
        opacity: 1;
    }

    .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .action-buttons .btn:focus {
        transform: translateY(-2px);
        box-shadow: 0 0 0 8px rgba(0,0,0,0.2);
        outline: none;
    }

    .action-buttons .btn:active {
        transform: translateY(0);
        box-shadow: 0 0 0 4px rgba(0,0,0,0.25);
    }

    /* Reactbits Animation Classes */
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    .action-buttons .btn {
        animation: slideIn 0.3s ease-out;
    }

    .action-buttons .btn:hover {
        animation: pulse 0.5s ease-in-out infinite;
    }

    .action-buttons .btn:active {
        animation: none;
    }

    .no-data-content {
        padding: 3rem 2rem;
    }

    .table-footer {
        background: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 2rem 0;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    /* Target Laravel pagination structure */
    .pagination .page-link,
    .pagination > span > a,
    .pagination > a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        height: 40px;
        margin: 0 0.25rem;
        padding: 0;
        border: 2px solid transparent;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.875rem;
        line-height: 1;
        color: #6c757d;
        background: #ffffff;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        animation: slideIn 0.4s ease-out;
    }

    .pagination .page-link::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(102, 126, 234, 0.1);
        transform: translate(-50%, -50%);
        transition: all 0.3s ease;
    }

    .pagination .page-link:hover::before {
        width: 100%;
        height: 100%;
        background: rgba(102, 126, 234, 0.15);
    }

    .pagination .page-link:hover {
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        transform: translateY(-2px) scale(1.05);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .pagination .page-link:focus {
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        transform: translateY(-1px) scale(1.02);
        box-shadow: 0 0 0 8px rgba(102, 126, 234, 0.4);
        outline: none;
    }

    .pagination .page-link:active {
        transform: translateY(0) scale(0.98);
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
    }

    .pagination .active {
        color: #ffffff;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #667eea;
        font-weight: 600;
        transform: scale(1.05);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        animation: pulse 2s ease-in-out infinite;
    }

    .pagination .disabled {
        color: #adb5bd;
        background: #f8f9fa;
        border-color: #e9ecef;
        cursor: not-allowed;
        opacity: 0.6;
        transform: none;
        animation: none;
    }

    .pagination .disabled:hover {
        transform: none;
        box-shadow: none;
        animation: none;
    }

    .pagination .disabled::before {
        display: none;
    }

    /* Pagination info text */
    .pagination-info {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 1rem;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 1px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.875rem;
        color: #6c757d;
        font-weight: 500;
        margin: 0 1rem;
        animation: slideIn 0.5s ease-out;
    }

    .pagination-info strong {
        color: #495057;
        font-weight: 600;
    }

    /* Responsive pagination */
    @media (max-width: 768px) {
        .pagination {
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .pagination .page-link {
            min-width: 35px;
            height: 35px;
            font-size: 0.8rem;
            margin: 0.125rem;
        }
        
        .pagination-info {
            font-size: 0.8rem;
            margin: 0.5rem 0;
        }
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

</script>

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

<style>
.table-card {
    box-shadow: none;
    border: 1px solid #ddd;
}

body {
    font-size: 12px;
}

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
@endsection