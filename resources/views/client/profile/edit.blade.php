@extends('layouts.client')

@section('title', 'My Profile - ESA Portal')

@section('content')
<style>
:root {
    --bg: #f4f6fb;
    --surface: #ffffff;
    --surface-2: #f8f9fc;
    --border: #e5e7eb;
    --accent: #7c3aed;
    --accent-2: #8b5cf6;
    --accent-3: #06b6d4;
    --accent-warm: #f59e0b;
    --text-primary: #1f2937;
    --text-muted: #6b7280;
    --text-dim: #9ca3af;
}

* { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
    background: var(--bg);
    color: var(--text-primary);
    line-height: 1.6;
}

/* Topbar */
.topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    border-radius: 12px 12px 0 0;
    margin-bottom: 24px;
}

.topbar-left h1 {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.topbar-left p {
    font-size: 14px;
    color: var(--text-muted);
}

.topbar-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Page Content Wrapper */
.page-content {
    padding: 0 24px 24px;
}

/* Profile Sections */
.profile-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* Profile Card */
.profile-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}

.card-title {
    font-family: 'Syne', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 8px;
}

.card-title i {
    color: var(--accent);
    font-size: 20px;
}

.card-actions {
    display: flex;
    gap: 8px;
}

/* Form Styles */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-label {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.form-input {
    padding: 12px 16px;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--surface);
    color: var(--text-primary);
    font-size: 14px;
    transition: all 0.2s ease;
    width: 100%;
}

.form-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.stat-card {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: all 0.2s ease;
}

.stat-card:hover {
    background: var(--surface);
    border-color: var(--accent);
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    margin: 0 auto 12px;
}

.stat-card.exams .stat-icon {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
}

.stat-card.tokens .stat-icon {
    background: linear-gradient(135deg, var(--accent-warm), #f59e0b);
}

.stat-card.score .stat-icon {
    background: linear-gradient(135deg, var(--accent-3), var(--accent));
}

.stat-value {
    font-family: 'Syne', sans-serif;
    font-size: 24px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 4px;
    line-height: 1;
}

.stat-label {
    font-size: 12px;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-primary {
    background: linear-gradient(135deg, var(--accent), var(--accent-2));
    color: white;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
}

.btn-secondary {
    background: var(--surface-2);
    color: var(--text-primary);
    border: 1px solid var(--border);
}

.btn-secondary:hover {
    background: var(--surface);
    border-color: var(--accent);
    color: var(--accent);
}

/* Responsive */
@media (max-width: 768px) {
    .topbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .topbar-right {
        width: 100%;
        justify-content: flex-start;
    }
    
    .page-content {
        padding: 0 16px 16px;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>My Profile</h1>
        <p>Manage your personal information</p>
    </div>
    <div class="topbar-right">
        <a href="{{ route('client.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i>
            Back to Dashboard
        </a>
    </div>
</div>

<!-- Page Content -->
<div class="page-content">
    <div class="profile-grid">
        <!-- Personal Information -->
        <div class="profile-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-user"></i>
                    Personal Information
                </h2>
                <div class="card-actions">
                    <button class="btn btn-primary" onclick="enableEdit()">
                        <i class="fas fa-edit"></i>
                        Edit
                    </button>
                </div>
            </div>
            
            <form action="{{ route('client.profile.update') }}" method="POST" id="profileForm">
                @csrf
                @method('PUT')
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">Full Name</label>
                        <input type="text" 
                               name="name" 
                               value="{{ old('name', $client->name) }}" 
                               class="form-input" 
                               {{ $errors->has('name') ? 'border-red-500' : '' }}"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('name') }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Email Address</label>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email', $client->email) }}" 
                               class="form-input" 
                               {{ $errors->has('email') ? 'border-red-500' : '' }}"
                               required>
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('email') }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" 
                               name="phone" 
                               value="{{ old('phone', $client->phone) }}" 
                               class="form-input" 
                               {{ $errors->has('phone') ? 'border-red-500' : '' }}">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $errors->first('phone') }}</p>
                        @enderror
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Account Information -->
        <div class="profile-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-shield-alt"></i>
                    Account Information
                </h2>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon exams">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-value">{{ $client->examsTaken() ?? 0 }}</div>
                    <div class="stat-label">Exams Taken</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon tokens">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-value">{{ $client->getCurrentTokens() }}</div>
                    <div class="stat-label">Current Tokens</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-icon score">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-value">{{ $client->averageScore() ?? 0 }}%</div>
                    <div class="stat-label">Average Score</div>
                </div>
            </div>
        </div>
        
        <!-- Account Status -->
        <div class="profile-card">
            <div class="card-header">
                <h2 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Account Status
                </h2>
            </div>
            
            <div class="form-grid">
                <div class="form-group">
                    <label class="form-label">Account Status</label>
                    <div class="form-input" style="padding: 16px; background: var(--surface-2);">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-check-circle" style="color: var(--accent-3);"></i>
                            <span style="font-weight: 600; color: var(--text-primary);">Active</span>
                        </div>
                        <p style="margin-top: 8px; font-size: 13px; color: var(--text-muted);">Your account is in good standing</p>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Member Since</label>
                    <div class="form-input" style="padding: 16px; background: var(--surface-2);">
                        <div style="font-weight: 600; color: var(--text-primary);">{{ $client->created_at->format('F j, Y') }}</div>
                        <p style="margin-top: 8px; font-size: 13px; color: var(--text-muted);">{{ $client->created_at->diffInDays(now()) }} days as member</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function enableEdit() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('.form-input');
    
    inputs.forEach(input => {
        input.disabled = false;
        input.style.backgroundColor = 'var(--surface)';
        input.style.borderColor = 'var(--border)';
    });
}
</script>
@endsection
