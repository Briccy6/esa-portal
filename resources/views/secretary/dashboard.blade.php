@extends('secretary.layout')

@section('page_title', 'Overview')
@section('title', 'Secretary Dashboard')

@section('styles')
<style>
    .dashboard-kpis {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .dashboard-kpi {
        min-width: 0;
        padding: 1.15rem;
        border: 1px solid #e1e8f0;
        border-radius: 0.85rem;
        background: #fff;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
    }

    .dashboard-kpi-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .dashboard-kpi-label {
        color: #64748b;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .dashboard-kpi-value {
        display: block;
        margin-top: 0.65rem;
        color: #172b4d;
        font-size: clamp(1.45rem, 2vw, 1.9rem);
        font-weight: 700;
        line-height: 1.1;
    }

    .dashboard-kpi-note {
        display: block;
        margin-top: 0.4rem;
        color: #94a3b8;
        font-size: 0.78rem;
    }

    .dashboard-kpi-icon {
        display: inline-flex;
        width: 40px;
        height: 40px;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        border-radius: 0.7rem;
        background: #edf4ff;
        color: #2457a7;
        font-size: 1.1rem;
    }

    .dashboard-kpi-icon.is-green { background: #ecfdf3; color: #15803d; }
    .dashboard-kpi-icon.is-amber { background: #fff7e6; color: #b45309; }
    .dashboard-kpi-icon.is-red { background: #fff1f2; color: #be123c; }

    .dashboard-actions {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .action-card {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        padding: 1rem 1.1rem;
        border: 1px solid #e1e8f0;
        border-radius: 0.85rem;
        background: #fff;
        text-decoration: none;
        color: inherit;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        min-width: 0;
    }

    .action-card:hover {
        border-color: #c7d7ea;
        color: inherit;
    }

    .action-card h3 {
        margin: 0;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .action-card p {
        margin: 0.2rem 0 0;
        color: #94a3b8;
        font-size: 0.8rem;
    }

    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .student-meta { color: #94a3b8; font-size: 0.78rem; }
    .amount-add { color: #15803d; font-weight: 700; }
    .amount-spend { color: #be123c; font-weight: 700; }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.25rem 0.55rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-pill.is-warning { background: #fff7e6; color: #b45309; }
    .status-pill.is-danger { background: #fff1f2; color: #be123c; }
    .status-pill.is-ok { background: #ecfdf3; color: #15803d; }

    .table-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.4rem;
    }

    .table-actions .btn { min-height: 36px; padding: 0.3rem 0.7rem; font-size: 0.8rem; }

    @media (max-width: 1100px) {
        .dashboard-kpis,
        .dashboard-actions,
        .dashboard-grid { grid-template-columns: 1fr 1fr; }
    }

    @media (max-width: 640px) {
        .dashboard-kpis,
        .dashboard-actions,
        .dashboard-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>Secretary dashboard</h1>
            <p>Track registrations, tokens, and students that need follow-up.</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus"></i> Register student
            </a>
            <a href="{{ route('secretary.student-registration.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-clipboard-check"></i> Complete registrations
            </a>
        </div>
    </div>

    @include('partials.academic-year-banner')

    <div class="dashboard-kpis">
        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <span class="dashboard-kpi-label">Total students</span>
                <span class="dashboard-kpi-icon"><i class="bi bi-mortarboard"></i></span>
            </div>
            <strong class="dashboard-kpi-value">{{ number_format($totalStudents ?? 0) }}</strong>
            <span class="dashboard-kpi-note">In the selected academic year</span>
        </article>
        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <span class="dashboard-kpi-label">Total tokens</span>
                <span class="dashboard-kpi-icon is-green"><i class="bi bi-coin"></i></span>
            </div>
            <strong class="dashboard-kpi-value">{{ number_format($totalTokens ?? 0) }}</strong>
            <span class="dashboard-kpi-note">Current student balances</span>
        </article>
        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <span class="dashboard-kpi-label">Pending registrations</span>
                <span class="dashboard-kpi-icon is-amber"><i class="bi bi-hourglass-split"></i></span>
            </div>
            <strong class="dashboard-kpi-value">{{ number_format($pendingRegistrations ?? 0) }}</strong>
            <span class="dashboard-kpi-note">NESA registration incomplete</span>
        </article>
        <article class="dashboard-kpi">
            <div class="dashboard-kpi-top">
                <span class="dashboard-kpi-label">Low token balance</span>
                <span class="dashboard-kpi-icon is-red"><i class="bi bi-exclamation-triangle"></i></span>
            </div>
            <strong class="dashboard-kpi-value">{{ number_format($lowBalanceStudents ?? 0) }}</strong>
            <span class="dashboard-kpi-note">Students with fewer than 5 tokens</span>
        </article>
    </div>

    <div class="dashboard-actions">
        <a class="action-card" href="{{ route('students.create') }}">
            <span class="dashboard-kpi-icon"><i class="bi bi-person-plus"></i></span>
            <div>
                <h3>Register a student</h3>
                <p>Create a new student record</p>
            </div>
        </a>
        <a class="action-card" href="{{ route('secretary.tokens.index') }}">
            <span class="dashboard-kpi-icon is-green"><i class="bi bi-coin"></i></span>
            <div>
                <h3>Manage tokens</h3>
                <p>Add, deduct, or review balances</p>
            </div>
        </a>
        <a class="action-card" href="{{ route('secretary.student-registration.index') }}">
            <span class="dashboard-kpi-icon is-amber"><i class="bi bi-file-earmark-check"></i></span>
            <div>
                <h3>Finish registration</h3>
                <p>Upload IDs and supporting documents</p>
            </div>
        </a>
    </div>

    <div class="dashboard-grid">
        <section class="sec-card">
            <div class="sec-card-header">
                <div>
                    <h2>Recent students</h2>
                    <p>Latest registrations in this academic year</p>
                </div>
            </div>
            <div class="sec-table-wrap">
                <table class="sec-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Registration</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentStudents ?? [] as $student)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $student->full_name }}</div>
                                    <div class="student-meta">{{ $student->email }}</div>
                                </td>
                                <td>{{ $student->reg_number }}</td>
                                <td>{{ \Carbon\Carbon::parse($student->created_at)->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="sec-empty">No recent students yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="sec-card">
            <div class="sec-card-header">
                <div>
                    <h2>Recent token activity</h2>
                    <p>Latest additions and deductions</p>
                </div>
            </div>
            <div class="sec-table-wrap">
                <table class="sec-table">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions ?? [] as $transaction)
                            <tr>
                                <td>{{ $transaction->student->full_name ?? 'N/A' }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    @if($transaction->type == 'add')
                                        <span class="amount-add">+{{ $transaction->amount }}</span>
                                    @else
                                        <span class="amount-spend">-{{ $transaction->amount }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="sec-empty">No token transactions yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <section class="sec-card">
        <div class="sec-card-header">
            <div>
                <h2>Students needing attention</h2>
                <p>Low token balances or incomplete NESA registration</p>
            </div>
            <a href="{{ route('secretary.tokens.index') }}" class="btn btn-outline-secondary btn-sm">Open tokens</a>
        </div>
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Registration</th>
                        <th>Course</th>
                        <th>Tokens</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentsNeedingAttention ?? [] as $student)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $student->full_name }}</div>
                                <div class="student-meta">{{ $student->email }}</div>
                            </td>
                            <td>{{ $student->reg_number }}</td>
                            <td>{{ $student->course->course_name ?? 'N/A' }}</td>
                            <td>{{ $student->token->balance ?? 0 }}</td>
                            <td>
                                @if($student->token && $student->token->balance < 5)
                                    <span class="status-pill is-warning"><i class="bi bi-exclamation-triangle"></i> Low balance</span>
                                @elseif(!$student->nesa_registered)
                                    <span class="status-pill is-danger"><i class="bi bi-clock"></i> NESA pending</span>
                                @else
                                    <span class="status-pill is-ok"><i class="bi bi-check"></i> OK</span>
                                @endif
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('secretary.tokens.index') }}" class="btn btn-primary btn-sm">Tokens</a>
                                    <a href="{{ route('secretary.student-registration.edit', $student) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="sec-empty">All students currently look complete.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
