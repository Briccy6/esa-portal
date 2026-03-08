@extends('admin.layout')
@section('title', 'Edit Teacher')

@section('content')
<style>
    .container-fluid {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem 1rem;
    }

    .main-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2.5rem;
        text-align: center;
        position: relative;
    }

    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        opacity: 0.1;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .header-icon {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        backdrop-filter: blur(10px);
    }

    .form-section {
        padding: 3rem;
    }

    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        font-weight: 700 !important;
        color: #2d3748 !important;
        margin-bottom: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
        font-size: 1rem !important;
    }

    .label-icon {
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 0.75rem;
        color: white;
        font-size: 0.875rem;
    }

    .input-wrapper {
        position: relative;
    }

    .form-control {
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 1rem 1.25rem !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
        background: #fff !important;
    }

    .form-control:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        outline: none !important;
        transform: translateY(-1px) !important;
    }

    .selection-container {
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 0.5rem;
    }

    .search-box {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 1.5rem;
        width: 100%;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .search-box:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .item-card {
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .item-card:hover {
        border-color: #667eea;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        transform: translateY(-2px);
    }

    .item-card.selected {
        border-color: #667eea !important;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05)) !important;
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2) !important;
    }

    .item-card input[type="checkbox"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .item-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.5rem;
    }

    .item-name {
        font-weight: 600;
        color: #2d3748;
        font-size: 1.1rem;
        flex: 1;
        margin-right: 1rem;
    }

    .checkmark {
        width: 24px;
        height: 24px;
        border: 2px solid #cbd5e0;
        border-radius: 6px;
        position: relative;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .item-card.selected .checkmark {
        background: #667eea !important;
        border-color: #667eea !important;
    }

    .item-card.selected .checkmark::after {
        content: '✓';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: bold;
        font-size: 16px;
    }

    .selection-summary {
        background: linear-gradient(135deg, #e6fffa, #f0fff4);
        border: 1px solid #9ae6b4;
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1.5rem;
        text-align: center;
    }

    .selection-count {
        color: #22543d;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-top: 3rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    .btn {
        padding: 1rem 2.5rem !important;
        border-radius: 12px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        border: none !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.75rem !important;
        font-size: 1.1rem !important;
        min-width: 160px !important;
        justify-content: center !important;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
        color: white !important;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #5a67d8, #6b46c1) !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3) !important;
        color: white !important;
    }

    .btn-secondary {
        background: #e2e8f0 !important;
        color: #4a5568 !important;
    }

    .btn-secondary:hover {
        background: #cbd5e0 !important;
        transform: translateY(-2px) !important;
        color: #4a5568 !important;
        text-decoration: none !important;
    }

    .error-message {
        background: #fed7d7;
        border: 1px solid #feb2b2;
        color: #c53030;
        padding: 0.75rem 1rem;
        border-radius: 8px;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .help-text {
        color: #718096;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        margin: 2.5rem 0;
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 2rem 1.5rem;
        }
        
        .items-grid {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn {
            width: 100% !important;
        }
    }

    .fade-in {
        opacity: 0;
        animation: fadeIn 0.5s ease forwards;
    }

    @keyframes fadeIn {
        to {
            opacity: 1;
        }
    }

    /* Override Bootstrap/Framework styles */
    .container-fluid .form-control,
    .container-fluid .btn,
    .container-fluid .form-label {
        all: revert;
    }

    .container-fluid .form-control {
        border: 2px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 1rem 1.25rem !important;
        font-size: 1rem !important;
        transition: all 0.3s ease !important;
        background: #fff !important;
    }

    .container-fluid .form-control:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1) !important;
        outline: none !important;
    }
</style>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-8">
            <div class="main-card fade-in">
                <!-- Header Section -->
                <div class="header-section">
                    <div class="header-content">
                        <div class="header-icon">
                            <i class="fas fa-user-edit" style="font-size: 2rem;"></i>
                        </div>
                        <h1 style="margin: 0; font-size: 2.5rem; font-weight: 700;">Edit Teacher</h1>
                        <p style="margin: 0.5rem 0 0; opacity: 0.9; font-size: 1.1rem;">Update teacher information and assignments</p>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="form-section">
                    <form method="POST" action="{{ route('admin.teachers.update', $teacher->id) }}" id="editTeacherForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Teacher Name Field -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <span class="label-icon">
                                    <i class="fas fa-user"></i>
                                </span>
                                Teacher Name
                            </label>
                            <div class="input-wrapper">
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       name="name" 
                                       id="name" 
                                       value="{{ old('name', $teacher->teacher_name) }}" 
                                       placeholder="Enter teacher's full name"
                                       required>
                            </div>
                            @error('name')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Teacher Email Field -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <span class="label-icon">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                Teacher Email
                            </label>
                            <div class="input-wrapper">
                                <input type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       id="email" 
                                       value="{{ old('email', $teacher->email) }}" 
                                       placeholder="Enter teacher's email address"
                                       required>
                            </div>
                            @error('email')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Teacher Photo Field -->
                        <div class="form-group">
                            <label for="photo" class="form-label">Teacher Photo</label>
                            <input type="file"
                                   class="form-control @error('photo') is-invalid @enderror"
                                   name="photo"
                                   id="photo"
                                   accept="image/jpeg,image/png,image/jpg">
                            @if($teacher->photo)
                                <div class="mt-3">
                                    <img src="data:image/jpeg;base64,{{ $teacher->photo }}" alt="Current Photo" style="max-width: 120px; border-radius: 10px;">
                                    <div class="help-text">Current photo</div>
                                </div>
                            @endif
                            @error('photo')
                                <div class="error-message">
                                    {{ $message }}
                                </div>
                            @enderror
                            <div class="help-text">
                                Upload a new photo to replace the current one (JPEG/PNG, max 100KB).
                            </div>
                        </div>

                        <div class="section-divider"></div>

                        <!-- Course Assignment Section -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">
                                    <i class="fas fa-book"></i>
                                </span>
                                Assign Courses
                            </label>
                            
                            <div class="selection-container">
                                <input type="text" 
                                       class="search-box" 
                                       placeholder="🔍 Search courses..."
                                       onkeyup="filterItems('course')">
                                
                                <div class="items-grid" id="courseGrid">
                                    @foreach($courses as $course)
                                        <div class="item-card {{ (in_array($course->id, old('courses', $assignedCourses ?? []))) ? 'selected' : '' }}" 
                                             onclick="toggleItem(this, 'courses[]', {{ $course->id }})"
                                             data-name="{{ strtolower($course->course_name) }}"
                                             data-type="course">
                                            <input type="checkbox" 
                                                   name="courses[]" 
                                                   value="{{ $course->id }}"
                                                   {{ (in_array($course->id, old('courses', $assignedCourses ?? []))) ? 'checked' : '' }}>
                                            
                                            <div class="item-header">
                                                <div class="item-name">{{ $course->course_name }}</div>
                                                <div class="checkmark"></div>
                                            </div>
                                            
                                            @if($course->course_code ?? null)
                                                <div style="color: #718096; font-size: 0.9rem; margin-top: 0.5rem;">
                                                    <i class="fas fa-tag" style="margin-right: 0.5rem;"></i>
                                                    {{ $course->course_code }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="selection-summary">
                                    <div class="selection-count" id="courseCount">
                                        {{ count(old('courses', $assignedCourses ?? [])) }} course(s) selected
                                    </div>
                                    <div class="help-text">
                                        <i class="fas fa-info-circle"></i>
                                        Click on course cards to select or deselect them
                                    </div>
                                </div>
                            </div>

                            @error('courses')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="section-divider"></div>

                        <!-- Branch Assignment Section -->
                        <div class="form-group">
                            <label class="form-label">
                                <span class="label-icon">
                                    <i class="fas fa-code-branch"></i>
                                </span>
                                Assign Branches
                            </label>
                            
                            <div class="selection-container">
                                <input type="text" 
                                       class="search-box" 
                                       placeholder="🔍 Search branches..."
                                       onkeyup="filterItems('branch')">
                                
                                <div class="items-grid" id="branchGrid">
                                    @foreach($branches as $branch)
                                        <div class="item-card {{ in_array($branch->id, old('branch_ids', $assignedBranches ?? $teacher->branches->pluck('id')->toArray())) ? 'selected' : '' }}" 
                                             onclick="toggleItem(this, 'branch_ids[]', {{ $branch->id }})"
                                             data-name="{{ strtolower($branch->name) }}"
                                             data-type="branch">
                                            <input type="checkbox" 
                                                   name="branch_ids[]" 
                                                   value="{{ $branch->id }}"
                                                   {{ in_array($branch->id, old('branch_ids', $assignedBranches ?? $teacher->branches->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            
                                            <div class="item-header">
                                                <div class="item-name">{{ $branch->name }}</div>
                                                <div class="checkmark"></div>
                                            </div>
                                            
                                            @if($branch->location ?? null)
                                                <div style="color: #718096; font-size: 0.9rem; margin-top: 0.5rem;">
                                                    <i class="fas fa-map-marker-alt" style="margin-right: 0.5rem;"></i>
                                                    {{ $branch->location }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                <div class="selection-summary">
                                    <div class="selection-count" id="branchCount">
                                        {{ count(old('branch_ids', $assignedBranches ?? $teacher->branches->pluck('id')->toArray())) }} branch(es) selected
                                    </div>
                                    <div class="help-text">
                                        <i class="fas fa-info-circle"></i>
                                        Click on branch cards to select or deselect them
                                    </div>
                                </div>
                            </div>

                            @error('branch_ids')
                                <div class="error-message">
                                    <i class="fas fa-exclamation-circle"></i>
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <a href="{{ route('admin.teachers.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i>
                                Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i>
                                Update Teacher
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleItem(card, inputName, itemId) {
    const checkbox = card.querySelector('input[type="checkbox"]');
    const isSelected = card.classList.contains('selected');
    
    if (isSelected) {
        card.classList.remove('selected');
        checkbox.checked = false;
    } else {
        card.classList.add('selected');
        checkbox.checked = true;
    }
    
    updateSelectionCount(inputName);
}

function updateSelectionCount(inputName) {
    if (inputName === 'courses[]') {
        const selectedCourses = document.querySelectorAll('input[name="courses[]"]:checked');
        document.getElementById('courseCount').textContent = `${selectedCourses.length} course(s) selected`;
    } else if (inputName === 'branch_ids[]') {
        const selectedBranches = document.querySelectorAll('input[name="branch_ids[]"]:checked');
        document.getElementById('branchCount').textContent = `${selectedBranches.length} branch(es) selected`;
    }
}

function filterItems(type) {
    const searchInput = event.target;
    const searchTerm = searchInput.value.toLowerCase();
    const cards = document.querySelectorAll(`[data-type="${type}"]`);
    
    cards.forEach(card => {
        const itemName = card.getAttribute('data-name');
        if (itemName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

// Form validation and submission
document.getElementById('editTeacherForm').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    
    // Add loading state
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitButton.disabled = true;
    
    // Re-enable button after 5 seconds in case of errors
    setTimeout(() => {
        submitButton.innerHTML = '<i class="fas fa-save"></i> Update Teacher';
        submitButton.disabled = false;
    }, 5000);
});

// Initialize selection counts on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectionCount('courses[]');
    updateSelectionCount('branch_ids[]');
    
    // Smooth scroll to first error field
    const errorField = document.querySelector('.is-invalid');
    if (errorField) {
        errorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        errorField.focus();
    }
});

// Add smooth transitions for form elements
document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('focus', function() {
        this.parentNode.style.transform = 'translateY(-1px)';
    });
    
    input.addEventListener('blur', function() {
        this.parentNode.style.transform = 'translateY(0)';
    });
});
</script>
@endsection