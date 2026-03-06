{{-- filepath: d:\website\esa\resources\views\students\exams.blade.php --}}
@extends('students.app')

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .exams-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        margin: 20px auto;
        max-width: 1200px;
        overflow: hidden;
    }
    
    .exams-header {
        background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
        color: white;
        padding: 30px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .exams-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: repeating-linear-gradient(
            45deg,
            transparent,
            transparent 10px,
            rgba(255, 255, 255, 0.1) 10px,
            rgba(255, 255, 255, 0.1) 20px
        );
        animation: shimmer 20s linear infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
    }
    
    .exams-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        position: relative;
        z-index: 1;
    }
    
    .exams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
        gap: 25px;
        padding: 30px;
    }
    
    .exam-card {
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        padding: 25px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .exam-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(45deg, #4facfe, #00f2fe);
    }
    
    .exam-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }
    
    .exam-card-future::before {
        background: linear-gradient(45deg, #56ab2f, #a8e6cf);
    }
    
    .exam-card-completed::before {
        background: linear-gradient(45deg, #4facfe, #00f2fe);
    }
    
    .exam-card-past::before {
        background: linear-gradient(45deg, #bdc3c7, #2c3e50);
    }
    
    .exam-title {
        font-size: 1.4rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
        line-height: 1.3;
    }
    
    .exam-description {
        color: #7f8c8d;
        font-size: 1rem;
        margin-bottom: 20px;
        line-height: 1.5;
        min-height: 48px;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .exam-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        margin-bottom: 20px;
    }
    
    .exam-detail {
        background: rgba(79, 172, 254, 0.1);
        padding: 12px 15px;
        border-radius: 12px;
        text-align: center;
        border: 1px solid rgba(79, 172, 254, 0.2);
    }
    
    .exam-detail-label {
        font-size: 0.8rem;
        color: #7f8c8d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .exam-detail-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2c3e50;
    }
    
    .exam-date-detail {
        grid-column: 1 / -1;
        background: rgba(255, 193, 7, 0.1);
        border-color: rgba(255, 193, 7, 0.3);
    }
    
    .exam-date-detail .exam-detail-value {
        color: #f39c12;
        font-size: 1rem;
    }
    
    .exam-actions {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    
    .action-btn {
        padding: 12px 25px;
        border-radius: 25px;
        font-weight: 600;
        border: none;
        text-decoration: none;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }
    
    .action-btn:hover::before {
        left: 100%;
    }
    
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        text-decoration: none;
    }
    
    .btn-take-exam {
        background: linear-gradient(45deg, #56ab2f, #a8e6cf);
        color: white;
        box-shadow: 0 5px 15px rgba(86, 171, 47, 0.3);
    }
    
    .btn-take-exam:hover {
        background: linear-gradient(45deg, #4c956c, #a8e6cf);
        color: white;
    }
    
    .btn-view-marks {
        background: linear-gradient(45deg, #4facfe, #00f2fe);
        color: white;
        box-shadow: 0 5px 15px rgba(79, 172, 254, 0.3);
    }
    
    .btn-view-marks:hover {
        background: linear-gradient(45deg, #3498db, #00f2fe);
        color: white;
    }
    
    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-completed {
        background: linear-gradient(45deg, #56ab2f, #a8e6cf);
        color: white;
        box-shadow: 0 3px 10px rgba(86, 171, 47, 0.3);
    }
    
    .badge-not-attempted {
        background: linear-gradient(45deg, #bdc3c7, #2c3e50);
        color: white;
        box-shadow: 0 3px 10px rgba(189, 195, 199, 0.3);
    }
    
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 20px;
        border: 2px dashed #e3f2fd;
    }
    
    .empty-state-icon {
        font-size: 4rem;
        color: #bdc3c7;
        margin-bottom: 20px;
    }
    
    .empty-state-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin-bottom: 10px;
    }
    
    .empty-state-text {
        color: #7f8c8d;
        font-size: 1.1rem;
    }
    
    .exam-status-indicator {
        position: absolute;
        top: 15px;
        right: 15px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.8);
    }
    
    .status-future {
        background: #56ab2f;
        animation: pulse-green 2s infinite;
    }
    
    .status-completed {
        background: #4facfe;
    }
    
    .status-past {
        background: #bdc3c7;
    }
    
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(86, 171, 47, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(86, 171, 47, 0); }
        100% { box-shadow: 0 0 0 0 rgba(86, 171, 47, 0); }
    }
    
    .exam-meta {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 0.9rem;
        color: #7f8c8d;
    }
    
    .exam-meta-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    @media (max-width: 768px) {
        .exams-grid {
            grid-template-columns: 1fr;
            padding: 20px;
            gap: 20px;
        }
        
        .exams-title {
            font-size: 2rem;
        }
        
        .exam-details {
            grid-template-columns: 1fr;
        }
        
        .exam-detail {
            padding: 10px 12px;
        }
        
        .action-btn {
            padding: 10px 20px;
            font-size: 0.8rem;
        }
    }
    
    @media (max-width: 480px) {
        .exam-card {
            padding: 20px;
        }
        
        .exam-title {
            font-size: 1.2rem;
        }
        
        .exams-header {
            padding: 20px;
        }
    }
</style>

<div class="exams-container">
    <div class="exams-header">
        <h2 class="exams-title">📚 My Exams</h2>
    </div>
    
    <div class="exams-grid">
        @forelse($exams as $exam)
            @php
                $today = \Carbon\Carbon::today();
                $examDate = \Carbon\Carbon::parse($exam->date);
                $hasMark = $exam->marks && $exam->marks->count() > 0;
                
                $cardClass = '';
                $statusClass = '';
                if ($examDate->isFuture() || $examDate->isToday()) {
                    $cardClass = 'exam-card-future';
                    $statusClass = 'status-future';
                } elseif ($hasMark) {
                    $cardClass = 'exam-card-completed';
                    $statusClass = 'status-completed';
                } else {
                    $cardClass = 'exam-card-past';
                    $statusClass = 'status-past';
                }
            @endphp
            
            <div class="exam-card {{ $cardClass }}">
                <div class="exam-status-indicator {{ $statusClass }}"></div>
                
                <div class="exam-title">{{ $exam->title }}</div>
                
                <div class="exam-description">
                    {{ $exam->description ?? 'No description available for this exam.' }}
                </div>
                
                <div class="exam-meta">
                    <div class="exam-meta-item">
                        <span>📅</span>
                        <span>{{ \Carbon\Carbon::parse($exam->date)->format('M d, Y') }}</span>
                    </div>
                    <div class="exam-meta-item">
                        <span>⏱️</span>
                        <span>{{ \Carbon\Carbon::parse($exam->date)->format('h:i A') }}</span>
                    </div>
                </div>
                
                <div class="exam-details">
                    <div class="exam-detail">
                        <div class="exam-detail-label">Total Marks</div>
                        <div class="exam-detail-value">{{ $exam->total_marks }}</div>
                    </div>
                    <div class="exam-detail">
                        <div class="exam-detail-label">Weight</div>
                        <div class="exam-detail-value">{{ $exam->exam_weight }}%</div>
                    </div>
                    <div class="exam-detail exam-date-detail">
                        <div class="exam-detail-label">Exam Date</div>
                        <div class="exam-detail-value">{{ \Carbon\Carbon::parse($exam->date)->format('l, F j, Y \a\t g:i A') }}</div>
                    </div>
                </div>
                
                <div class="exam-actions">
                    @if($examDate->isFuture() || $examDate->isToday())
                        @if(!$hasMark)
                            <a href="{{ route('exam.take', $exam->id) }}" class="action-btn btn-take-exam">
                                🚀 Take Exam
                            </a>
                        @else
                            <div class="status-badge badge-completed">
                                ✅ Completed
                            </div>
                        @endif
                    @elseif($examDate->isPast())
                        @if($hasMark)
                            <a href="{{ route('student.exams.marks', $exam->id) }}" class="action-btn btn-view-marks">
                                📊 View Marks
                            </a>
                        @else
                            <div class="status-badge badge-not-attempted">
                                ⏰ Not Attempted
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <div class="empty-state-icon">📚</div>
                <div class="empty-state-title">No Exams Available</div>
                <div class="empty-state-text">You don't have any exams scheduled at the moment. Check back later or contact your instructor.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
