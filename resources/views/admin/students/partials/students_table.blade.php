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
                        <td colspan="5" class="text-center py-5">
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

    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
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