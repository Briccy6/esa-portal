@extends('admin.layout')

@section('title', 'Edit Announcement')

@section('content')
<div class="container">
    <h2>Edit Announcement</h2>

    <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data" id="announcementForm" novalidate>
        @csrf
        @method('PUT') <!-- Use PUT method for update -->

        <div class="mb-3">
            <label>Title</label>
            <input name="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Audience</label>
            <select name="audience" class="form-control" required>
                <option value="public" {{ $announcement->audience === 'public' ? 'selected' : '' }}>All</option>
                <option value="students" {{ $announcement->audience === 'students' ? 'selected' : '' }}>Students</option>
                <option value="staff" {{ $announcement->audience === 'staff' ? 'selected' : '' }}>Staff</option>
                <option value="public" {{ $announcement->audience === 'public' ? 'selected' : '' }}>Public (Website)</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Message Content</label>
            <textarea name="content" id="content" class="form-control">{{ old('content', $announcement->content) }}</textarea>
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

<!-- CKEditor 5 (Modern WYSIWYG) -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('content');
    if (!el || typeof ClassicEditor === 'undefined') return;

    let editorInstance = null;
    let editorReady = false;
    ClassicEditor
        .create(el, {
            toolbar: {
                items: [
                    'heading',
                    '|',
                    'bold', 'italic',
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
            editorReady = true;
        })
        .catch(function (error) {
            console.error('CKEditor init failed:', error);
        });

    const form = document.getElementById('announcementForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            if (!editorReady) {
                e.preventDefault();
                alert('Editor is still loading. Please wait 1–2 seconds and try again.');
                return;
            }

            const html = editorInstance ? editorInstance.getData() : (el.value || '');
            const cleaned = (html || '').replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim();
            if (!cleaned) {
                e.preventDefault();
                alert('Please enter announcement content.');
                if (editorInstance) editorInstance.editing.view.focus();
                else el.focus();
                return;
            }

            // Ensure the HTML content is submitted with the form
            el.value = html;
        });
    }
});
</script>
@endsection
