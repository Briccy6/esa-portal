@extends('admin.layout')

@section('title', 'Schedule Live Class')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* ============================================
   ROOT & RESET
============================================ */
:root {
    --primary:       #4f46e5;
    --primary-light: #eef2ff;
    --primary-dark:  #3730a3;
    --success:       #10b981;
    --success-light: #ecfdf5;
    --danger:        #ef4444;
    --warning:       #f59e0b;
    --surface:       #ffffff;
    --surface-2:     #f8fafc;
    --border:        #e2e8f0;
    --border-focus:  #4f46e5;
    --text-primary:  #0f172a;
    --text-secondary:#64748b;
    --text-muted:    #94a3b8;
    --shadow-sm:     0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
    --shadow-md:     0 4px 12px rgba(0,0,0,.08), 0 2px 6px rgba(0,0,0,.04);
    --shadow-lg:     0 10px 30px rgba(0,0,0,.10), 0 4px 10px rgba(0,0,0,.06);
    --radius-sm:     8px;
    --radius-md:     12px;
    --radius-lg:     16px;
    --transition:    all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

* { box-sizing: border-box; }

body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f1f5f9;
    color: var(--text-primary);
    -webkit-font-smoothing: antialiased;
}

/* ============================================
   PAGE WRAPPER
============================================ */
.lc-page {
    padding: 24px 16px;
    max-width: 1100px;
    margin: 0 auto;
}

/* ============================================
   CARD SHELL
============================================ */
.lc-card {
    background: var(--surface);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border);
}

/* ============================================
   PAGE HEADER
============================================ */
.lc-header {
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6366f1 100%);
    padding: 32px 36px;
    position: relative;
    overflow: hidden;
}

.lc-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3Ccircle cx='0' cy='0' r='20'/%3E%3Ccircle cx='60' cy='60' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}

.lc-header-inner {
    position: relative;
    display: flex;
    align-items: center;
    gap: 16px;
}

.lc-header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    color: #fff;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,0.2);
}

.lc-header-text h2 {
    color: #fff;
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    letter-spacing: -0.3px;
}

.lc-header-text p {
    color: rgba(255,255,255,0.75);
    margin: 0;
    font-size: 14px;
    font-weight: 400;
}

/* ============================================
   FORM BODY
============================================ */
.lc-body {
    padding: 32px 36px;
}

/* ============================================
   ALERT
============================================ */
.lc-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-left: 4px solid var(--danger);
    border-radius: var(--radius-sm);
    padding: 16px 20px;
    margin-bottom: 28px;
    position: relative;
}

.lc-alert h6 {
    color: #dc2626;
    font-weight: 600;
    margin: 0 0 8px;
    font-size: 14px;
}

.lc-alert ul {
    margin: 0;
    padding-left: 20px;
    color: #b91c1c;
    font-size: 13px;
}

.lc-alert-close {
    position: absolute;
    top: 12px;
    right: 16px;
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    font-size: 18px;
    line-height: 1;
    opacity: 0.6;
    padding: 0;
}

.lc-alert-close:hover { opacity: 1; }

/* ============================================
   SECTION HEADERS
============================================ */
.lc-section {
    margin-bottom: 36px;
}

.lc-section-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 2px solid var(--border);
}

.lc-section-icon {
    width: 36px;
    height: 36px;
    background: var(--primary-light);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 15px;
    flex-shrink: 0;
}

.lc-section-title {
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.2px;
    margin: 0;
}

.lc-section-subtitle {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
}

/* ============================================
   GRID
============================================ */
.lc-grid {
    display: grid;
    gap: 20px;
}

.lc-grid-2 { grid-template-columns: 1fr 1fr; }
.lc-grid-1 { grid-template-columns: 1fr; }

/* ============================================
   FORM FIELDS
============================================ */
.lc-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.lc-label {
    font-size: 13px;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 6px;
}

.lc-label .req {
    color: var(--danger);
    font-size: 14px;
}

.lc-hint {
    font-size: 12px;
    color: var(--text-muted);
    margin: 0;
    line-height: 1.5;
}

.lc-input-wrap {
    position: relative;
}

.lc-input-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 14px;
    pointer-events: none;
    z-index: 2;
}

.lc-textarea-icon {
    top: 14px;
    transform: none;
}

.lc-input,
.lc-select,
.lc-textarea {
    width: 100%;
    height: 46px;
    padding: 0 14px 0 40px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface);
    color: var(--text-primary);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 500;
    transition: var(--transition);
    outline: none;
    -webkit-appearance: none;
    appearance: none;
}

.lc-textarea {
    height: auto;
    padding: 12px 14px 12px 40px;
    resize: vertical;
    min-height: 100px;
}

.lc-input:hover,
.lc-select:hover,
.lc-textarea:hover {
    border-color: #a5b4fc;
    background: #fafafe;
}

.lc-input:focus,
.lc-select:focus,
.lc-textarea:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
    background: var(--surface);
}

.lc-input::placeholder,
.lc-textarea::placeholder { color: var(--text-muted); font-weight: 400; }

/* select arrow */
.lc-select-wrap::after {
    content: '\f078';
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 11px;
    pointer-events: none;
}

/* fee row */
.lc-fee-row {
    display: flex;
    gap: 8px;
}

.lc-fee-row .lc-input { flex: 1; }

.lc-currency-select {
    width: 90px;
    height: 46px;
    padding: 0 10px;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    background: var(--surface-2);
    color: var(--text-primary);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    outline: none;
    transition: var(--transition);
    flex-shrink: 0;
    -webkit-appearance: none;
    appearance: none;
}

.lc-currency-select:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(79,70,229,0.12);
}

/* ============================================
   SELECT2 OVERRIDES
============================================ */
.select2-container {
    width: 100% !important;
}

.select2-container--default .select2-selection--multiple,
.select2-container--default .select2-selection--single {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    background: var(--surface) !important;
    min-height: 46px !important;
    display: flex !important;
    align-items: center !important;
    padding: 4px 8px 4px 40px !important;
    transition: var(--transition) !important;
    box-shadow: none !important;
    cursor: pointer !important;
}

.select2-container--default .select2-selection--single {
    height: 46px !important;
}

.select2-container--default.select2-container--focus .select2-selection--multiple,
.select2-container--default.select2-container--open .select2-selection--multiple,
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.12) !important;
    background: var(--surface) !important;
}

.select2-container--default .select2-selection--multiple:hover,
.select2-container--default .select2-selection--single:hover {
    border-color: #a5b4fc !important;
    background: #fafafe !important;
}

/* Tags */
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: var(--primary) !important;
    border: none !important;
    border-radius: 20px !important;
    color: #fff !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    padding: 3px 10px 3px 8px !important;
    margin: 3px 4px 3px 0 !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
    line-height: 1.4 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
    color: rgba(255,255,255,0.7) !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    margin: 0 !important;
    order: 2 !important;
    border: none !important;
    background: none !important;
    padding: 0 !important;
    line-height: 1 !important;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    color: #fff !important;
}

/* Placeholder */
.select2-container--default .select2-selection--multiple .select2-selection__placeholder {
    color: var(--text-muted) !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    padding-left: 4px !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary) !important;
    font-size: 14px !important;
    font-weight: 500 !important;
    line-height: 44px !important;
    padding: 0 32px 0 4px !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
    color: var(--text-muted) !important;
    font-weight: 400 !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 44px !important;
    right: 10px !important;
}

.select2-container--default .select2-selection--single .select2-selection__arrow b {
    border-color: var(--text-muted) transparent transparent transparent !important;
}

/* Dropdown */
.select2-dropdown {
    border: 1.5px solid var(--primary) !important;
    border-radius: var(--radius-md) !important;
    box-shadow: var(--shadow-lg) !important;
    background: var(--surface) !important;
    overflow: hidden !important;
    margin-top: 4px !important;
}

.select2-search--dropdown {
    padding: 10px !important;
    border-bottom: 1px solid var(--border) !important;
}

.select2-search--dropdown .select2-search__field {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    padding: 8px 12px !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    outline: none !important;
    transition: var(--transition) !important;
    width: 100% !important;
}

.select2-search--dropdown .select2-search__field:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.12) !important;
}

.select2-results__options {
    max-height: 220px !important;
    overflow-y: auto !important;
    padding: 6px !important;
}

.select2-results__option {
    border-radius: 8px !important;
    padding: 9px 12px !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: var(--text-primary) !important;
    transition: var(--transition) !important;
    margin-bottom: 2px !important;
}

.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: var(--primary-light) !important;
    color: var(--primary) !important;
}

.select2-container--default .select2-results__option[aria-selected=true] {
    background: var(--primary) !important;
    color: #fff !important;
}

.select2-results__option--load-more,
.select2-results__message {
    color: var(--text-muted) !important;
    font-size: 12px !important;
    text-align: center !important;
}

/* Inline search */
.select2-container--default .select2-search--inline .select2-search__field {
    margin-top: 4px !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    color: var(--text-primary) !important;
}

/* ============================================
   TOGGLE CARDS
============================================ */
.lc-toggle-card {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
    height: 100%;
}

.lc-toggle-card-title {
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.lc-toggle-card-title .dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.dot-primary { background: var(--primary); }
.dot-success  { background: var(--success); }

/* Toggle switch */
.lc-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
    gap: 12px;
    cursor: pointer;
}

.lc-toggle:last-child { border-bottom: none; padding-bottom: 0; }
.lc-toggle:first-of-type { padding-top: 0; }

.lc-toggle-label {
    font-size: 13px;
    font-weight: 500;
    color: var(--text-secondary);
    flex: 1;
    line-height: 1.4;
    cursor: pointer;
}

.lc-toggle input[type="checkbox"] {
    display: none;
}

.lc-switch {
    position: relative;
    width: 44px;
    height: 24px;
    background: #cbd5e1;
    border-radius: 12px;
    transition: var(--transition);
    flex-shrink: 0;
    cursor: pointer;
}

.lc-switch::after {
    content: '';
    position: absolute;
    top: 3px;
    left: 3px;
    width: 18px;
    height: 18px;
    background: #fff;
    border-radius: 50%;
    transition: var(--transition);
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.lc-toggle input:checked + .lc-switch {
    background: var(--primary);
}

.lc-toggle input:checked + .lc-switch::after {
    left: 23px;
}

/* ============================================
   DATETIME FIELD
============================================ */
.lc-datetime-card {
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-md);
    padding: 20px;
}

.lc-datetime-label {
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.lc-datetime-card .lc-label {
    margin-bottom: 8px;
}

input[type="datetime-local"].lc-input {
    font-size: 14px;
    color: var(--text-primary);
    cursor: pointer;
}

/* ============================================
   FORM FOOTER
============================================ */
.lc-footer {
    padding: 24px 36px;
    background: var(--surface-2);
    border-top: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
}

.lc-footer-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.lc-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0 24px;
    height: 46px;
    border-radius: var(--radius-sm);
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    border: none;
    text-decoration: none;
    white-space: nowrap;
}

.lc-btn-primary {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 4px 14px rgba(79,70,229,0.35);
}

.lc-btn-primary:hover {
    background: var(--primary-dark);
    box-shadow: 0 6px 20px rgba(79,70,229,0.45);
    transform: translateY(-1px);
    color: #fff;
    text-decoration: none;
}

.lc-btn-primary:active {
    transform: translateY(0);
}

.lc-btn-secondary {
    background: var(--surface);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
}

.lc-btn-secondary:hover {
    background: var(--surface-2);
    border-color: #a5b4fc;
    color: var(--primary);
    text-decoration: none;
}

.lc-footer-note {
    font-size: 12px;
    color: var(--text-muted);
    display: flex;
    align-items: center;
    gap: 6px;
}

/* ============================================
   COLLAPSIBLE FIELD (public fee)
============================================ */
.lc-collapsible {
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1), opacity 0.25s ease;
}

.lc-collapsible.open {
    max-height: 120px;
    opacity: 1;
}

/* ============================================
   BADGE COUNTER
============================================ */
.lc-field-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: var(--text-muted);
    font-weight: 500;
}

/* ============================================
   VALIDATION STATES
============================================ */
.was-validated .lc-input:invalid,
.was-validated .lc-select:invalid {
    border-color: var(--danger) !important;
}

.was-validated .lc-input:valid,
.was-validated .lc-select:valid {
    border-color: var(--success) !important;
}

/* ============================================
   RESPONSIVE
============================================ */
@media (max-width: 768px) {
    .lc-page { padding: 12px 10px; }
    .lc-header { padding: 20px 20px; }
    .lc-header-text h2 { font-size: 18px; }
    .lc-header-icon { width: 44px; height: 44px; font-size: 18px; }
    .lc-body { padding: 20px 16px; }
    .lc-grid-2 { grid-template-columns: 1fr; }
    .lc-footer { padding: 20px 16px; flex-direction: column; align-items: stretch; }
    .lc-footer-actions { flex-direction: column; }
    .lc-btn { justify-content: center; }
    .lc-footer-note { justify-content: center; }
    .select2-container--default .select2-selection--multiple,
    .select2-container--default .select2-selection--single {
        padding-left: 14px !important;
    }
}

@media (max-width: 480px) {
    .lc-section-header { flex-direction: column; align-items: flex-start; gap: 8px; }
    .lc-fee-row { flex-direction: column; }
    .lc-currency-select { width: 100%; }
}
</style>
@endsection

@section('content')
<div class="lc-page">
<div class="lc-card">

    {{-- ========== HEADER ========== --}}
    <div class="lc-header">
        <div class="lc-header-inner">
            <div class="lc-header-icon">
                <i class="fas fa-video"></i>
            </div>
            <div class="lc-header-text">
                <h2>Schedule New Live Class</h2>
                <p>Set up a live streaming session with access controls</p>
            </div>
        </div>
    </div>

    {{-- ========== BODY ========== --}}
    <div class="lc-body">

        {{-- Errors --}}
        @if($errors->any())
        <div class="lc-alert">
            <button class="lc-alert-close" onclick="this.closest('.lc-alert').remove()">&times;</button>
            <h6><i class="fas fa-exclamation-triangle" style="margin-right:6px;"></i>Please fix the following errors:</h6>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('admin.live-classes.store') }}" method="POST" class="needs-validation" novalidate id="lcForm">
        @csrf

        {{-- ===========================
             SECTION 1 – BASIC INFO
        =========================== --}}
        <div class="lc-section">
            <div class="lc-section-header">
                <div class="lc-section-icon"><i class="fas fa-info-circle"></i></div>
                <div>
                    <p class="lc-section-title">Basic Information</p>
                    <p class="lc-section-subtitle">Title, description and access settings</p>
                </div>
            </div>

            <div class="lc-grid lc-grid-2">

                {{-- LEFT COLUMN --}}
                <div style="display:flex;flex-direction:column;gap:20px;">

                    {{-- Title --}}
                    <div class="lc-field">
                        <label class="lc-label" for="title">
                            Class Title <span class="req">*</span>
                        </label>
                        <div class="lc-input-wrap">
                            <i class="fas fa-heading lc-input-icon"></i>
                            <input type="text" class="lc-input" id="title" name="title"
                                   value="{{ old('title') }}" required
                                   placeholder="e.g. Advanced Python Workshop">
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="lc-field">
                        <label class="lc-label" for="description">Description</label>
                        <div class="lc-input-wrap">
                            <i class="fas fa-align-left lc-input-icon lc-textarea-icon"></i>
                            <textarea class="lc-textarea" id="description" name="description"
                                      rows="3" placeholder="What will students learn in this session?">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    {{-- Meeting Link --}}
                    <div class="lc-field">
                        <label class="lc-label" for="meet_link">
                            Meeting Link <span class="req">*</span>
                            <small style="color:var(--text-muted);font-weight:normal;margin-left:8px;">External meeting platform (Google Meet, Zoom, Teams, etc.)</small>
                        </label>
                        <div class="lc-input-wrap">
                            <i class="fas fa-video lc-input-icon"></i>
                            <input type="url" class="lc-input" id="meet_link" name="meet_link"
                                   value="{{ old('meet_link') }}" required
                                   placeholder="https://meet.google.com/xxx-xxx-xxx or https://zoom.us/j/xxxxx">
                        </div>
                        @error('meet_link')
                            <div class="lc-error" style="margin-top:6px;">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    {{-- Access Type --}}
                    <div class="lc-field">
                        <label class="lc-label" for="access_type">
                            Access Type <span class="req">*</span>
                        </label>
                        <div class="lc-input-wrap lc-select-wrap">
                            <i class="fas fa-lock lc-input-icon"></i>
                            <select class="lc-select" id="access_type" name="access_type" required onchange="togglePublicFields()">
                                <option value="">— Choose access type —</option>
                                <option value="students_only" {{ old('access_type') == 'students_only' ? 'selected' : '' }}>Students Only</option>
                                <option value="public"        {{ old('access_type') == 'public'        ? 'selected' : '' }}>Public Only</option>
                                <option value="both"          {{ old('access_type') == 'both'          ? 'selected' : '' }}>Both Students & Public</option>
                            </select>
                        </div>
                    </div>

                    {{-- Public Fee (collapsible) --}}
                    <div class="lc-collapsible" id="public_fee_group">
                        <div class="lc-field">
                            <label class="lc-label" for="public_fee">Public Fee</label>
                            <div class="lc-fee-row">
                                <div class="lc-input-wrap" style="flex:1;">
                                    <i class="fas fa-dollar-sign lc-input-icon"></i>
                                    <input type="number" class="lc-input" id="public_fee" name="public_fee"
                                           step="0.01" min="0" value="{{ old('public_fee') }}" placeholder="0.00">
                                </div>
                                <select class="lc-currency-select" name="currency">
                                    <option value="Rwf" {{ old('currency','Rwf') == 'Rwf' ? 'selected' : '' }}>Rwf</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>

                </div>{{-- /left --}}

                {{-- RIGHT COLUMN --}}
                <div style="display:flex;flex-direction:column;gap:20px;">

                    {{-- Branches --}}
                    <div class="lc-field">
                        <label class="lc-label" for="branches">
                            Branches <span class="req">*</span>
                        </label>
                        <div class="lc-input-wrap" style="position:relative;">
                            <i class="fas fa-building lc-input-icon" style="z-index:10;"></i>
                            <select class="lc-select2-multi" id="branches" name="branches[]" multiple required
                                    data-placeholder="Search branches…">
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}" {{ old('branches') && in_array($branch->id, old('branches')) ? 'selected' : '' }}>
                                        {{ $branch->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="lc-hint">Select one or more branches</p>
                    </div>

                    {{-- Courses --}}
                    <div class="lc-field">
                        <label class="lc-label" for="courses">
                            Courses <span class="req">*</span>
                        </label>
                        <div class="lc-input-wrap" style="position:relative;">
                            <i class="fas fa-book lc-input-icon" style="z-index:10;"></i>
                            <select class="lc-select2-multi" id="courses" name="courses[]" multiple required
                                    data-placeholder="Search courses…">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}" {{ old('courses') && in_array($course->id, old('courses')) ? 'selected' : '' }}>
                                        {{ $course->course_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="lc-hint">Select one or more courses</p>
                    </div>

                    {{-- Lessons --}}
                    <div class="lc-field">
                        <label class="lc-label" for="lessons">Lessons <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
                        <div class="lc-input-wrap" style="position:relative;">
                            <i class="fas fa-chalkboard lc-input-icon" style="z-index:10;"></i>
                            <select class="lc-select2-multi" id="lessons" name="lessons[]" multiple
                                    data-placeholder="Search lessons…">
                                @foreach($lessons as $lesson)
                                    <option value="{{ $lesson->id }}" {{ old('lessons') && in_array($lesson->id, old('lessons')) ? 'selected' : '' }}>
                                        {{ $lesson->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <p class="lc-hint">Filtered by selected courses</p>
                    </div>

                    {{-- Teacher --}}
                    <div class="lc-field">
                        <label class="lc-label" for="teacher_id">Teacher <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
                        <div class="lc-input-wrap" style="position:relative;">
                            <i class="fas fa-chalkboard-teacher lc-input-icon" style="z-index:10;"></i>
                            <select class="lc-select2-single" id="teacher_id" name="teacher_id"
                                    data-placeholder="Search teachers…">
                                <option value=""></option>
                                @foreach($teachers as $teacher)
                                    <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                        {{ $teacher->teacher_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>{{-- /right --}}

            </div>{{-- /grid --}}
        </div>{{-- /section 1 --}}

        {{-- ===========================
             SECTION 2 – SCHEDULE
        =========================== --}}
        <div class="lc-section">
            <div class="lc-section-header">
                <div class="lc-section-icon"><i class="fas fa-calendar-alt"></i></div>
                <div>
                    <p class="lc-section-title">Schedule</p>
                    <p class="lc-section-subtitle">When does the class start and end?</p>
                </div>
            </div>

            <div class="lc-grid lc-grid-2">
                <div class="lc-datetime-card">
                    <p class="lc-datetime-label"><i class="fas fa-play" style="margin-right:6px;color:var(--primary);"></i>Start Time</p>
                    <div class="lc-input-wrap">
                        <i class="fas fa-clock lc-input-icon"></i>
                        <input type="datetime-local" class="lc-input" id="start_time" name="start_time"
                               value="{{ old('start_time') }}" required>
                    </div>
                </div>

                <div class="lc-datetime-card">
                    <p class="lc-datetime-label"><i class="fas fa-stop" style="margin-right:6px;color:#ef4444;"></i>End Time</p>
                    <div class="lc-input-wrap">
                        <i class="fas fa-clock lc-input-icon"></i>
                        <input type="datetime-local" class="lc-input" id="end_time" name="end_time"
                               value="{{ old('end_time') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===========================
             SECTION 3 – ACCESS CONTROL
        =========================== --}}
        <div class="lc-section">
            <div class="lc-section-header">
                <div class="lc-section-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <p class="lc-section-title">Access Control</p>
                    <p class="lc-section-subtitle">Participation limits and payment requirements</p>
                </div>
            </div>

            <div class="lc-grid lc-grid-2" style="margin-bottom:24px;">

                <div class="lc-field">
                    <label class="lc-label" for="max_participants">Max Participants <span style="font-weight:400;color:var(--text-muted)">(optional)</span></label>
                    <div class="lc-input-wrap">
                        <i class="fas fa-users lc-input-icon"></i>
                        <input type="number" class="lc-input" id="max_participants" name="max_participants"
                               min="1" value="{{ old('max_participants') }}" placeholder="No limit">
                    </div>
                    <p class="lc-hint">Leave blank for unlimited participants</p>
                </div>

                <div class="lc-field">
                    <label class="lc-label" for="required_payment_percentage">Minimum Payment % for Students</label>
                    <div class="lc-input-wrap">
                        <i class="fas fa-percentage lc-input-icon"></i>
                        <input type="number" class="lc-input" id="required_payment_percentage"
                               name="required_payment_percentage" min="0" max="100"
                               value="{{ old('required_payment_percentage', 0) }}" placeholder="0">
                    </div>
                    <p class="lc-hint">Students must have paid at least this % to join</p>
                </div>

            </div>

            {{-- Toggle Cards --}}
            <div class="lc-grid lc-grid-2">

                {{-- Student Access --}}
                <div class="lc-toggle-card">
                    <p class="lc-toggle-card-title">
                        <span class="dot dot-primary"></span>
                        Student Access
                    </p>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Allow Public Users to Request Access</span>
                        <input type="checkbox" name="allow_public_requests" value="1"
                               id="allow_public_requests" {{ old('allow_public_requests') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Auto-approve Online Branch Students</span>
                        <input type="checkbox" name="auto_approve_online_students" value="1"
                               {{ old('auto_approve_online_students', '1') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Require Approval for Non-Online Students</span>
                        <input type="checkbox" name="require_student_approval" value="1"
                               {{ old('require_student_approval') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>
                </div>

                {{-- Class Features --}}
                <div class="lc-toggle-card">
                    <p class="lc-toggle-card-title">
                        <span class="dot dot-success"></span>
                        Class Features
                    </p>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Restrict to One Device per Email</span>
                        <input type="checkbox" name="device_restriction" value="1"
                               {{ old('device_restriction', '1') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Enable Recording</span>
                        <input type="checkbox" name="recording_enabled" value="1"
                               {{ old('recording_enabled') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Enable Chat</span>
                        <input type="checkbox" name="chat_enabled" value="1"
                               {{ old('chat_enabled', '1') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>

                    <label class="lc-toggle">
                        <span class="lc-toggle-label">Enable Screen Sharing</span>
                        <input type="checkbox" name="screen_share_enabled" value="1"
                               {{ old('screen_share_enabled', '1') ? 'checked' : '' }}>
                        <span class="lc-switch"></span>
                    </label>
                </div>

            </div>
        </div>

        </form>
    </div>{{-- /lc-body --}}

    {{-- ========== FOOTER ========== --}}
    <div class="lc-footer">
        <div class="lc-footer-actions">
            <button type="submit" form="lcForm" class="lc-btn lc-btn-primary">
                <i class="fas fa-satellite-dish"></i>
                Schedule Live Class
            </button>
            <a href="{{ route('admin.live-classes.index') }}" class="lc-btn lc-btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Back to Classes
            </a>
        </div>
        <p class="lc-footer-note">
            <i class="fas fa-asterisk" style="color:var(--danger);font-size:9px;"></i>
            Required fields
        </p>
    </div>

</div>{{-- /lc-card --}}
</div>{{-- /lc-page --}}
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Datetime min/auto-advance ─────────────── */
    const now = new Date();
    const localDT = new Date(now.getTime() - now.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    const startEl = document.getElementById('start_time');
    const endEl   = document.getElementById('end_time');
    startEl.min = localDT;
    endEl.min   = localDT;

    startEl.addEventListener('change', function () {
        endEl.min = this.value;
        if (endEl.value && endEl.value <= this.value) {
            const s = new Date(this.value);
            s.setHours(s.getHours() + 1);
            endEl.value = s.toISOString().slice(0, 16);
        }
    });

    /* ── Select2 initialisation ─────────────────── */
    const s2common = {
        width: '100%',
        minimumInputLength: 0,
        language: {
            noResults:    () => 'No results found',
            searching:    () => 'Searching…',
            inputTooShort:() => 'Type to search'
        }
    };

    /* Multi-selects (local options) */
    $('#branches').select2({
        ...s2common,
        placeholder: 'Search branches…',
        ajax: { url: '{{ route("api.branches.search") }}', dataType:'json', delay:250,
                data: p => ({ q: p.term }),
                processResults: d => ({ results: d }), cache:true }
    });

    $('#courses').select2({
        ...s2common,
        placeholder: 'Search courses…',
        ajax: { url: '{{ route("api.courses.search") }}', dataType:'json', delay:250,
                data: p => ({ q: p.term }),
                processResults: d => ({ results: d }), cache:true }
    });

    $('#lessons').select2({
        ...s2common,
        placeholder: 'Search lessons…',
        allowClear: true,
        ajax: { url: '{{ route("api.lessons.search") }}', dataType:'json', delay:250,
                data: p => ({ q: p.term, course_ids: $('#courses').val() || [] }),
                processResults: d => ({ results: d }), cache:true }
    });

    $('#teacher_id').select2({
        ...s2common,
        placeholder: 'Search teachers…',
        allowClear: true,
        ajax: { url: '{{ route("api.teachers.search") }}', dataType:'json', delay:250,
                data: p => ({ q: p.term }),
                processResults: d => ({ results: d }), cache:true }
    });

    /* Refresh lessons when courses change */
    $('#courses').on('change', function () {
        $('#lessons').val(null).trigger('change');
    });

    /* ── Public fee toggle ──────────────────────── */
    togglePublicFields();

    /* ── Form validation ────────────────────────── */
    const form = document.getElementById('lcForm');
    form.addEventListener('submit', function (e) {
        if (!form.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
        form.classList.add('was-validated');
    });
});

function togglePublicFields() {
    const val   = document.getElementById('access_type').value;
    const group = document.getElementById('public_fee_group');
    const allowPub = document.getElementById('allow_public_requests');
    const show  = val === 'public' || val === 'both';

    group.classList.toggle('open', show);
    allowPub.disabled = !show;
    if (!show) allowPub.checked = false;
}
</script>
@endsection