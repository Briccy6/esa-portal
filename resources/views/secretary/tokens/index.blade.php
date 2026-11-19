@extends('secretary.layout')

@section('page_title', 'Tokens')
@section('title', 'Token Management')

@section('content')
<div class="page-shell">
    <div class="page-heading">
        <div>
            <h1>Token management</h1>
            <p>Search students, add or deduct tokens, and review history.</p>
        </div>
        <form method="GET" action="{{ route('secretary.tokens.index') }}" class="page-actions" style="flex: 1; max-width: 420px;">
            <div class="input-group">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search name, email, or reg number">
                <button class="btn btn-primary" type="submit">Search</button>
            </div>
        </form>
    </div>

    <section class="sec-card">
        <div class="sec-card-header">
            <div>
                <h2>Student balances</h2>
                <p>{{ $students->total() }} student{{ $students->total() === 1 ? '' : 's' }} shown</p>
            </div>
            @if(request('q'))
                <a href="{{ route('secretary.tokens.index') }}" class="btn btn-sm btn-outline-secondary">Clear search</a>
            @endif
        </div>
        <div class="sec-table-wrap">
            <table class="sec-table">
                <thead>
                    <tr>
                        <th>Student</th>
                        <th>Registration</th>
                        <th>Balance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        @php $balance = $student->token_balance ?? ($student->token->balance ?? 0); @endphp
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $student->full_name }}</div>
                                <div class="text-muted small">{{ $student->email }}</div>
                            </td>
                            <td>{{ $student->reg_number }}</td>
                            <td>
                                @if($balance > 4)
                                    <span class="badge text-bg-success">{{ $balance }} tokens</span>
                                @elseif($balance > 0)
                                    <span class="badge text-bg-warning">{{ $balance }} tokens</span>
                                @else
                                    <span class="badge text-bg-danger">{{ $balance }} tokens</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <button type="button" class="btn btn-sm btn-success"
                                        data-bs-toggle="modal" data-bs-target="#tokenModal"
                                        data-mode="add"
                                        data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->full_name }}"
                                        data-action="{{ route('secretary.tokens.add', $student) }}">
                                        Add
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal" data-bs-target="#tokenModal"
                                        data-mode="deduct"
                                        data-student-id="{{ $student->id }}"
                                        data-student-name="{{ $student->full_name }}"
                                        data-action="{{ route('secretary.tokens.deduct', $student) }}">
                                        Deduct
                                    </button>
                                    <a href="{{ route('secretary.tokens.history', $student) }}" class="btn btn-sm btn-outline-secondary">History</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="sec-empty">No students found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
            <div class="p-3 d-flex justify-content-center">{{ $students->links() }}</div>
        @endif
    </section>
</div>

<div class="modal fade" id="tokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form method="POST" id="tokenForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tokenModalTitle">Adjust tokens</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted" id="tokenStudentLabel"></p>
                    <div class="mb-3">
                        <label class="form-label" for="token_amount">Amount</label>
                        <input type="number" min="1" max="1000" name="amount" id="token_amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="token_description">Description</label>
                        <input type="text" name="description" id="token_description" class="form-control" required>
                    </div>
                    <div class="mb-0" id="tokenSourceWrap">
                        <label class="form-label" for="token_source">Source</label>
                        <select name="source" id="token_source" class="form-select">
                            <option value="system">System</option>
                            <option value="bonus">Bonus</option>
                            <option value="compensation">Compensation</option>
                            <option value="refund">Refund</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="tokenSubmitBtn">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('tokenModal')?.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const mode = button.getAttribute('data-mode');
    const form = document.getElementById('tokenForm');
    const sourceWrap = document.getElementById('tokenSourceWrap');
    form.action = button.getAttribute('data-action');
    document.getElementById('tokenStudentLabel').textContent = button.getAttribute('data-student-name');
    document.getElementById('token_description').value = mode === 'add' ? 'Tokens added by secretary' : 'Tokens deducted by secretary';
    sourceWrap.style.display = mode === 'add' ? 'block' : 'none';
    document.getElementById('token_source').required = mode === 'add';
    document.getElementById('tokenModalTitle').textContent = mode === 'add' ? 'Add tokens' : 'Deduct tokens';
    document.getElementById('tokenSubmitBtn').textContent = mode === 'add' ? 'Add tokens' : 'Deduct tokens';
});
</script>
@endsection
