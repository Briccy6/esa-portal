@extends('admin.layout')

@section('title', 'Edit Announcement')

@section('content')
<div class="container">
    <h2>Edit Announcement</h2>

    <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" id="announcementForm">
        @csrf
        @method('PUT') <!-- Use PUT method for update -->

        <div class="mb-3">
            <label>Title</label>
            <input name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Audience</label>
            <select name="audience" class="form-control" required>
                <option value="all" {{ $announcement->audience === 'all' ? 'selected' : '' }}>All</option>
                <option value="students" {{ $announcement->audience === 'students' ? 'selected' : '' }}>Students</option>
                <option value="teachers" {{ $announcement->audience === 'teachers' ? 'selected' : '' }}>Teachers</option>
                <option value="admin" {{ $announcement->audience === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="public" {{ $announcement->audience === 'public' ? 'selected' : '' }}>Public</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Message Content</label>
            <textarea name="content" id="summernote" class="summernote form-control" required>{{ old('content', $announcement->content) }}</textarea>
        </div>

        <div class="mb-3">
    <label>Attachment (optional)</label>
    <input type="file" name="attachment" class="form-control">

    @if ($announcement->attachment)
        <div class="mt-2">
            <label>Previous Attachment Preview:</label>
            @if (\Illuminate\Support\Str::endsWith($announcement->attachment, ['.jpg', '.jpeg', '.png', '.gif', '.webp']))
                <img src="{{ asset('storage/' . $announcement->attachment) }}" alt="Preview" class="img-fluid mt-2" style="max-height: 200px;">
            @elseif (\Illuminate\Support\Str::endsWith($announcement->attachment, ['.pdf']))
                <iframe src="{{ asset('storage/' . $announcement->attachment) }}" width="100%" height="400px" class="mt-2"></iframe>
            @else
                <p class="text-muted mt-2">Preview not supported for this file type.</p>
                <a href="{{ asset('storage/' . $announcement->attachment) }}" target="_blank" class="btn btn-sm btn-secondary">Download File</a>
            @endif
        </div>
    @endif
</div>


        <div class="mb-3">
            <label>Sender Name</label>
            <input type="text" name="sender" class="form-control" value="{{ old('sender', $announcement->sender) }}">
        </div>

        <button class="btn btn-primary">Update Announcement</button>
    </form>
</div>

<!-- Summernote (Bootstrap 5 build) -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs5.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('summernote');
    if (!el || !window.$ || !$.fn || !$.fn.summernote) return;

    $('#summernote').summernote({
        placeholder: 'Write the announcement here...',
        tabsize: 2,
        height: 280,
        minHeight: 220,
        maxHeight: 600,
        focus: false,
        toolbar: [
            ['style', ['style', 'strikethrough', 'superscript', 'subscript']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph', 'hr']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview', 'help']]
        ]
    });

    const form = document.getElementById('announcementForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const html = $('#summernote').summernote('code');
            if (!html || html.trim() === '' || html.trim() === '<p><br></p>') {
                e.preventDefault();
                alert('Please enter announcement content.');
                $('#summernote').summernote('focus');
            }
        });
    }
});
</script>
@endsection
