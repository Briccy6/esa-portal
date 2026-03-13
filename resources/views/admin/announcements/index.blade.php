@extends('admin.layout')

@section('title', 'Announcements')

@section('content')
<div class="container">
    <h2>Announcements</h2>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary mb-3">Create Announcement</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Audience</th>
                <th>Sender</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($announcements as $announcement)
            <tr>
                <td>{{ $announcement->title }}</td>
                <td>{{ ucfirst($announcement->audience) }}</td>
                <td>{{ $announcement->sender }}</td>
                <td>
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button onclick="return confirm('Are you sure?')" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endsection
