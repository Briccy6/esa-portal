@php
    // Determine current user and guard
    $currentUser = null;
    $currentGuard = null;
    $userName = 'User';
    
    if (auth('admin')->check()) {
        $currentUser = auth('admin')->user();
        $currentGuard = 'admin';
        $userName = 'Admin';
    } elseif (auth('staff')->check()) {
        $currentUser = auth('staff')->user();
        $currentGuard = 'staff';
        $userName = $currentUser->name ?? 'Staff Member';
    } elseif (auth('teacher')->check()) {
        $currentUser = auth('teacher')->user();
        $currentGuard = 'teacher';
        $userName = $currentUser->teacher_name ?? 'Teacher';
    } elseif (auth('student')->check()) {
        $currentUser = auth('student')->user();
        $currentGuard = 'student';
        $userName = $currentUser->name ?? 'Student';
    }
@endphp

@if($currentUser)
<!-- Floating WhatsApp-style Community Spaces Button -->
<a href="#" class="floating-spaces-btn" id="openSpacesChatBtn" title="Community Spaces Chat">
    <i class="fab fa-whatsapp"></i>
    <span class="spaces-unread-badge" id="spacesUnreadBadge">0</span>
</a>

<!-- WhatsApp-inspired Chat Modal -->
<div class="wa-chat-modal" id="waChatModal" style="display:none;">
    <div class="wa-chat-sidebar">
        <div class="wa-chat-sidebar-header">
            <span>Spaces</span>
            <button class="wa-close-btn" id="closeSpacesChatBtn">&times;</button>
        </div>
        <div class="wa-chat-spaces-list" id="waSpacesList">
            <!-- Spaces will be loaded here -->
        </div>
    </div>
    <div class="wa-chat-main">
        <div class="wa-chat-header" id="waChatHeader">
            <span id="waChatSpaceName">Select a space</span>
        </div>
        <div class="wa-chat-messages" id="waChatMessages">
            <div class="wa-chat-placeholder">Select a space to start chatting</div>
        </div>
        <form class="wa-chat-input-area" id="waChatForm" style="display:none;" enctype="multipart/form-data">
            <input type="text" id="waChatInput" class="wa-chat-input" placeholder="Type a message..." autocomplete="off" />
            <label class="wa-chat-attach-btn">
                <input type="file" id="waChatFile" style="display:none;" accept="image/*,application/pdf,.doc,.docx,.txt,audio/*,video/*" />
                <i class="fas fa-paperclip"></i>
            </label>
            <button type="submit" class="wa-chat-send-btn"><i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
<div class="wa-chat-modal-overlay" id="waChatModalOverlay" style="display:none;"></div>

<div id="waChatError" style="color: #e74c3c; padding: 4px 12px; min-height: 20px;"></div>

<style>
    /* WhatsApp-inspired Chat Modal Styles */
    .wa-chat-modal {
        position: fixed;
        bottom: 90px;
        right: 40px;
        width: 400px;
        height: 400px;
        background: #f0f0f0;
        border-radius: 18px;
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.28);
        z-index: 2000;
        display: flex;
        overflow: hidden;
        flex-direction: row;
        animation: waChatFadeIn 0.2s;
    }
    @keyframes waChatFadeIn {
        from { opacity: 0; transform: translateY(40px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .wa-chat-modal-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.2);
        z-index: 1999;
    }
    .wa-chat-sidebar {
        width: 120px;
        background: #075e54;
        color: #fff;
        display: flex;
        flex-direction: column;
    }
    .wa-chat-sidebar-header {
        padding: 16px 10px 8px 16px;
        font-weight: bold;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid #0b7a6b;
    }
    .wa-close-btn {
        background: none;
        border: none;
        color: #fff;
        font-size: 1.5rem;
        cursor: pointer;
    }
    .wa-chat-spaces-list {
        flex: 1;
        overflow-y: auto;
        padding: 8px 0;
    }
    .wa-space-item {
        padding: 12px 10px;
        cursor: pointer;
        border-bottom: 1px solid #0b7a6b;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s;
    }
    .wa-space-item.active, .wa-space-item:hover {
        background: #128c7e;
    }
    .wa-space-unread-badge {
        background: #25d366;
        color: #fff;
        border-radius: 50%;
        padding: 2px 7px;
        font-size: 0.8rem;
        font-weight: bold;
        min-width: 20px;
        text-align: center;
    }
    .wa-chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #ece5dd;
    }
    .wa-chat-header {
        background: #075e54;
        color: #fff;
        padding: 16px;
        font-weight: bold;
        font-size: 1.1rem;
        border-bottom: 1px solid #0b7a6b;
    }
    .wa-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 18px 12px 12px 12px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .wa-chat-placeholder {
        color: #888;
        text-align: center;
        margin-top: 40%;
    }
    .wa-chat-bubble {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 16px;
        font-size: 1rem;
        line-height: 1.5;
        position: relative;
        background: #fff;
        color: #222;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        word-break: break-word;
    }
    .wa-chat-bubble.me {
        background: #dcf8c6;
        align-self: flex-end;
    }
    .wa-chat-bubble .wa-chat-file {
        display: block;
        margin-top: 6px;
    }
    .wa-chat-bubble .wa-chat-file img {
        max-width: 180px;
        border-radius: 8px;
        margin-top: 4px;
    }
    .wa-chat-bubble .wa-chat-file a {
        color: #128c7e;
        text-decoration: underline;
    }
    .wa-chat-bubble .wa-chat-meta {
        font-size: 0.8rem;
        color: #888;
        margin-top: 4px;
        text-align: right;
    }
    .wa-chat-bubble .wa-chat-meta.me {
        color: #4caf50;
    }
    .wa-chat-input-area {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        background: #f7f7f7;
        border-top: 1px solid #ddd;
        gap: 8px;
    }
    .wa-chat-input {
        flex: 1;
        border: none;
        border-radius: 20px;
        padding: 8px 16px;
        font-size: 1rem;
        background: #fff;
        outline: none;
    }
    .wa-chat-attach-btn {
        color: #888;
        font-size: 1.2rem;
        cursor: pointer;
        margin-right: 4px;
    }
    .wa-chat-send-btn {
        background: #25d366;
        color: #fff;
        border: none;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        cursor: pointer;
        transition: background 0.2s;
    }
    .wa-chat-send-btn:hover {
        background: #128c7e;
    }
    .floating-spaces-btn {
        position: fixed;
        bottom: 32px;
        right: 32px;
        z-index: 1200;
        background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
        color: #fff;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 16px rgba(44, 62, 80, 0.18);
        font-size: 2rem;
        transition: box-shadow 0.2s, transform 0.2s;
        cursor: pointer;
        border: none;
        outline: none;
    }
    .floating-spaces-btn:hover {
        box-shadow: 0 8px 32px rgba(44, 62, 80, 0.28);
        transform: scale(1.07);
        color: #fff;
        text-decoration: none;
    }
    .spaces-unread-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #e74c3c;
        color: #fff;
        border-radius: 50%;
        padding: 2px 8px;
        font-size: 0.9rem;
        font-weight: bold;
        min-width: 22px;
        text-align: center;
        border: 2px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
    }
    .wa-access-denied {
        background: #ffebee;
        color: #c62828;
        padding: 20px;
        border-radius: 12px;
        margin: 20px;
        text-align: center;
    }
    .wa-access-denied .contact-info {
        background: #fff3e0;
        color: #ef6c00;
        padding: 10px;
        border-radius: 8px;
        margin-top: 10px;
        font-size: 0.9rem;
    }
    @media (max-width: 600px) {
        .wa-chat-modal {
            width: 100vw;
            height: 100vh;
            right: 0;
            bottom: 0;
            border-radius: 0;
        }
        .wa-chat-sidebar {
            width: 90px;
        }
        .floating-spaces-btn {
            bottom: 18px;
            right: 18px;
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
        .spaces-unread-badge {
            top: 4px;
            right: 4px;
            font-size: 0.8rem;
            min-width: 18px;
        }
    }
</style>

<script>
    // WhatsApp Chat Modal Logic
    let waSelectedSpaceId = null;
    let waChatPolling = null;
    let waCurrentUserId = {{ $currentUser->id }};
    let waCurrentUserType = '{{ get_class($currentUser) }}';
    let waCurrentUserName = '{{ $userName }}';

    function fetchSpacesAndUpdateBadge() {
        fetch('/admin/api/spaces')
            .then(res => res.json())
            .then(spaces => {
                let totalUnread = 0;
                let html = '';
                spaces.forEach(space => {
                    totalUnread += space.unread_count;
                    html += `<div class="wa-space-item" data-id="${space.id}">
                        <span>${space.name}</span>
                        ${space.unread_count > 0 ? `<span class="wa-space-unread-badge">${space.unread_count}</span>` : ''}
                    </div>`;
                });
                document.getElementById('waSpacesList').innerHTML = html;
                document.getElementById('spacesUnreadBadge').textContent = totalUnread;
            })
            .catch(err => {
                console.error('Error fetching spaces:', err);
            });
    }

    function fetchMessages(spaceId, scrollToBottom = false) {
        fetch(`/admin/api/spaces/${spaceId}/messages`)
            .then(res => {
                if (!res.ok) {
                    if (res.status === 403) {
                        return res.json().then(data => {
                            throw new Error(data.message || 'Access denied');
                        });
                    }
                    throw new Error('Failed to fetch messages');
                }
                return res.json();
            })
            .then(messages => {
                let html = '';
                if (!messages.length) {
                    html = '<div class="wa-chat-placeholder">No messages yet. Start the conversation!</div>';
                } else {
                    messages.forEach(msg => {
                        let isMe = msg.sender_id == waCurrentUserId && msg.sender_type === waCurrentUserType;
                        let bubbleClass = 'wa-chat-bubble' + (isMe ? ' me' : '');
                        let fileHtml = '';
                        
                        if (msg.file_path) {
                            if (msg.file_type && msg.file_type.startsWith('image/')) {
                                fileHtml = `<div class="wa-chat-file"><img src="${msg.file_path}" alt="attachment"></div>`;
                            } else if (msg.file_type && msg.file_type.startsWith('audio/')) {
                                fileHtml = `<div class="wa-chat-file"><audio controls src="${msg.file_path}"></audio></div>`;
                            } else if (msg.file_type && msg.file_type.startsWith('video/')) {
                                fileHtml = `<div class="wa-chat-file"><video controls src="${msg.file_path}" style="max-width: 200px;"></video></div>`;
                            } else {
                                fileHtml = `<div class="wa-chat-file"><a href="${msg.file_path}" target="_blank">📎 Download file</a></div>`;
                            }
                        }
                        
                        html += `<div class="${bubbleClass}">
                            <div class="wa-sender-name" style="font-size: 0.8rem; color: #666; margin-bottom: 4px; font-weight: bold;">
                                ${msg.sender_name}
                            </div>
                            ${msg.message ? msg.message.replace(/\n/g, '<br>') : ''}
                            ${fileHtml}
                            <div class="wa-chat-meta${isMe ? ' me' : ''}">${new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</div>
                        </div>`;
                    });
                }
                document.getElementById('waChatMessages').innerHTML = html;
                if (scrollToBottom) {
                    let chatDiv = document.getElementById('waChatMessages');
                    chatDiv.scrollTop = chatDiv.scrollHeight;
                }
            })
            .catch(err => {
                document.getElementById('waChatMessages').innerHTML = `
                    <div class="wa-access-denied">
                        <i class="fas fa-exclamation-triangle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <div><strong>Access Denied</strong></div>
                        <div>${err.message}</div>
                        <div class="contact-info">
                            <i class="fas fa-phone"></i> Contact: admin@school.com or call +250 123 456 789
                        </div>
                    </div>`;
            });
    }

    function markSpaceAsRead(spaceId) {
        fetch(`/admin/api/spaces/${spaceId}/mark-as-read`, {method: 'POST', headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}});
    }

    document.getElementById('openSpacesChatBtn').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('waChatModal').style.display = 'flex';
        document.getElementById('waChatModalOverlay').style.display = 'block';
        fetchSpacesAndUpdateBadge();
    });
    
    document.getElementById('closeSpacesChatBtn').addEventListener('click', function() {
        document.getElementById('waChatModal').style.display = 'none';
        document.getElementById('waChatModalOverlay').style.display = 'none';
        waSelectedSpaceId = null;
        clearInterval(waChatPolling);
    });
    
    document.getElementById('waChatModalOverlay').addEventListener('click', function() {
        document.getElementById('waChatModal').style.display = 'none';
        document.getElementById('waChatModalOverlay').style.display = 'none';
        waSelectedSpaceId = null;
        clearInterval(waChatPolling);
    });

    document.getElementById('waSpacesList').addEventListener('click', function(e) {
        let item = e.target.closest('.wa-space-item');
        if (!item) return;
        let spaceId = item.getAttribute('data-id');
        waSelectedSpaceId = spaceId;
        document.querySelectorAll('.wa-space-item').forEach(el => el.classList.remove('active'));
        item.classList.add('active');
        document.getElementById('waChatHeader').innerHTML = `<span id="waChatSpaceName">${item.querySelector('span').textContent}</span>`;
        document.getElementById('waChatForm').style.display = 'flex';
        fetchMessages(spaceId, true);
        markSpaceAsRead(spaceId);
        if (waChatPolling) clearInterval(waChatPolling);
        waChatPolling = setInterval(() => {
            fetchMessages(spaceId);
            fetchSpacesAndUpdateBadge();
        }, 3000);
    });

    document.getElementById('waChatForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const errorDiv = document.getElementById('waChatError');
        errorDiv.textContent = '';
        
        if (!waSelectedSpaceId) {
            errorDiv.textContent = 'Please select a space first.';
            return;
        }
        
        let input = document.getElementById('waChatInput');
        let fileInput = document.getElementById('waChatFile');
        let formData = new FormData();
        
        if (input.value.trim()) formData.append('message', input.value.trim());
        if (fileInput.files[0]) formData.append('file', fileInput.files[0]);
        
        fetch(`/admin/api/spaces/${waSelectedSpaceId}/messages`, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: formData
        })
        .then(async res => {
            let data;
            try {
                data = await res.json();
            } catch {
                errorDiv.textContent = 'A server error occurred. Please try again or contact support.';
                return;
            }
            
            if (!res.ok || data.error) {
                if (data.details) {
                    errorDiv.textContent = Object.values(data.details).flat().join(' ');
                } else if (data.error) {
                    errorDiv.textContent = data.error.replace(/^Server error:/, '').trim();
                } else {
                    errorDiv.textContent = 'Failed to send message. Please try again.';
                }
                return;
            }
            
            input.value = '';
            fileInput.value = '';
            fetchMessages(waSelectedSpaceId, true);
            fetchSpacesAndUpdateBadge();
        })
        .catch(err => {
            errorDiv.textContent = 'Network error: ' + (err.message || err);
        });
    });

    // Auto-scroll to bottom on new message
    setInterval(() => {
        if (waSelectedSpaceId) {
            let chatDiv = document.getElementById('waChatMessages');
            chatDiv.scrollTop = chatDiv.scrollHeight;
        }
    }, 1000);

    // Initial badge update
    fetchSpacesAndUpdateBadge();
</script>
@endif 