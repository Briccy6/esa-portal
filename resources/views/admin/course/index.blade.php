@extends('admin.layout')

@section('title', 'Courses')

@section('content')
<div class="page-container">
    <div class="page-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-book me-3"></i>
                Courses
            </h1>
            <div class="header-actions">
                <a href="{{ route('admin.course.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus me-2"></i>
                    Add New Course
                </a>
                <button class="btn btn-outline-secondary" onclick="exportData()">
                    <i class="fas fa-download me-2"></i>
                    Export
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-container">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-wrapper">
        <!-- Search box -->
        <div class="table-header" style="margin-bottom: 1rem;">
            <div class="search-box" style="max-width: 300px;">
                <i class="fas fa-search search-icon"></i>
                <input type="text" id="searchInput" placeholder="Search courses..." class="search-input">
            </div>
        </div>

        <div class="table-card">
            <table class="data-table" id="coursesTable">
                <thead>
                    <tr>
                        <th class="sortable" data-sort="course_name">
                            <div class="th-content">
                                Course Name
                                <i class="fas fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th class="sortable" data-sort="abbreviation">
                            <div class="th-content">
                                Abbreviation
                                <i class="fas fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th class="sortable" data-sort="course_type">
                            <div class="th-content">
                                Course Type
                                <i class="fas fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th class="sortable" data-sort="course_code">
                            <div class="th-content">
                                Course Code
                                <i class="fas fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th class="sortable" data-sort="branches">
                            <div class="th-content">
                                Branches
                                <i class="fas fa-sort sort-icon"></i>
                            </div>
                        </th>
                        <th class="no-sort">
                            <div class="th-content">
                                Actions
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr data-course_name="{{ strtolower($course->course_name) }}" data-abbreviation="{{ strtolower($course->abbreviation) }}" data-course_type="{{ strtolower($course->course_type) }}" data-course_code="{{ strtolower($course->course_code) }}" data-branches="{{ strtolower(implode(', ', $course->branches->pluck('name')->toArray())) }}">
                            <td>{{ $course->course_name }}</td>
                            <td>{{ $course->abbreviation }}</td>
                            <td>{{ $course->course_type }}</td>
                            <td>{{ $course->course_code }}</td>
                            <td>{{ implode(', ', $course->branches->pluck('name')->toArray()) }}</td>
                            <td>
                                <a href="{{ route('admin.course.edit', $course->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($course->courseTermFees->count() > 0)
                                    <a href="{{ route('course.edit-fees', $course->id) }}" class="btn btn-sm btn-info" title="Edit Fees">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                @else
                                    <a href="{{ route('course.assign-fees', $course->id) }}" class="btn btn-sm btn-success" title="Assign Fees">
                                        <i class="fas fa-plus-circle"></i>
                                    </a>
                                @endif

                                <form action="{{ route('admin.course.destroy', $course->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirmDelete();">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No courses found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    // Confirm delete
    function confirmDelete() {
        return confirm('Are you sure you want to delete this course?');
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase();
        const rows = document.querySelectorAll('#coursesTable tbody tr');

        rows.forEach(row => {
            const courseName = row.getAttribute('data-course_name');
            const abbreviation = row.getAttribute('data-abbreviation');
            const courseType = row.getAttribute('data-course_type');
            const courseCode = row.getAttribute('data-course_code');
            const branches = row.getAttribute('data-branches');

            const matches = courseName.includes(searchValue) || abbreviation.includes(searchValue) || courseType.includes(searchValue) || courseCode.includes(searchValue) || branches.includes(searchValue);

            row.style.display = matches ? '' : 'none';
        });
    });

    // Sorting functionality
    document.querySelectorAll('.sortable').forEach(header => {
        header.addEventListener('click', function() {
            const sortBy = this.getAttribute('data-sort');
            const tbody = document.querySelector('#coursesTable tbody');
            const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
            
            const isAsc = this.classList.contains('sort-asc');

            // Clear all sort indicators
            document.querySelectorAll('.sortable').forEach(h => h.classList.remove('sort-asc', 'sort-desc'));

            this.classList.toggle('sort-asc', !isAsc);
            this.classList.toggle('sort-desc', isAsc);

            rows.sort((a, b) => {
                let aVal = a.getAttribute('data-' + sortBy) || '';
                let bVal = b.getAttribute('data-' + sortBy) || '';

                // If data is numeric convert for correct comparison
                const aNum = parseFloat(aVal);
                const bNum = parseFloat(bVal);
                if(!isNaN(aNum) && !isNaN(bNum)) {
                    aVal = aNum;
                    bVal = bNum;
                }

                if (aVal < bVal) {
                    return isAsc ? 1 : -1;
                }
                if (aVal > bVal) {
                    return isAsc ? -1 : 1;
                }
                return 0;
            });

            rows.forEach(row => tbody.appendChild(row));
        });
    });

    // Export to CSV spreadsheet
    function exportData() {
        const rows = document.querySelectorAll('#coursesTable tbody tr');
        let csvContent = 'Course Name,Abbreviation,Course Type,Course Code,Branches\n';

        rows.forEach(row => {
            if(row.style.display === 'none') return; // Skip hidden rows

            const cells = row.querySelectorAll('td');
            const courseName = cells[0].textContent.trim();
            const abbreviation = cells[1].textContent.trim();
            const courseType = cells[2].textContent.trim();
            const courseCode = cells[3].textContent.trim();
            const branches = cells[4].textContent.trim();

            csvContent += `"${courseName}","${abbreviation}","${courseType}","${courseCode}","${branches}"\n`;
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.setAttribute('href', url);
        link.setAttribute('download', 'courses.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

<style>
    /* Page Container */
    .page-container {
        padding: 2rem;
        min-height: 100vh;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .header-content {
        max-width: 1400px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .page-title {
        font-size: 2.2rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
    }

    .header-actions .btn {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
        padding: 0.8rem 2rem;
        border-radius: 8px;
        font-size: 1rem;
    }

    .header-actions .btn:hover {
        background: rgba(255,255,255,0.2);
        transform: translateY(-2px);
    }

    /* Content Wrapper */
    .content-wrapper {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0;
    }

    /* Table Card */
    .table-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.2);
    }

    /* Search Box */
    .search-box {
        position: relative;
    }

    .search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: #6c757d;
        z-index: 5;
        font-size: 1rem;
    }

    .search-input {
        padding: 0.7rem 1rem 0.7rem 2.5rem;
        border: 2px solid #e9ecef;
        border-radius: 25px;
        width: 300px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: white;
    }

    .search-input:focus {
        border-color: #667eea;
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.95rem;
    }

    .data-table th {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
        padding: 1.2rem 1rem;
        font-weight: 600;
        text-align: left;
        position: sticky;
        top: 0;
        z-index: 10;
        user-select: none;
        cursor: pointer;
    }

    .th-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .sort-icon {
        opacity: 0.5;
        transition: all 0.3s ease;
        font-size: 0.8rem;
    }

    th.sort-asc .sort-icon {
        opacity: 1;
        transform: rotate(180deg);
    }
    th.sort-desc .sort-icon {
        opacity: 1;
        transform: rotate(0deg);
    }

    .data-table td {
        padding: 1rem;
        border-bottom: 1px solid #e9ecef;
        vertical-align: middle;
    }

    .data-table tr:hover {
        background: #f8f9fa;
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    /* Buttons inside table */
    .btn-sm {
        border-radius: 8px;
        padding: 0.3rem 0.6rem;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .btn-warning {
        background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-warning:hover {
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        transform: translateY(-2px);
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        transform: translateY(-2px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 1rem;
        }

        .page-title {
            font-size: 1.8rem;
        }

        .search-box {
            margin: 0 auto;
        }

        .data-table {
            font-size: 0.85rem;
        }

        .data-table th,
        .data-table td {
            padding: 0.8rem 0.5rem;
        }
    }
</style>
@endsection