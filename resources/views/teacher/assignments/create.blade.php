@extends('teacher.layout')

@section('content')
<style>
    .assignment-form-page { max-width: 1000px; margin: 0 auto; padding: clamp(1rem, 3vw, 2rem); color: #172a3d; }
    .assignment-form-hero { padding: clamp(1.3rem, 4vw, 2.2rem); margin-bottom: 1.25rem; border-radius: 20px; color: #f7f5eb; background: radial-gradient(circle at 90% 10%, #446b5a 0, transparent 35%), linear-gradient(125deg, #162e36, #23453f); }
    .assignment-form-hero h1 { margin: .3rem 0; font: 700 clamp(1.6rem, 4vw, 2.3rem)/1.1 Georgia, serif; }
    .assignment-form-hero p { margin: 0; color: #d3ded5; }
    .assignment-form-card { padding: clamp(1rem, 3vw, 2rem); border: 1px solid #dfe7df; border-radius: 17px; background: #fff; box-shadow: 0 10px 24px #172a3d0a; }
    .assignment-form-card .form-label { color: #354e40; font-weight: 700; }
    .assignment-form-card .form-control, .assignment-form-card .form-select { min-height: 46px; border-color: #d6e0d7; }
    .assignment-types { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: .6rem; padding: .9rem; border: 1px solid #dfe7df; border-radius: 12px; background: #f7f9f6; }
    .assignment-type-option { display: flex; gap: .55rem; align-items: center; padding: .55rem; border-radius: 8px; background: #fff; }
    .assignment-type-option label { margin: 0; }
</style>

<main class="assignment-form-page">
    <header class="assignment-form-hero">
        <div class="text-uppercase small fw-bold" style="color:#e5bd70;letter-spacing:.15em">Lesson work</div>
        <h1>Create an assignment</h1>
        <p>Assignments are shared with students in every course linked to this lesson at the selected branch.</p>
    </header>

    @if($errors->any())
        <div class="alert alert-danger"><strong>Please check the form:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if($targets->isEmpty())
        <div class="alert alert-warning">You have no lesson and branch assignments yet. Ask your administrator to assign a lesson before creating assignment work.</div>
    @endif

    <form method="POST" action="{{ route('teacher.assignments.store') }}" class="assignment-form-card">
        @csrf
        <div class="row g-3">
            <div class="col-12">
                <label for="target" class="form-label">Lesson and branch</label>
                <select id="target" class="form-select" required>
                    <option value="">Choose a lesson and branch</option>
                    @foreach($targets as $target)
                        <option value="{{ $target['lesson']->id }}:{{ $target['branch']->id }}" @selected(old('lesson_id') == $target['lesson']->id && old('branch_id') == $target['branch']->id)>
                            {{ $target['lesson']->title }} &mdash; {{ $target['branch']->name }} ({{ $target['courses']->implode(', ') }})
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="lesson_id" id="lesson_id" value="{{ old('lesson_id') }}">
                <input type="hidden" name="branch_id" id="branch_id" value="{{ old('branch_id') }}">
            </div>
            <div class="col-md-7">
                <label for="title" class="form-label">Assignment title</label>
                <input id="title" name="title" class="form-control" maxlength="255" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-5">
                <label for="term_id" class="form-label">Term for marks</label>
                <select id="term_id" name="term_id" class="form-select" required>
                    <option value="">Choose a term</option>
                    @foreach($terms as $term)
                        <option value="{{ $term->id }}" @selected(old('term_id') == $term->id)>{{ $term->name }}{{ $term->academicYear ? ' - ' . $term->academicYear->year : '' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Instructions <span class="text-muted fw-normal">(optional)</span></label>
                <textarea id="description" name="description" rows="4" class="form-control" maxlength="10000">{{ old('description') }}</textarea>
            </div>
            <div class="col-md-4">
                <label for="submission_method" class="form-label">How students submit</label>
                <select id="submission_method" name="submission_method" class="form-select" required>
                    <option value="system" @selected(old('submission_method', 'system') === 'system')>Upload in the system</option>
                    <option value="paper" @selected(old('submission_method') === 'paper')>Hand in on paper</option>
                    <option value="in_class" @selected(old('submission_method') === 'in_class')>Submit in class</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="due_at" class="form-label">Submission deadline</label>
                <input id="due_at" name="due_at" type="datetime-local" class="form-control" value="{{ old('due_at') }}" required>
            </div>
            <div class="col-md-4">
                <label for="marks_out_of" class="form-label">Marks out of</label>
                <input id="marks_out_of" name="marks_out_of" type="number" min="1" max="10000" class="form-control" value="{{ old('marks_out_of', 100) }}" required>
            </div>
            <div class="col-12" id="file-types-section">
                <span class="form-label d-block">File types students may upload</span>
                <div class="assignment-types">
                    @foreach(['zip' => 'ZIP archive', 'pdf' => 'PDF', 'jpg' => 'JPG', 'jpeg' => 'JPEG', 'png' => 'PNG', 'gif' => 'GIF', 'webp' => 'WebP', 'bmp' => 'BMP', 'tif' => 'TIFF'] as $extension => $label)
                        <div class="assignment-type-option">
                            <input class="form-check-input m-0" type="checkbox" name="allowed_file_types[]" value="{{ $extension }}" id="type-{{ $extension }}" @checked(in_array($extension, old('allowed_file_types', ['pdf', 'zip', 'jpg', 'png']), true))>
                            <label for="type-{{ $extension }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted">Maximum file size: 20 MB. Choose one or more types.</small>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
            <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle me-1"></i> Create assignment</button>
        </div>
    </form>
</main>
<script>
    (function () {
        var target = document.getElementById('target');
        var lesson = document.getElementById('lesson_id');
        var branch = document.getElementById('branch_id');
        var method = document.getElementById('submission_method');
        var fileTypes = document.getElementById('file-types-section');
        function setTarget() {
            var values = target.value.split(':');
            lesson.value = values[0] || '';
            branch.value = values[1] || '';
        }
        function setMethod() {
            fileTypes.hidden = method.value !== 'system';
        }
        target.addEventListener('change', setTarget);
        method.addEventListener('change', setMethod);
        setTarget();
        setMethod();
    }());
</script>
@endsection
