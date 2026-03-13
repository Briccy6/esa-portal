@extends('staff.partials.sidebar_dos')

@section('content')
<style>
    .show-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 30px 0;
    }
    .show-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        max-width: 900px;
        margin: 0 auto;
        overflow: hidden;
    }
    .show-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        text-align: center;
    }
    .show-header h2 { margin: 0; font-size: 2rem; font-weight: 700; }
    .show-body { padding: 40px; }
    .section-title {
        color: #667eea;
        font-size: 1.2rem;
        font-weight: 600;
        margin: 25px 0 15px 0;
        padding-bottom: 8px;
        border-bottom: 2px solid #667eea;
    }
    .info-box {
        background: #f8f9fa;
        border-left: 4px solid #667eea;
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 0 8px 8px 0;
    }
    .info-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    .info-value {
        color: #666;
        margin-bottom: 0;
    }
    .unit-item {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }
    .unit-item:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    .unit-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 10px;
    }
    .unit-name {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }
    .unit-hours {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.9rem;
    }
    .term-badge {
        background: #28a745;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.85rem;
        margin-right: 10px;
    }
    .btn-back {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102,126,234,0.3);
        color: white;
        text-decoration: none;
    }
    .success-message {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
</style>

<div class="show-container">
    <div class="show-card">
        <div class="show-header">
            <h2>Lesson Details</h2>
            <p class="mb-0">{{ $lesson->title }}</p>
        </div>
        
        <div class="show-body">
            @if(session('success'))
                <div class="success-message">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Lesson Information -->
            <div class="info-box">
                <h5 class="mb-3">Lesson Information</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-label">Course</div>
                        <div class="info-value">{{ $lesson->course->name }}</div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-label">Teacher</div>
                        <div class="info-value">
                            {{ $lesson->teacher ? $lesson->teacher->teacher_name : 'Not assigned' }}
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-4">
                        <div class="info-label">Max Test Marks</div>
                        <div class="info-value">{{ $lesson->max_test_marks }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Max Exam Marks</div>
                        <div class="info-value">{{ $lesson->max_exam_marks }}</div>
                    </div>
                    <div class="col-md-4">
                        <div class="info-label">Lesson Weight</div>
                        <div class="info-value">{{ $lesson->lesson_weight }}</div>
                    </div>
                </div>
            </div>

            <!-- Terms -->
            <div class="mb-4">
                <h6 class="section-title">Associated Terms</h6>
                @if($lesson->terms->count() > 0)
                    <div>
                        @foreach($lesson->terms as $term)
                            <span class="term-badge">{{ $term->name }} {{ $term->academicYear ? '(' . $term->academicYear->name . ')' : '' }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted">No terms assigned to this lesson.</p>
                @endif
            </div>

            <!-- Lesson Units -->
            <div class="mb-4">
                <h6 class="section-title">Lesson Units</h6>
                @if($lesson->units && $lesson->units->count() > 0)
                    @foreach($lesson->units as $unit)
                        <div class="unit-item">
                            <div class="unit-header">
                                <div class="unit-name">{{ $unit->unit_name }}</div>
                                <div class="unit-hours">{{ $unit->hours }} hours</div>
                            </div>
                            <div class="text-muted small">
                                Term: {{ $unit->term->name }} | Level: {{ ucfirst($unit->course_level) }}
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-3">No units have been added to this lesson yet.</p>
                        <a href="{{ route('staff.lesson.details.create', $lesson->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Lesson Units
                        </a>
                    </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="d-flex justify-content-between">
                <a href="{{ route('staff.lessons.index') }}" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Lessons
                </a>
                <div>
                    <a href="{{ route('staff.lessons.edit', $lesson->id) }}" class="btn btn-outline-primary me-2">
                        <i class="fas fa-edit"></i> Edit Lesson
                    </a>
                    @if(!$lesson->units || $lesson->units->count() == 0)
                        <a href="{{ route('staff.lesson.details.create', $lesson->id) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Units
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
