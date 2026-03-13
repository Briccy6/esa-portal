@extends('admin.layout')

@section('title', 'Chat Test')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Community Spaces Chat Test
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Test Instructions:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Look for the floating WhatsApp-style button in the bottom-right corner</li>
                            <li>Click on it to open the chat modal</li>
                            <li>Select a space from the sidebar to start chatting</li>
                            <li>Try sending messages and files</li>
                            <li>Messages should appear with sender names</li>
                            <li>Chat should update in real-time without refreshing</li>
                        </ul>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h5>Features to Test:</h5>
                            <ul>
                                <li>✅ WhatsApp-style floating button</li>
                                <li>✅ Real-time messaging</li>
                                <li>✅ File attachments (images, documents, audio, video)</li>
                                <li>✅ Sender names displayed</li>
                                <li>✅ Unread message badges</li>
                                <li>✅ Access control based on user type</li>
                                <li>✅ Mobile responsive design</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Access Control:</h5>
                            <ul>
                                <li><strong>Admin:</strong> Access to all spaces</li>
                                <li><strong>Staff:</strong> Access to all spaces</li>
                                <li><strong>Teachers:</strong> Access only to assigned spaces</li>
                                <li><strong>Students:</strong> Access based on course enrollment and fee payment</li>
                            </ul>
                        </div>
                    </div>

                    <hr>

                    <div class="text-center">
                        <h5>Current User Information</h5>
                        <p><strong>Type:</strong> Admin</p>
                        <p><strong>ID:</strong> {{ auth('admin')->id() }}</p>
                        <p><strong>Email:</strong> {{ auth('admin')->user()->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 