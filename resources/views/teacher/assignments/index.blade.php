@extends('teacher.layout')

@section('content')
<style>
    .assignment-page { max-width: 1240px; margin: 0 auto; padding: clamp(1rem, 3vw, 2rem); color: #172a3d; }
    .assignment-hero { display: flex; justify-content: space-between; align-items: flex-end; gap: 1rem; padding: clamp(1.35rem, 4vw, 2.5rem); margin-bottom: 1.4rem; border-radius: 20px; color: #f7f5eb; background: radial-gradient(circle at 85% 20%, #446b5a 0, transparent 34%), linear-gradient(125deg, #162e36, #23453f 72%, #285343); box-shadow: 0 18px 38px #18352b24; }
    .assignment-hero h1 { margin: .25rem 0 .4rem; font: 700 clamp(1.7rem, 4vw, 2.45rem)/1.1 Georgia, serif; letter-spacing: -.035em; }
    .assignment-hero p { margin: 0; color: #d3ded5; }
    .assignment-kicker { color: #e5bd70; text-transform: uppercase; font-size: .73rem; font-weight: 800; letter-spacing: .15em; }
    .assignment-panel { overflow: hidden; border: 1px solid #dfe7df; border-radius: 17px; background: #fff; box-shadow: 0 10px 24px #172a3d0a; }
    .assignment-panel .table { margin: 0; }
    .assignment-panel th { padding: .9rem 1rem; color: #607367; background: #f4f7f2; font-size: .75rem; letter-spacing: .07em; text-transform: uppercase; white-space: nowrap; }
    .assignment-panel td { padding: 1rem; vertical-align: middle; }
    .assignment-title { color: #172a3d; font-weight: 750; }
    .assignment-sub { color: #6f7d75; font-size: .84rem; }
    .assignment-pill { display: inline-flex; align-items: center; gap: .35rem; padding: .35rem .65rem; border-radius: 999px; background: #eef4ed; color: #315d43; font-size: .8rem; font-weight: 700; }
    @media (max-width: 767.98px) { .assignment-hero { align-items: flex-start; flex-direction: column; } .assignment-panel { border: 0; background: transparent; box-shadow: none; } .assignment-panel table, .assignment-panel tbody, .assignment-panel tr, .assignment-panel td { display: block; width: 100%; } .assignment-panel thead { display: none; } .assignment-panel tr { padding: .85rem; margin-bottom: .85rem; border: 1px solid #dfe7df; border-radius: 15px; background: #fff; box-shadow: 0 8px 18px #172a3d0a; } .assignment-panel td { display: flex; justify-content: space-between; gap: 1rem; padding: .45rem .25rem; border: 0; text-align: right; } .assignment-panel td:first-child { display: block; text-align: left; } .assignment-panel td::before { content: attr(data-label); color: #748278; font-size: .76rem; font-weight: 700; text-align: left; } .assignment-panel td:first-child::before { display: none; } .assignment-panel td:last-child { justify-content: flex-start; } }
</style>

<main class="assignment-page">
    <header class="assignment-hero">
        <div>
            <div class="assignment-kicker">Lesson work</div>
            <h1>Assignments</h1>
            <p>Create lesson work, track submissions and record marks.</p>
        </div>
        <a href="{{ route('teacher.assignments.create') }}" class="btn btn-warning fw-bold px-4 py-2">
            <i class="bi bi-plus-lg me-1"></i> Create assignment
        </a>
    </header>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <section class="assignment-panel">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr><th>Assignment</th><th>Lesson &amp; branch</th><th>Submission</th><th>Deadline</th><th>Progress</th><th></th></tr>
                </thead>
                <tbody>
                    @forelse($assignments as $assignment)
                        <tr>
                            <td data-label="Assignment">
                                <div class="assignment-title">{{ $assignment->title }}</div>
                                <div class="assignment-sub">{{ $assignment->marks_out_of }} marks &middot; {{ $assignment->term->name ?? 'Term' }}</div>
                            </td>
                            <td data-label="Lesson & branch">
                                <div>{{ $assignment->lesson->title ?? 'Lesson' }}</div>
                                <div class="assignment-sub">{{ $assignment->branch->name ?? 'Branch' }}</div>
                            </td>
                            <td data-label="Submission"><span class="assignment-pill">{{ str_replace('_', ' ', ucfirst($assignment->submission_method)) }}</span></td>
                            <td data-label="Deadline">{{ $assignment->due_at->format('M j, Y g:i A') }}</td>
                            <td data-label="Progress">
                                <span class="assignment-sub">{{ $assignment->submissions_count }} submitted &middot; {{ $assignment->marks_count }} marked</span>
                            </td>
                            <td data-label="Actions" class="text-md-end">
                                <div class="d-flex flex-wrap justify-content-md-end gap-2">
                                    <a href="{{ route('teacher.assignments.show', $assignment) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-folder2-open me-1"></i>Review submissions
                                    </a>
                                    <a href="{{ route('teacher.assignments.configure', $assignment) }}" class="btn btn-sm btn-outline-secondary" aria-label="Configure {{ $assignment->title }}">
                                        <i class="bi bi-gear me-1"></i>Configure
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="py-5 text-center text-muted">No assignments yet. Create one for a lesson you teach.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
