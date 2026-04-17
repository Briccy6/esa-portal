@extends('admin.layout')
@section('title', 'Send Message')

@section('content')
<div class="container">
    <h2>Send a Message</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.messages.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Recipient</label>
            <select name="recipient_type" class="form-select" required>
                <option value="students">Students</option>
                <option value="staff">Staff</option>
                <option value="public">Public</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Content</label>
            <textarea name="content" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Attach a file (optional)</label>
            <input type="file" name="file" class="form-control">
        </div>

        <button class="btn btn-primary">Send Message</button>
    </form>
</div>
@endsection
