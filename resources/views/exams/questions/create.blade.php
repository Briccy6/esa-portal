@extends(
    auth('admin')->check() ? 'admin.questions.create' :
    (auth('staff')->check() ? 'staff.questions.create' :
    (auth('teacher')->check() ? 'teacher.questions.create' :
    'layouts.app'))
)

@section('content')
<div class="container py-4">
    <h2>Add Question to Exam: {{ $exam->title ?? 'Exam' }}</h2>
    <form method="POST" action="{{ route('exams.questions.store', $exam->id) }}">
        @csrf
        <div class="mb-3">
            <label class="form-label">Question Text</label>
            <input type="text" name="text" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Option A</label>
            <input type="text" name="option_a" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Option B</label>
            <input type="text" name="option_b" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Option C</label>
            <input type="text" name="option_c" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Option D</label>
            <input type="text" name="option_d" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Correct Answer</label>
            <input type="text" name="correct_answer" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Marks</label>
            <input type="number" name="marks" class="form-control" value="1" min="1">
        </div>
        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="{{ route('exams.questions', $exam->id) }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection