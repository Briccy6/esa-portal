@extends('admin.layout')
@section('title', 'Messages')

@section('content')
<div class="container">
    <h2>All Messages</h2>

    <a href="{{ route('admin.messages.create') }}" class="btn btn-success mb-3">New Message</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <input type="text" id="liveSearch" class="form-control mb-3" placeholder="Search messages...">

    @if($messages->count())
        <table class="table table-bordered" id="messagesTable">
            <thead>
                <tr>
                    <th data-column="title" class="sortable">Title <span class="sort-icon"></span></th>
                    <th data-column="recipient" class="sortable">Recipient <span class="sort-icon"></span></th>
                    <th data-column="sent_at" class="sortable">Sent At <span class="sort-icon"></span></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($messages as $msg)
                <tr>
                    <td>{{ $msg->title }}</td>
                    <td>{{ ucfirst($msg->recipient_type) }}</td>
                    <td>{{ $msg->created_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('admin.messages.show', $msg) }}" class="btn btn-sm btn-info">View</a>
                        <form action="{{ route('admin.messages.destroy', $msg) }}" method="POST" style="display:inline-block">
                            @csrf @method('DELETE')
                            <button onclick="return confirm('Delete this message?')" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{ $messages->links() }}
    @else
        <p>No messages found.</p>
    @endif
</div>

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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = document.getElementById('messagesTable');
        const headers = table.querySelectorAll('th.sortable');
        const tbody = table.querySelector('tbody');
        const liveSearchInput = document.getElementById('liveSearch');

        let currentSortColumn = null;
        let currentSortOrder = 'asc';

        // Sort function for table rows
        function sortTable(column, order) {
            const rows = Array.from(tbody.querySelectorAll('tr'));

            rows.sort((a, b) => {
                let aText = a.querySelector(`td:nth-child(${column + 1})`).textContent.trim().toLowerCase();
                let bText = b.querySelector(`td:nth-child(${column + 1})`).textContent.trim().toLowerCase();

                // For date column, parse date for correct sorting
                if(column === 2) { // Sent At column index 2 (0-based)
                    aText = Date.parse(aText) || 0;
                    bText = Date.parse(bText) || 0;
                }

                if (aText < bText) return order === 'asc' ? -1 : 1;
                if (aText > bText) return order === 'asc' ? 1 : -1;
                return 0;
            });

            // Append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        }

        // Remove sorting classes from headers
        function clearSortClasses() {
            headers.forEach(h => h.classList.remove('sort-asc', 'sort-desc'));
        }

        // Click event for sorting
        headers.forEach((header, index) => {
            header.addEventListener('click', () => {
                if (currentSortColumn === index) {
                    // toggle sort order
                    currentSortOrder = currentSortOrder === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSortColumn = index;
                    currentSortOrder = 'asc';
                }

                clearSortClasses();
                header.classList.add(currentSortOrder === 'asc' ? 'sort-asc' : 'sort-desc');

                sortTable(index, currentSortOrder);
            });
        });

        // Live search filtering
        liveSearchInput.addEventListener('input', () => {
            const filter = liveSearchInput.value.toLowerCase();
            const rows = tbody.querySelectorAll('tr');

            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                const matches = Array.from(cells).some(td => td.textContent.toLowerCase().includes(filter));
                row.style.display = matches ? '' : 'none';
            });
        });
    });
</script>
@endsection
