@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    .accountant-module {
        --accountant-primary: #0f766e;
        --accountant-primary-dark: #115e59;
        --accountant-secondary: #334155;
        --accountant-accent: #b45309;
        --accountant-border: #e2e8f0;
        --accountant-surface: #ffffff;
        --accountant-muted: #64748b;
        font-family: 'Poppins', sans-serif;
    }

    .accountant-module .card {
        border: 1px solid var(--accountant-border);
        border-radius: 12px;
        background-color: var(--accountant-surface);
        box-shadow: 0 4px 16px rgba(15, 23, 42, .05);
    }

    .accountant-module .card-header {
        border-bottom-color: var(--accountant-border);
        background-color: var(--accountant-surface);
    }

    .accountant-module .btn-primary,
    .accountant-module .bg-primary {
        --bs-primary-rgb: 15, 118, 110;
        background-color: rgba(var(--bs-primary-rgb), var(--bs-bg-opacity, 1)) !important;
        border-color: var(--accountant-primary) !important;
    }
    .accountant-module .bg-dark { background-color: #243447 !important; }

    .accountant-module .btn-primary:hover,
    .accountant-module .btn-primary:focus-visible {
        background-color: var(--accountant-primary-dark) !important;
        border-color: var(--accountant-primary-dark) !important;
    }

    .accountant-module .text-primary { color: var(--accountant-primary) !important; }
    .accountant-module .btn-outline-primary {
        color: var(--accountant-primary);
        border-color: var(--accountant-primary);
    }
    .accountant-module .btn-outline-primary:hover {
        color: #fff;
        background-color: var(--accountant-primary);
        border-color: var(--accountant-primary);
    }
    .accountant-module .bg-info { background-color: #2563a6 !important; }
    .accountant-module .text-info { color: #2563a6 !important; }
    .accountant-module .btn-info {
        color: #fff;
        background-color: #2563a6;
        border-color: #2563a6;
    }
    .accountant-module .form-control:focus,
    .accountant-module .form-select:focus {
        border-color: #5aa69f;
        box-shadow: 0 0 0 .2rem rgba(15, 118, 110, .15);
    }
    .accountant-module .table thead th {
        color: var(--accountant-secondary);
        font-size: .75rem;
        letter-spacing: .04em;
        text-transform: uppercase;
        white-space: nowrap;
    }
    .accountant-module .table-responsive { border-radius: 0 0 12px 12px; }

    @media (prefers-reduced-motion: no-preference) {
        .accountant-module .card,
        .accountant-module .btn { transition: box-shadow .18s ease, transform .18s ease, background-color .18s ease; }
        .accountant-module .card:hover { box-shadow: 0 8px 22px rgba(15, 23, 42, .08); }
    }
</style>
@endpush
