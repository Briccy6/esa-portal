@extends('teacher.layout')

@section('content')
<style>
    .resources-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .content-wrapper {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        overflow: hidden;
        margin: 0 1rem;
    }
    
    .header-section {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }
    
    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.1);
        border-radius: 50%;
        transform: translate(50px, -50px);
    }
    
    .header-section h2 {
        margin: 0;
        font-size: 2.5rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .create-btn {
        background: linear-gradient(45deg, #FF6B6B, #FF8E53);
        border: none;
        padding: 12px 24px;
        border-radius: 50px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(255,107,107,0.4);
    }
    
    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255,107,107,0.6);
        color: white;
        text-decoration: none;
    }
    
    .table-section {
        padding: 2rem;
    }
    
    .search-container {
        position: relative;
        margin-bottom: 2rem;
    }
    
    .search-input {
        width: 100%;
        padding: 15px 20px 15px 50px;
        border: 2px solid #e1e8ed;
        border-radius: 50px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
    }
    
    .search-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: white;
    }
    
    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1.2rem;
    }
    
    .alert-success {
        background: linear-gradient(45deg, #48bb78, #38a169);
        border: none;
        border-radius: 15px;
        color: white;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
    }
    
    .table-responsive {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    
    .modern-table {
        margin: 0;
        background: white;
    }
    
    .modern-table thead {
        background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .modern-table thead th {
        color: #111 !important; /* Black */
        font-weight: bold !important;
        padding: 1.2rem 1rem;
        border: none;
        font-size: 0.9rem;
        letter-spacing: 0.3px;
        position: relative;
        transition: all 0.3s ease;
        white-space: nowrap;
        text-align: center;
        background: #fff !important; /* White background */
    }
    
    .modern-table thead th[data-sort] {
        cursor: pointer;
        user-select: none;
    }
    
    .modern-table thead th[data-sort]:hover {
        background: rgba(255,255,255,0.1);
    }
    
    .modern-table tbody tr {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .modern-table tbody tr:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        transform: scale(1.01);
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }
    
    .modern-table tbody td {
        padding: 1.5rem 1rem;
        vertical-align: middle;
        border: none;
        position: relative;
    }
    
    .media-container {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .media-container:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .media-container video,
    .media-container iframe {
        border-radius: 10px;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
        margin: 0 2px;
    }
    
    .btn-warning {
        background: linear-gradient(45deg, #ed8936, #f6ad55);
        color: white;
    }
    
    .btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(237, 137, 54, 0.4);
        color: white;
    }
    
    .btn-danger {
        background: linear-gradient(45deg, #e53e3e, #fc8181);
        color: white;
    }
    
    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(229, 62, 62, 0.4);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem;
        color: #718096;
        font-size: 1.1rem;
    }
    
    .empty-state::before {
        content: "📚";
        display: block;
        font-size: 4rem;
        margin-bottom: 1rem;
    }
    
    .pagination {
        justify-content: center;
        margin-top: 2rem;
    }
    
    .page-link {
        border: none;
        padding: 10px 16px;
        margin: 0 2px;
        border-radius: 25px;
        transition: all 0.3s ease;
        background: #f8fafc;
        color: #4a5568;
    }
    
    .page-link:hover,
    .page-item.active .page-link {
        background: linear-gradient(45deg, #667eea, #764ba2);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    /* Mobile Responsiveness */
    @media (max-width: 768px) {
        .header-section {
            padding: 1.5rem;
            text-align: center;
        }
        
        .header-section h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
        }
        
        .table-section {
            padding: 1rem;
        }
        
        .modern-table {
            font-size: 0.875rem;
        }
        
        .modern-table thead th,
        .modern-table tbody td {
            padding: 1rem 0.5rem;
        }
        
        .media-container video,
        .media-container iframe {
            width: 150px !important;
            height: 100px !important;
        }
        
        .btn-action {
            padding: 6px 12px;
            font-size: 0.75rem;
            margin: 2px 0;
            display: block;
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .content-wrapper {
            margin: 0 0.5rem;
            border-radius: 15px;
        }
        
        .modern-table thead {
            display: none;
        }
        
        .modern-table tbody tr {
            display: block;
            margin-bottom: 1rem;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        
        .modern-table tbody td {
            display: block;
            text-align: right;
            border-bottom: 1px solid #e2e8f0;
            position: relative;
            padding-left: 50%;
        }
        
        .modern-table tbody td::before {
            content: attr(data-label);
            position: absolute;
            left: 1rem;
            top: 1.5rem;
            font-weight: 600;
            color: #4a5568;
        }
        
        .modern-table tbody td:last-child {
            border-bottom: none;
        }
    }
</style>
@php
    if (!function_exists('getYoutubeId')) {
        function getYoutubeId($url) {
            if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?|watch(?:_popup)?)(?:\.php)?(?:\?|\#)v=|embed\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/', $url, $matches)) {
                return $matches[1];
            }
            return null;
        }
    }
@endphp
<div class="resources-container">
    <div class="container">
        <div class="content-wrapper">
            <div class="header-section">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <h2>My Uploaded Resources</h2>
                    <a href="{{ route('teacher.resources.create') }}" class="create-btn">
                        ✨ Create New Resource
                    </a>
                </div>
            </div>
            
            <div class="table-section">
                @if(session('success'))
                    <div class="alert alert-success">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                <div class="search-container">
                    <div class="search-icon">🔍</div>
                    <input type="text" id="searchInput" class="search-input" placeholder="Search resources by course, lesson, or date...">
                </div>

                

                <div class="table-responsive">
                    <table class="table modern-table" id="resourcesTable">
                        <thead>
                            <tr>
                                <th data-sort="course"><i class="fas fa-graduation-cap"></i> Course</th>
                                <th data-sort="lesson"><i class="fas fa-book-open"></i> Lesson</th>
                                <th><i class="fas fa-video"></i> Media</th>
                                <th><i class="fas fa-file-alt"></i> Document</th>
                                <th data-sort="created"><i class="fas fa-calendar-alt"></i> Created</th>
                                <th><i class="fas fa-cogs"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($resources as $resource)
                                <tr>
                                    <td class="course" data-label="Course">{{ $resource->course->course_name ?? '-' }}</td>
                                    <td class="lesson" data-label="Lesson">{{ $resource->lesson->title ?? '-' }}</td>
                                    <td data-label="Video">
                                        @php
                                            $videoId = getYoutubeId($resource->youtube_link);
                                        @endphp
                                        @if($videoId)
                                            <div class="media-container">
                                                <iframe width="200" height="113"
                                                    src="https://www.youtube.com/embed/{{ $videoId }}"
                                                    frameborder="0"
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                                    allowfullscreen>
                                                </iframe>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td data-label="Document">
                                        @if($resource->document_name)
                                            <a href="{{ route('teacher.resources.viewDocument', $resource->id) }}" target="_blank" class="btn btn-sm btn-info">View Document</a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="created" data-label="Created">{{ $resource->created_at->format('Y-m-d') }}</td>
                                    <td data-label="Actions">
                                        <a href="{{ route('teacher.resources.edit', $resource->id) }}" class="btn btn-action btn-warning">✏️ Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        No resources found. Start by creating your first resource!
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center">
                    {{ $resources->links() }}
                </div>
            </div>
        </div>
    </div>
</div>



<script>
    // Live Search with enhanced functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('#resourcesTable tbody tr');
        let visibleRows = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(filter);
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleRows++;
        });
        
        // Show/hide empty state message
        const emptyRow = document.querySelector('.empty-state');
        if (emptyRow && visibleRows === 0 && filter.length > 0) {
            emptyRow.style.display = '';
            emptyRow.innerHTML = `No resources found matching "${this.value}". Try adjusting your search terms.`;
        }
    });

    // Enhanced Sort Functionality
    const getCellValue = (tr, className) => {
        const cell = tr.querySelector('.' + className);
        return cell ? cell.textContent.trim() : '';
    };

    const comparer = (className, asc) => (a, b) => {
        let v1 = getCellValue(a, className);
        let v2 = getCellValue(b, className);

        // For date, parse to timestamp
        if(className === 'created') {
            v1 = Date.parse(v1);
            v2 = Date.parse(v2);
        }

        return (v1 > v2 ? 1 : v1 < v2 ? -1 : 0) * (asc ? 1 : -1);
    };

    document.querySelectorAll('#resourcesTable thead th[data-sort]').forEach(th => {
        let asc = true;
        th.style.cursor = 'pointer';
        th.addEventListener('click', () => {
            // Remove sorting indicators from other columns
            document.querySelectorAll('#resourcesTable thead th[data-sort]').forEach(header => {
                header.style.background = '';
            });
            
            // Add visual feedback for current sorted column
            th.style.background = 'rgba(255,255,255,0.2)';
            
            const tbody = th.closest('table').querySelector('tbody');
            Array.from(tbody.querySelectorAll('tr'))
                .sort(comparer(th.getAttribute('data-sort'), asc))
                .forEach(tr => tbody.appendChild(tr));
            asc = !asc;
        });
    });

    // Add loading animation for media elements
    document.querySelectorAll('iframe, video').forEach(media => {
        media.addEventListener('load', function() {
            this.style.opacity = '1';
        });
        media.style.opacity = '0.7';
        media.style.transition = 'opacity 0.3s ease';
    });
</script>
@endsection