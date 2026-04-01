@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container-fluid py-4">
    <!-- Success/Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                @if(request('nesa') == 'true')
                    <h2 class="page-title mb-2">
                        <i class="fas fa-award me-2"></i>NESA Registration Management
                    </h2>
                    <p class="page-subtitle mb-0">Register students for NESA examination and manage approval status</p>
                @else
                    <h2 class="page-title mb-2">
                        <i class="fas fa-users me-2"></i>All Students
                    </h2>
                    <p class="page-subtitle mb-0">Manage student records and payments</p>
                @endif
            </div>
            <div>
                <span class="badge bg-primary fs-6">
                    <i class="fas fa-database me-1"></i>{{ $students->total() }} Total Students
                </span>
                @if(request('nesa') == 'true')
                    <div class="mt-2">
                        <button type="button" class="btn btn-success btn-sm" onclick="downloadAllNesaDocuments()">
                            <i class="fas fa-download me-1"></i>Download All NESA Documents
                        </button>
                        <button type="button" class="btn btn-info btn-sm" onclick="downloadNesaReport()">
                            <i class="fas fa-file-excel me-1"></i>Export NESA Report
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Live Search Form -->
    <div class="card mb-4 search-card">
        <div class="card-body">
            <form id="liveSearchForm">
                <div class="row g-3">
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchName" name="name" class="form-control" placeholder="Name" value="{{ request('name') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchRegNumber" name="reg_number" class="form-control" placeholder="Reg Number" value="{{ request('reg_number') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchEmail" name="email" class="form-control" placeholder="Email" value="{{ request('email') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchTelephone" name="telephone" class="form-control" placeholder="Telephone" value="{{ request('telephone') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <input type="text" id="searchCourse" name="course" class="form-control" placeholder="Course" value="{{ request('course') }}">
                    </div>
                    <div class="col-lg-2 col-md-6 col-sm-12">
                        <button type="button" id="resetBtn" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-redo me-1"></i>Reset
                        </button>
                    </div>
                </div>
                <div class="mt-2">
                    <span id="loadingIndicator" class="text-primary" style="display: none;">
                        <span class="spinner-border spinner-border-sm me-1"></span> Searching...
                    </span>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div id="studentsTableWrapper">
        @include('staff.partials.students_table', ['students' => $students])
    </div>
</div>

<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }

    .page-title {
        font-size: clamp(1.5rem, 4vw, 2rem);
        font-weight: 700;
        margin-bottom: 0;
    }

    .page-subtitle {
        font-size: clamp(0.9rem, 2vw, 1rem);
        opacity: 0.9;
    }

    .search-card {
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-radius: 15px;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.15);
    }

    #loadingIndicator {
        font-size: 14px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .page-title {
            font-size: 1.5rem;
        }

        .search-card .card-body {
            padding: 1rem;
        }
    }

    @media (max-width: 576px) {
        .badge {
            font-size: 0.85rem !important;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
// ─── Global Functions (must be loaded first) ─────────────────────────────────────

// Update NESA Status - Global function
window.updateNesaStatus = function(studentId) {
    console.log('updateNesaStatus called for student:', studentId);
    
    const registeredRadio = document.getElementById(`registered_${studentId}`);
    const failedRadio = document.getElementById(`failed_${studentId}`);
    const reasonSection = document.getElementById(`reason_section_${studentId}`);
    const documentSection = document.getElementById(`document_section_${studentId}`);
    const sendEmailCheckbox = document.getElementById(`send_email_${studentId}`);
    
    if (!registeredRadio || !failedRadio) {
        console.error('Radio buttons not found for student:', studentId);
        showAlert('danger', 'Form elements not found. Please refresh the page and try again.');
        return;
    }
    
    const status = registeredRadio.checked ? 'registered' : 'failed';
    const reason = document.getElementById(`failure_reason_${studentId}`)?.value || '';
    const sendEmail = sendEmailCheckbox ? sendEmailCheckbox.checked : false;
    
    console.log('Status:', status, 'Send Email:', sendEmail);
    
    // Validation
    if (status === 'failed' && !reason.trim()) {
        showAlert('danger', 'Please provide a reason for failure');
        return;
    }
    
    if (status === 'registered') {
        const documentInput = document.getElementById(`nesa_document_${studentId}`);
        const documentSection = document.getElementById(`document_section_${studentId}`);
        
        console.log('Document input:', documentInput, 'Files:', documentInput?.files?.length);
        console.log('Document section:', documentSection, 'Display style:', documentSection?.style.display);
        
        // Check if document input exists
        if (!documentInput) {
            showAlert('danger', 'Document upload field not found. Please refresh the page and try again.');
            return;
        }
        
        // Force document section to be visible for registered status
        if (documentSection) {
            documentSection.style.display = 'block';
            console.log('Force showing document section for registered status');
        }
        
        // Small delay to ensure the section is visible before checking files
        setTimeout(() => {
            if (!documentInput.files || documentInput.files.length === 0) {
                showAlert('danger', 'Please upload the NESA document as proof of registration');
                return;
            }
            
            // Continue with form submission if document is uploaded
            submitNesaStatusForm(studentId, status, sendEmail, reason, documentInput);
        }, 100);
        
        return; // Exit early, we'll continue in the timeout
    }
    
    // For failed status, proceed directly
    submitNesaStatusForm(studentId, status, sendEmail, reason, null);
}

// Separate function to handle the actual form submission
function submitNesaStatusForm(studentId, status, sendEmail, reason, documentInput) {
    if (!confirm(`Update NESA status to "${status}" for this student?`)) return;
    
    // Find the button that was clicked
    const button = event.target.closest('button') || event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Updating...';
    
    const formData = new FormData();
    formData.append('status', status);
    formData.append('send_email', sendEmail ? '1' : '0'); // Send as '1' or '0' for proper boolean handling
    
    if (status === 'failed') {
        formData.append('reason', reason);
    } else if (documentInput && documentInput.files && documentInput.files.length > 0) {
        formData.append('nesa_document', documentInput.files[0]);
        console.log('Appending document:', documentInput.files[0].name);
    }
    
    // Log FormData contents for debugging
    console.log('FormData contents:');
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    fetch(`/staff/students/${studentId}/update-nesa-status`, {
        method: 'POST',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData,
        credentials: 'same-origin'
    })
    .then(async response => {
        console.log('Response status:', response.status);
        const ct = response.headers.get('content-type') || '';
        let data;
        if (ct.includes('application/json')) {
            data = await response.json();
        } else {
            const text = await response.text();
            console.log('Non-JSON response:', text);
            try { data = JSON.parse(text); } catch { data = { success: response.ok, message: response.ok ? 'Status updated successfully' : 'Update failed' }; }
        }
        return { ok: response.ok, data };
    })
    .then(({ ok, data }) => {
        console.log('Response data:', data);
        if (ok && data.success !== false) {
            bootstrap.Modal.getInstance(document.getElementById(`nesaStatusModal${studentId}`))?.hide();
            showAlert('success', data.message || 'NESA status updated successfully');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            console.error('Update failed:', data);
            
            // Handle validation errors specifically
            if (data.errors) {
                let errorMessage = 'Validation errors:\n';
                for (const [field, messages] of Object.entries(data.errors)) {
                    errorMessage += `${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}\n`;
                }
                console.error('Validation errors:', data.errors);
                showAlert('danger', errorMessage || 'Validation failed. Please check all required fields.');
            } else {
                showAlert('danger', data.message || data.error || 'Failed to update NESA status. Please try again.');
            }
        }
    })
    .catch(err => {
        console.error('NESA status update error:', err);
        showAlert('danger', 'Failed to update NESA status. Please check your connection and try again.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
};

$(function() {
    let searchTimeout;
    
    // Live search functionality
    function performSearch(page = 1) {
        const searchData = {
            name: $('#searchName').val(),
            reg_number: $('#searchRegNumber').val(),
            email: $('#searchEmail').val(),
            telephone: $('#searchTelephone').val(),
            course: $('#searchCourse').val(),
            page: page
        };

        $('#loadingIndicator').show();
        
        $.ajax({
            url: "{{ route('students.index') }}",
            method: 'GET',
            data: searchData,
            success: function(response) {
                $('#studentsTableWrapper').html(response);
                
                const urlParams = new URLSearchParams(searchData);
                const newUrl = "{{ route('students.index') }}?" + urlParams.toString();
                window.history.pushState({path: newUrl}, '', newUrl);
                
                initializeModals();
                initializePaginationLinks();
                
                $('#loadingIndicator').hide();
            },
            error: function(xhr) {
                console.error('Search failed:', xhr);
                $('#loadingIndicator').hide();
                alert('Search failed. Please try again.');
            }
        });
    }

    // Debounced live search on input
    $('#liveSearchForm input').on('keyup', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            performSearch();
        }, 500);
    });

    // Reset button
    $('#resetBtn').on('click', function() {
        $('#liveSearchForm')[0].reset();
        performSearch();
    });

    // Pagination click handler
    function initializePaginationLinks() {
        $(document).off('click', '#paginationLinks a, .pagination a');
        $(document).on('click', '#paginationLinks a, .pagination a', function(e) {
            e.preventDefault();
            const url = $(this).attr('href');
            const page = new URL(url).searchParams.get('page') || 1;
            performSearch(page);
        });
    }

    // Initialize modals
    function initializeModals() {
        // Transcript modal
        $('[id^=transcriptModal]').off('show.bs.modal').on('show.bs.modal', function (event) {
            var modal = $(this);
            var studentId = modal.attr('id').replace('transcriptModal', '');
            var body = modal.find('.transcript-modal-body');
            body.html('<div class="text-center"><span class="spinner-border"></span> Loading...</div>');
            $.get("{{ url('/students') }}/" + studentId + "/transcript/ajax", function(data) {
                body.html(data);
            }).fail(function() {
                body.html('<div class="alert alert-danger">Failed to load transcript.</div>');
            });
        });
        
        // NESA Status modal - re-initialize radio button listeners when opened
        $('[id^=nesaStatusModal]').off('show.bs.modal').on('show.bs.modal', function (event) {
            // Re-initialize NESA modal listeners for this specific modal
            initializeNesaModalListeners();
        });
        
        // NESA Status modal radio button listeners
        initializeNesaModalListeners();
    }
    
    // Initialize NESA modal listeners
    function initializeNesaModalListeners() {
        // Add event listeners for NESA status radio buttons
        document.querySelectorAll('[id^="registered_"], [id^="failed_"]').forEach(radio => {
            radio.removeEventListener('change', handleNesaRadioChange);
            radio.addEventListener('change', handleNesaRadioChange);
        });
        
        // Initialize visibility based on current selection for both registered and failed
        document.querySelectorAll('[id^="registered_"], [id^="failed_"]').forEach(radio => {
            if (radio.checked) {
                console.log('Initializing radio:', radio.id, 'checked:', radio.checked);
                handleNesaRadioChange.call(radio);
            }
        });
        
        // Additional check: ensure document section is visible if registered is checked
        document.querySelectorAll('[id^="registered_"]').forEach(radio => {
            if (radio.checked) {
                const studentId = radio.id.split('_').pop();
                const documentSection = document.getElementById(`document_section_${studentId}`);
                if (documentSection) {
                    documentSection.style.display = 'block';
                    console.log('Force showing document section on modal open for student:', studentId);
                }
            }
        });
    }
    
    // Handle NESA radio button change
    function handleNesaRadioChange() {
        const studentId = this.id.split('_').pop();
        const reasonSection = document.getElementById(`reason_section_${studentId}`);
        const documentSection = document.getElementById(`document_section_${studentId}`);
        
        console.log('Radio button changed:', this.id, this.value, 'studentId:', studentId);
        
        if (this.value === 'failed') {
            if (reasonSection) reasonSection.style.display = 'block';
            if (documentSection) documentSection.style.display = 'none';
        } else {
            if (reasonSection) reasonSection.style.display = 'none';
            if (documentSection) {
                documentSection.style.display = 'block';
                console.log('Document section made visible for student:', studentId);
            }
        }
    }

    // Initialize on page load
    initializeModals();
    initializePaginationLinks();
    
    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
});

// ─── NESA Bulk Functions ───────────────────────────────────────────────────────

function downloadAllNesaDocuments() {
    if (!confirm('Download all NESA documents for registered students? This may take some time if there are many documents.')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Preparing...';
    
    fetch('/staff/students/download-all-nesa-documents', {
        method: 'GET',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Download failed');
        }
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'All_NESA_Documents.zip';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showAlert('success', 'All NESA documents downloaded successfully');
    })
    .catch(error => {
        console.error('Bulk download error:', error);
        showAlert('danger', 'Failed to download NESA documents. Please try again.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

function downloadNesaReport() {
    if (!confirm('Export NESA registration report to Excel?')) return;
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Exporting...';
    
    // Get current search parameters
    const searchParams = new URLSearchParams(window.location.search);
    
    fetch('/staff/students/export-nesa-report?' + searchParams.toString(), {
        method: 'GET',
        headers: { 
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Export failed');
        }
        return response.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'NESA_Registration_Report.xlsx';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        document.body.removeChild(a);
        
        showAlert('success', 'NESA report exported successfully');
    })
    .catch(error => {
        console.error('Report export error:', error);
        showAlert('danger', 'Failed to export NESA report. Please try again.');
    })
    .finally(() => {
        button.disabled = false;
        button.innerHTML = originalText;
    });
}

// ─── Utility ────────────────────────────────────────────────────────────────

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '';
}

function showAlert(type, message) {
    const icons = { success: 'check-circle', danger: 'exclamation-circle', warning: 'exclamation-triangle' };
    const titles = { success: 'Success', danger: 'Error', warning: 'Warning' };

    const html = `
        <div class="alert alert-${type} alert-dismissible fade show alert-enhanced d-flex align-items-start" role="alert">
            <div class="alert-icon"><i class="fas fa-${icons[type] || 'info-circle'}"></i></div>
            <div class="alert-content">
                <div class="alert-title">${titles[type] || type}</div>
                <p class="alert-message mb-0">${message}</p>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`;

    let container = document.querySelector('.alert-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'alert-container';
        document.body.insertBefore(container, document.body.firstChild);
    }
    container.innerHTML = html;
    window.scrollTo({ top: 0, behavior: 'smooth' });

    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) { alert.classList.remove('show'); setTimeout(() => alert.remove(), 300); }
    }, 5000);
}
</script>
@endsection
