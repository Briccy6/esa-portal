@extends('admin.layout')

@section('title', 'Balance Sheet')

@section('content')
<style>
    .balance-sheet-print {
        background: #fff;
        color: #1a1a1a;
    }
    .balance-sheet-print .bs-org-name {
        font-size: 1.35rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .balance-sheet-print .bs-title {
        font-size: 1.1rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        margin-top: 0.25rem;
    }
    .balance-sheet-print .bs-asof {
        font-size: 0.95rem;
        color: #444;
        margin-bottom: 1.25rem;
    }
    .balance-sheet-print .bs-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.92rem;
    }
    .balance-sheet-print .bs-table th,
    .balance-sheet-print .bs-table td {
        padding: 0.35rem 0.5rem;
        vertical-align: top;
    }
    .balance-sheet-print .bs-table .bs-num {
        text-align: right;
        white-space: nowrap;
        font-variant-numeric: tabular-nums;
    }
    .balance-sheet-print .bs-section-head {
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 0.8rem;
        padding-top: 0.75rem;
        border-bottom: 2px solid #2c3e50;
        margin-bottom: 0.35rem;
    }
    .balance-sheet-print .bs-total-row td {
        font-weight: 700;
        border-top: 2px solid #2c3e50;
        padding-top: 0.5rem;
    }
    .balance-sheet-print .bs-grand td {
        font-weight: 700;
        font-size: 1.02rem;
        border-top: 3px double #2c3e50;
        padding-top: 0.6rem;
    }
    .balance-sheet-print .bs-muted {
        color: #6c757d;
        font-size: 0.85rem;
    }
    @media print {
        .main-sidebar, .main-header, .card-tools, .no-print, .filter-form, footer, .nav-section { display: none !important; }
        .content-wrapper, .content { margin: 0 !important; padding: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        .balance-sheet-print { padding: 1rem; }
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex flex-wrap align-items-center justify-content-between gap-2">
                    <h3 class="card-title mb-0">Balance Sheet</h3>
                    <div class="card-tools no-print">
                        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
                            <i class="fas fa-print mr-1"></i> Print
                        </button>
                        <a href="{{ route('admin.accounting.export.balance-sheet', request()->query()) }}" class="btn btn-success btn-sm ml-1">
                            <i class="fas fa-download"></i> Export CSV
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.accounting.balance-sheet') }}" class="mb-4 no-print">
                        <div class="row align-items-end">
                            <div class="col-md-3">
                                <label for="term_id" class="form-label">Academic Term</label>
                                <select id="term_id" name="term_id" class="form-control">
                                    <option value="">All Terms</option>
                                    @foreach($terms as $term)
                                        <option value="{{ $term->id }}" {{ $termId == $term->id ? 'selected' : '' }}>
                                            {{ $term->name }} - {{ $term->academicYear->year ?? '' }}
                                            ({{ $term->start_date->format('M Y') }} - {{ $term->end_date->format('M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="as_of_date" class="form-label">As at (report date)</label>
                                <input type="date" id="as_of_date" name="as_of_date" class="form-control" value="{{ $asOfDate->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary">Generate</button>
                                <a href="{{ route('admin.accounting.balance-sheet') }}" class="btn btn-secondary">Today</a>
                            </div>
                        </div>
                    </form>

                    <div class="balance-sheet-print p-2 p-md-3">
                        <div class="text-center mb-3">
                            @if($school && $school->logo_url)
                                <div class="mb-2">
                                    <img src="{{ $school->logo_url }}" alt="" style="max-height: 56px;">
                                </div>
                            @endif
                            <div class="bs-org-name">{{ $school ? $school->school_name : config('app.name', 'School') }}</div>
                            @if($school && ($school->address || $school->phone || $school->email))
                                <div class="bs-muted">
                                    @if($school->address){{ $school->address }}@endif
                                    @if($school->phone) · {{ $school->phone }}@endif
                                    @if($school->email) · {{ $school->email }}@endif
                                </div>
                            @endif
                            <div class="bs-title">Statement of Financial Position</div>
                            <div class="bs-asof">
                                As at {{ $asOfDate->format('j F Y') }}
                                @if($termId && isset($terms))
                                    @php
                                        $selectedTerm = $terms->where('id', $termId)->first();
                                    @endphp
                                    <br><small>{{ $selectedTerm ? $selectedTerm->name . ' - ' . ($selectedTerm->academicYear->year ?? '') : 'Selected Term' }}</small>
                                @endif
                            </div>
                        </div>

                        <p class="bs-muted small no-print mb-3">
                            Built from your live tables: <code>student_transactions</code> (paid/successful, through the report date),
                            <code>expenses</code> (through the report date), and <code>course_term_fees</code> for accurate fee positions per course and term.
                            This matches the cash (receipts less expenses) plus fees students still owe based on the course term fee schedule.
                        </p>
                        @if(!empty($data['meta']))
                            <p class="bs-muted small no-print mb-3">
                                <strong>Report Source:</strong>
                                @if(!empty($data['meta']['source_description']))
                                    {{ $data['meta']['source_description'] }}<br>
                                @endif
                                <strong>Summary:</strong> Receipts to date: <strong>{{ number_format($data['meta']['total_student_receipts'], 2) }}</strong> RWF ·
                                Expenses to date: <strong>{{ number_format($data['meta']['total_expenses'], 2) }}</strong> RWF
                            </p>
                        @endif

                        <div class="row">
                            <div class="col-lg-6 mb-4 mb-lg-0">
                                <div class="bs-section-head">Assets</div>
                                <p class="bs-muted small mb-2">What you own and money owed to you:</p>
                                <table class="bs-table">
                                    <tbody>
                                        @forelse($data['assets']['details'] as $row)
                                            <tr>
                                                <td>
                                                    @if(!empty($row['account_code']))
                                                        <span class="text-muted">{{ $row['account_code'] }}</span>
                                                        —
                                                    @endif
                                                    {{ $row['name'] }}
                                                </td>
                                                <td class="bs-num">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="bs-muted">No asset lines (unexpected — check data).</td>
                                            </tr>
                                        @endforelse
                                        <tr class="bs-total-row">
                                            <td>Total assets</td>
                                            <td class="bs-num">{{ number_format($data['assets']['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-info small mt-2 no-print" role="alert">
                                    <strong>What this means:</strong><br>
                                    <strong>CASH:</strong> Actual money you have collected from students and kept after paying expenses.<br>
                                    <strong>AR-STU (Accounts Receivable):</strong> Money that students still owe based on their course fees. This is money you expect to collect.
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="bs-section-head">Liabilities</div>
                                <p class="bs-muted small mb-2">Money you owe:</p>
                                <table class="bs-table">
                                    <tbody>
                                        @forelse($data['liabilities']['details'] as $row)
                                            <tr>
                                                <td>
                                                    @if(!empty($row['account_code']))
                                                        <span class="text-muted">{{ $row['account_code'] }}</span>
                                                        —
                                                    @endif
                                                    {{ $row['name'] }}
                                                </td>
                                                <td class="bs-num">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="bs-muted">No liabilities (no student overpayments).</td>
                                            </tr>
                                        @endforelse
                                        <tr class="bs-total-row">
                                            <td>Total liabilities</td>
                                            <td class="bs-num">{{ number_format($data['liabilities']['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="bs-section-head mt-3">Equity</div>
                                <p class="bs-muted small mb-2">Your school's net worth (Assets - Liabilities):</p>
                                <table class="bs-table">
                                    <tbody>
                                        @foreach($data['equity']['details'] as $row)
                                            <tr>
                                                <td>
                                                    @if(!empty($row['account_code']))
                                                        <span class="text-muted">{{ $row['account_code'] }}</span>
                                                        —
                                                    @endif
                                                    {{ $row['name'] }}
                                                </td>
                                                <td class="bs-num">{{ number_format($row['balance'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="bs-total-row">
                                            <td>Total equity</td>
                                            <td class="bs-num">{{ number_format($data['equity']['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="alert alert-success small mt-2 no-print" role="alert">
                                    <strong>What this means:</strong> The school's net worth. Your financial position = Total Assets minus Total Liabilities.
                                </div>

                                <table class="bs-table mt-2">
                                    <tbody>
                                        <tr class="bs-grand">
                                            <td>Total liabilities and equity</td>
                                            <td class="bs-num">{{ number_format($data['liabilities']['total'] + $data['equity']['total'], 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="row mt-4 pt-3 border-top align-items-end">
                            <div class="col-md-6 mb-2 mb-md-0">
                                <div class="d-flex justify-content-between bs-grand px-1">
                                    <span>Total assets</span>
                                    <span class="bs-num">{{ number_format($data['assets']['total'], 2) }} RWF</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between bs-grand px-1">
                                    <span>Total liabilities and equity</span>
                                    <span class="bs-num">{{ number_format($data['liabilities']['total'] + $data['equity']['total'], 2) }} RWF</span>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4 pt-4 border-top">
                            <div class="col-12">
                                <h5 class="mb-3">Balance Sheet Explained</h5>
                                <div class="alert alert-light border-left border-primary" style="border-left-width: 4px;">
                                    <p class="mb-2"><strong>What is this Balance Sheet?</strong></p>
                                    <p class="mb-2">
                                        A Balance Sheet shows your school's financial position at a specific point in time. It has three main parts:
                                    </p>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <strong class="text-primary">1. ASSETS (What you own and are owed)</strong>
                                            <table class="table table-sm mt-2 mb-0">
                                                <tr>
                                                    <td><strong>CASH:</strong></td>
                                                    <td>{{ number_format($data['meta']['total_student_receipts'] ?? 0, 2) }} - {{ number_format($data['meta']['total_expenses'] ?? 0, 2) }} RWF</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2" class="small text-muted">Actual money = Total student payments received MINUS total expenses paid out</td>
                                                </tr>
                                                @if(collect($data['assets']['details'])->where('account_code', 'AR-STU')->isNotEmpty())
                                                    @php $arStu = collect($data['assets']['details'])->where('account_code', 'AR-STU')->first(); @endphp
                                                    <tr>
                                                        <td><strong>RECEIVABLES:</strong></td>
                                                        <td>{{ number_format($arStu['balance'] ?? 0, 2) }} RWF</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="small text-muted">Money students STILL OWE based on their course fees</td>
                                                    </tr>
                                                @endif
                                            </table>
                                        </div>
                                        <div class="col-md-6">
                                            <strong class="text-danger">2. LIABILITIES (What you owe)</strong>
                                            @if($data['liabilities']['total'] > 0.01)
                                                <table class="table table-sm mt-2 mb-0">
                                                    <tr>
                                                        <td><strong>STUDENT ADVANCES:</strong></td>
                                                        <td>{{ number_format($data['liabilities']['total'], 2) }} RWF</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" class="small text-muted">When students paid more than they owed</td>
                                                    </tr>
                                                </table>
                                            @else
                                                <p class="text-muted">No liabilities (students haven't overpaid)</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <hr class="my-3">
                                    
                                    <p class="mb-2"><strong class="text-success">3. EQUITY (School's Net Worth)</strong></p>
                                    <p class="small mb-0">
                                        <strong>Formula:</strong> Total Assets ({{ number_format($data['assets']['total'], 2) }}) - Total Liabilities ({{ number_format($data['liabilities']['total'], 2) }}) = 
                                        <strong>{{ number_format($data['equity']['total'], 2) }} RWF</strong>
                                    </p>
                                    <p class="small text-muted mt-2 mb-0">
                                        This is what the school is worth financially.
                                    </p>
                                </div>

                                <div class="alert alert-warning mt-3 no-print" role="alert">
                                    <strong>⚠️ Important Note:</strong> The Total Assets figure ({{ number_format($data['assets']['total'], 2) }} RWF) is the sum of:
                                    <ul class="mb-0 mt-2">
                                        <li><strong>{{ number_format($data['meta']['total_student_receipts'] - $data['meta']['total_expenses'], 2) }} RWF</strong> in actual cash (money in hand)</li>
                                        <li><strong>{{ number_format(collect($data['assets']['details'])->where('account_code', 'AR-STU')->sum('balance'), 2) }} RWF</strong> in expected receivables (money students owe you)</li>
                                    </ul>
                                    <strong>Only the CASH figure is money you can spend right now.</strong>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="mt-1 d-flex flex-wrap align-items-center gap-3">
                                    <span class="font-weight-bold">Accounting equation:</span>
                                    @if($data['is_balanced'])
                                        <span class="badge badge-success">Balanced</span>
                                        <span class="text-muted small">Assets equal liabilities plus equity.</span>
                                    @else
                                        <span class="badge badge-danger">Out of balance</span>
                                        <span class="text-danger small">
                                            Difference: {{ number_format($data['difference'] ?? 0, 2) }} RWF — check posted journals and account types.
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
