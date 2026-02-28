@extends('admin.layout')
@section('title', 'View Message')

@section('content')
<div class="container">
    <h2>{{ $message->title }}</h2>
    <p><strong>Recipient:</strong> {{ ucfirst($message->recipient_type) }}</p>
    <p><strong>Sent At:</strong> {{ $message->created_at->format('Y-m-d H:i') }}</p>
    <hr>
    <p>{{ $message->content }}</p>

    @if($message->file_path)
        <div class="mt-3">
            <a href="{{ asset('storage/' . $message->file_path) }}" target="_blank" class="btn btn-outline-primary">View Attached File</a>
        </div>
    @endif

    <a href="{{ route('admin.messages.index') }}" class="btn btn-secondary mt-4">Back</a>
</div>
@endsection
