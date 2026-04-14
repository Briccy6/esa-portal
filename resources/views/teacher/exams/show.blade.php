@extends('teacher.layout')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Exam Details</h1>
        <div>
            <a href="{{ route('teacher.exams.index') }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left me-2"></i>Back to Exams
            </a>
            @if(auth()->check() && auth()->user()->id === $exam->teacher_id)
                <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>Edit Exam
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <!-- Exam Details -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $exam->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Description:</strong>
                            <p>{{ $exam->description ?: 'No description provided' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>Exam Type:</strong>
                            <p><span class="badge bg-info">{{ $exam->exam_type }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Total Marks:</strong>
                            <p>{{ $exam->total_marks }}</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Exam Weight:</strong>
                            <p>{{ $exam->exam_weight }}%</p>
                        </div>
                        <div class="col-md-4">
                            <strong>Exam Date:</strong>
                            <p>{{ $exam->class_date->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong>
                            <p>
                                @if($exam->is_public)
                                    <span class="badge bg-success">Public</span>
                                @else
                                    <span class="badge bg-warning">Private</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>Exam Password:</strong>
                            <p>{{ $exam->exam_password ?: 'No password set' }}</p>
                        </div>
                    </div>

                    @if($exam->notes)
                        <div class="mb-3">
                            <strong>Notes:</strong>
                            <p>{{ $exam->notes }}</p>
                        </div>
                    @endif

                    <!-- Courses and Lessons -->
                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Associated Courses</h6>
                        @if($exam->courses->count() > 0)
                            <div>
                                @foreach($exam->courses as $course)
                                    <span class="badge bg-secondary me-2">{{ $course->course_name }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No courses assigned</p>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h6 class="text-primary mb-2">Associated Lessons</h6>
                        @if($exam->lessons->count() > 0)
                            <div>
                                @foreach($exam->lessons as $lesson)
                                    <span class="badge bg-secondary me-2">{{ $lesson->lesson_title }}</span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">No lessons assigned</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('teacher.exams.questions', $exam) }}" class="btn btn-outline-info">
                            <i class="bi bi-question-circle me-2"></i>Manage Questions
                        </a>
                        @if(auth()->check() && auth()->user()->id === $exam->teacher_id)
                            <a href="{{ route('teacher.exams.edit', $exam) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-2"></i>Edit Exam
                            </a>
                            @if(!$exam->is_public)
                                <button class="btn btn-outline-success" onclick="makePublic()">
                                    <i class="bi bi-globe me-2"></i>Make Public
                                </button>
                            @endif
                            <form action="{{ route('teacher.exams.destroy', $exam) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger w-100" 
                                        onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone and will remove all associated data.')">
                                    <i class="bi bi-trash me-2"></i>Delete Exam
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Academic Information -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="card-title mb-0">Academic Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Branch:</strong>
                        <p>{{ $exam->branch ? $exam->branch->branch_name : 'Not specified' }}</p>
                    </div>
                    <div class="mb-2">
                        <strong>Academic Year:</strong>
                        <p>{{ $exam->academicYear ? $exam->academicYear->year : 'Not specified' }}</p>
                    </div>
                    <div class="mb-2">
                        <strong>Term:</strong>
                        <p>{{ $exam->term ? $exam->term->term_name : 'Not specified' }}</p>
                    </div>
                    <div class="mb-2">
                        <strong>Created by:</strong>
                        <p>{{ $exam->teacher ? $exam->teacher->teacher_name : 'Not specified' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function makePublic() {
    if(confirm('Are you sure you want to make this exam public? Students will be able to access and take this exam.')) {
        // You can implement the make public functionality here
        alert('Make public functionality coming soon!');
    }
}
</script>
@endsection
