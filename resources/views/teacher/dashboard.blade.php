{{-- filepath: resources/views/teacher/dashboard.blade.php --}}
@extends('teacher.layout')

@section('content')
<style>
    :root {
        --primary: #1e40af;
        --primary-light: #3b82f6;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --bg-card: #ffffff;
        --bg-hover: #f8fafc;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        --radius-md: 8px;
        --radius-lg: 12px;
    }

    .dashboard-wrapper {
        padding: 0;
    }

    .welcome-section {
        margin-bottom: 1.5rem;
    }

    .welcome-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.375rem;
    }

    .welcome-subtitle {
        font-size: 0.9375rem;
        color: var(--text-secondary);
        margin: 0;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1rem;
    }

    .dashboard-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .dashboard-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--primary-light);
    }

    .card-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: var(--bg-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        font-size: 1.5rem;
        color: var(--primary);
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }

    .card-description {
        font-size: 0.875rem;
        color: var(--text-secondary);
        line-height: 1.5;
        margin-bottom: 1rem;
        flex-grow: 1;
    }

    .card-action {
        margin-top: auto;
    }

    .btn-card {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.625rem 1rem;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        min-height: 40px;
    }

    .btn-card-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-card-primary:hover {
        background: var(--primary-light);
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-card-outline {
        background: transparent;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-card-outline:hover {
        background: var(--primary);
        color: #fff;
    }

    /* Salary card special styling */
    .salary-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary);
        margin: 0.5rem 0;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .welcome-title {
            font-size: 1.25rem;
        }

        .welcome-subtitle {
            font-size: 0.875rem;
        }

        .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 0.75rem;
        }

        .dashboard-card {
            padding: 1rem;
        }

        .card-icon {
            width: 40px;
            height: 40px;
            font-size: 1.25rem;
            margin-bottom: 0.75rem;
        }

        .card-title {
            font-size: 0.9375rem;
        }

        .card-description {
            font-size: 0.8125rem;
        }
    }

    @media (max-width: 480px) {
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dashboard-wrapper">
    @include('partials.academic-year-banner')

    <div class="welcome-section">
        <h1 class="welcome-title">Welcome, {{ Auth::guard('teacher')->user()->teacher_name ?? 'Teacher' }}!</h1>
        <p class="welcome-subtitle">Manage your classes, exams, and resources from one place.</p>
    </div>

    <div class="dashboard-grid">
        <!-- Marks Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
            <h3 class="card-title">Marks</h3>
            <p class="card-description">Insert or view student marks for your assigned courses.</p>
            <div class="card-action">
                <a href="{{ route('teacher.marks.index') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-pencil-square"></i> Manage Marks
                </a>
            </div>
        </div>

        <!-- Exams Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <h3 class="card-title">Exams</h3>
            <p class="card-description">View and manage examinations for your courses.</p>
            <div class="card-action">
                <a href="{{ route('teacher.exams.index') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-list-ul"></i> View Exams
                </a>
            </div>
        </div>

        <!-- Resources Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-cloud-upload"></i>
            </div>
            <h3 class="card-title">Study Resources</h3>
            <p class="card-description">Upload documents, videos, and YouTube links for students.</p>
            <div class="card-action">
                <a href="{{ route('teacher.resources.create') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-upload"></i> Upload Resource
                </a>
            </div>
        </div>

        <!-- Live Classes Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-camera-video"></i>
            </div>
            <h3 class="card-title">Live Classes</h3>
            <p class="card-description">Manage and join your scheduled live classes.</p>
            <div class="card-action">
                <a href="{{ route('teacher.live-classes.index') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-play-circle"></i> Live Classes
                </a>
            </div>
        </div>

        <!-- Courses Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-book"></i>
            </div>
            <h3 class="card-title">Courses</h3>
            <p class="card-description">View your assigned courses and student details.</p>
            <div class="card-action">
                <a href="{{ route('teacher.courses.details') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-info-circle"></i> View Courses
                </a>
            </div>
        </div>

        <!-- Question Banks Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-question-circle"></i>
            </div>
            <h3 class="card-title">Question Banks</h3>
            <p class="card-description">Import and manage questions for your examinations.</p>
            <div class="card-action">
                <a href="{{ route('teacher.question-banks.index') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-database"></i> Manage Banks
                </a>
            </div>
        </div>

        <!-- Timetables Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-calendar2-week"></i>
            </div>
            <h3 class="card-title">My Timetables</h3>
            <p class="card-description">View your weekly schedule and class sessions.</p>
            <div class="card-action">
                <a href="{{ route('teacher.timetables.index') }}" class="btn-card btn-card-primary">
                    <i class="bi bi-calendar"></i> View Schedule
                </a>
            </div>
        </div>

        <!-- Salary Card -->
        <div class="dashboard-card">
            <div class="card-icon">
                <i class="bi bi-cash-coin"></i>
            </div>
            <h3 class="card-title">Salary</h3>
            <div class="salary-value" id="salaryAmount" style="display:none;">
                {{ Auth::guard('teacher')->user()->salary ?? 'N/A' }}
            </div>
            <div class="salary-value" id="salaryHidden">******</div>
            <p class="card-description">View your salary information and request advances.</p>
            <div class="card-action">
                <button class="btn-card btn-card-outline" id="showSalaryBtn">
                    <i class="bi bi-eye"></i> Show Salary
                </button>
                <button class="btn-card btn-card-outline" onclick="requestAdvance()">
                    <i class="bi bi-arrow-up-circle"></i> Request Advance
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('showSalaryBtn').addEventListener('click', function() {
    document.getElementById('salaryAmount').style.display = 'block';
    document.getElementById('salaryHidden').style.display = 'none';
    this.style.display = 'none';
});
function requestAdvance() {
    alert('Advance request feature coming soon!');
}
</script>
@endsection
