@extends('teacher.layout')

@section('content')
<style>
    .assignment-grade-page { max-width: 1250px; margin: 0 auto; padding: clamp(1rem, 3vw, 2rem); color: #172a3d; }
    .assignment-grade-hero { padding: clamp(1.25rem, 4vw, 2.25rem); margin-bottom: 1.25rem; border-radius: 20px; color: #f7f5eb; background: radial-gradient(circle at 90% 10%, #446b5a 0, transparent 35%), linear-gradient(125deg, #162e36, #23453f); }
    .assignment-grade-hero h1 { margin: .3rem 0; font: 700 clamp(1.55rem, 4vw, 2.3rem)/1.1 Georgia, serif; }
    .assignment-grade-panel { overflow: hidden; border: 1px solid #dfe7df; border-radius: 17px; background: #fff; }
    .assignment-grade-panel th { background: #f4f7f2; color: #607367; font-size: .75rem; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
    .assignment-grade-panel td, .assignment-grade-panel th { padding: .85rem; vertical-align: middle; }
    .assignment-grade-panel .form-control { min-width: 100px; }
    .assignment-grade-note { color: #d7e2d9; }
    @media (max-width: 767.98px) { .assignment-grade-panel table, .assignment-grade-panel tbody, .assignment-grade-panel tr, .assignment-grade-panel td { display:block; width:100%; } .assignment-grade-panel thead { display:none; } .assignment-grade-panel tr { padding:.65rem; border-bottom:1px solid #e4ebe4; } .assignment-grade-panel td { display:flex; justify-content:space-between; gap:1rem; border:0; padding:.45rem; } .assignment-grade-panel td::before { content:attr(data-label); color:#748278; font-size:.75rem; font-weight:700; } .assignment-grade-panel td:first-child { display:block; } .assignment-grade-panel td:first-child::before { display:none; } }
</style>

<main class="assignment-grade-page">
    <header class="assignment-grade-hero">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div>
                <div class="text-uppercase small fw-bold" style="color:#e5bd70;letter-spacing:.15em">Assignment marks</div>
                <h1>{{ $assignment->title }}</h1>
                <div class="assignment-grade-note">{{ $assignment->lesson->title }} &middot; {{ $assignment->branch->name }} &middot; {{ $assignment->term->name }}</div>
                <div class="assignment-grade-note mt-2">{{ $assignment->marks_out_of }} marks &middot; {{ ucfirst(str_replace('_', ' ', $assignment->submission_method)) }} &middot; Due {{ $assignment->due_at->format('M j, Y g:i A') }}</div>
            </div>
            <a href="{{ route('teacher.assignments.configure', $assignment) }}" class="btn btn-outline-light"><i class="bi bi-gear me-1"></i> Configure assignment</a>
        </div>
        @if($assignment->description)<p class="mb-0 mt-3">{{ $assignment->description }}</p>@endif
    </header>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    @if($assignment->submission_method === 'system' && $assignment->due_at->isPast())
        <div class="alert alert-info">Students without a system submission are recorded as zero after the deadline.</div>
    @endif

    <form method="POST" action="{{ route('teacher.assignments.marks.save', $assignment) }}" class="assignment-grade-panel">
        @csrf
        @method('PUT')
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th>Student</th><th>Course</th><th>Submission</th><th>Mark / {{ $assignment->marks_out_of }}</th></tr></thead>
                <tbody>
                    @forelse($students as $student)
                        @php($submission = $submissions->get($student->id))
                        @php($mark = $marks->get($student->id))
                        <tr>
                            <td data-label="Student"><strong>{{ $student->full_name }}</strong><div class="small text-muted">{{ $student->reg_number }}</div></td>
                            <td data-label="Course">{{ $student->course->course_name ?? '-' }}</td>
                            <td data-label="Submission">
                                @if($assignment->submission_method === 'system')
                                    @if($submission)
                                        <a href="{{ route('teacher.assignments.submission.download', [$assignment, $submission]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-download me-1"></i>Download for review
                                        </a>
                                        <div class="small text-muted mt-1">{{ $submission->original_file_name }}</div>
                                        <div class="small text-muted">{{ $submission->submitted_at->format('M j g:i A') }}</div>
                                    @elseif($assignment->due_at->isPast())
                                        <span class="badge text-bg-danger">Not submitted &middot; 0</span>
                                    @else
                                        <span class="badge text-bg-warning">Awaiting submission</span>
                                    @endif
                                @else
                                    <span class="badge text-bg-light border text-dark">Record offline work</span>
                                @endif
                            </td>
                            <td data-label="Mark">
                                @php($lockedZero = $assignment->submission_method === 'system' && $assignment->due_at->isPast() && !$submission)
                                <input name="marks[{{ $student->id }}]" type="number" min="0" max="{{ $assignment->marks_out_of }}" class="form-control" value="{{ $lockedZero ? 0 : ($mark->value ?? '') }}" @disabled($lockedZero) aria-label="Mark for {{ $student->full_name }}">
                                @if($lockedZero)<input type="hidden" name="marks[{{ $student->id }}]" value="0">@endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-5 text-center text-muted">No students are enrolled in the lesson courses at this branch.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->isNotEmpty())
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3">
                <a href="{{ route('teacher.assignments.index') }}" class="btn btn-outline-secondary">Back to assignments</a>
                <button class="btn btn-success px-4"><i class="bi bi-save me-1"></i> Save marks</button>
            </div>
        @endif
    </form>
</main>
@endsection
