@extends('staff.partials.sidebar_dos')

@section('title', 'Exam Details - ' . $exam->title)

@section('content')
<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="bi bi-clipboard-data-fill me-2"></i> Exam Details
        </h2>
        <div class="d-flex gap-2">
            <a href="{{ route('dos.exams.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Exams
            </a>
            <button onclick="window.open('/dos/exams/{{ $exam->id }}/print', '_blank')" class="btn btn-success">
                <i class="bi bi-printer me-1"></i> Print Exam
            </button>
            <a href="{{ route('dos.exams.edit', $exam->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-1"></i> Edit Exam
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">{{ $exam->title }}</h4>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Subject</th>
                            <td>{{ $exam->subject ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Exam Type</th>
                            <td>{{ ucfirst($exam->exam_type) }}</td>
                        </tr>
                        <tr>
                            <th>Total Marks</th>
                            <td>{{ $exam->total_marks }}</td>
                        </tr>
                        <tr>
                            <th>Exam Weight</th>
                            <td>{{ $exam->exam_weight }}%</td>
                        </tr>
                        <tr>
                            <th>Class Date</th>
                            <td>{{ $exam->class_date ? \Carbon\Carbon::parse($exam->class_date)->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">Course</th>
                            <td>
                                @if($exam->course)
                                    {{ $exam->course->course_name }}
                                @elseif($exam->courses->count() > 0)
                                    {{ $exam->courses->first()->course_name }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Lesson</th>
                            <td>
                                @if($exam->lesson)
                                    {{ $exam->lesson->title }}
                                @elseif($exam->lessons->count() > 0)
                                    {{ $exam->lessons->first()->title }}
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Teacher</th>
                            <td>{{ $exam->teacher->teacher_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Branch</th>
                            <td>{{ $exam->branch->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($exam->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            @if($exam->description)
            <div class="mt-4">
                <h5>Description</h5>
                <p>{{ $exam->description }}</p>
            </div>
            @endif

            @if($exam->notes)
            <div class="mt-4">
                <h5>Notes</h5>
                <p>{{ $exam->notes }}</p>
            </div>
            @endif

            <div class="mt-4">
                <h5>Associated Courses</h5>
                @if($exam->courses->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($exam->courses as $course)
                            <span class="badge bg-info">{{ $course->course_name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No courses associated</p>
                @endif
            </div>

            <div class="mt-4">
                <h5>Associated Lessons</h5>
                @if($exam->lessons->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($exam->lessons as $lesson)
                            <span class="badge bg-warning">{{ $lesson->title }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No lessons associated</p>
                @endif
            </div>

            <div class="mt-4">
                <h5>Questions</h5>
                @if($exam->questions && $exam->questions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Question</th>
                                    <th>Type</th>
                                    <th>Marks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exam->questions as $index => $question)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $question->question_text ?? $question->text ?? 'N/A' }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $question->question_type ?? 'N/A')) }}</td>
                                    <td>{{ $question->marks ?? 0 }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">No questions added yet</p>
                    <a href="{{ route('dos.exams.questions.index', $exam->id) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add Questions
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
