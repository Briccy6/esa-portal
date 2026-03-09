@extends('admin.layout')

@section('title', 'Exam Details - ' . $exam->title)

@section('styles')
<style>
.exam-details {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.exam-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 15px;
    margin-bottom: 30px;
}

.exam-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 15px;
}

.exam-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.detail-card {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #007bff;
}

.detail-card h3 {
    color: #007bff;
    margin-bottom: 20px;
    font-size: 1.2rem;
}

.detail-item {
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
}

.detail-label {
    font-weight: 600;
    color: #495057;
}

.detail-value {
    color: #6c757d;
}

.related-items {
    margin-top: 15px;
}

.related-item {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    margin-bottom: 8px;
    border-left: 3px solid #28a745;
}

.action-buttons {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn-custom {
    padding: 12px 25px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-primary-custom {
    background: #007bff;
    color: white;
}

.btn-primary-custom:hover {
    background: #0056b3;
    color: white;
}

.btn-secondary-custom {
    background: #6c757d;
    color: white;
}

.btn-secondary-custom:hover {
    background: #545b62;
    color: white;
}

.status-badge {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 500;
}

.status-active {
    background: #d4edda;
    color: #155724;
}

.status-inactive {
    background: #f8d7da;
    color: #721c24;
}
</style>
@endsection

@section('content')
<div class="exam-details">
    <!-- Exam Header -->
    <div class="exam-header">
        <h1 class="exam-title">{{ $exam->title }}</h1>
        <p class="lead">{{ $exam->description ?? 'No description available' }}</p>
        
        <div class="exam-meta">
            <div class="meta-item">
                <i class="fas fa-calendar"></i>
                <span>{{ \Carbon\Carbon::parse($exam->class_date)->format('M d, Y') }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-tag"></i>
                <span>{{ ucfirst($exam->exam_type) }}</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-chart-line"></i>
                <span>{{ $exam->exam_weight }}% Weight</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-trophy"></i>
                <span>{{ $exam->total_marks }} Marks</span>
            </div>
            <div class="meta-item">
                <i class="fas fa-shield-alt"></i>
                <span>Password: {{ $exam->exam_password }}</span>
            </div>
            <div class="meta-item">
                <span class="status-badge {{ $exam->is_active ? 'status-active' : 'status-inactive' }}">
                    {{ $exam->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Basic Information -->
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Basic Information</h3>
            <div class="detail-item">
                <span class="detail-label">Subject:</span>
                <span class="detail-value">{{ $exam->subject ?? 'Not specified' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Exam Date:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($exam->class_date)->format('d M Y H:i') }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Weight:</span>
                <span class="detail-value">{{ $exam->exam_weight }}%</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Total Marks:</span>
                <span class="detail-value">{{ $exam->total_marks }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Status:</span>
                <span class="detail-value">{{ $exam->is_active ? 'Active' : 'Inactive' }}</span>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="detail-card">
            <h3><i class="fas fa-graduation-cap"></i> Academic Information</h3>
            <div class="detail-item">
                <span class="detail-label">Academic Year:</span>
                <span class="detail-value">{{ $exam->academic_year->year ?? 'Not specified' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Term:</span>
                <span class="detail-value">{{ $exam->term->name ?? 'Not specified' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Teacher:</span>
                <span class="detail-value">{{ $exam->teacher->first_name . ' ' . $exam->teacher->last_name ?? 'Not assigned' }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Branch:</span>
                <span class="detail-value">{{ $exam->branch->name ?? 'Not specified' }}</span>
            </div>
        </div>

        <!-- Security Information -->
        <div class="detail-card">
            <h3><i class="fas fa-lock"></i> Security</h3>
            <div class="detail-item">
                <span class="detail-label">Exam Password:</span>
                <span class="detail-value">{{ $exam->exam_password }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Created:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($exam->created_at)->format('d M Y H:i') }}</span>
            </div>
            <div class="detail-item">
                <span class="detail-label">Last Updated:</span>
                <span class="detail-value">{{ \Carbon\Carbon::parse($exam->updated_at)->format('d M Y H:i') }}</span>
            </div>
        </div>
    </div>

    <!-- Related Courses and Lessons -->
    <div class="details-grid">
        <!-- Related Courses -->
        <div class="detail-card">
            <h3><i class="fas fa-book"></i> Related Courses</h3>
            @if($exam->courses->count() > 0)
                <div class="related-items">
                    @foreach($exam->courses as $course)
                        <div class="related-item">
                            <strong>{{ $course->course_name ?? 'Course ' . $course->id }}</strong>
                            @if($course->course_code)
                                <br><small>Code: {{ $course->course_code }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No courses assigned to this exam.</p>
            @endif
        </div>

        <!-- Related Lessons -->
        <div class="detail-card">
            <h3><i class="fas fa-chalkboard-teacher"></i> Related Lessons</h3>
            @if($exam->lessons->count() > 0)
                <div class="related-items">
                    @foreach($exam->lessons as $lesson)
                        <div class="related-item">
                            <strong>{{ $lesson->title ?? 'Lesson ' . $lesson->id }}</strong>
                            @if($lesson->subject_name)
                                <br><small>Subject: {{ $lesson->subject_name }}</small>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted">No lessons assigned to this exam.</p>
            @endif
        </div>
    </div>

    <!-- Notes Section -->
    @if($exam->notes)
        <div class="detail-card">
            <h3><i class="fas fa-sticky-note"></i> Additional Notes</h3>
            <p>{{ $exam->notes }}</p>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn-custom btn-primary-custom">
            <i class="fas fa-edit"></i> Edit Exam
        </a>
        <a href="{{ route('admin.exams.preview', $exam->id) }}" class="btn-custom btn-success-custom" target="_blank">
            <i class="fas fa-play"></i> Preview Exam
        </a>
        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-custom" style="background: #dc3545; color: white;" onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone and will delete all related data including questions, marks, attempts, and analytics.')">
                <i class="fas fa-trash"></i> Delete Exam
            </button>
        </form>
        <a href="{{ route('admin.exams.index') }}" class="btn-custom btn-secondary-custom">
            <i class="fas fa-arrow-left"></i> Back to Exams
        </a>
    </div>
</div>
@endsection
