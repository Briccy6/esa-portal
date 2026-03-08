@extends(
    auth('admin')->check() ? 'admin.layout' :
    (auth('teacher')->check() ? 'teacher.layout' :
    (auth('student')->check() ? 'student.layout' : 'staff.partials.sidebar_dos'))
)

@section('content')
<div class="container-fluid px-2 px-md-3 py-4">
    <h2 class="mb-4">Questions for Exam: {{ $exam->title ?? 'Exam' }}</h2>

    <!-- Add & Import Buttons -->
    <div class="mb-3 d-flex gap-2">
        <a href="{{ route('exams.questions.create', $exam->id) }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Question
        </a>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="bi bi-upload"></i> Import Questions
        </button>
    </div>

    @if($exam->questions->count())
        <ul class="list-group mb-4">
            @foreach($exam->questions as $question)
                <li class="list-group-item">
                    {{ $question->text ?? $question->question }}
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-info">
            No questions found for this exam.
        </div>
    @endif
    <a href="{{ 
        auth('admin')->check() ? route('admin.exams.index') :
        (auth('teacher')->check() ? route('teacher.exams.index') :
        (auth('staff')->check() ? route('dos.exams.index') : route('public.exams.index'))
    ) }}" class="btn btn-secondary">Back to Exams</a>
</div>

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('exams.questions.import', $exam->id) }}" enctype="multipart/form-data" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="importModalLabel">Import Questions</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="questionsFile" class="form-label">Upload CSV or Excel File</label>
          <input type="file" class="form-control" id="questionsFile" name="questions_file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required>
          <div class="form-text">
              Download a <a href="{{ asset('questions_template.csv') }}" download>sample template</a>.
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Import</button>
      </div>
    </form>
  </div>
</div>
@endsection