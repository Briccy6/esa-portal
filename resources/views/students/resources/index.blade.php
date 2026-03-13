@extends('students.app')

@section('content')
@php
    use Illuminate\Support\Str;
    $student = Auth::guard('student')->user();
@endphp

<div class="container mt-4">
    <h2 class="mb-4"><i class="bi bi-file-earmark-text"></i> Study References</h2>

    @if($resources->isEmpty())
        <div class="alert alert-info">
            @if(is_null($student))
                You are not logged in as a student. Please log in to view your study resources.
            @else
                No study resources available for your courses at the moment.
            @endif
        </div>
    @else
        {{-- Search and Sort Controls --}}
        <div class="mb-3 d-flex flex-wrap align-items-center gap-2">
            <input 
                type="text" 
                id="resource-search" 
                class="form-control" 
                placeholder="Search resources..." 
                style="max-width: 250px;"
            >
            <select id="resource-sort" class="form-select" style="max-width: 180px;">
                <option value="newest">Sort: Newest First</option>
                <option value="oldest">Sort: Oldest First</option>
                <option value="title_asc">Sort: Title A-Z</option>
                <option value="title_desc">Sort: Title Z-A</option>
            </select>
        </div>

        {{-- Resources Table --}}
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="resources-table">
                <thead class="table-dark">
                    <tr>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Description</th>
                        <th>Uploaded</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($resources as $resource)
                        @php
                            $enrolled = $student && $student->courses && $student->courses->contains($resource->course_id);
                            $resourceTitle = $resource->title ?? $resource->lesson->title ?? 'Untitled';
                        @endphp
                        @if($enrolled)
                            <tr>
                                <td class="resource-title">{{ $resourceTitle }}</td>
                                <td class="resource-course">{{ $resource->course->course_name ?? 'N/A' }}</td>
                                <td class="resource-description">
                                    {{ Str::limit($resource->description ?? '', 80) }}
                                </td>
                                <td class="resource-uploaded">{{ $resource->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('student.resources.show', $resource->id) }}" 
                                       class="btn btn-primary btn-sm" target="_blank" rel="noopener">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                    @if($resource->document_path)
                                        <a href="{{ asset('storage/' . $resource->document_path) }}" 
                                           class="btn btn-outline-secondary btn-sm ms-1" download>
                                            <i class="bi bi-download"></i> Download
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('resource-search');
    const sortSelect = document.getElementById('resource-sort');
    const table = document.getElementById('resources-table');
    const tbody = table.querySelector('tbody');

    function getRowData(row) {
        return {
            title: row.querySelector('.resource-title')?.innerText.toLowerCase() || '',
            course: row.querySelector('.resource-course')?.innerText.toLowerCase() || '',
            uploaded: row.querySelector('.resource-uploaded')?.innerText || '',
            row: row
        };
    }

    function filterAndSort() {
        const query = searchInput.value.trim().toLowerCase();
        const sort = sortSelect.value;
        const rows = Array.from(tbody.querySelectorAll('tr'));

        // Filter rows
        rows.forEach(row => {
            const data = getRowData(row);
            const visible = data.title.includes(query) || data.course.includes(query);
            row.style.display = visible ? '' : 'none';
        });

        // Sort visible rows
        let visibleRows = rows.filter(row => row.style.display !== 'none');

        visibleRows.sort((a, b) => {
            const aData = getRowData(a);
            const bData = getRowData(b);

            if (sort === 'newest' || sort === 'oldest') {
                const dateA = new Date(aData.uploaded);
                const dateB = new Date(bData.uploaded);
                return sort === 'newest' ? dateB - dateA : dateA - dateB;
            } else if (sort === 'title_asc') {
                return aData.title.localeCompare(bData.title);
            } else if (sort === 'title_desc') {
                return bData.title.localeCompare(aData.title);
            }
            return 0;
        });

        // Append rows back to tbody in new order
        visibleRows.forEach(row => tbody.appendChild(row));
    }

    searchInput.addEventListener('input', filterAndSort);
    sortSelect.addEventListener('change', filterAndSort);
});
</script>
@endsection
