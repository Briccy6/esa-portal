@extends('admin.layout')

@section('title', 'School Tariffs')

@section('content')

<style>
    /* --- Styles same as before --- */
    h1 {
        font-size: 2.4rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        color: #2c3e50;
        text-transform: uppercase;
        letter-spacing: 1.2px;
    }
    .alert-success {
        background: #d4edda;
        border: 1px solid #c3e6cb;
        color: #155724;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 8px rgba(21, 87, 36, 0.2);
    }
    .btn-primary {
        background: linear-gradient(135deg, #4a90e2, #357ABD);
        border: none;
        padding: 0.6rem 1.6rem;
        border-radius: 10px;
        font-weight: 600;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 3px 8px rgba(53, 122, 189, 0.4);
        color: white;
        text-decoration: none;
        display: inline-block;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #357ABD, #2a5e93);
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(41, 94, 147, 0.6);
        color: white;
        text-decoration: none;
    }
    table.table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 12px;
        font-size: 1rem;
        color: #34495e;
    }
    table.table thead tr th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: #fff;
        font-weight: 700;
        padding: 1rem 1.2rem;
        text-align: left;
        border-radius: 12px 12px 0 0;
        user-select: none;
        cursor: pointer;
        position: relative;
    }
    table.table thead tr th.sort-asc::after {
        content: "▲";
        position: absolute;
        right: 10px;
        font-size: 0.7rem;
    }
    table.table thead tr th.sort-desc::after {
        content: "▼";
        position: absolute;
        right: 10px;
        font-size: 0.7rem;
    }
    table.table tbody tr {
        background: #fff;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 0 0 12px 12px;
    }
    table.table tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
    }
    table.table tbody td {
        padding: 1rem 1.2rem;
        vertical-align: middle;
    }
    .btn-sm {
        padding: 0.35rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        color: white;
        display: inline-block;
        text-align: center;
        text-decoration: none;
    }
    .btn-warning {
        background: linear-gradient(135deg, #f6c23e, #dda20a);
        box-shadow: 0 3px 8px rgba(221, 162, 10, 0.4);
    }
    .btn-warning:hover {
        background: linear-gradient(135deg, #dda20a, #a97503);
        box-shadow: 0 6px 15px rgba(169, 117, 3, 0.7);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }
    .btn-danger {
        background: linear-gradient(135deg, #e74c3c, #c0392b);
        box-shadow: 0 3px 8px rgba(192, 57, 43, 0.4);
    }
    .btn-danger:hover {
        background: linear-gradient(135deg, #c0392b, #82241f);
        box-shadow: 0 6px 15px rgba(130, 36, 31, 0.7);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }
    .pagination {
        display: flex;
        list-style: none;
        padding-left: 0;
        gap: 0.5rem;
        margin-top: 1.5rem;
        justify-content: center;
    }
    .pagination li a,
    .pagination li span {
        color: #667eea;
        padding: 0.5rem 0.9rem;
        border-radius: 8px;
        border: 1px solid transparent;
        transition: all 0.3s ease;
        font-weight: 600;
        text-decoration: none;
    }
    .pagination li.active span {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
    .pagination li a:hover {
        background: #5763c6;
        color: white;
        border-color: #5763c6;
        text-decoration: none;
    }
    /* Search input */
    #searchInput {
        padding: 0.5rem 1rem;
        margin-bottom: 1.5rem;
        border: 2px solid #667eea;
        border-radius: 12px;
        font-size: 1rem;
        width: 100%;
        max-width: 350px;
        box-sizing: border-box;
        transition: border-color 0.3s ease;
    }
    #searchInput:focus {
        outline: none;
        border-color: #764ba2;
        box-shadow: 0 0 8px #764ba2aa;
    }
</style>

<h1>School Tariffs</h1>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<input type="text" id="searchInput" placeholder="Search by Activity..." autocomplete="off" />

<a href="{{ route('admin.tariffs.create') }}" class="btn btn-primary mb-3">Add New Tariff</a>

<table class="table table-bordered" id="tariffsTable">
    <thead>
        <tr>
            <th data-column="activity">Activity</th>
            <th data-column="amount">Amount</th>
            <th data-column="discount">Discount</th>
            <th data-column="deadline">Deadline</th>
            <th data-column="actions" style="cursor:default;">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tariffs as $tariff)
        <tr>
            <td class="activity">{{ $tariff->activity }}</td>
            <td class="amount">${{ number_format($tariff->amount, 2) }}</td>
            <td class="discount">
                @if($tariff->has_discount)
                    {{ $tariff->discount_type == 'percentage' ? $tariff->discount_amount . '%' : '$' . number_format($tariff->discount_amount, 2) }}
                @else
                    None
                @endif
            </td>
            <td class="deadline">
                @if($tariff->deadline_type == 'date')
                    {{ \Carbon\Carbon::parse($tariff->deadline_date)->format('Y-m-d H:i') }}
                @else
                    {{ $tariff->deadline_hours }} hours from creation
                @endif
            </td>
            <td class="actions">
                <a href="{{ route('admin.tariffs.edit', $tariff->id) }}" class="btn btn-sm btn-warning">Edit</a>

                <form action="{{ route('admin.tariffs.destroy', $tariff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this tariff?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @empty
        <tr><td colspan="5">No tariffs found.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $tariffs->links() }}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchInput');
    const table = document.getElementById('tariffsTable');
    const tbody = table.querySelector('tbody');
    const headers = table.querySelectorAll('thead th[data-column]');
    let sortColumn = null;
    let sortDirection = 'asc'; // or 'desc'

    // Live search filter by Activity
    searchInput.addEventListener('input', function () {
        const filter = this.value.toLowerCase();
        const rows = tbody.querySelectorAll('tr');
        rows.forEach(row => {
            const activityCell = row.querySelector('.activity');
            if (!activityCell) return;
            const text = activityCell.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Sort helper functions
    function compareValues(a, b, isNumeric) {
        if (isNumeric) {
            a = parseFloat(a.replace(/[^0-9.-]+/g,"")) || 0;
            b = parseFloat(b.replace(/[^0-9.-]+/g,"")) || 0;
        } else {
            a = a.toString().toLowerCase();
            b = b.toString().toLowerCase();
        }
        if (a < b) return -1;
        if (a > b) return 1;
        return 0;
    }

    // Sorting function
    function sortTable(column, direction) {
        const rows = Array.from(tbody.querySelectorAll('tr'));

        // Determine if numeric column for proper sorting
        const numericColumns = ['amount', 'discount'];
        const dateColumns = ['deadline'];

        rows.sort((rowA, rowB) => {
            let cellA = rowA.querySelector(`.${column}`);
            let cellB = rowB.querySelector(`.${column}`);
            if (!cellA || !cellB) return 0;
            let valA = cellA.textContent.trim();
            let valB = cellB.textContent.trim();

            // Special handling for deadline column with dates
            if (dateColumns.includes(column)) {
                // Try parsing dates or fallback to string compare
                let dateA = Date.parse(valA);
                let dateB = Date.parse(valB);
                if (!isNaN(dateA) && !isNaN(dateB)) {
                    return direction === 'asc' ? dateA - dateB : dateB - dateA;
                }
            }

            let isNumeric = numericColumns.includes(column);
            let comparison = compareValues(valA, valB, isNumeric);
            return direction === 'asc' ? comparison : -comparison;
        });

        // Re-add sorted rows to tbody
        rows.forEach(row => tbody.appendChild(row));
    }

    // Handle header click for sorting
    headers.forEach(header => {
        header.addEventListener('click', function () {
            // Do not sort actions column
            if (this.dataset.column === 'actions') return;

            const column = this.dataset.column;

            if (sortColumn === column) {
                // Toggle direction
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            // Remove sort classes from all headers
            headers.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            // Add class to current header
            this.classList.add(sortDirection === 'asc' ? 'sort-asc' : 'sort-desc');

            sortTable(sortColumn, sortDirection);
        });
    });
});
</script>

@endsection
