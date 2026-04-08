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
            <textarea name="content" id="content" class="form-control" required></textarea>
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

<!-- CKEditor 5 (Modern WYSIWYG) -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('content');
    if (!el || typeof ClassicEditor === 'undefined') return;

    let editorInstance = null;
    ClassicEditor
        .create(el, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold', 'italic', 'underline', 'strikethrough',
                    '|',
                    'bulletedList', 'numberedList',
                    '|',
                    'link', 'blockQuote', 'insertTable',
                    '|',
                    'undo', 'redo'
                ]
            },
            table: {
                contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
            }
        })
        .then(function (editor) {
            editorInstance = editor;
        })
        .catch(function (error) {
            console.error('CKEditor init failed:', error);
        });

    const form = document.getElementById('announcementForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const html = editorInstance ? editorInstance.getData() : (el.value || '');
            const cleaned = (html || '').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
            if (!cleaned) {
                e.preventDefault();
                alert('Please enter announcement content.');
                if (editorInstance) editorInstance.editing.view.focus();
                else el.focus();
            }
        });
    }
});
</script>
@endsection
