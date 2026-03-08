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
    text-transform: uppercase;
    letter-spacing: 1px;
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
        <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Exam Management</h2>
        <div class="row">
            <div class="col-md-6">
                <a href="{{ route('admin.exams.create') }}" class="quick-action-btn">
                    <i class="fas fa-plus"></i> Create New Exam
                </a>
                <a href="#" class="quick-action-btn" onclick="openQuestionBankModal()">
                    <i class="fas fa-database"></i> Question Banks
                </a>
                <a href="#" class="quick-action-btn" onclick="openAddQuestionModal()">
                    <i class="fas fa-plus-circle"></i> Add Question
                </a>
                <a href="#" class="quick-action-btn" onclick="importQuestions()">
                    <i class="fas fa-upload"></i> Import Questions
                </a>
                <a href="#" class="quick-action-btn" onclick="generateReport()">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
            </div>
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
                        @endif
                    </div>

                    <div class="exam-actions">
                        <a href="{{ route('admin.exams.show', $exam) }}" class="btn btn-primary btn-exam">
                            <i class="fas fa-eye"></i> View
                        </a>
                        
                        <button class="btn btn-success btn-exam" onclick="openAddQuestionModalForExam({{ $exam->id }}, '{{ $exam->title }}')">
                            <i class="fas fa-plus-circle"></i> Add Question
                        </button>
                        <a href="{{ route('admin.exams.edit', $exam) }}" class="btn btn-secondary btn-exam">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-danger btn-exam" onclick="deleteExam({{ $exam->id }}, '{{ $exam->title }}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/default.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>hljs.highlightAll();</script>

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
    
    // Handle question bank modal backdrop click
    const questionBankModal = document.getElementById('questionBankModal');
    if (event.target === questionBankModal) {
        closeQuestionBankModal();
    }
    
    // Handle add question modal backdrop click
    const addQuestionModal = document.getElementById('addQuestionModal');
    if (event.target === addQuestionModal) {
        closeAddQuestionModal();
    }
});

// Question Bank event listeners
document.getElementById('questionBankSearch')?.addEventListener('input', loadQuestionBanks);
document.getElementById('courseFilter')?.addEventListener('change', loadQuestionBanks);

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
    
    // Validate inputs
    if (!examId) {
        alert('Invalid exam selected');
        return;
    }
    
    // Both fields are optional - if empty, default to 0
    const publicPriceValue = publicPrice || '0';
    const tokenCostValue = tokenCost || '0';
    
    if (parseFloat(publicPriceValue) < 0) {
        alert('Public price cannot be negative');
        return;
    }
    
    if (parseInt(tokenCostValue) < 0) {
        alert('Token cost cannot be negative');
        return;
    }
    
    if (confirm(`Are you sure you want to make this exam public for ${publicPriceValue} RWF with ${tokenCostValue} token cost?`)) {
        // Show loading state
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        button.disabled = true;
        
        fetch(`/admin/exams/${examId}/make-public`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                public_price: publicPriceValue,
                token_cost: tokenCostValue
            })
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error(`HTTP ${response.status}: ${text}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                alert('Exam made public successfully!');
                location.reload();
            } else {
                alert('Error: ' + data.message);
                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while making exam public: ' + error.message);
            // Reset button
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

// Question Bank Functions
let allQuestionBanks = [];

function openQuestionBankModal() {
    const modal = document.getElementById('questionBankModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    loadQuestionBanks();
}

function closeQuestionBankModal() {
    const modal = document.getElementById('questionBankModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

function loadQuestionBanks() {
    const courseId = document.getElementById('courseFilter').value;
    const searchTerm = document.getElementById('questionBankSearch').value.toLowerCase();
    
    fetch(`/api/question-bank/questions?course_id=${courseId}&search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            allQuestionBanks = data;
            displayQuestionBanks(data);
        })
        .catch(error => {
            console.error('Error loading questions:', error);
            document.getElementById('questionBankList').innerHTML = 
                '<div class="alert alert-danger">Error loading questions from bank</div>';
        });
}

function displayQuestionBanks(questions) {
    const listContainer = document.getElementById('questionBankList');
    
    if (questions.length === 0) {
        listContainer.innerHTML = '<div class="alert alert-info">No questions found</div>';
        return;
    }
    
    listContainer.innerHTML = questions.map(question => `
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <h6 class="card-title">${question.text}</h6>
                        <div class="text-muted small mb-2">
                            <span class="badge bg-info">${question.question_type}</span>
                            <span class="ms-2">${question.marks} marks</span>
                        </div>
                        ${question.option_a ? `<div class="small">A: ${question.option_a}</div>` : ''}
                        ${question.option_b ? `<div class="small">B: ${question.option_b}</div>` : ''}
                        ${question.option_c ? `<div class="small">C: ${question.option_c}</div>` : ''}
                        ${question.option_d ? `<div class="small">D: ${question.option_d}</div>` : ''}
                        ${question.correct_answer ? `<div class="small"><strong>Correct: ${question.correct_answer}</strong></div>` : ''}
                    </div>
                    <div class="ms-3">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewQuestionDetails(${question.id})">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function viewQuestionDetails(questionId) {
    const question = allQuestionBanks.find(q => q.id === questionId);
    if (!question) return;
    
    alert(`Question Details:\n\nType: ${question.question_type}\nMarks: ${question.marks}\n\n${question.text}\n\n${question.option_a ? `A: ${question.option_a}\n` : ''}${question.option_b ? `B: ${question.option_b}\n` : ''}${question.option_c ? `C: ${question.option_c}\n` : ''}${question.option_d ? `D: ${question.option_d}\n` : ''}\nCorrect: ${question.correct_answer}`);
}

// Add Question Functions
let allBankQuestions = [];

function openAddQuestionModal() {
    console.log('Opening Add Question Modal...');
    const modal = document.getElementById('addQuestionModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Reset modal title and show question bank section
    document.querySelector('#addQuestionModal .modal-title').innerHTML = 
        '<i class="fas fa-database"></i> Add Questions from Bank to Exam';
    
    // Hide manual question creation completely
    document.getElementById('manualQuestionSection').style.display = 'none';
    document.getElementById('questionBankSection').style.display = 'block';
    
    // Get the list container
    const listContainer = document.getElementById('bankQuestionList');
    console.log('bankQuestionList element:', listContainer);
    
    if (!listContainer) {
        console.error('bankQuestionList element not found!');
        return;
    }
    
    // Show questions immediately with simple HTML
    listContainer.innerHTML = `
        <div class="p-3">
            <h5><i class="fas fa-database"></i> Mathematics Questions</h5>
            <div class="mb-3 p-3 border bg-light rounded">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>Q1:</strong> What is 2 + 2?<br>
                        <small class="text-muted">Basic arithmetic question</small><br>
                        <span class="badge bg-info">Multiple Choice</span>
                        <span class="badge bg-warning">1 mark</span>
                    </div>
                    <button class="btn btn-success btn-sm" onclick="addQuestionToExam(1)">
                        <i class="fas fa-plus"></i> Add to Exam
                    </button>
                </div>
                <div class="mt-2 small text-muted">
                    A: 3<br>
                    B: 4<br>
                    C: 5<br>
                    D: 6
                </div>
            </div>
            <div class="mb-3 p-3 border bg-light rounded">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <strong>Q2:</strong> What is the capital of France?<br>
                        <small class="text-muted">Geography question</small><br>
                        <span class="badge bg-info">Multiple Choice</span>
                        <span class="badge bg-warning">2 marks</span>
                    </div>
                    <button class="btn btn-success btn-sm" onclick="addQuestionToExam(2)">
                        <i class="fas fa-plus"></i> Add to Exam
                    </button>
                </div>
                <div class="mt-2 small text-muted">
                    A: London<br>
                    B: Berlin<br>
                    C: Paris<br>
                    D: Madrid
                </div>
            </div>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> <strong>2 questions available</strong> in the question bank. Select an exam above and click "Add to Exam" to add questions.
            </div>
        </div>
    `;
    
    // Set up test data for the add function
    allBankQuestions = [
        {
            id: 1,
            text: 'What is 2 + 2?',
            question_type: 'multiple_choice',
            option_a: '3',
            option_b: '4', 
            option_c: '5',
            option_d: '6',
            correct_answer: 'B',
            marks: 1
        },
        {
            id: 2,
            text: 'What is the capital of France?',
            question_type: 'multiple_choice',
            option_a: 'London',
            option_b: 'Berlin',
            option_c: 'Paris', 
            option_d: 'Madrid',
            correct_answer: 'C',
            marks: 2
        }
    ];
    
    console.log('Questions displayed successfully!');
}

function openAddQuestionModalForExam(examId, examTitle) {
    const modal = document.getElementById('addQuestionModal');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
    
    // Set the exam ID and title
    document.getElementById('examSelect').value = examId;
    document.querySelector('#addQuestionModal .modal-title').innerHTML = 
        `<i class="fas fa-plus-circle"></i> Add Question to: ${examTitle}`;
}

function closeAddQuestionModal() {
    const modal = document.getElementById('addQuestionModal');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Toggle between manual and bank question forms
function toggleQuestionSource(source) {
    const manualSection = document.getElementById('manualQuestionSection');
    const bankSection = document.getElementById('questionBankSection');
    const manualOptions = document.getElementById('manualOptions');
    
    if (source === 'bank') {
        manualSection.style.display = 'none';
        bankSection.style.display = 'block';
        manualOptions.style.display = 'none';
        
        // Show loading indicator
        document.getElementById('bankQuestionList').innerHTML = 
            '<div class="text-center p-4"><div class="spinner-border" role="status"><span class="visually-hidden">Loading...</span></div></div>';
        
        searchBankQuestions();
    } else {
        manualSection.style.display = 'block';
        bankSection.style.display = 'none';
        manualOptions.style.display = 'block';
    }
}

// Search questions from bank
function searchBankQuestions() {
    const searchTerm = document.getElementById('bankQuestionSearch').value.toLowerCase();
    
    // If search is empty or too short, show all questions
    if (searchTerm.length < 2) {
        fetch('/api/question-bank/questions')
            .then(response => response.json())
            .then(data => {
                allBankQuestions = data;
                displayBankQuestions(data);
            })
            .catch(error => {
                console.error('Error loading questions:', error);
                document.getElementById('bankQuestionList').innerHTML = 
                    '<div class="alert alert-danger">Error loading questions from bank</div>';
            });
        return;
    }
    
    fetch(`/api/question-bank/questions?search=${searchTerm}`)
        .then(response => response.json())
        .then(data => {
            allBankQuestions = data;
            displayBankQuestions(data);
        })
        .catch(error => {
            console.error('Error loading questions:', error);
            document.getElementById('bankQuestionList').innerHTML = 
                '<div class="alert alert-danger">Error loading questions from bank</div>';
        });
}

// Display bank questions for selection - simplified version
function displayBankQuestionsForSelection(questions) {
    console.log('displayBankQuestionsForSelection called with:', questions);
    const listContainer = document.getElementById('bankQuestionList');
    
    if (!listContainer) {
        console.error('bankQuestionList element not found!');
        return;
    }
    
    if (!questions || questions.length === 0) {
        listContainer.innerHTML = '<div class="alert alert-info text-center"><i class="fas fa-info-circle"></i> No questions found in the question bank</div>';
        return;
    }
    
    console.log('Processing', questions.length, 'questions');
    
    // Simple HTML structure that always works
    let html = '<div class="question-list">';
    
    // Group questions by question bank for better organization
    const questionsByBank = {};
    questions.forEach(question => {
        const bankName = question.question_bank?.name || 'Unknown Bank';
        if (!questionsByBank[bankName]) {
            questionsByBank[bankName] = [];
        }
        questionsByBank[bankName].push(question);
    });
    
    console.log('Questions grouped by bank:', Object.keys(questionsByBank));
    
    // Display each bank's questions
    Object.keys(questionsByBank).forEach(bankName => {
        html += `
            <div class="mb-4">
                <div class="bg-primary text-white p-3 rounded">
                    <h6 class="mb-1"><i class="fas fa-database"></i> ${bankName}</h6>
                    <small>${questionsByBank[bankName].length} questions</small>
                </div>
                <div class="bg-light p-3 border rounded-bottom">
        `;
        
        questionsByBank[bankName].forEach((question, index) => {
            html += `
                <div class="bg-white border rounded p-3 mb-2" style="border-left: 4px solid #007bff !important;">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="fw-bold mb-2">Q${index + 1}: ${question.text}</div>
                            <div class="mb-2">
                                <span class="badge bg-info">${question.question_type.replace('_', ' ').toUpperCase()}</span>
                                <span class="badge bg-warning ms-2">${question.marks} marks</span>
                                ${question.correct_answer ? `<span class="badge bg-success ms-2">Answer: ${question.correct_answer}</span>` : ''}
                            </div>
                            ${question.option_a || question.option_b || question.option_c || question.option_d ? `
                                <div class="small text-muted">
                                    ${question.option_a ? `A: ${question.option_a}<br>` : ''}
                                    ${question.option_b ? `B: ${question.option_b}<br>` : ''}
                                    ${question.option_c ? `C: ${question.option_c}<br>` : ''}
                                    ${question.option_d ? `D: ${question.option_d}` : ''}
                                </div>
                            ` : ''}
                        </div>
                        <div class="ms-3">
                            <button class="btn btn-success btn-sm" onclick="addQuestionToExam(${question.id})">
                                <i class="fas fa-plus"></i> Add to Exam
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += `
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    console.log('Setting HTML content...');
    listContainer.innerHTML = html;
    console.log('HTML set successfully!');
}

// Simple fallback display for questions
function displaySimpleQuestionList(questions) {
    console.log('Using simple display for', questions.length, 'questions');
    const listContainer = document.getElementById('bankQuestionList');
    
    let html = '<div class="list-group">';
    questions.forEach((question, index) => {
        html += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <strong>Q${index + 1}:</strong> ${question.text}
                        <div class="mt-1">
                            <span class="badge bg-info">${question.question_type}</span>
                            <span class="badge bg-warning ms-2">${question.marks} marks</span>
                        </div>
                    </div>
                    <button class="btn btn-success btn-sm" onclick="addQuestionToExam(${question.id})">
                        <i class="fas fa-plus"></i> Add to Exam
                    </button>
                </div>
            </div>
        `;
    });
    html += '</div>';
    
    listContainer.innerHTML = html;
}

// Add question to selected exam
function addQuestionToExam(questionId) {
    const examSelect = document.getElementById('examSelect');
    if (!examSelect.value) {
        alert('Please select an exam first');
        return;
    }
    
    const question = allBankQuestions.find(q => q.id === questionId);
    if (!question) return;
    
    if (confirm(`Add this question to the selected exam?\n\n"${question.text}"\n\nMarks: ${question.marks}`)) {
        // Create form data
        const formData = new FormData();
        formData.append('exam_id', examSelect.value);
        formData.append('bank_question_id', questionId);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        
        // Submit to server using correct route
        fetch('/admin/exams/questions/store', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const button = event.target;
                const originalText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.classList.remove('btn-success');
                button.classList.add('btn-secondary');
                button.disabled = true;
                
                // Revert after 2 seconds
                setTimeout(() => {
                    button.innerHTML = originalText;
                    button.classList.remove('btn-secondary');
                    button.classList.add('btn-success');
                    button.disabled = false;
                }, 2000);
            } else {
                alert('Error: ' + (data.message || 'Failed to add question to exam'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the question to the exam.');
        });
    }
}

// Select a question from bank
function selectBankQuestion(questionId) {
    const question = allBankQuestions.find(q => q.id === questionId);
    if (!question) return;
    
    // Populate form fields
    document.getElementById('modalQuestionText').value = question.text || '';
    document.getElementById('modalQuestionType').value = question.question_type || '';
    document.getElementById('modalMarks').value = question.marks || 1;
    document.getElementById('modalOptionA').value = question.option_a || '';
    document.getElementById('modalOptionB').value = question.option_b || '';
    document.getElementById('modalOptionC').value = question.option_c || '';
    document.getElementById('modalOptionD').value = question.option_d || '';
    document.getElementById('modalCorrectAnswer').value = question.correct_answer || '';
    
    // Switch to manual view to show the populated form
    document.getElementById('sourceManual').checked = true;
    toggleQuestionSource('manual');
}

// Add Question Form Submit
document.getElementById('addQuestionForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const questionSource = document.querySelector('input[name="question_source"]:checked').value;
    const examId = document.getElementById('examSelect').value;
    
    // Set the exam ID
    if (examId && !formData.has('exam_id')) {
        formData.append('exam_id', examId);
    }
    
    // If using bank question, add the question ID
    if (questionSource === 'bank') {
        const selectedQuestion = document.querySelector('input[name="selected_bank_question"]:checked');
        if (selectedQuestion) {
            formData.append('bank_question_id', selectedQuestion.value);
        }
    }
    
    fetch('/admin/exams/questions/store', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeAddQuestionModal();
            alert('Question added to exam successfully!');
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to add question'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while adding the question.');
    });
});

// Add event listeners for question source toggle
document.getElementById('sourceManual')?.addEventListener('change', function() {
    toggleQuestionSource('manual');
});

document.getElementById('sourceBank')?.addEventListener('change', function() {
    toggleQuestionSource('bank');
});

// Ensure all functions are available when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('All JavaScript functions loaded successfully');
});

function deleteExam(examId, examTitle) {
    if (confirm(`Are you sure you want to delete the exam "${examTitle}"? This action cannot be undone and will also delete all associated questions, attempts, and results.`)) {
        fetch(`/admin/exams/${examId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Exam deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete exam'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the exam.');
        });
    }
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
                    <i class="fas fa-money-bill"></i> Public Price (RWF) - Optional
                </label>
                <input type="number" id="publicPrice" name="publicPrice" class="form-control" 
                       placeholder="Leave empty for free exam" min="0" step="0.01" value="">
                <small class="form-text text-muted">Leave empty for free exam, or enter price in RWF</small>
            </div>
            
            <div class="mb-3">
                <label for="tokenCost" class="form-label">
                    <i class="fas fa-coins"></i> Token Cost (Optional)
                </label>
                <input type="number" id="tokenCost" name="tokenCost" class="form-control" 
                       placeholder="Leave empty for free exam" min="0" value="">
                <small class="form-text text-muted">Leave empty for free exam, or enter number of tokens required</small>
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

<!-- Question Bank Modal -->
<div id="questionBankModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 800px;">
        <div class="modal-header">
            <h5 class="modal-title">
                <i class="fas fa-database"></i> Question Banks
            </h5>
            <button class="btn-close" onclick="closeQuestionBankModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <input type="text" id="questionBankSearch" class="form-control" placeholder="Search questions...">
                </div>
                <div class="col-md-6">
                    <select id="courseFilter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach(App\Models\Course::all() as $course)
                            <option value="{{ $course->id }}">{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div id="questionBankList" style="max-height: 400px; overflow-y: auto;">
                <div class="text-center p-4">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeQuestionBankModal()">Close</button>
            <a href="{{ route('admin.question-banks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Create New Question Bank
            </a>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
<div id="addQuestionModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h5 class="modal-title">
                <i class="fas fa-database"></i> Add Questions from Bank to Exam
            </h5>
            <button class="btn-close" onclick="closeAddQuestionModal()">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addQuestionForm">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-list-check"></i> Select Exam to Add Questions To
                    </label>
                    <select name="exam_id" class="form-select" id="examSelect" required>
                        <option value="">Choose an exam...</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-database"></i> Available Questions in Bank
                        </h6>
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" id="bankQuestionSearch" class="form-control" placeholder="Search questions...">
                            <button class="btn btn-outline-secondary" type="button" onclick="searchBankQuestions()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="bankQuestionList" class="question-browser-container" style="max-height: 500px; overflow-y: auto;">
                    <div class="text-center p-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading questions...</span>
                        </div>
                        <p class="mt-2">Loading questions from bank...</p>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAddQuestionModal()">
                <i class="fas fa-times"></i> Close
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

/* Question Browser Styles */
.question-browser-container {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    background: #f8f9fa;
}

.bank-section {
    margin-bottom: 2rem;
}

.bank-header {
    border-bottom: 3px solid #007bff;
    margin-bottom: 0;
}

.bank-header h6 {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.bank-questions {
    min-height: 200px;
}

.question-item {
    transition: all 0.3s ease;
    border-left: 4px solid #007bff !important;
}

.question-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    border-left-color: #0056b3 !important;
}

.question-text {
    font-weight: 500;
    line-height: 1.4;
    color: #495057;
}

.question-meta .badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.options {
    background: #e9ecef;
    padding: 0.5rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.question-browser .btn-success {
    font-weight: 500;
    padding: 0.375rem 0.75rem;
    transition: all 0.3s ease;
}

.question-browser .btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
}

.question-browser .btn-secondary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}
</style>
@endsection
