@extends('teacher.layout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Edit Marks - {{ $lesson->title }}</h4>
                    <a href="{{ route('teacher.marks.view') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to Marks
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i>
                        <strong>Test Date:</strong> {{ \Carbon\Carbon::parse($testDate)->format('d M Y') }}
                    </div>

                    <form id="editMarksForm" method="POST" action="{{ route('teacher.marks.update', [$lesson->id, $testDate]) }}">
                        @csrf
                        @method('PUT')
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Original Mark</th>
                                        <th>Normalized (100%)</th>
                                        <th>New Mark</th>
                                        <th>Max Mark</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($marks as $mark)
                                        <tr>
                                            <td>{{ $mark->student->full_name ?? 'N/A' }}</td>
                                            <td>{{ $mark->course->course_name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="text-muted">{{ $mark->value }}/{{ $mark->marks_out_of }}</span>
                                            </td>
                                            <td><strong>{{ $mark->normalized_percentage }}</strong></td>
                                            <td>
                                                <input type="number" 
                                                       name="marks[{{ $mark->id }}][value]" 
                                                       value="{{ $mark->value }}" 
                                                       class="form-control mark-input" 
                                                       min="0" 
                                                       max="{{ $mark->marks_out_of }}"
                                                       required>
                                                <input type="hidden" name="marks[{{ $mark->id }}][marks_out_of]" value="{{ $mark->marks_out_of }}">
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $mark->marks_out_of }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('teacher.marks.view') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Update Marks
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editMarksForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    var form = e.target;
    var formData = new FormData(form);
    var submitBtn = form.querySelector('button[type="submit"]');
    var originalText = submitBtn.innerHTML;
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Updating...';
    
    fetch(form.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Show success message
            var alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.parentElement.insertBefore(alertDiv, form);
            
            // Redirect back to view marks after 2 seconds
            setTimeout(() => {
                window.location.href = '{{ route("teacher.marks.view") }}';
            }, 2000);
        } else {
            // Show error message
            var alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-circle"></i> ${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            form.parentElement.insertBefore(alertDiv, form);
        }
    })
    .catch(error => {
        // Show error message
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-circle"></i> Error: ${error.message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        form.parentElement.insertBefore(alertDiv, form);
    })
    .finally(() => {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Live validation for mark inputs
document.addEventListener('DOMContentLoaded', function() {
    var markInputs = document.querySelectorAll('.mark-input');
    
    markInputs.forEach(function(input) {
        var errorSpan = document.createElement('span');
        errorSpan.className = 'text-danger small d-block mt-1';
        errorSpan.style.display = 'none';
        input.parentNode.appendChild(errorSpan);

        input.addEventListener('input', function() {
            var max = parseInt(input.getAttribute('max'), 10);
            var value = parseInt(input.value, 10);
            
            if (value > max) {
                input.classList.add('is-invalid');
                errorSpan.textContent = `Mark cannot be greater than ${max}.`;
                errorSpan.style.display = 'block';
                input.value = max;
            } else if (value < 0) {
                input.classList.add('is-invalid');
                errorSpan.textContent = `Mark cannot be negative.`;
                errorSpan.style.display = 'block';
                input.value = 0;
            } else {
                input.classList.remove('is-invalid');
                errorSpan.textContent = '';
                errorSpan.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
