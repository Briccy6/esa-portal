@extends('admin.layout')

@section('title', 'Create Announcement')

@section('content')
<div class="container">
    <h2>Create Announcement</h2>

    <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
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
            <textarea name="content" rows="4" class="form-control" required></textarea>
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
@endsection
