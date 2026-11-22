@extends('teacher.layout')

@section('title', 'Lesson Units')

@section('content')
<style>
    .lesson-units-page {
        --primary: #1e40af;
        --primary-light: #3b82f6;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --bg-card: #ffffff;
        --bg-hover: #f8fafc;
    }

    .page-header {
        margin-bottom: 2rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .page-subtitle {
        color: var(--text-secondary);
        margin: 0;
    }

    .lessons-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .lesson-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.2s ease;
    }

    .lesson-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .lesson-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.75rem;
    }

    .lesson-courses {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .course-badge {
        background: var(--bg-hover);
        color: var(--text-secondary);
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.875rem;
    }

    .units-count {
        color: var(--text-secondary);
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }

    .btn-view-units {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.625rem 1.25rem;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-view-units:hover {
        background: var(--primary-light);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--text-secondary);
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
</style>

<div class="container py-4 lesson-units-page">
    <div class="page-header">
        <h1 class="page-title">Lesson Units</h1>
        <p class="page-subtitle">Manage units for your assigned lessons. Upload files, import from CSV, or export to CSV.</p>
    </div>

    @if($lessons->isEmpty())
        <div class="empty-state">
            <i class="bi bi-book-half"></i>
            <h3>No Lessons Assigned</h3>
            <p>You don't have any lessons assigned to you yet. Contact the administrator.</p>
        </div>
    @else
        <div class="lessons-grid">
            @foreach($lessons as $lesson)
                <div class="lesson-card">
                    <h3 class="lesson-title">{{ $lesson->title }}</h3>
                    <div class="lesson-courses">
                        @foreach($lesson->courses as $course)
                            <span class="course-badge">{{ $course->course_name }}</span>
                        @endforeach
                    </div>
                    <p class="units-count">
                        <i class="bi bi-collection"></i>
                        {{ $lesson->units->count() }} unit{{ $lesson->units->count() !== 1 ? 's' : '' }}
                    </p>
                    <a href="{{ route('teacher.lesson-units.show', $lesson->id) }}" class="btn-view-units">
                        <i class="bi bi-folder-open"></i> Manage Units
                    </a>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
