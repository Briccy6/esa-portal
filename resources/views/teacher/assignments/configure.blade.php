@extends('teacher.layout')

@section('content')
<style>
    .assignment-config-page { max-width: 1000px; margin: 0 auto; padding: clamp(1rem, 3vw, 2rem); color: #172a3d; }
    .assignment-config-hero { padding: clamp(1.3rem, 4vw, 2.2rem); margin-bottom: 1.25rem; border-radius: 20px; color: #f7f5eb; background: radial-gradient(circle at 90% 10%, #446b5a 0, transparent 35%), linear-gradient(125deg, #162e36, #23453f); }
    .assignment-config-hero h1 { margin: .3rem 0; font: 700 clamp(1.6rem, 4vw, 2.3rem)/1.1 Georgia, serif; }
    .assignment-config-hero p { margin: 0; color: #d3ded5; }
    .assignment-config-card { padding: clamp(1rem, 3vw, 2rem); border: 1px solid #dfe7df; border-radius: 17px; background: #fff; box-shadow: 0 10px 24px #172a3d0a; }
    .assignment-config-card .form-label { color: #354e40; font-weight: 700; }
    .assignment-config-card .form-control, .assignment-config-card .form-select { min-height: 46px; border-color: #d6e0d7; }
    .assignment-config-types { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: .6rem; padding: .9rem; border: 1px solid #dfe7df; border-radius: 12px; background: #f7f9f6; }
    .assignment-config-type { display: flex; gap: .55rem; align-items: center; padding: .55rem; border-radius: 8px; background: #fff; }
    .assignment-config-type label { margin: 0; }
    .assignment-current-pdf { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; padding: .8rem 1rem; border: 1px solid #dfe7df; border-radius: 12px; background: #f7f9f6; }
</style>

<main class="assignment-config-page">
    <header class="assignment-config-hero">
        <div class="text-uppercase small fw-bold" style="color:#e5bd70;letter-spacing:.15em">Assignment settings</div>
        <h1>Configure assignment</h1>
        <p>{{ $assignment->title }} · {{ $assignment->lesson->title }} · {{ $assignment->branch->name }}</p>
    </header>

    @if($errors->any())
        <div class="alert alert-danger"><strong>Please check the form:</strong><ul class="mb-0 mt-2">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" action="{{ route('teacher.assignments.configuration.update', $assignment) }}" class="assignment-config-card" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row g-3">
            <div class="col-12">
                <label for="title" class="form-label">Assignment title</label>
                <input id="title" name="title" class="form-control" maxlength="255" value="{{ old('title', $assignment->title) }}" required>
            </div>
            <div class="col-12">
                <label for="description" class="form-label">Instructions <span class="text-muted fw-normal">(optional)</span></label>
                <textarea id="description" name="description" rows="3" class="form-control" maxlength="10000">{{ old('description', $assignment->description) }}</textarea>
            </div>
            <div class="col-12">
                <label for="question" class="form-label">Assignment question</label>
                <textarea id="question" name="question" rows="4" class="form-control" maxlength="20000" required>{{ old('question', $assignment->question) }}</textarea>
            </div>
            <div class="col-md-4">
                <label for="submission_method" class="form-label">How students submit</label>
                <select id="submission_method" name="submission_method" class="form-select" required>
                    <option value="system" @selected(old('submission_method', $assignment->submission_method) === 'system')>Upload in the system</option>
                    <option value="paper" @selected(old('submission_method', $assignment->submission_method) === 'paper')>Hand in on paper</option>
                    <option value="in_class" @selected(old('submission_method', $assignment->submission_method) === 'in_class')>Submit in class</option>
                </select>
                <small class="text-muted">Submission method cannot change after work is submitted or marked.</small>
            </div>
            <div class="col-md-4">
                <label for="due_at" class="form-label">Submission deadline</label>
                <input id="due_at" name="due_at" type="datetime-local" class="form-control" value="{{ old('due_at', $assignment->due_at->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="col-md-4">
                <label for="marks_out_of" class="form-label">Marks out of</label>
                <input id="marks_out_of" name="marks_out_of" type="number" min="1" max="10000" class="form-control" value="{{ old('marks_out_of', $assignment->marks_out_of) }}" required>
            </div>
            <div class="col-12" id="assignment-file-types">
                <span class="form-label d-block">Allowed student submission file types</span>
                <div class="assignment-config-types">
                    @foreach(['zip' => 'ZIP archive', 'pdf' => 'PDF', 'jpg' => 'JPG', 'jpeg' => 'JPEG', 'png' => 'PNG', 'gif' => 'GIF', 'webp' => 'WebP', 'bmp' => 'BMP', 'tif' => 'TIFF'] as $extension => $label)
                        <div class="assignment-config-type">
                            <input class="form-check-input m-0" type="checkbox" name="allowed_file_types[]" value="{{ $extension }}" id="configure-type-{{ $extension }}" @checked(in_array($extension, old('allowed_file_types', $assignment->allowed_file_types ?? []), true))>
                            <label for="configure-type-{{ $extension }}">{{ $label }}</label>
                        </div>
                    @endforeach
                </div>
                <small class="text-muted">Student submissions are limited to 20 MB each.</small>
            </div>
            <div class="col-12">
                <label for="attachment" class="form-label">Assignment reading PDF</label>
                @if($assignment->attachment_path)
                    <div class="assignment-current-pdf mb-2">
                        <span><i class="bi bi-file-earmark-pdf text-danger me-2"></i>{{ $assignment->attachment_original_name }}</span>
                        <a href="{{ route('teacher.assignments.pdf', $assignment) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success">Read current PDF</a>
                    </div>
                @endif
                <input id="attachment" name="attachment" type="file" class="form-control" accept=".pdf,application/pdf" @required(!$assignment->attachment_path)>
                <small class="text-muted">Upload a PDF to replace the current reading file. Maximum 20 MB.</small>
            </div>
        </div>
        <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
            <a href="{{ route('teacher.assignments.show', $assignment) }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success px-4"><i class="bi bi-check2-circle me-1"></i> Save settings</button>
        </div>
    </form>
</main>
<script>
    (function () {
        var method = document.getElementById('submission_method');
        var fileTypes = document.getElementById('assignment-file-types');
        function updateFileTypes() {
            fileTypes.hidden = method.value !== 'system';
        }
        method.addEventListener('change', updateFileTypes);
        updateFileTypes();
    }());
</script>
@endsection
