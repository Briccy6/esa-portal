@extends('admin.layout')

@section('title', 'Create Announcement')

@section('content')
<div class="container">
    <h2>Create Announcement</h2>

    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data" id="announcementForm">
        @csrf
        <div class="mb-3">
            <label>Title</label>
            <input name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Audience</label>
            <select name="audience" class="form-control" required>
                <option value="all">All</option>
                <option value="students">Students</option>
                <option value="teachers">Teachers</option>
                <option value="admin">Admin</option>
                <option value="public">Public</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Message Content</label>
            <textarea name="content" id="summernote" class="summernote form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Attachment (optional)</label>
            <input type="file" name="attachment" class="form-control">
        </div>

        <div class="mb-3">
            <label>Sender Name</label>
            <input type="text" name="sender" class="form-control">
        </div>

        <button class="btn btn-success">Send Announcement</button>
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
