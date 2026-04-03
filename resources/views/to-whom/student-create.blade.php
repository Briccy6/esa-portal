@extends('students.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">Generate Professional Letter</h1>
                <a href="{{ route('to-whom.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Letters
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Letter Details</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('to-whom.store') }}" method="POST">
                        @csrf
                        @if($students->isNotEmpty())
                            <input type="hidden" name="student_id" value="{{ $students->first()->id }}" />
                        @endif
                        
                        <div class="row">
                            <div class="col-md-6">
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
            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">Available Letter Types</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Letter Types:</h6>
                            <ul class="list-unstyled">
                                @foreach($letterTypes as $value => $label)
                                    <li class="mb-2">
                                        <strong>{{ $label }}:</strong>
                                        <p class="text-muted small mb-0">
                                            @switch($value)
                                                @case('enrollment')
                                                    Confirms your current enrollment status and student details.
                                                    @break
                                                @case('academic_standing')
                                                    Verifies your academic performance and standing.
                                                    @break
                                                @case('financial_status')
                                                    Certifies your financial account status.
                                                    @break
                                                @case('completion')
                                                    Confirms your course/program completion.
                                                    @break
                                                @case('good_standing')
                                                    Certifies your good standing with the institution.
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
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
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
