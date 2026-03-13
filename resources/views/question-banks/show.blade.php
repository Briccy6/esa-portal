@extends('admin.layout')

@section('title', 'Question Bank Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $questionBank->name }}</h5>
                    <div class="btn-group">
                        <a href="{{ route('admin.question-banks.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                        <a href="{{ route('admin.question-banks.add-question', $questionBank) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </a>
                        <a href="{{ route('admin.question-banks.edit', $questionBank) }}" class="btn btn-outline-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($message = Session::get('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($message = Session::get('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ $message }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('errors'))
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <strong>Import Issues:</strong>
                            <ul class="mb-0">
                                @foreach(session('errors') as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Question Bank Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Information</h6>
                            <p><strong>Description:</strong> {{ $questionBank->description ?? 'N/A' }}</p>
                            <p><strong>Course:</strong> {{ $questionBank->course?->course_name ?? 'N/A' }}</p>
                            <p><strong>Academic Year:</strong> {{ $questionBank->academicYear?->year ?? 'N/A' }}</p>
                            <p><strong>Teacher:</strong> {{ $questionBank->teacher?->teacher_name ?? 'N/A' }}</p>
                            <p><strong>Status:</strong> 
                                @if($questionBank->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Statistics</h6>
                            <p><strong>Total Questions:</strong> {{ $questionBank->questions->count() }}</p>
                            <p><strong>Created:</strong> {{ $questionBank->created_at->format('M d, Y') }}</p>
                            <p><strong>Last Updated:</strong> {{ $questionBank->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <!-- CSV Import/Export -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6>Import/Export Questions</h6>
                            <div class="d-flex gap-2">
                                <form action="{{ route('admin.question-banks.import', $questionBank) }}" method="POST" enctype="multipart/form-data" class="d-flex gap-2">
                                    @csrf
                                    <div class="input-group" style="max-width: 400px;">
                                        <input type="file" name="csv_file" class="form-control" accept=".csv" required>
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-upload"></i> Import CSV
                                        </button>
                                    </div>
                                </form>
                                <a href="{{ route('admin.question-banks.export', $questionBank) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-download"></i> Export CSV
                                </a>
                                <a href="{{ asset('questions_template.csv') }}" class="btn btn-outline-info" target="_blank">
                                    <i class="fas fa-file-alt"></i> Download Template
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="row">
                        <div class="col-12">
                            <h6>Questions ({{ $questionBank->questions->count() }})</h6>
                            @if($questionBank->questions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Question</th>
                                                <th>Type</th>
                                                <th>Options</th>
                                                <th>Correct Answer</th>
                                                <th>Marks</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($questionBank->questions as $index => $question)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ Str::limit($question->text, 100) }}</td>
                                                    <td>
                                                        <span class="badge bg-info">{{ $question->question_type }}</span>
                                                    </td>
                                                    <td>
                                                        @if(in_array($question->question_type, ['multiple_choice', 'multiple_select']))
                                                            <small>
                                                                A) {{ $question->option_a }}<br>
                                                                B) {{ $question->option_b }}<br>
                                                                @if($question->option_c) C) {{ $question->option_c }}<br>@endif
                                                                @if($question->option_d) D) {{ $question->option_d }}@endif
                                                            </small>
                                                        @else
                                                            <span class="text-muted">N/A</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <strong>{{ $question->correct_answer }}</strong>
                                                        @if($question->correct_answers)
                                                            <br><small>{{ implode(', ', $question->correct_answers) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $question->marks }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button class="btn btn-outline-primary" onclick="viewQuestion({{ $question->id }})">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <form action="#" method="POST" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Delete this question?')">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <p class="text-muted">No questions in this bank yet.</p>
                                    <a href="{{ route('admin.question-banks.add-question', $questionBank) }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Add First Question
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Question Detail Modal -->
<div class="modal fade" id="questionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Question Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="questionModalBody">
                <!-- Question details will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function viewQuestion(questionId) {
    // This would typically load question details via AJAX
    // For now, just show a simple alert
    alert('Question ID: ' + questionId + '\n\nFull question details would be loaded here.');
}
</script>
@endsection
