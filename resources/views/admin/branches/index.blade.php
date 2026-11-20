@extends('admin.layout')

@section('title', 'Branches')

@push('styles')
<style>
    .esa-page-head {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }
    .esa-eyebrow {
        color: #166b4d;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .14em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .esa-page-head h1 {
        margin: 0;
        font-size: clamp(2rem, 4vw, 3.25rem);
        line-height: 1;
    }
    .esa-panel {
        background: #fff;
        border: 1px solid #dde3da;
        border-radius: 18px;
        box-shadow: 0 18px 50px rgba(27,49,38,.08);
        overflow: hidden;
    }
    .esa-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 16px 18px;
        border-bottom: 1px solid #e6eadf;
    }
    .esa-search {
        position: relative;
        max-width: 360px;
        width: 100%;
    }
    .esa-search i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #87928b;
    }
    .esa-search input {
        width: 100%;
        height: 44px;
        padding: 0 14px 0 40px;
        border: 1px solid #dde3da;
        border-radius: 10px;
        outline: none;
    }
    .esa-search input:focus {
        border-color: #166b4d;
        box-shadow: 0 0 0 3px rgba(22,107,77,.10);
    }
    .esa-table th {
        background: #edf1e9;
        color: #566159;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        border: 0;
    }
    .esa-table td {
        border-color: #edf0ea;
        vertical-align: middle;
    }
    .esa-index {
        display: inline-grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border-radius: 50%;
        background: #eef3eb;
        color: #166b4d;
        font-weight: 800;
    }
    .esa-actions {
        display: flex;
        justify-content: end;
        gap: 8px;
    }
    .esa-action {
        width: 38px;
        height: 38px;
        display: inline-grid;
        place-items: center;
        border: 1px solid #dde3da;
        border-radius: 9px;
        background: #fff;
        color: #566159;
        text-decoration: none;
    }
    .esa-action-danger {
        color: #b42318;
        cursor: pointer;
    }
    .esa-empty {
        padding: 54px 20px;
        text-align: center;
        color: #87928b;
    }
    @media (max-width: 640px) {
        .esa-page-head, .esa-toolbar {
            align-items: stretch;
            flex-direction: column;
        }
        .esa-page-head .btn { width: 100%; }
        .esa-toolbar .esa-search { max-width: none; }
        .esa-actions { justify-content: start; }
    }
</style>
@endpush

@section('content')
<div class="esa-page-head">
    <div>
        <div class="esa-eyebrow">Institution network</div>
        <h1>School branches</h1>
    </div>
    <a href="{{ route('admin.branch.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add branch
    </a>
</div>

<section class="esa-panel">
    <div class="esa-toolbar">
        <div class="esa-search">
            <i class="fas fa-search"></i>
            <input type="search" id="branchSearch" placeholder="Search branch or location" aria-label="Search branches">
        </div>
        <span class="text-muted" id="branchCount">{{ count($branches) }} entries</span>
    </div>

    <div class="table-responsive">
        <table class="table esa-table mb-0" id="branchesTable">
            <thead>
                <tr>
                    <th style="width:72px">#</th>
                    <th>Branch name</th>
                    <th>Location</th>
                    <th class="text-end" style="width:140px">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($branches as $index => $branch)
                    <tr class="branch-row">
                        <td><span class="esa-index">{{ $index + 1 }}</span></td>
                        <td class="fw-bold">{{ $branch->name }}</td>
                        <td>{{ $branch->location ?: 'Not specified' }}</td>
                        <td>
                            <div class="esa-actions">
                                <a href="{{ route('admin.branch.edit', $branch->id) }}" class="esa-action" title="Edit branch" aria-label="Edit {{ $branch->name }}">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.branch.destroy', $branch->id) }}" method="POST" class="branch-delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="esa-action esa-action-danger" title="Delete branch" aria-label="Delete {{ $branch->name }}" onclick="ESA.confirmDelete({ title: 'Delete {{ addslashes($branch->name) }}?', text: 'This branch and its related references may be affected.', form: this.closest('form') })">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="esa-empty">
                                <i class="fas fa-map-location-dot fa-2x mb-3"></i>
                                <h3>No branches found</h3>
                                <p>Create the first branch to start organizing the institution.</p>
                                <a href="{{ route('admin.branch.create') }}" class="btn btn-primary">Create branch</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('branchSearch');
        const rows = Array.from(document.querySelectorAll('.branch-row'));
        const count = document.getElementById('branchCount');
        if (!input) return;

        let timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                const term = input.value.trim().toLowerCase();
                let visible = 0;
                rows.forEach(function (row) {
                    const matches = row.textContent.toLowerCase().includes(term);
                    row.style.display = matches ? '' : 'none';
                    if (matches) visible++;
                });
                count.textContent = visible + ' entry' + (visible === 1 ? '' : ' entries');
            }, 300);
        });
    });
</script>
@endpush
