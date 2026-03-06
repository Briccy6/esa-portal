@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('styles')
<style>
.teacher-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.welcome-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px;
    border-radius: 15px;
    margin-bottom: 30px;
    text-align: center;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    text-align: center;
    transition: all 0.3s ease;
    border-left: 4px solid #007bff;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
}

.stat-card.primary { border-left-color: #007bff; }
.stat-card.success { border-left-color: #28a745; }
.stat-card.warning { border-left-color: #ffc107; }
.stat-card.danger { border-left-color: #dc3545; }
.stat-card.info { border-left-color: #17a2b8; }

.stat-value {
    font-size: 36px;
    font-weight: bold;
    color: #495057;
    margin-bottom: 10px;
}

.stat-label {
    color: #6c757d;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.quick-actions {
    background: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.action-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.action-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 12px;
    text-decoration: none;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    transition: all 0.3s ease;
    min-height: 120px;
    justify-content: center;
}

.action-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    color: white;
}

.action-btn i {
    font-size: 32px;
    margin-bottom: 10px;
}

.action-btn h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.recent-activity {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.activity-item {
    padding: 15px 0;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    align-items: center;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 15px;
    font-size: 18px;
}

.activity-icon.exam { background: #e3f2fd; color: #2196f3; }
.activity-icon.question { background: #f3e5f5; color: #9c27b0; }
.activity-icon.student { background: #fff3e0; color: #ff9800; }
.activity-icon.course { background: #e8f5e8; color: #4caf50; }

.activity-content {
    flex: 1;
}

.activity-text {
    color: #495057;
    margin-bottom: 2px;
}

.activity-time {
    color: #6c757d;
    font-size: 12px;
}

@media (max-width: 768px) {
    .teacher-dashboard {
        padding: 10px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .action-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection

@section('content')
<div class="teacher-dashboard">
    <!-- Welcome Section -->
    <div class="welcome-section">
        <h1 class="mb-3">Welcome back, {{ $teacher->first_name }}!</h1>
        <p class="mb-0">{{ $systemName }} - Teacher Dashboard</p>
        <p class="mb-0">{{ now()->format('l, F j, Y') }}</p>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-value">{{ $courses->count() }}</div>
            <div class="stat-label">My Courses</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">{{ $courses->sum(function($course) { return $course->lessons->count(); }) }}</div>
            <div class="stat-label">Total Lessons</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value">{{ \App\Models\Exam::where('teacher_id', $teacher->id)->count() }}</div>
            <div class="stat-label">My Exams</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">{{ \App\Models\QuestionBank::where('teacher_id', $teacher->id)->count() }}</div>
            <div class="stat-label">Question Banks</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="quick-actions">
        <h3 class="mb-4"><i class="fas fa-bolt"></i> Quick Actions</h3>
        <div class="action-grid">
            <a href="{{ route('teacher.exams.create') }}" class="action-btn">
                <i class="fas fa-plus-circle"></i>
                <h4>Create Exam</h4>
            </a>
            <a href="{{ route('teacher.exams.index') }}" class="action-btn">
                <i class="fas fa-list-alt"></i>
                <h4>Manage Exams</h4>
            </a>
            <a href="{{ route('exams.question-banks.index') }}" class="action-btn">
                <i class="fas fa-database"></i>
                <h4>Question Banks</h4>
            </a>
            <a href="{{ route('teacher.courses.details') }}" class="action-btn">
                <i class="fas fa-book"></i>
                <h4>Course Details</h4>
            </a>
            <a href="{{ route('teacher.profile.edit') }}" class="action-btn">
                <i class="fas fa-user"></i>
                <h4>My Profile</h4>
            </a>
            <a href="{{ route('teacher.resources.index') }}" class="action-btn">
                <i class="fas fa-folder"></i>
                <h4>Resources</h4>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="recent-activity">
        <h3 class="mb-4"><i class="fas fa-history"></i> Recent Activity</h3>
        
        @php
            $recentActivities = collect([
                (object)[
                    'type' => 'exam',
                    'text' => 'Created new exam: "Mathematics Test"',
                    'time' => '2 hours ago'
                ],
                (object)[
                    'type' => 'question',
                    'text' => 'Added 10 questions to "Physics Question Bank"',
                    'time' => '5 hours ago'
                ],
                (object)[
                    'type' => 'student',
                    'text' => '25 students completed "Chemistry Quiz"',
                    'time' => '1 day ago'
                ],
                (object)[
                    'type' => 'course',
                    'text' => 'Updated lesson materials for "Biology Course"',
                    'time' => '2 days ago'
                ]
            ]);
        @endphp

        @foreach($recentActivities as $activity)
            <div class="activity-item">
                <div class="activity-icon {{ $activity->type }}">
                    @if($activity->type === 'exam')
                        <i class="fas fa-file-alt"></i>
                    @elseif($activity->type === 'question')
                        <i class="fas fa-question"></i>
                    @elseif($activity->type === 'student')
                        <i class="fas fa-user-graduate"></i>
                    @else
                        <i class="fas fa-book"></i>
                    @endif
                </div>
                <div class="activity-content">
                    <div class="activity-text">{{ $activity->text }}</div>
                    <div class="activity-time">{{ $activity->time }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Announcements -->
    @if($announcements->count() > 0)
        <div class="recent-activity mt-3">
            <h3 class="mb-4"><i class="fas fa-bullhorn"></i> Latest Announcements</h3>
            @foreach($announcements as $announcement)
                <div class="activity-item">
                    <div class="activity-icon" style="background: #fff3cd; color: #856404;">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="activity-content">
                        <div class="activity-text">{{ $announcement->title }}</div>
                        <div class="activity-time">{{ $announcement->created_at->diffForHumans() }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate stat cards
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.stat-card, .action-btn').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Update time every second
    function updateTime() {
        const timeElement = document.querySelector('.welcome-section p:last-of-type');
        if (timeElement) {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            timeElement.textContent = now.toLocaleDateString('en-US', options);
        }
    }

    setInterval(updateTime, 1000);
});
</script>
@endsection
