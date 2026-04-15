@extends('admin.layout')

@push('styles')
    @include('admin.report-cards.partials.layout-fixes')
@endpush

@section('content')
<div class="content">
    <div class="report-container">
        <div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-exclamation-triangle text-warning"></i>
                        Outstanding Fees Management
                    </h2>
                    <small class="text-muted">Manage students with pending payments for report card generation</small>
                </div>
                <a href="{{ route('admin.report-cards.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Report Cards
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Students</p>
                            <h3 class="mb-0 text-primary">{{ count($studentsWithFees) }}</h3>
                        </div>
                        <i class="bi bi-people fs-2 text-primary opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Outstanding</p>
                            <h3 class="mb-0 text-danger">
                                {{ config('app.currency_symbol', 'RWF') }} 
                                {{ number_format(collect($studentsWithFees)->sum('outstanding_amount'), 2) }}
                            </h3>
                        </div>
                        <i class="bi bi-cash-coin fs-2 text-danger opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Avg Outstanding</p>
                            <h3 class="mb-0 text-warning">
                                {{ config('app.currency_symbol', 'RWF') }} 
                                {{ number_format(count($studentsWithFees) > 0 ? collect($studentsWithFees)->avg('outstanding_amount') : 0, 2) }}
                            </h3>
                        </div>
                        <i class="bi bi-graph-up fs-2 text-warning opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1">Total Paid</p>
                            <h3 class="mb-0 text-success">
                                {{ config('app.currency_symbol', 'RWF') }} 
                                {{ number_format(collect($studentsWithFees)->sum('total_paid'), 2) }}
                            </h3>
                        </div>
                        <i class="bi bi-check-circle fs-2 text-success opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="courseFilter" class="form-label">
                        <i class="bi bi-funnel me-1"></i>Filter by Course
                    </label>
                    <select id="courseFilter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="branchFilter" class="form-label">
                        <i class="bi bi-funnel me-1"></i>Filter by Branch
                    </label>
                    <select id="branchFilter" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchInput" class="form-label">
                        <i class="bi bi-search me-1"></i>Search by Name/ID
                    </label>
                    <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="button" class="btn btn-danger" onclick="clearFilters()">
                        <i class="bi bi-arrow-clockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Actions Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <p class="mb-0"><strong>Bulk Actions:</strong></p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-info" onclick="sendAllEmails()">
                        <i class="bi bi-envelope-fill me-1"></i> Send Emails to All
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-info" onclick="selectAllRows()">
                        <i class="bi bi-check2-square me-1"></i> Select All
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-outline-secondary" onclick="deselectAllRows()">
                        <i class="bi bi-dash-square me-1"></i> Deselect All
                    </button>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-warning" onclick="sendSelectedEmails()" id="sendSelectedBtn" style="display:none;">
                        <i class="bi bi-envelope-fill me-1"></i> Send to Selected (<span id="selectedCount">0</span>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    @if(count($studentsWithFees) > 0)
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="studentsTable">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th width="40">
                                <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)">
                            </th>
                            <th>Student Name</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Branch</th>
                            <th>Email</th>
                            <th class="text-end">Required Fees</th>
                            <th class="text-end">Paid Amount</th>
                            <th class="text-end">Outstanding</th>
                            <th class="text-center">Payment %</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studentsWithFees as $student)
                            <tr class="student-row" data-course="{{ $student['course']['id'] ?? '' }}" 
                                data-branch="{{ $student['branch']['id'] ?? '' }}"
                                data-name="{{ strtolower($student['full_name']) }}"
                                data-id="{{ strtolower($student['student_id'] ?? '') }}">
                                <td>
                                    <input type="checkbox" class="student-checkbox" value="{{ $student['id'] }}" 
                                        onchange="updateSelectedCount()">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px;">
                                            {{ substr($student['full_name'], 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-bold">{{ $student['full_name'] }}</p>
                                            <small class="text-muted">{{ $student['email'] ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $student['student_id'] ?? 'N/A' }}</td>
                                <td>{{ $student['course']['course_name'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $student['branch']['name'] ?? 'N/A' }}</span>
                                </td>
                                <td>
                                    @if($student['email'])
                                        <small>{{ substr($student['email'], 0, 20) }}...</small>
                                    @else
                                        <span class="text-danger"><strong>No Email</strong></span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <strong>{{ config('app.currency_symbol', 'RWF') }} {{ number_format($student['required_fees'], 2) }}</strong>
                                </td>
                                <td class="text-end">
                                    <span class="text-success">{{ config('app.currency_symbol', 'RWF') }} {{ number_format($student['total_paid'], 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-danger fs-6">{{ config('app.currency_symbol', 'RWF') }} {{ number_format($student['outstanding_amount'], 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @php
                                        $percentage = $student['payment_percentage'];
                                    @endphp
                                    <div class="progress" style="height: 20px; width: 80px;">
                                        <div class="progress-bar {{ $percentage >= 75 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <small>{{ number_format($percentage, 0) }}%</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($student['email'])
                                            <button type="button" class="btn btn-outline-primary" 
                                                onclick="sendEmailToStudent({{ $student['id'] }}, '{{ addslashes($student['full_name']) }}')">
                                                <i class="bi bi-envelope me-1"></i> Email
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-outline-secondary" disabled>
                                                <i class="bi bi-envelope-exclamation me-1"></i> No Email
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.report-cards.create', ['student_id' => $student['id'], 'course_id' => $student['course']['id'] ?? null, 'academic_year_id' => $student['academic_year_id'] ?? null]) }}"
                                           class="btn btn-outline-success">
                                            <i class="bi bi-file-earmark-plus me-1"></i> Generate
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle text-success display-1"></i>
                <h4 class="mt-3">All Students Have Paid Fees!</h4>
                <p class="text-muted">There are no students with outstanding fees at this time.</p>
                <a href="{{ route('admin.report-cards.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i> Back to Report Cards
                </a>
            </div>
        </div>
    @endif
        </div>
    </div>
</div>

<!-- Include Styles -->
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .badge {
        font-size: 0.875rem;
        padding: 0.35rem 0.75rem;
    }

    .btn-group-sm .btn {
        padding: 0.35rem 0.75rem;
        font-size: 0.875rem;
    }

    .card {
        border-radius: 0.5rem;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 0.25rem;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.875rem;
        font-weight: bold;
    }

    .sticky-top {
        z-index: 10;
    }
</style>

<!-- Include Scripts -->
<script>
    function toggleSelectAll(checkbox) {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = checkbox.checked;
        });
        updateSelectedCount();
    }

    function selectAllRows() {
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.checked = true;
        });
        document.getElementById('selectAllCheckbox').checked = true;
        updateSelectedCount();
    }

    function deselectAllRows() {
        document.querySelectorAll('.student-checkbox').forEach(cb => {
            cb.checked = false;
        });
        document.getElementById('selectAllCheckbox').checked = false;
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.student-checkbox:checked').length;
        const sendBtn = document.getElementById('sendSelectedBtn');
        const selectedCountSpan = document.getElementById('selectedCount');
        
        selectedCountSpan.textContent = selectedCount;
        
        if (selectedCount > 0) {
            sendBtn.style.display = 'inline-block';
        } else {
            sendBtn.style.display = 'none';
        }
    }

    function clearFilters() {
        document.getElementById('courseFilter').value = '';
        document.getElementById('branchFilter').value = '';
        document.getElementById('searchInput').value = '';
        filterTable();
    }

    function filterTable() {
        const courseId = document.getElementById('courseFilter').value;
        const branchId = document.getElementById('branchFilter').value;
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();

        document.querySelectorAll('.student-row').forEach(row => {
            let show = true;

            if (courseId && row.dataset.course !== courseId) show = false;
            if (branchId && row.dataset.branch !== branchId) show = false;
            if (searchTerm && !row.dataset.name.includes(searchTerm) && !row.dataset.id.includes(searchTerm)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    document.getElementById('courseFilter').addEventListener('change', filterTable);
    document.getElementById('branchFilter').addEventListener('change', filterTable);
    document.getElementById('searchInput').addEventListener('keyup', filterTable);

    function sendEmailToStudent(studentId, studentName) {
        if (!confirm(`Send outstanding fees email to ${studentName}?`)) return;

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

        fetch(`/admin/report-cards/outstanding-fees/${studentId}/email`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope me-1"></i> Email';

            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope me-1"></i> Email';
            showAlert('danger', 'Error sending email: ' + error.message);
        });
    }

    function sendSelectedEmails() {
        const selectedIds = Array.from(document.querySelectorAll('.student-checkbox:checked'))
            .map(cb => cb.value);

        if (selectedIds.length === 0) {
            showAlert('warning', 'Please select at least one student');
            return;
        }

        if (!confirm(`Send emails to ${selectedIds.length} selected student(s)?`)) return;

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

        fetch('/admin/report-cards/outstanding-fees/bulk-email', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ student_ids: selectedIds })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope-fill me-1"></i> Send to Selected (<span id="selectedCount">' + selectedIds.length + '</span>)';

            if (data.success) {
                showAlert('success', data.message);
                deselectAllRows();
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope-fill me-1"></i> Send to Selected';
            showAlert('danger', 'Error sending emails: ' + error.message);
        });
    }

    function sendAllEmails() {
        const totalStudents = document.querySelectorAll('.student-row:not([style*="display: none"])').length;
        
        if (totalStudents === 0) {
            showAlert('warning', 'No students to send emails to');
            return;
        }

        if (!confirm(`Send emails to all ${totalStudents} student(s) with outstanding fees?`)) return;

        const btn = event.target;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Sending...';

        fetch('/admin/report-cards/outstanding-fees/bulk-email', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ student_ids: [] })
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope-fill me-1"></i> Send Emails to All';

            if (data.success) {
                showAlert('success', data.message);
            } else {
                showAlert('danger', data.message);
            }
        })
        .catch(error => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-envelope-fill me-1"></i> Send Emails to All';
            showAlert('danger', 'Error sending emails: ' + error.message);
        });
    }

    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.report-container .container-fluid') || document.querySelector('.container-fluid');
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => alertDiv.remove(), 5000);
    }

    // Auto-refresh outstanding fees status every 10 seconds
    let refreshInterval;

    function startAutoRefresh() {
        // Refresh every 10 seconds (adjust as needed)
        refreshInterval = setInterval(refreshOutstandingStatus, 10000);
        // Log for debugging
        console.log('Auto-refresh started - checking every 10 seconds');
    }

    function stopAutoRefresh() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            console.log('Auto-refresh stopped');
        }
    }

    async function refreshOutstandingStatus() {
        const studentRows = document.querySelectorAll('.student-row');
        
        for (const row of studentRows) {
            const studentId = row.querySelector('.student-checkbox').value;
            await updateStudentOutstandingStatus(studentId, row);
        }
    }

    async function updateStudentOutstandingStatus(studentId, rowElement) {
        try {
            const response = await fetch(`/admin/report-cards/outstanding-fees/${studentId}/status`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                console.error(`Failed to fetch status for student ${studentId}`);
                return;
            }

            const data = await response.json();

            if (!data.success) {
                console.error(`Error fetching status: ${data.message}`);
                return;
            }

            // Update the row with new data
            if (data.has_outstanding === false) {
                // Student has NO outstanding fees - remove row with animation
                rowElement.style.opacity = '0.5';
                rowElement.style.textDecoration = 'line-through';
                
                // Add a badge to show the row is being removed
                const outstandingCell = rowElement.querySelector('td:nth-child(9)');
                if (outstandingCell) {
                    outstandingCell.innerHTML = '<span class="badge bg-success">Paid</span>';
                }

                // Disable action buttons
                const buttons = rowElement.querySelectorAll('.btn-group button, .btn-group a');
                buttons.forEach(btn => {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                    btn.style.cursor = 'not-allowed';
                    btn.style.pointerEvents = 'none';
                    btn.setAttribute('title', 'Student has completed payment');
                    
                    // Prevent click on links
                    if (btn.tagName === 'A') {
                        btn.onclick = (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                            return false;
                        };
                    }
                });

                // Show a notification and remove after a delay
                console.log(`Student ${studentId} has completed payment`);
                
                // Remove the row after 5 seconds
                setTimeout(() => {
                    rowElement.remove();
                    // Update statistics
                    updateStatistics();
                }, 5000);
            } else {
                // Student still has outstanding fees - update the amounts
                const requiredFeesCell = rowElement.querySelector('td:nth-child(7)');
                const paidCell = rowElement.querySelector('td:nth-child(8)');
                const outstandingCell = rowElement.querySelector('td:nth-child(9)');
                const percentageCell = rowElement.querySelector('td:nth-child(10)');

                if (requiredFeesCell) {
                    requiredFeesCell.innerHTML = `<strong>{{ config('app.currency_symbol', 'RWF') }} ${formatCurrency(data.required_fees)}</strong>`;
                }
                if (paidCell) {
                    paidCell.innerHTML = `<span class="text-success">{{ config('app.currency_symbol', 'RWF') }} ${formatCurrency(data.total_paid)}</span>`;
                }
                if (outstandingCell) {
                    outstandingCell.innerHTML = `<span class="badge bg-danger fs-6">{{ config('app.currency_symbol', 'RWF') }} ${formatCurrency(data.outstanding_amount)}</span>`;
                }
                if (percentageCell) {
                    const percentage = data.payment_percentage;
                    const barColor = percentage >= 75 ? 'bg-success' : (percentage >= 50 ? 'bg-warning' : 'bg-danger');
                    percentageCell.innerHTML = `
                        <div class="progress" style="height: 20px; width: 80px;">
                            <div class="progress-bar ${barColor}" 
                                role="progressbar" 
                                style="width: ${percentage}%"
                                aria-valuenow="${percentage}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                <small>${Math.round(percentage)}%</small>
                            </div>
                        </div>
                    `;
                }

                // Ensure action buttons are enabled
                const buttons = rowElement.querySelectorAll('.btn-group button, .btn-group a');
                buttons.forEach(btn => {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                    btn.style.cursor = 'pointer';
                    btn.style.pointerEvents = 'auto';
                    btn.removeAttribute('title');
                    
                    // Remove click prevention
                    if (btn.tagName === 'A') {
                        btn.onclick = null;
                    }
                });
            }
        } catch (error) {
            console.error(`Error updating student ${studentId}:`, error);
        }
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(amount);
    }

    function updateStatistics() {
        // Recalculate and update the statistics cards
        let totalStudents = 0;
        let totalOutstanding = 0;
        let totalPaid = 0;

        document.querySelectorAll('.student-row').forEach(row => {
            totalStudents++;
            const outstandingText = row.querySelector('td:nth-child(9)').textContent.replace(/[^0-9.]/g, '');
            const paidText = row.querySelector('td:nth-child(8)').textContent.replace(/[^0-9.]/g, '');
            totalOutstanding += parseFloat(outstandingText) || 0;
            totalPaid += parseFloat(paidText) || 0;
        });

        // Check if there are no more students with outstanding fees
        if (totalStudents === 0) {
            // Reload the page or show a message
            location.reload();
        }
    }

    // Start auto-refresh when page loads
    document.addEventListener('DOMContentLoaded', function() {
        startAutoRefresh();

        // Optional: Stop auto-refresh when user leaves the page
        window.addEventListener('beforeunload', stopAutoRefresh);
    });
</script>
@endsection
