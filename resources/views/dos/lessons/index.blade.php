@extends('dos.layout')

@section('content')
<style>
    .index-container {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .index-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        margin: 0 auto;
        overflow: hidden;
    }
    .index-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .index-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .index-body { padding: 40px; }
    .lesson-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        background: white;
    }
    .lesson-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        border-color: #667eea;
    }
    .lesson-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: #333;
        margin-bottom: 15px;
    }
    .lesson-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 15px;
    }
    .meta-item {
        background: #f8f9fa;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 0.9rem;
        color: #666;
    }
    .meta-item strong {
        color: #333;
    }
    .branch-assignments {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #e9ecef;
    }
    .assignment-badge {
        display: inline-block;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin: 3px;
    }
    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.9rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .btn-view {
        background: #667eea;
        color: white;
        border: none;
    }
    .btn-view:hover {
        background: #5a6fd8;
        transform: translateY(-1px);
    }
    .btn-edit {
        background: #28a745;
        color: white;
        border: none;
    }
    .btn-edit:hover {
        background: #218838;
        transform: translateY(-1px);
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }
    .empty-state i {
        font-size: 4rem;
        color: #ddd;
        margin-bottom: 20px;
    }
</style>

<div class="index-container">
    <div class="container">
        <div class="index-card">
            <div class="index-header">
                <h2><i class="fas fa-book me-2"></i>Lessons Management</h2>
                <p class="mb-0 mt-1 opacity-75">Manage lessons across all branches and courses</p>
            </div>

            <div class="index-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0">All Lessons</h4>
                    <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>Create New Lesson
                    </a>
                </div>

                @if($lessonDetails->count() > 0)
                    <div class="row">
                        @foreach($lessonDetails as $lesson)
                            <div class="col-lg-6 col-xl-4">
                                <div class="lesson-card">
                                    <div class="lesson-title">
                                        <i class="fas fa-book-open me-2"></i>{{ $lesson->title }}
                                    </div>
                                    
                                    <div class="lesson-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-weight me-1"></i>
                                            Weight: <strong>{{ $lesson->lesson_weight ?? 100 }}%</strong>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-chart-line me-1"></i>
                                            Test: <strong>{{ $lesson->max_test_marks ?? 100 }}</strong>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-file-alt me-1"></i>
                                            Exam: <strong>{{ $lesson->max_exam_marks ?? 100 }}</strong>
                                        </div>
                                    </div>

                                    @if($lesson->terms->count() > 0)
                                        <div class="meta-item">
                                            <i class="fas fa-calendar me-1"></i>
                                            Terms: <strong>{{ $lesson->terms->pluck('name')->implode(', ') }}</strong>
                                        </div>
                                    @endif

                                    @if($lesson->academicYear)
                                        <div class="meta-item">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            Year: <strong>{{ $lesson->academicYear->year }}</strong>
                                        </div>
                                    @endif

                                    {{-- Show branch assignments if available --}}
                                    @if($lesson->branches->count() > 0)
                                        <div class="branch-assignments">
                                            <strong><i class="fas fa-network-wired me-2"></i>Branch Assignments:</strong>
                                            <div class="mt-2">
                                                @foreach($lesson->branches as $branch)
                                                    <span class="assignment-badge">
                                                        {{ $branch->name }}
                                                        @if($lesson->courses->where('id', $branch->pivot->course_id)->first())
                                                            - {{ $lesson->courses->where('id', $branch->pivot->course_id)->first()->course_name }}
                                                        @endif
                                                        @if($branch->pivot->teacher_id)
                                                            ({{ \App\Models\Teacher::find($branch->pivot->teacher_id)->teacher_name ?? 'Unknown' }})
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        {{-- Show single course assignment for backward compatibility --}}
                                        @if($lesson->course)
                                            <div class="branch-assignments">
                                                <strong><i class="fas fa-book me-2"></i>Course:</strong>
                                                <div class="mt-2">
                                                    <span class="assignment-badge">
                                                        {{ $lesson->course->course_name }}
                                                        @if($lesson->teacher)
                                                            ({{ $lesson->teacher->teacher_name }})
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="d-flex gap-2 mt-3">
                                        <a href="{{ route('staff.lessons.show', $lesson->id) }}" class="btn btn-action btn-view">
                                            <i class="fas fa-eye me-1"></i>View
                                        </a>
                                        <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-action btn-edit">
                                            <i class="fas fa-edit me-1"></i>Edit
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-book"></i>
                        <h4>No Lessons Found</h4>
                        <p class="text-muted">Start by creating your first lesson using the button above.</p>
                        <a href="{{ route('staff.lessons.create') }}" class="btn btn-primary mt-3">
                            <i class="fas fa-plus-circle me-2"></i>Create Your First Lesson
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
