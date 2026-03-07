@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Generate New Report Card</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.report-cards.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="student_id">Student *</label>
                                    <select name="student_id" id="student_id" class="form-control @error('student_id') is-invalid @enderror" required>
                                        <option value="">Select Student</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->full_name }} - {{ $student->reg_number ?? 'No Reg No' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="course_id">Course *</label>
                                    <select name="course_id" id="course_id" class="form-control @error('course_id') is-invalid @enderror" required>
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                {{ $course->course_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('course_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="academic_year_id">Academic Year *</label>
                                    <select name="academic_year_id" id="academic_year_id" class="form-control @error('academic_year_id') is-invalid @enderror" required>
                                        <option value="">Select Academic Year</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" {{ old('academic_year_id') == $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                                @if($year->is_ongoing) <span class="badge badge-success">Current</span> @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('academic_year_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="term_id">Term *</label>
                                    <select name="term_id" id="term_id" class="form-control @error('term_id') is-invalid @enderror" required>
                                        <option value="">Select Term</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term->id }}" {{ old('term_id') == $term->id ? 'selected' : '' }}>
                                                Term {{ $term->term }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term_id')
                                        <span class="invalid-feedback" role="alert">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>System Information</label>
                                    <div class="alert alert-info">
                                        <small>
                                            <i class="fas fa-info-circle"></i>
                                            The system will automatically fetch all exam and test marks for the selected student and academic period.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Generate Report Card
                                    </button>
                                    <a href="{{ route('admin.report-cards.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Back
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-select course when student is selected
    $('#student_id').change(function() {
        var studentId = $(this).val();
        if (studentId) {
            // Get student info and auto-fill course
            $.get('/api/students/' + studentId + '/course', function(data) {
                if (data.course_id) {
                    $('#course_id').val(data.course_id);
                }
                if (data.academic_year_id) {
                    $('#academic_year_id').val(data.academic_year_id);
                }
            });
        }
    });
});
</script>
@endpush
