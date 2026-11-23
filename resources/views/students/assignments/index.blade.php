@extends('students.app')

@section('content')
<style>
    .student-assignment-page { --ink:#1d302d; --muted:#6d7b70; --line:#dce5d8; --green:#264e3f; max-width:1180px; margin:0 auto; padding:clamp(1rem,3vw,2.2rem); color:var(--ink); }
    .student-assignment-hero { position:relative; overflow:hidden; padding:clamp(1.5rem,5vw,3rem); margin-bottom:1.35rem; border-radius:22px; color:#f8f6ec; background:radial-gradient(circle at 87% 14%,#b58a4d66 0,transparent 26%),linear-gradient(125deg,#18343a,#244c40 72%,#426247); box-shadow:0 20px 42px #1d302d1c; }
    .student-assignment-hero::after { content:""; position:absolute; right:-2rem; bottom:-6rem; width:17rem; height:17rem; border:1px solid #ffffff24; border-radius:50%; box-shadow:0 0 0 2rem #ffffff08,0 0 0 4rem #ffffff05; }
    .student-assignment-kicker { color:#e7c47d; font-size:.75rem; font-weight:800; letter-spacing:.16em; text-transform:uppercase; }
    .student-assignment-hero h1 { position:relative; z-index:1; margin:.45rem 0 .6rem; font:700 clamp(2rem,5vw,3.2rem)/1 Georgia,serif; letter-spacing:-.04em; }
    .student-assignment-hero p { position:relative; z-index:1; max-width:660px; margin:0; color:#d5e0d7; }
    .assignment-reminder { display:flex; align-items:center; gap:.75rem; padding:.85rem 1rem; margin:0 0 1.15rem; border-left:4px solid #d39a45; border-radius:9px; color:#4f402a; background:#fff7e7; font-weight:700; }
    .student-assignment-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:1rem; }
    .student-assignment-card { min-width:0; padding:clamp(1rem,3vw,1.4rem); border:1px solid var(--line); border-radius:17px; background:#fff; box-shadow:0 9px 22px #172a3d0a; }
    .student-assignment-card h2 { margin:0; font:700 1.35rem/1.2 Georgia,serif; }
    .student-assignment-meta { display:flex; flex-wrap:wrap; gap:.5rem 1rem; margin:.8rem 0; color:var(--muted); font-size:.88rem; }
    .student-assignment-meta span { display:inline-flex; align-items:center; gap:.35rem; }
    .assignment-description { padding:.8rem; margin:.8rem 0; border-radius:10px; background:#f5f8f3; white-space:pre-line; }
    .assignment-question { padding:1rem; margin:.85rem 0; border-left:3px solid #bc9151; border-radius:0 10px 10px 0; background:#fbf8f0; white-space:pre-line; }
    .assignment-question-label { display:block; margin-bottom:.35rem; color:#80653b; font-size:.72rem; font-weight:800; letter-spacing:.1em; text-transform:uppercase; }
    .assignment-reading { margin:1rem 0; border:1px solid var(--line); border-radius:12px; background:#f8faf7; }
    .assignment-reading summary { padding:.85rem 1rem; color:#28543d; font-weight:750; cursor:pointer; }
    .assignment-pdf-frame { display:block; width:100%; height:min(72vh, 850px); min-height:420px; border:0; border-top:1px solid var(--line); background:#e9eeea; }
    .assignment-countdown { display:flex; flex-wrap:wrap; align-items:center; gap:.5rem; padding:.7rem .8rem; margin:1rem 0; border-radius:11px; color:#28543d; background:#edf5eb; font-weight:750; font-variant-numeric:tabular-nums; }
    .assignment-countdown.is-overdue { color:#8a3c30; background:#fff0ed; }
    .assignment-score { display:inline-flex; padding:.32rem .65rem; border:1px solid var(--line); border-radius:999px; color:#315d43; background:#f4f8f1; font-weight:750; }
    .assignment-upload { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; padding-top:.9rem; border-top:1px solid #e8eee7; }
    .assignment-upload input[type=file] { min-width:0; flex:1 1 190px; }
    .assignment-empty { grid-column:1/-1; padding:3rem 1rem; border:1px dashed #cbd9cc; border-radius:16px; background:#fff; text-align:center; color:var(--muted); }
    @media(max-width:760px) { .student-assignment-grid{grid-template-columns:1fr;} .student-assignment-hero::after{right:-7rem;} }
</style>

<main class="student-assignment-page">
    <header class="student-assignment-hero">
        <div class="student-assignment-kicker">Your course work</div>
        <h1>Lesson assignments</h1>
        <p>See what is due, how to submit, and the marks recorded for your work.</p>
    </header>

    <div class="assignment-reminder"><i class="bi bi-alarm" aria-hidden="true"></i><span>Submit your work before this time. Check the countdown on each assignment.</span></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <section class="student-assignment-grid" aria-label="Assignments">
        @forelse($assignments as $assignment)
            @php($submission = $assignment->studentSubmission)
            @php($mark = $assignment->studentMark)
            <article class="student-assignment-card">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <h2>{{ $assignment->title }}</h2>
                    @if($mark)<span class="assignment-score">{{ $mark->value }}/{{ $assignment->marks_out_of }}</span>@endif
                </div>
                <div class="student-assignment-meta">
                    <span><i class="bi bi-journal-text"></i>{{ $assignment->lesson->title }}</span>
                    <span><i class="bi bi-calendar3"></i>{{ $assignment->term->name }}</span>
                    <span><i class="bi bi-person"></i>{{ $assignment->teacher->teacher_name ?? 'Teacher' }}</span>
                </div>
                @if($assignment->description)<div class="assignment-description">{{ $assignment->description }}</div>@endif
                @if($assignment->question)
                    <div class="assignment-question">
                        <span class="assignment-question-label">Your question</span>
                        {{ $assignment->question }}
                    </div>
                @endif
                @if($assignment->attachment_path)
                    <details class="assignment-reading">
                        <summary><i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>Read assignment PDF in this page</summary>
                        <iframe
                            class="assignment-pdf-frame"
                            src="{{ route('student.assignments.pdf', $assignment) }}"
                            title="Assignment PDF: {{ $assignment->title }}"
                            loading="lazy"
                        ></iframe>
                        <div class="p-2 text-end">
                            <a href="{{ route('student.assignments.pdf', $assignment) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Open PDF in a new tab
                            </a>
                        </div>
                    </details>
                @endif
                <div class="small text-muted">Deadline: {{ $assignment->due_at->format('l, M j g:i A') }}</div>
                <div class="assignment-countdown" data-deadline="{{ $assignment->due_at->toIso8601String() }}">
                    <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                    <span class="countdown-copy">Submit before {{ $assignment->due_at->format('g:i A, M j') }}</span>
                </div>
                <div class="student-assignment-meta mb-2">
                    <span><i class="bi bi-award"></i>Out of {{ $assignment->marks_out_of }}</span>
                    @if($assignment->submission_method === 'system')
                        <span><i class="bi bi-paperclip"></i>Files: {{ implode(', ', $assignment->allowed_file_types ?? []) }}</span>
                    @else
                        <span><i class="bi bi-hand-index-thumb"></i>{{ $assignment->submission_method === 'paper' ? 'Hand in on paper' : 'Submit in class' }}</span>
                    @endif
                </div>
                @if($assignment->submission_method === 'system')
                    @if($submission)
                        <div class="small text-success mb-2"><i class="bi bi-check-circle me-1"></i>Submitted {{ $submission->submitted_at->format('M j, g:i A') }} &middot; {{ $submission->original_file_name }}</div>
                        <div class="d-flex flex-wrap gap-2 align-items-center">
                            <a class="btn btn-sm btn-outline-success" href="{{ route('student.assignments.submission.download', $assignment) }}">View submitted file</a>
                            @if($assignment->due_at->isFuture())
                                <form class="assignment-upload flex-grow-1" method="POST" enctype="multipart/form-data" action="{{ route('student.assignments.submit', $assignment) }}">
                                    @csrf
                                    <input type="file" name="file" class="form-control form-control-sm" accept="{{ collect($assignment->allowed_file_types ?? [])->map(fn($extension) => '.' . $extension)->implode(',') }}" required>
                                    <button class="btn btn-sm btn-success">Replace file</button>
                                </form>
                            @endif
                        </div>
                    @elseif($assignment->due_at->isFuture())
                        <form class="assignment-upload" method="POST" enctype="multipart/form-data" action="{{ route('student.assignments.submit', $assignment) }}">
                            @csrf
                            <input type="file" name="file" class="form-control form-control-sm" accept="{{ collect($assignment->allowed_file_types ?? [])->map(fn($extension) => '.' . $extension)->implode(',') }}" required>
                            <button class="btn btn-success">Submit assignment</button>
                        </form>
                    @else
                        <div class="small text-danger"><i class="bi bi-exclamation-circle me-1"></i>Deadline passed without a submission. This assignment is recorded as 0.</div>
                    @endif
                @else
                    <div class="small text-muted">This assignment is submitted {{ $assignment->submission_method === 'paper' ? 'on paper' : 'in class' }}. Your teacher records the mark.</div>
                @endif
            </article>
        @empty
            <div class="assignment-empty">
                <i class="bi bi-journal-check fs-2 d-block mb-2"></i>
                <strong>No assignments yet</strong>
                <div>Your teachers' lesson assignments will appear here.</div>
            </div>
        @endforelse
    </section>
</main>
<script>
    document.querySelectorAll('[data-deadline]').forEach(function (countdown) {
        var deadline = new Date(countdown.dataset.deadline).getTime();
        var output = countdown.querySelector('.countdown-copy');
        function updateCountdown() {
            var remaining = deadline - Date.now();
            if (remaining <= 0) {
                countdown.classList.add('is-overdue');
                output.textContent = 'Deadline passed';
                return;
            }
            var days = Math.floor(remaining / 86400000);
            var hours = Math.floor((remaining % 86400000) / 3600000);
            var minutes = Math.floor((remaining % 3600000) / 60000);
            var seconds = Math.floor((remaining % 60000) / 1000);
            output.textContent = 'Submit before this time - ' + days + 'd ' +
                String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') +
                ':' + String(seconds).padStart(2, '0') + ' remaining';
        }
        updateCountdown();
        window.setInterval(updateCountdown, 1000);
    });
</script>
@endsection
