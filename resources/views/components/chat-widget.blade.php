<div id="esa-chat-widget">
    <!-- Chat Launcher Button -->
    <button id="chat-launcher" class="chat-launcher">
        <i class="fas fa-comments"></i>
        <span class="badge" id="unread-badge" style="display: none;">0</span>
    </button>

    <!-- Chat Window -->
    <div id="chat-window" class="chat-window" style="display: none;">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-left">
                <div class="chat-avatar">
                    <i class="fas fa-robot"></i>
                </div>
                <div class="chat-title">
                    <h4>ESA AI Assistant</h4>
                    <span class="status online">Online</span>
                </div>
            </div>
            <div class="chat-header-right">
                <button id="btn-minimize" class="chat-btn"><i class="fas fa-minus"></i></button>
                <button id="btn-close" class="chat-btn"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="chat-messages">
            <div class="chat-loading">
                <div class="spinner"></div>
                <p>Loading conversation...</p>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input">
            <textarea id="message-input" placeholder="Type your message..." rows="1"></textarea>
            <div class="chat-input-actions">
                <button id="btn-request-human" class="btn-human" title="Request human support">
                    <i class="fas fa-headset"></i>
                </button>
                <button id="btn-send" class="btn-send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#esa-chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Chat Launcher */
.chat-launcher {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #477a57 0%, #356346 100%);
    color: white;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(71, 122, 87, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    transition: all 0.3s ease;
}

.chat-launcher:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 16px rgba(71, 122, 87, 0.5);
}

.chat-launcher .badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #e74c3c;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* Chat Window */
.chat-window {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 380px;
    height: 550px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s ease;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Chat Header */
.chat-header {
    background: linear-gradient(135deg, #477a57 0%, #356346 100%);
    color: white;
    padding: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.chat-avatar {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
}

.chat-title h4 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.chat-title .status {
    font-size: 12px;
    opacity: 0.9;
}

.chat-header-right {
    display: flex;
    gap: 8px;
}

.chat-btn {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.chat-btn:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Chat Messages */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    background: #f8f9fa;
}

.chat-message {
    margin-bottom: 16px;
    max-width: 80%;
}

.chat-message.user {
    margin-left: auto;
}

.chat-message.ai {
    margin-right: auto;
}

.chat-message.system {
    text-align: center;
    max-width: 100%;
}

.chat-message .message-bubble {
    padding: 12px 16px;
    border-radius: 16px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.chat-message.user .message-bubble {
    background: #477a57;
    color: white;
    border-bottom-right-radius: 4px;
}

.chat-message.ai .message-bubble {
    background: white;
    color: #202b24;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.chat-message.system .message-bubble {
    background: rgba(0, 0, 0, 0.05);
    color: #647168;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
}

.chat-message .message-time {
    font-size: 11px;
    color: #999;
    margin-top: 4px;
}

.chat-message.user .message-time {
    text-align: right;
}

/* Typing Indicator */
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
}

.typing-indicator .dot {
    width: 8px;
    height: 8px;
    background: #999;
    border-radius: 50%;
    animation: typing 1.4s infinite;
}

.typing-indicator .dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator .dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
    }
    30% {
        transform: translateY(-4px);
    }
}

/* Chat Input */
.chat-input {
    padding: 12px 16px;
    background: white;
    border-top: 1px solid #e5e5e5;
}

.chat-input textarea {
    width: 100%;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    resize: none;
    outline: none;
    transition: border-color 0.2s;
}

.chat-input textarea:focus {
    border-color: #477a57;
}

.chat-input-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.btn-human {
    background: transparent;
    border: 1px solid #477a57;
    color: #477a57;
    padding: 8px 16px;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
}

.btn-human:hover {
    background: #477a57;
    color: white;
}

.btn-send {
    background: #477a57;
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}

.btn-send:hover {
    background: #356346;
}

.btn-send:disabled {
    background: #ccc;
    cursor: not-allowed;
}

/* Loading State */
.chat-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #647168;
}

.spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #477a57;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 12px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Mobile Responsive */
@media (max-width: 480px) {
    .chat-window {
        width: calc(100vw - 40px);
        height: calc(100vh - 120px);
        bottom: 80px;
        right: 20px;
    }

    .chat-window.minimized {
        height: auto;
    }
}
</style>

<script>
let currentConversationId = null;
let isChatOpen = false;

$(document).ready(function() {
    // Toggle chat window
    $('#chat-launcher').click(function() {
        toggleChat();
    });

    $('#btn-minimize').click(function() {
        minimizeChat();
    });

    $('#btn-close').click(function() {
        closeChat();
    });

    // Send message
    $('#btn-send').click(sendMessage);
    $('#message-input').keypress(function(e) {
        if (e.which === 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Request human support
    $('#btn-request-human').click(requestHumanSupport);
});

function toggleChat() {
    isChatOpen = !isChatOpen;
    if (isChatOpen) {
        $('#chat-window').show();
        loadConversation();
    } else {
        $('#chat-window').hide();
    }
}

function minimizeChat() {
    $('#chat-window').hide();
    isChatOpen = false;
}

function closeChat() {
    $('#chat-window').hide();
    isChatOpen = false;
}

function loadConversation() {
    $('#chat-messages').html('<div class="chat-loading"><div class="spinner"></div><p>Loading conversation...</p></div>');

    $.get('{{ route("api.chat.conversation") }}', function(data) {
        if (data.conversation) {
            currentConversationId = data.conversation.id;
            displayMessages(data.conversation.messages);
        }
    }).fail(function() {
        $('#chat-messages').html('<div class="chat-message system"><div class="message-bubble">Unable to load conversation. Please try again.</div></div>');
    });
}

function displayMessages(messages) {
    let html = '';
    messages.forEach(msg => {
        const roleClass = msg.sender_role === 'user' ? 'user' : (msg.sender_role === 'ai' ? 'ai' : 'system');
        const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

        html += `
            <div class="chat-message ${roleClass}">
                <div class="message-bubble">${escapeHtml(msg.message_content)}</div>
                <div class="message-time">${time}</div>
            </div>
        `;
    });
    $('#chat-messages').html(html);
    scrollToBottom();
}

function sendMessage() {
    const message = $('#message-input').val().trim();
    if (!message) return;

    // Disable send button
    $('#btn-send').prop('disabled', true);

    // Add user message to UI
    const time = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    $('#chat-messages').append(`
        <div class="chat-message user">
            <div class="message-bubble">${escapeHtml(message)}</div>
            <div class="message-time">${time}</div>
        </div>
    `);
    scrollToBottom();

    // Clear input
    $('#message-input').val('');

    // Show typing indicator
    $('#chat-messages').append(`
        <div class="chat-message ai" id="typing-indicator">
            <div class="message-bubble">
                <div class="typing-indicator">
                    <div class="dot"></div>
                    <div class="dot"></div>
                    <div class="dot"></div>
                </div>
            </div>
        </div>
    `);
    scrollToBottom();

    // Send to server
    $.post('{{ route("api.chat.message") }}', {
        message: message,
        conversation_id: currentConversationId,
        _token: '{{ csrf_token() }}'
    }, function(data) {
        // Remove typing indicator
        $('#typing-indicator').remove();

        // Add AI message
        if (data.ai_message) {
            const aiTime = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            $('#chat-messages').append(`
                <div class="chat-message ai">
                    <div class="message-bubble">${escapeHtml(data.ai_message.message)}</div>
                    <div class="message-time">${aiTime}</div>
                </div>
            `);
            scrollToBottom();
        }

        currentConversationId = data.conversation.id;
    }).fail(function() {
        $('#typing-indicator').remove();
        $('#chat-messages').append(`
            <div class="chat-message system">
                <div class="message-bubble">Failed to send message. Please try again.</div>
            </div>
        `);
        scrollToBottom();
    }).always(function() {
        $('#btn-send').prop('disabled', false);
    });
}

function requestHumanSupport() {
    if (!currentConversationId) return;

    if (confirm('Are you sure you want to request human support?')) {
        $.post('{{ route("api.chat.request-human") }}', {
            conversation_id: currentConversationId,
            _token: '{{ csrf_token() }}'
        }, function(data) {
            $('#chat-messages').append(`
                <div class="chat-message system">
                    <div class="message-bubble">${escapeHtml(data.message)}</div>
                </div>
            `);
            scrollToBottom();
        });
    }
}

function scrollToBottom() {
    const messagesContainer = $('#chat-messages');
    messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
