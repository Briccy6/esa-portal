@extends('staff.partials.sidebar_sec')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Generate Professional Letter</h1>
                <a href="{{ route('to-whom.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Letters
                </a>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Letter Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('to-whom.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                                    <input type="hidden" name="student_id" id="student_id" required>
                                    <input type="text" id="student_search" class="form-control" placeholder="Search student by name or registration number..." autocomplete="off">
                                    <div id="student_suggestions" class="list-group mt-2" style="position: absolute; width: calc(100% - 30px); max-height: 300px; overflow-y: auto; display: none; z-index: 1000;"></div>
                                    <div id="selected_student" class="mt-2 p-2 bg-light rounded" style="display: none;">
                                        <strong id="selected_student_text"></strong>
                                    </div>
                                    @error('student_id')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="letter_type" class="form-label">Letter Type <span class="text-danger">*</span></label>
                                    <select name="letter_type" id="letter_type" class="form-select" required>
                                        <option value="">Select Letter Type</option>
                                        @foreach($letterTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('letter_type')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="recipient_title" class="form-label">Recipient Title <span class="text-danger">*</span></label>
                                    <select name="recipient_title" id="recipient_title" class="form-select" required>
                                        <option value="">Select Recipient</option>
                                        @foreach($recipientTitles as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('recipient_title')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="purpose" class="form-label">Purpose <span class="text-danger">*</span></label>
                                    <textarea name="purpose" id="purpose" class="form-control" rows="3" required
                                              placeholder="e.g., Employment verification, visa application, scholarship application, etc."></textarea>
                                    @error('purpose')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="letter_date" class="form-label">Letter Date <span class="text-danger">*</span></label>
                                    <input type="date" name="letter_date" id="letter_date" class="form-control" 
                                           value="{{ date('Y-m-d') }}" required>
                                    @error('letter_date')
                                        <div class="text-danger small">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('to-whom.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-file-alt"></i> Generate Letter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Letter Type Information -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Letter Types</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Available Letter Types:</h6>
                            <ul class="list-unstyled">
                                @foreach($letterTypes as $value => $label)
                                    <li class="mb-2">
                                        <strong>{{ $label }}:</strong>
                                        <p class="text-muted small mb-0">
                                            @switch($value)
                                                @case('enrollment')
                                                    Confirms current enrollment status and student details.
                                                    @break
                                                @case('academic_standing')
                                                    Verifies academic performance and standing.
                                                    @break
                                                @case('financial_status')
                                                    Certifies financial account status.
                                                    @break
                                                @case('completion')
                                                    Confirms course/program completion.
                                                    @break
                                                @case('good_standing')
                                                    Certifies good standing with the institution.
                                                    @break
                                                @case('custom')
                                                    Custom letter for specific requirements.
                                                    @break
                                            @endswitch
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Recipient Options:</h6>
                            <ul class="list-unstyled">
                                @foreach($recipientTitles as $value => $label)
                                    <li class="mb-1">
                                        <i class="fas fa-check-circle text-success"></i>
                                        {{ $label }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('to-whom.store') }}"]');
    const studentSearch = document.getElementById('student_search');
    const studentId = document.getElementById('student_id');
    const suggestions = document.getElementById('student_suggestions');
    const selectedStudent = document.getElementById('selected_student');
    const selectedStudentText = document.getElementById('selected_student_text');
    
    let debounceTimer;
    
    // Handle student search autocomplete
    studentSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            suggestions.style.display = 'none';
            return;
        }
        
        debounceTimer = setTimeout(function() {
            fetch(`{{ route('to-whom.search-students') }}?q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    suggestions.innerHTML = '';
                    
                    if (data.length === 0) {
                        suggestions.innerHTML = '<div class="list-group-item">No students found</div>';
                        suggestions.style.display = 'block';
                        return;
                    }
                    
                    data.forEach(student => {
                        const item = document.createElement('a');
                        item.href = '#';
                        item.className = 'list-group-item list-group-item-action';
                        item.innerHTML = `
                            <div class="d-flex w-100 justify-content-between">
                                <strong>${student.full_name}</strong>
                                <small>${student.reg_number}</small>
                            </div>
                            <small class="text-muted">${student.branch} • ${student.course}</small>
                        `;
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            studentId.value = student.id;
                            studentSearch.value = '';
                            selectedStudentText.textContent = `${student.full_name} (${student.reg_number})`;
                            selectedStudent.style.display = 'block';
                            suggestions.style.display = 'none';
                        });
                        suggestions.appendChild(item);
                    });
                    
                    suggestions.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });
    
    // Close suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target !== studentSearch && !suggestions.contains(e.target)) {
            suggestions.style.display = 'none';
        }
    });
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!studentId.value) {
            alert('Please select a student');
            return;
        }
        
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData,
            credentials: 'same-origin'
        })
        .then(response => {
            if (response.status === 422) {
                // Outstanding fees error
                return response.json().then(data => {
                    if (data.error === 'Student has outstanding fees') {
                        alert('⚠️ Cannot Generate Letter\n\n' + data.message);
                        throw new Error('Outstanding fees');
                    }
                    throw new Error('Validation error');
                });
            }
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // If successful, redirect by submitting form normally
            form.submit();
        })
        .catch(error => {
            if (error.message !== 'Outstanding fees') {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-weight: 600;
        color: #495057;
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .card {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush
