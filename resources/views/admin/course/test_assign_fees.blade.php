@extends('admin.layout')

@section('title', 'Test Term Fees')

@section('content')
<div class="container py-5">
    <h2>Debug: Term Fees Assignment</h2>
    
    <!-- Debug Information -->
    <div class="card mb-4">
        <div class="card-header">
            <h5>Debug Information</h5>
        </div>
        <div class="card-body">
            <p><strong>Course ID:</strong> {{ $course->id ?? 'N/A' }}</p>
            <p><strong>Course Name:</strong> {{ $course->course_name ?? 'N/A' }}</p>
            <p><strong>Terms Count:</strong> {{ $terms->count() ?? 'N/A' }}</p>
            
            @if(isset($terms) && $terms->count() > 0)
                <h6>Available Terms:</h6>
                <ul>
                    @foreach($terms as $term)
                        <li>Term ID: {{ $term->id }}, Name: {{ $term->name }}, Year: {{ $term->academicYear->year ?? 'N/A' }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-danger">❌ No terms available or $terms is not set</p>
            @endif
        </div>
    </div>
    
    <!-- Simplified Test Form -->
    <div class="card">
        <div class="card-header">
            <h5>Test Form</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.course.store-fees', $course->id) }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label class="form-label">Select Term</label>
                    <select name="term_fees[0][term_id]" class="form-select" required>
                        <option value="">Choose a term...</option>
                        @if(isset($terms))
                            @foreach($terms as $term)
                                <option value="{{ $term->id }}">
                                    {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                
                <div class="row">
                    <div class="col-md-3">
                        <label class="form-label">Registration Fee</label>
                        <input type="number" name="term_fees[0][registration_fee]" class="form-control" min="0" step="0.01" value="10000" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tuition Fee</label>
                        <input type="number" name="term_fees[0][tuition_fee]" class="form-control" min="0" step="0.01" value="50000" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Practical Fee</label>
                        <input type="number" name="term_fees[0][practical_fee]" class="form-control" min="0" step="0.01" value="0" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Trip Fee</label>
                        <input type="number" name="term_fees[0][trip_fee]" class="form-control" min="0" step="0.01" value="0" required>
                    </div>
                </div>
                
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Test Submit
                    </button>
                    <a href="{{ route('course.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
