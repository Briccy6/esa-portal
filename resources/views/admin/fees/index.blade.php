@extends('admin.layout')

@section('content')
<div class="container mt-5">
    <h2>Set School Fees</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.fees.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="academic_year_id" class="form-label">Academic Year</label>
            <select name="academic_year_id" id="academic_year_id" class="form-select" required>
                <option value="">-- Select Academic Year --</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                        {{ $year->year }}
                    </option>
                @endforeach
            </select>
            @error('academic_year_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="term_id" class="form-label">Term</label>
            <select name="term_id" id="term_id" class="form-select" required>
                <option value="">-- Select Term --</option>
                <!-- Terms will be loaded dynamically based on selected academic year -->
            </select>
            @error('term_id')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="amount" class="form-label">Amount (RWF)</label>
            <input type="number" name="amount" id="amount" class="form-control" value="{{ old('amount') }}" required min="0" step="0.01" />
            @error('amount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Set Fee</button>
    </form>
</div>

<script>
document.getElementById('academic_year_id').addEventListener('change', function() {
    let yearId = this.value;
    let termSelect = document.getElementById('term_id');
    
    // Show loading state
    termSelect.innerHTML = '<option value="">Loading terms...</option>';
    termSelect.disabled = true;

    if (!yearId) {
        termSelect.innerHTML = '<option value="">-- Select Term --</option>';
        termSelect.disabled = false;
        return;
    }

    // Debug: Log the request
    console.log('Fetching terms for academic year ID:', yearId);
    
    // Make the API call
    fetch(`/api/academic-years/${yearId}/terms`)
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Terms data received:', data);
            
            // Clear loading state
            termSelect.disabled = false;
            
            if (Array.isArray(data) && data.length > 0) {
                let options = '<option value="">-- Select Term --</option>';
                data.forEach(term => {
                    options += `<option value="${term.id}">${term.term} (Starts: ${term.starting_date})</option>`;
                });
                termSelect.innerHTML = options;
            } else {
                termSelect.innerHTML = '<option value="">-- No terms found for this academic year --</option>';
            }
        })
        .catch(error => {
            console.error('Error fetching terms:', error);
            termSelect.disabled = false;
            termSelect.innerHTML = '<option value="">-- Failed to load terms --</option>';
            
            // Show user-friendly error
            alert('Failed to load terms. Please check the console for details or try refreshing the page.');
        });
});

// Restore selected values after form validation errors
document.addEventListener('DOMContentLoaded', function() {
    const academicYearSelect = document.getElementById('academic_year_id');
    const termSelect = document.getElementById('term_id');
    
    // If there's an old term_id value (from form validation), trigger the academic year change
    const oldTermId = '{{ old("term_id") }}';
    if (academicYearSelect.value && oldTermId) {
        // Trigger the change event to load terms
        academicYearSelect.dispatchEvent(new Event('change'));
        
        // After a brief delay, set the selected term
        setTimeout(() => {
            termSelect.value = oldTermId;
        }, 500);
    }
});
</script>
@endsection