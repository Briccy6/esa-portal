@extends('dos.layout')

@section('content')
<style>
    .timetable-library { max-width: 1500px; margin: 0 auto; padding: 1.5rem 0 3rem; }
    .library-hero {
        display: flex; align-items: flex-end; justify-content: space-between; gap: 1.25rem;
        padding: 1.6rem; margin-bottom: 1.25rem; color: #fff;
        border: 1px solid rgba(255,255,255,.12); border-radius: 18px;
        background: #17243a; box-shadow: 0 18px 42px rgba(15,23,42,.12);
    }
    .library-hero h1 { margin: 0; font-size: clamp(1.5rem, 3vw, 2.25rem); font-weight: 800; letter-spacing: -.03em; }
    .library-hero p { margin: .35rem 0 0; color: #b9c4d5; }
    .create-timetable-btn { flex: 0 0 auto; border: 0; border-radius: 10px; padding: .8rem 1.1rem; font-weight: 750; color: #17243a; background: #f6c453; text-decoration: none; }
    .create-timetable-btn:hover { color: #17243a; background: #ffd975; transform: translateY(-1px); }
    .library-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 1rem; }
    .timetable-card { min-width: 0; overflow: hidden; border: 1px solid #e5e9f0; border-radius: 16px; background: #fff; box-shadow: 0 8px 24px rgba(15,23,42,.06); }
    .timetable-card-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; padding: 1.15rem 1.15rem .8rem; }
    .timetable-card h3 { margin: 0 0 .2rem; font-size: 1.05rem; font-weight: 800; color: #17243a; }
    .branch-name { color: #64748b; font-size: .86rem; }
    .status-pill { flex: 0 0 auto; border-radius: 999px; padding: .32rem .65rem; font-size: .72rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em; }
    .status-published { background: #dcfce7; color: #166534; }
    .status-draft { background: #f1f5f9; color: #475569; }
    .status-other { background: #fef3c7; color: #92400e; }
    .timetable-meta { display: grid; grid-template-columns: 1fr 1fr; gap: .55rem; padding: 0 1.15rem 1rem; }
    .meta-item { min-width: 0; padding: .7rem; border-radius: 10px; background: #f8fafc; }
    .meta-item span { display: block; color: #94a3b8; font-size: .68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .05em; }
    .meta-item strong { display: block; margin-top: .15rem; color: #334155; font-size: .86rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .timetable-card-actions { display: flex; align-items: center; gap: .5rem; padding: .85rem 1.15rem; border-top: 1px solid #edf0f4; }
    .card-action { border-radius: 8px; padding: .48rem .72rem; font-size: .82rem; font-weight: 750; }
    .card-action-edit { color: #17243a; border: 1px solid #dbe2ea; background: #fff; text-decoration: none; }
    .card-action-edit:hover { border-color: #17243a; background: #f8fafc; }
    .card-action-danger { color: #b91c1c; border: 1px solid #fecaca; background: #fff; }
    .empty-library { padding: 4rem 1.5rem; text-align: center; border: 1px dashed #cbd5e1; border-radius: 16px; background: #fff; }
    .empty-library i { display: block; margin-bottom: .8rem; color: #94a3b8; font-size: 2.5rem; }
    @media (max-width: 991.98px) { .library-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 575.98px) {
        .timetable-library { padding-top: .5rem; }
        .library-hero { align-items: stretch; flex-direction: column; padding: 1.25rem; }
        .create-timetable-btn { width: 100%; text-align: center; }
        .library-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="timetable-library">
    <div class="library-hero">
        <div>
            <h1><i class="bi bi-calendar-week me-2"></i>Timetable library</h1>
            <p>{{ $timetables->count() }} schedule{{ $timetables->count() === 1 ? '' : 's' }} available to your assigned branches</p>
        </div>
        <a href="{{ route('dos.timetable.create') }}" class="create-timetable-btn">
            <i class="bi bi-plus-lg me-1"></i>Create timetable
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($timetables->count() > 0)
        <div class="library-grid">
            @foreach($timetables as $timetable)
                <article class="timetable-card">
                    <div class="timetable-card-head">
                        <div style="min-width:0">
                            <h3>{{ $timetable->title }}</h3>
                            <div class="branch-name"><i class="bi bi-building me-1"></i>{{ $timetable->branch->name ?? 'N/A' }}</div>
                        </div>
                        @if($timetable->status === 'published')
                            <span class="status-pill status-published">Published</span>
                        @elseif($timetable->status === 'draft')
                            <span class="status-pill status-draft">Draft</span>
                        @else
                            <span class="status-pill status-other">{{ ucfirst($timetable->status) }}</span>
                        @endif
                    </div>
                    <div class="timetable-meta">
                        <div class="meta-item"><span>Academic year</span><strong>{{ $timetable->academicYear->year ?? 'N/A' }}</strong></div>
                        <div class="meta-item"><span>Term</span><strong>{{ $timetable->term->name ?? 'N/A' }}</strong></div>
                        <div class="meta-item"><span>Valid from</span><strong>{{ $timetable->valid_from ? \Carbon\Carbon::parse($timetable->valid_from)->format('M d, Y') : 'N/A' }}</strong></div>
                        <div class="meta-item"><span>Valid until</span><strong>{{ $timetable->valid_until ? \Carbon\Carbon::parse($timetable->valid_until)->format('M d, Y') : 'N/A' }}</strong></div>
                    </div>
                    <div class="timetable-card-actions">
                        <a href="{{ route('dos.timetable.edit', $timetable->id) }}" class="card-action card-action-edit"><i class="bi bi-pencil me-1"></i>Edit</a>
                        @if($timetable->status === 'draft')
                            <form action="{{ route('dos.timetable.publish', $timetable->id) }}" method="POST" class="ms-auto">
                                @csrf
                                <button type="submit" class="card-action card-action-edit" onclick="return confirm('Run the approval checks and publish this timetable?');"><i class="bi bi-check2-circle me-1"></i>Approve &amp; publish</button>
                            </form>
                        @endif
                        <form action="{{ route('dos.timetable.destroy', $timetable->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="card-action card-action-danger" onclick="return confirm('Delete this timetable? This action cannot be undone.');"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="empty-library">
            <i class="bi bi-calendar2-x"></i>
            <h3 class="h5 fw-bold text-dark">No timetables yet</h3>
            <p class="text-muted mb-3">Build the first weekly schedule for an assigned branch.</p>
            <a href="{{ route('dos.timetable.create') }}" class="create-timetable-btn d-inline-block">Create timetable</a>
        </div>
    @endif
</div>
@endsection
