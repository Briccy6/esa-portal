@extends('admin.layout')

@section('title', 'Exams Management')

@section('styles')
<style>
.exams-dashboard {
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
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
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .exams-dashboard {
        padding: 1rem !important;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    .stat-card {
        padding: 1.5rem !important;
        border-radius: 0.75rem !important;
    }
    
    .stat-value {
        font-size: 1.75rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .stat-label {
        font-size: 0.875rem !important;
    }
    
    /* Mobile table styles */
    .table-responsive {
        font-size: 0.875rem !important;
    }
    
    .table th,
    .table td {
        padding: 0.5rem !important;
    }
    
    /* Mobile buttons */
    .btn {
        min-height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
    }
    
    .btn-group {
        flex-direction: column !important;
        width: 100% !important;
    }
    
    .btn-group .btn {
        margin-bottom: 0.5rem !important;
    }
}

@media (max-width: 640px) {
    .stats-grid {
        grid-template-columns: 1fr !important;
        gap: 0.75rem !important;
    }
    
    .stat-card {
        padding: 1rem !important;
    }
    
    .stat-value {
        font-size: 1.5rem !important;
    }
    
    .table-responsive {
        font-size: 0.8125rem !important;
    }
    
    .table th,
    .table td {
        padding: 0.375rem !important;
    }
}

/* Touch optimizations */
@media (hover: none) and (pointer: coarse) {
    .stat-card {
        cursor: pointer;
    }
    
    .btn {
        min-height: 44px;
        min-width: 44px;
    }
}

.exam-filters {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.exam-card {
    background: white;
    border-radius: 10px;
    padding: 25px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border-left: 4px solid #dee2e6;
}

.exam-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
}

.exam-card.active { border-left-color: #28a745; }
.exam-card.inactive { border-left-color: #dc3545; }
.exam-card.scheduled { border-left-color: #ffc107; }

.exam-header {
    display: flex;
    justify-content: between;
    align-items: start;
    margin-bottom: 15px;
}

.exam-title {
    font-size: 20px;
    font-weight: 600;
    color: #495057;
    margin-bottom: 5px;
}

.exam-meta {
    color: #6c757d;
    font-size: 14px;
}

.exam-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 15px;
    margin: 20px 0;
}

.exam-stat {
    text-align: center;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 8px;
}

.exam-stat-value {
    font-size: 18px;
    font-weight: bold;
    color: #007bff;
}

.exam-stat-label {
    font-size: 12px;
    color: #6c757d;
    margin-top: 2px;
}

.exam-actions {
    display: flex;
    gap: 10px;
    margin-top: 15px;
    flex-wrap: wrap;
}

.btn-exam {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.btn-exam:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.quick-actions {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 15px;
    padding: 30px;
    margin-bottom: 30px;
}

.quick-action-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 15px 25px;
    border-radius: 25px;
    text-decoration: none;
    display: inline-block;
    margin: 10px;
    transition: all 0.3s ease;
}

.quick-action-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
}

.recent-activity {
    background: white;
    border-radius: 10px;
    padding: 25px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
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
.activity-icon.result { background: #e8f5e8; color: #4caf50; }
.activity-icon.student { background: #fff3e0; color: #ff9800; }

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
    .exams-dashboard {
        padding: 10px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 10px;
    }
    
    .exam-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .exam-header {
        flex-direction: column;
    }
    
    .exam-actions {
        justify-content: center;
    }
}
</style>
@endsection

@section('content')
<div class="exams-dashboard">
    <!-- Quick Actions -->
    <div class="quick-actions">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Exam Dashboard</h2>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('admin.exams.create') }}" class="quick-action-btn">
                    <i class="fas fa-plus"></i> Create New Exam
                </a>
                <a href="{{ route('admin.exams.bulk-upload') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="fas fa-upload"></i> Bulk Upload Questions
                </a>
                <a href="#" class="quick-action-btn" onclick="alert('Question banks not yet implemented')">
                    <i class="fas fa-database"></i> Question Banks
                </a>
            </div>
            <div class="col-md-6">
                <a href="#" class="quick-action-btn" onclick="importQuestions()">
                    <i class="fas fa-upload"></i> Import Questions
                </a>
                <a href="#" class="quick-action-btn" onclick="generateReport()">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-value">{{ $exams->count() }}</div>
            <div class="stat-label">Total Exams</div>
        </div>
        <div class="stat-card success">
            <div class="stat-value">{{ $exams->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active Exams</div>
        </div>
        <div class="stat-card info">
            <div class="stat-value">{{ \App\Models\ExamAttempt::count() }}</div>
            <div class="stat-label">Total Attempts</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-value">{{ \App\Models\QuestionBank::count() }}</div>
            <div class="stat-label">Question Banks</div>
        </div>
        <div class="stat-card danger">
            <div class="stat-value">{{ \App\Models\StudentAnswer::where('needs_manual_grading', true)->count() }}</div>
            <div class="stat-label">Pending Grading</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="exam-filters">
        <form method="GET" action="{{ route('admin.exams.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search exams..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filter
                        </button>
                        <a href="{{ route('admin.exams.index') }}" class="btn btn-secondary">Clear</a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Exams List -->
    <div class="row">
        <div class="col-lg-8">
            @forelse($exams as $exam)
                @php
                    $statusClass = $exam->is_active ? 'active' : 'inactive';
                    $totalAttempts = $exam->attempts->count();
                    $completedAttempts = $exam->attempts->where('status', 'submitted')->count();
                    $pendingGrading = $exam->questions->sum(function($q) use ($exam) {
                        return \App\Models\StudentAnswer::whereHas('attempt', function($attempt) use ($exam) {
                            $attempt->where('exam_id', $exam->id);
                        })->where('question_id', $q->id)->where('needs_manual_grading', true)->count();
                    });
                @endphp
                
                <div class="exam-card {{ $statusClass }}">
                    <div class="exam-header">
                        <div class="flex-grow-1">
                            <h3 class="exam-title">{{ $exam->title }}</h3>
                            <div class="exam-meta">
                                <span><i class="fas fa-book"></i> {{ $exam->subject }}</span> •
                                <span><i class="fas fa-layer-group"></i> {{ $exam->courses->pluck('course_name')->join(', ') }}</span> •
                                <span><i class="fas fa-calendar"></i> {{ $exam->class_date->format('M d, Y') }}</span>
                            </div>
                        </div>
                        <div>
                            @if($exam->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-secondary">Inactive</span>
                            @endif
                        </div>
                    </div>

                    <div class="exam-stats">
                        <div class="exam-stat">
                            <div class="exam-stat-value">{{ $exam->questions->count() }}</div>
                            <div class="exam-stat-label">Questions</div>
                        </div>
                        <div class="exam-stat">
                            <div class="exam-stat-value">{{ $exam->questions->sum('marks') }}</div>
                            <div class="exam-stat-label">Total Marks</div>
                        </div>
                        <div class="exam-stat">
                            <div class="exam-stat-value">{{ $totalAttempts }}</div>
                            <div class="exam-stat-label">Attempts</div>
                        </div>
                        <div class="exam-stat">
                            <div class="exam-stat-value">{{ $completedAttempts }}</div>
                            <div class="exam-stat-label">Completed</div>
                        </div>
                        @if($pendingGrading > 0)
                            <div class="exam-stat">
                                <div class="exam-stat-value text-warning">{{ $pendingGrading }}</div>
                                <div class="exam-stat-label">To Grade</div>
                            </div>
                            <div class="btn-group" role="group">
                                <a href="{{ route('admin.exams.show', $exam->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                                <a href="{{ route('exams.questions.index', $exam->id) }}" class="btn btn-sm btn-outline-info">
                                    <i class="bi bi-question-circle"></i> Questions
                                </a>
                                <a href="{{ route('admin.exams.preview', $exam->id) }}" class="btn btn-sm btn-outline-success" target="_blank">
                                    <i class="bi bi-play-circle"></i> Preview
                                </a>
                                @if($exam->is_public)
                                    <form action="{{ route('admin.exams.makePrivate', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-warning">
                                            <i class="bi bi-lock"></i> Make Private
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.exams.makePublic', $exam->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-unlock"></i> Make Public
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.exams.results', $exam->id) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-graph-up"></i> Results
                                </a>
                                <a href="{{ route('admin.exams.edit', $exam->id) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form action="{{ route('admin.exams.destroy', $exam->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this exam? This action cannot be undone and will remove all associated questions, results, and data.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i> Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>

                    <div class="exam-actions">
                        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-primary btn-exam">
                            <i class="fas fa-eye"></i> View
                        </a>
                        <a href="{{ route('exams.questions', $exam) }}" class="btn btn-info btn-exam">
                            <i class="fas fa-question-circle"></i> Questions
                        </a>
                        @if($exam->is_public)
                            <span class="btn btn-success btn-exam">
                                <i class="fas fa-globe"></i> Public ({{ number_format($exam->public_price, 2) }} RWF)
                            </span>
                            <button class="btn btn-warning btn-exam" onclick="makePrivate({{ $exam->id }})">
                                <i class="fas fa-lock"></i> Make Private
                            </button>
                        @else
                            <button class="btn btn-success btn-exam" onclick="showMakePublicModal({{ $exam->id }}, {{ json_encode($exam->title) }})">
                                <i class="fas fa-globe"></i> Make Public
                            </button>
                        @endif
                        <a href="#" class="btn btn-info btn-exam" onclick="alert('Results not yet implemented')">
                            <i class="fas fa-chart-bar"></i> Results
                        </a>
                        <a href="#" class="btn btn-warning btn-exam" onclick="alert('Analytics not yet implemented')">
                            <i class="fas fa-analytics"></i> Analytics
                        </a>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary btn-exam">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <form action="{{ route('admin.exams.destroy', $exam) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-exam" onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone and will delete all related data including questions, marks, attempts, and analytics.')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No exams found</h4>
                    <p class="text-muted">Get started by creating your first exam.</p>
                    <a href="{{ route('admin.exams.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create Exam
                    </a>
                </div>
            @endforelse
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-4">
            <div class="recent-activity">
                <h5 class="mb-4"><i class="fas fa-history"></i> Recent Activity</h5>
                
                @php
                    $recentActivities = collect([
                        (object)[
                            'type' => 'exam',
                            'text' => 'New exam "Mathematics Test" created',
                            'time' => '2 hours ago'
                        ],
                        (object)[
                            'type' => 'student',
                            'text' => '5 students completed "Physics Quiz"',
                            'time' => '3 hours ago'
                        ],
                        (object)[
                            'type' => 'question',
                            'text' => '10 questions added to "Chemistry Bank"',
                            'time' => '5 hours ago'
                        ],
                        (object)[
                            'type' => 'result',
                            'text' => 'Grading completed for "Biology Exam"',
                            'time' => '1 day ago'
                        ]
                    ]);
                @endphp

                @foreach($recentActivities as $activity)
                    <div class="activity-item">
                        <div class="activity-icon {{ $activity->type }}">
                            @if($activity->type === 'exam')
                                <i class="fas fa-file-alt"></i>
                            @elseif($activity->type === 'student')
                                <i class="fas fa-user-graduate"></i>
                            @elseif($activity->type === 'question')
                                <i class="fas fa-question"></i>
                            @else
                                <i class="fas fa-chart-line"></i>
                            @endif
                        </div>
                        <div class="activity-content">
                            <div class="activity-text">{{ $activity->text }}</div>
                            <div class="activity-time">{{ $activity->time }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Quick Tips -->
            <div class="recent-activity mt-3">
                <h5 class="mb-4"><i class="fas fa-lightbulb"></i> Quick Tips</h5>
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Did you know?</h6>
                    <p class="mb-0">You can import questions from CSV files to quickly populate your question banks.</p>
                </div>
                <div class="alert alert-success">
                    <h6><i class="fas fa-check-circle"></i> Pro Tip</h6>
                    <p class="mb-0">Use question banks to organize questions by topic and difficulty for easy reuse.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    @if($exams->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $exams->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function importQuestions() {
    // Open import modal or redirect to import page
    alert('Import questions not yet implemented');
}

function generateReport() {
    // Generate comprehensive report
    alert('Report generation not yet implemented');
}

// Auto-refresh for real-time updates
setInterval(function() {
    // Refresh pending grading count - disabled until route is implemented
}, 30000); // Every 30 seconds

// Search functionality
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    if (e.target.value.length > 2 || e.target.value.length === 0) {
        e.target.form.submit();
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + N for new exam
    if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
        e.preventDefault();
        window.location.href = '{{ route("admin.exams.create") }}';
    }
    
    // Ctrl/Cmd + F for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
        e.preventDefault();
        document.querySelector('input[name="search"]').focus();
    }
});

// Smooth animations for stat cards
document.addEventListener('DOMContentLoaded', function() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    document.querySelectorAll('.stat-card, .exam-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });
});

// Make Exam Public Modal Functions
function showMakePublicModal(examId, examTitle) {
    const modal = document.getElementById('publicPriceModal');
    const examIdInput = document.getElementById('examId');
    const examTitleSpan = document.getElementById('examTitleModal');
    
    if (!modal || !examIdInput || !examTitleSpan) {
        alert('Error: Required modal elements not found');
        return;
    }
    
    examIdInput.value = examId;
    examTitleSpan.textContent = examTitle;
    
    // Show modal using traditional approach
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function hideMakePublicModal() {
    const modal = document.getElementById('publicPriceModal');
    if (modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto'; // Restore background scrolling
    }
}

// Close modal when clicking on backdrop
document.addEventListener('click', function(event) {
    const modal = document.getElementById('publicPriceModal');
    if (event.target === modal) {
        hideMakePublicModal();
    }
});

function makePrivate(examId) {
    if (confirm('Are you sure you want to make this exam private? Students will no longer be able to access it publicly.')) {
        fetch(`/admin/exams/${examId}/make-private`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while making the exam private.');
        });
    }
}

function confirmMakePublic() {
    const examId = document.getElementById('examId').value;
    const publicPrice = document.getElementById('publicPrice').value;
    const tokenCost = document.getElementById('tokenCost').value;
    
    // Convert to numbers for proper validation
    const price = parseFloat(publicPrice);
    const tokens = parseInt(tokenCost);
    
    if (isNaN(price) || price < 0) {
        alert('Please enter a valid price (0 or greater).');
        return;
    }
    
    if (isNaN(tokens) || tokens < 0) {
        alert('Please enter a valid token cost (0 or greater).');
        return;
    }
    
    const confirmMessage = price > 0 
        ? `Are you sure you want to make this exam public for $${price}?`
        : 'Are you sure you want to make this exam public for free?';
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    fetch(`/admin/exams/${examId}/make-public`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            public_price: price,
            token_cost: tokens
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while making the exam public.');
    });
}
</script>

<!-- Make Exam Public Modal -->
<div id="publicPriceModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                <i class="fas fa-globe"></i> Make Exam Public
            </h5>
            <button type="button" class="btn-close" onclick="hideMakePublicModal()"></button>
        </div>
        <div class="modal-body">
            <p>Set the pricing for "<span id="examTitleModal"></span>" to make it publicly available.</p>
            
            <input type="hidden" id="examId" name="examId">
            
            <div class="mb-3">
                <label for="publicPrice" class="form-label">
                    <i class="fas fa-money-bill"></i> Public Price (RWF)
                </label>
                <input type="number" id="publicPrice" name="publicPrice" class="form-control" 
                       placeholder="Enter 0 for free exam" min="0" step="0.01" required>
                <small class="form-text text-muted">Enter 0 to make the exam free, or set a price for paid access</small>
            </div>
            
            <div class="mb-3">
                <label for="tokenCost" class="form-label">
                    <i class="fas fa-coins"></i> Token Cost
                </label>
                <input type="number" id="tokenCost" name="tokenCost" class="form-control" 
                       placeholder="Enter token cost" min="0" value="10">
                <small class="form-text text-muted">Number of tokens required to take this exam</small>
            </div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Important:</strong> Once public, students can access this exam by paying the specified price. 
                Tokens will be automatically deducted from their account when they attempt the exam.
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="hideMakePublicModal()">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button type="button" class="btn btn-success" onclick="confirmMakePublic()">
                <i class="fas fa-globe"></i> Make Public
            </button>
        </div>
    </div>
</div>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1050;
}

.modal-content {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 8px 8px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.btn-close {
    background: none;
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-close:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #dee2e6;
    display: flex;
    gap: 0.5rem;
    justify-content: flex-end;
}
</style>
@endsection
