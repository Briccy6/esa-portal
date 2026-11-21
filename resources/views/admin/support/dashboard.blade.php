@extends('admin.layout')

@section('title', 'ESA Live Support')

@push('styles')
<style>
.support-dashboard {
    padding: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.stat-card h3 {
    font-size: 0.875rem;
    color: #647168;
    margin-bottom: 0.5rem;
}

.stat-card .value {
    font-size: 2rem;
    font-weight: 700;
    color: #202b24;
}

.conversations-layout {
    display: grid;
    grid-template-columns: 350px 1fr 300px;
    gap: 1rem;
    height: calc(100vh - 200px);
}

.conversations-list {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.conversations-header {
    padding: 1rem;
    border-bottom: 1px solid #dfe3d8;
}

.conversations-header h4 {
    margin: 0;
    font-size: 1rem;
}

.conversations-body {
    flex: 1;
    overflow-y: auto;
}

.conversation-item {
    padding: 1rem;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    transition: background 0.2s;
}

.conversation-item:hover {
    background: #f5f5f5;
}

.conversation-item.active {
    background: #e8f5e9;
    border-left: 3px solid #477a57;
}

.conversation-item .user-name {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.conversation-item .last-message {
    font-size: 0.875rem;
    color: #647168;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.conversation-item .meta {
    font-size: 0.75rem;
    color: #999;
    margin-top: 0.25rem;
}

.conversation-view {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.conversation-view-header {
    padding: 1rem;
    border-bottom: 1px solid #dfe3d8;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.conversation-view-body {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.message {
    margin-bottom: 1rem;
    max-width: 70%;
}

.message.user {
    margin-left: auto;
}

.message.ai {
    margin-right: auto;
}

.message.agent {
    margin-left: auto;
    background: #e3f2fd;
}

.message-content {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    background: #f5f5f5;
}

.message.user .message-content {
    background: #477a57;
    color: white;
}

.message .sender {
    font-size: 0.75rem;
    color: #999;
    margin-bottom: 0.25rem;
}

.message .time {
    font-size: 0.75rem;
    color: #999;
    margin-top: 0.25rem;
}

.conversation-view-footer {
    padding: 1rem;
    border-top: 1px solid #dfe3d8;
}

.conversation-view-footer textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #dfe3d8;
    border-radius: 0.5rem;
    resize: none;
    height: 80px;
}

.user-info-panel {
    background: white;
    border-radius: 0.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 1rem;
    overflow-y: auto;
}

.user-info-panel h4 {
    margin-bottom: 1rem;
}

.user-info-item {
    margin-bottom: 0.75rem;
}

.user-info-item label {
    font-size: 0.75rem;
    color: #647168;
    display: block;
}

.user-info-item .value {
    font-weight: 500;
}

.status-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.status-open { background: #e3f2fd; color: #1976d2; }
.status-pending { background: #fff3e0; color: #f57c00; }
.status-in_progress { background: #e8f5e9; color: #388e3c; }
.status-resolved { background: #f3e5f5; color: #7b1fa2; }
.status-closed { background: #eceff1; color: #455a64; }

@media (max-width: 1200px) {
    .conversations-layout {
        grid-template-columns: 300px 1fr;
    }
    .user-info-panel {
        display: none;
    }
}

@media (max-width: 768px) {
    .conversations-layout {
        grid-template-columns: 1fr;
    }
    .conversations-list {
        display: none;
    }
    .conversations-list.show {
        display: flex;
    }
}
</style>
@endpush

@section('content')
<div class="support-dashboard">
    <h2>ESA Live Support</h2>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <h3>Total Conversations</h3>
            <div class="value">{{ $stats['total'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Open</h3>
            <div class="value">{{ $stats['open'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Pending</h3>
            <div class="value">{{ $stats['pending'] }}</div>
        </div>
        <div class="stat-card">
            <h3>In Progress</h3>
            <div class="value">{{ $stats['in_progress'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Resolved</h3>
            <div class="value">{{ $stats['resolved'] }}</div>
        </div>
        <div class="stat-card">
            <h3>AI Handled</h3>
            <div class="value">{{ $stats['ai_handled'] }}</div>
        </div>
        <div class="stat-card">
            <h3>Human Handled</h3>
            <div class="value">{{ $stats['human_handled'] }}</div>
        </div>
    </div>

    <!-- Conversations Layout -->
    <div class="conversations-layout">
        <!-- Left Panel: Conversations List -->
        <div class="conversations-list">
            <div class="conversations-header">
                <h4>Conversations</h4>
                <div style="margin-top: 0.5rem;">
                    <select id="filter-status" class="form-select form-select-sm">
                        <option value="all">All Status</option>
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                    </select>
                </div>
            </div>
            <div class="conversations-body" id="conversations-list">
                <p class="text-center text-muted py-4">Loading conversations...</p>
            </div>
        </div>

        <!-- Middle Panel: Conversation View -->
        <div class="conversation-view">
            <div class="conversation-view-header">
                <div>
                    <h4 id="conversation-title">Select a conversation</h4>
                    <span id="conversation-status" class="status-badge status-open"></span>
                </div>
                <div>
                    <button id="btn-assign" class="btn btn-sm btn-primary" style="display: none;">Assign to Me</button>
                    <button id="btn-return-ai" class="btn btn-sm btn-secondary" style="display: none;">Return to AI</button>
                </div>
            </div>
            <div class="conversation-view-body" id="conversation-messages">
                <p class="text-center text-muted py-4">Select a conversation to view messages</p>
            </div>
            <div class="conversation-view-footer" id="reply-section" style="display: none;">
                <textarea id="reply-message" placeholder="Type your reply..."></textarea>
                <div style="margin-top: 0.5rem; display: flex; justify-content: space-between;">
                    <button id="btn-send-reply" class="btn btn-primary">Send Reply</button>
                    <select id="status-select" class="form-select form-select-sm" style="width: auto;">
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Right Panel: User Info -->
        <div class="user-info-panel" id="user-info-panel">
            <h4>User Information</h4>
            <div id="user-info-content">
                <p class="text-muted">Select a conversation to view user details</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentConversationId = null;
const conversationUrl = '{{ route("admin.support.conversation", ['id' => '__ID__']) }}';
const replyUrl = '{{ route("admin.support.reply", ['id' => '__ID__']) }}';
const statusUrl = '{{ route("admin.support.status", ['id' => '__ID__']) }}';
const assignUrl = '{{ route("admin.support.assign", ['id' => '__ID__']) }}';
const returnToAiUrl = '{{ route("admin.support.return-to-ai", ['id' => '__ID__']) }}';

// Load conversations on page load
$(document).ready(function() {
    loadConversations();
});

// Load conversations
function loadConversations() {
    const status = $('#filter-status').val();
    $.get('{{ route("admin.support.conversations") }}', { status: status }, function(data) {
        let html = '';
        data.data.forEach(conv => {
            const statusClass = `status-${conv.status}`;
            const userName = conv.user_details?.name || 'Unknown';
            const lastMessage = conv.latest_message?.message_content || 'No messages';
            const time = new Date(conv.last_message_at).toLocaleString();

            html += `
                <div class="conversation-item" data-id="${conv.id}">
                    <div class="user-name">${userName}</div>
                    <div class="last-message">${lastMessage}</div>
                    <div class="meta">
                        <span class="status-badge ${statusClass}">${conv.status}</span>
                        <span> • ${time}</span>
                    </div>
                </div>
            `;
        });
        $('#conversations-list').html(html || '<p class="text-center text-muted py-4">No conversations found</p>');

        // Add click handlers
        $('.conversation-item').click(function() {
            const id = $(this).data('id');
            loadConversation(id);
            $('.conversation-item').removeClass('active');
            $(this).addClass('active');
        });
    });
}

// Load single conversation
function loadConversation(id) {
    currentConversationId = id;
    $.get(conversationUrl.replace('__ID__', id), function(data) {
        // Update header
        const userName = data.user_details?.name || 'Unknown';
        $('#conversation-title').text(userName);
        $('#conversation-status').text(data.status).removeClass().addClass(`status-badge status-${data.status}`);

        // Show action buttons
        $('#btn-assign').show();
        $('#btn-return-ai').show();
        $('#reply-section').show();

        // Update status select
        $('#status-select').val(data.status);

        // Load messages
        let messagesHtml = '';
        data.messages.forEach(msg => {
            const roleClass = msg.sender_role === 'user' ? 'user' : (msg.sender_role === 'ai' ? 'ai' : 'agent');
            const senderName = msg.sender_role === 'ai' ? 'ESA AI' : (msg.sender_role === 'agent' ? 'Agent' : 'User');
            const time = new Date(msg.created_at).toLocaleString();

            messagesHtml += `
                <div class="message ${roleClass}">
                    <div class="sender">${senderName}</div>
                    <div class="message-content">${msg.message_content}</div>
                    <div class="time">${time}</div>
                </div>
            `;
        });
        $('#conversation-messages').html(messagesHtml);

        // Load user info
        let userInfoHtml = '';
        if (data.user_details) {
            for (const [key, value] of Object.entries(data.user_details)) {
                if (value) {
                    userInfoHtml += `
                        <div class="user-info-item">
                            <label>${key.replace('_', ' ').toUpperCase()}</label>
                            <div class="value">${value}</div>
                        </div>
                    `;
                }
            }
        }
        $('#user-info-content').html(userInfoHtml || '<p class="text-muted">No user information available</p>');
    });
}

// Send reply
$('#btn-send-reply').click(function() {
    const message = $('#reply-message').val().trim();
    if (!message || !currentConversationId) return;

    $.post(replyUrl.replace('__ID__', currentConversationId), {
        message: message,
        _token: '{{ csrf_token() }}'
    }, function(data) {
        $('#reply-message').val('');
        loadConversation(currentConversationId);
    });
});

// Update status
$('#status-select').change(function() {
    if (!currentConversationId) return;

    $.ajax({
        url: statusUrl.replace('__ID__', currentConversationId),
        method: 'PUT',
        data: {
            status: $(this).val(),
            _token: '{{ csrf_token() }}'
        },
        success: function() {
            loadConversation(currentConversationId);
            loadConversations();
        }
    });
});

// Assign to current user
$('#btn-assign').click(function() {
    if (!currentConversationId) return;

    $.post(assignUrl.replace('__ID__', currentConversationId), {
        agent_id: {{ auth()->guard('admin')->check() ? auth()->guard('admin')->id() : 'null' }},
        agent_type: 'admin',
        _token: '{{ csrf_token() }}'
    }, function() {
        loadConversation(currentConversationId);
        loadConversations();
    });
});

// Return to AI
$('#btn-return-ai').click(function() {
    if (!currentConversationId) return;

    $.post(returnToAiUrl.replace('__ID__', currentConversationId), {
        _token: '{{ csrf_token() }}'
    }, function() {
        loadConversation(currentConversationId);
        loadConversations();
    });
});

// Filter change
$('#filter-status').change(function() {
    loadConversations();
});
</script>
@endpush
