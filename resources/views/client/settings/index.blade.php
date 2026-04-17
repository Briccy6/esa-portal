@extends('layouts.client')

@section('title', 'Settings - ESA Portal')

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

/* Settings Grid */
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
    margin-bottom: 32px;
}

/* Settings Card */
.settings-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 32px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.settings-card:hover {
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

/* Settings Items */
.settings-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.setting-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    transition: all 0.2s ease;
}

.setting-item:hover {
    background: var(--surface);
    border-color: var(--accent);
}

.setting-info h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
}

.setting-info p {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.4;
}

/* Toggle Switch */
.toggle-switch {
    position: relative;
    width: 48px;
    height: 24px;
    background: var(--border);
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-switch input:checked + .toggle-slider {
    background: var(--accent);
}

.toggle-switch input:checked + .toggle-slider::before {
    transform: translateX(24px);
}

.toggle-slider {
    position: absolute;
    top: 2px;
    left: 2px;
    width: 20px;
    height: 20px;
    background: white;
    border-radius: 50%;
    transition: all 0.3s ease;
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

.btn-danger {
    background: #ef4444;
    color: white;
}

.btn-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.btn-group {
    display: flex;
    gap: 12px;
    margin-top: 24px;
}

/* Privacy Section */
.privacy-section {
    margin-top: 24px;
    padding-top: 24px;
    border-top: 1px solid var(--border);
}

.privacy-info {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
}

.privacy-info h4 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 12px;
}

.privacy-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.privacy-list li {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    padding: 8px 0;
    font-size: 13px;
    color: var(--text-primary);
}

.privacy-list li i {
    color: var(--accent-3);
    font-size: 12px;
    margin-top: 2px;
    flex-shrink: 0;
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
    
    .settings-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .setting-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
        padding: 16px;
    }
    
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-left">
        <h1>Settings</h1>
        <p>Manage your account preferences</p>
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
    <form action="{{ route('client.settings.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="settings-grid">
            <!-- Notification Settings -->
            <div class="settings-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-bell"></i>
                        Notifications
                    </h2>
                </div>
                
                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Email Notifications</h4>
                            <p>Receive email updates about your account activity</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="email_notifications" 
                                   value="1"
                                   @if(old('email_notifications', $client->email_notifications ?? true)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Exam Reminders</h4>
                            <p>Get reminded about upcoming exams and deadlines</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="exam_reminders" 
                                   value="1"
                                   @if(old('exam_reminders', $client->exam_reminders ?? true)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Marketing Emails</h4>
                            <p>Receive promotional offers and new features</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="marketing_emails" 
                                   value="1"
                                   @if(old('marketing_emails', $client->marketing_emails ?? false)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Display Preferences -->
            <div class="settings-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-palette"></i>
                        Display Preferences
                    </h2>
                </div>
                
                <div class="settings-list">
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Dark Mode</h4>
                            <p>Use dark theme across the platform</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="dark_mode" 
                                   value="1"
                                   @if(old('dark_mode', $client->dark_mode ?? false)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>High Contrast</h4>
                            <p>Increase contrast for better visibility</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="high_contrast" 
                                   value="1"
                                   @if(old('high_contrast', $client->high_contrast ?? false)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>Compact View</h4>
                            <p>Show more content in less space</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" 
                                   name="compact_view" 
                                   value="1"
                                   @if(old('compact_view', $client->compact_view ?? false)) checked @endif>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Privacy & Security -->
            <div class="settings-card">
                <div class="card-header">
                    <h2 class="card-title">
                        <i class="fas fa-shield-alt"></i>
                        Privacy & Security
                    </h2>
                </div>
                
                <div class="privacy-section">
                    <div class="privacy-info">
                        <h4>Data & Privacy</h4>
                        <ul class="privacy-list">
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>Your data is encrypted and stored securely</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>We never share your personal information with third parties</span>
                            </li>
                            <li>
                                <i class="fas fa-check-circle"></i>
                                <span>You can request a copy of your data at any time</span>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="btn-group">
                        <button type="button" class="btn btn-secondary">
                            <i class="fas fa-download"></i>
                            Download My Data
                        </button>
                        <button type="button" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Delete Account
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Save Button -->
        <div class="btn-group" style="justify-content: flex-end; margin-top: 32px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
