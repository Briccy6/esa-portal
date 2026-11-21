<script>
(() => {
    const root = document.getElementById('esa-chat-widget');
    if (!root) return;

    const config = @js($chatConfig);
    const panel = root.querySelector('.esa-chat-panel');
    const launcher = root.querySelector('.esa-chat-launcher');
    const messages = root.querySelector('.esa-chat-messages');
    const input = root.querySelector('textarea');
    const sendButton = root.querySelector('[data-chat-action="send"]');
    const humanButton = root.querySelector('[data-chat-action="human"]');
    const emojiButton = root.querySelector('.esa-chat-emoji-button');
    const emojiPicker = root.querySelector('.esa-chat-emoji-picker');
    const errorBox = root.querySelector('.esa-chat-error');
    const unreadBadge = root.querySelector('.esa-chat-unread');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    const state = {
        conversationId: null,
        loaded: false,
        loading: false,
        sending: false,
        unread: 0,
    };

    const showError = (message) => {
        errorBox.textContent = message;
        errorBox.hidden = false;
    };

    const clearError = () => {
        errorBox.textContent = '';
        errorBox.hidden = true;
    };

    const scrollToLatest = () => {
        messages.scrollTop = messages.scrollHeight;
    };

    const request = async (url, options = {}) => {
        const headers = {
            Accept: 'application/json',
            ...(options.body ? { 'Content-Type': 'application/json' } : {}),
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {}),
            ...options.headers,
        };
        const response = await fetch(url, { ...options, headers, credentials: 'same-origin' });
        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            throw new Error(data.message || data.error || 'The chat service is temporarily unavailable. Please try again.');
        }

        return data;
    };

    const appendMessage = (message, role) => {
        const item = document.createElement('div');
        item.className = `esa-chat-message ${role === 'user' ? 'is-user' : role === 'system' ? 'is-system' : ''}`.trim();

        const bubble = document.createElement('div');
        bubble.className = 'esa-chat-bubble';
        bubble.textContent = message;
        item.appendChild(bubble);

        if (role !== 'system') {
            const time = document.createElement('time');
            time.className = 'esa-chat-time';
            time.dateTime = new Date().toISOString();
            time.textContent = new Date().toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
            item.appendChild(time);
        }

        messages.appendChild(item);
        scrollToLatest();
        return item;
    };

    const renderHistory = (history) => {
        messages.replaceChildren();
        history.forEach((message) => {
            const role = message.message_type === 'system'
                ? 'system'
                : (message.sender_role === 'user' ? 'user' : 'ai');
            appendMessage(message.message_content || '', role);
        });
    };

    const updateUnread = (count) => {
        state.unread = count;
        unreadBadge.textContent = count > 9 ? '9+' : String(count);
        unreadBadge.hidden = count === 0;
    };

    const setOpen = (open) => {
        root.classList.toggle('esa-chat-open', open);
        panel.setAttribute('aria-hidden', String(!open));
        launcher.setAttribute('aria-expanded', String(open));
        if (open) {
            updateUnread(0);
            loadConversation();
            window.setTimeout(() => input.focus(), 220);
        } else {
            emojiPicker.hidden = true;
            emojiButton.setAttribute('aria-expanded', 'false');
            launcher.focus();
        }
    };

    const loadConversation = async () => {
        if (state.loaded || state.loading) return;

        state.loading = true;
        clearError();

        try {
            const data = await request(config.conversationUrl);
            const conversation = data.conversation;
            if (!conversation) throw new Error('Your conversation could not be loaded. Please try again.');

            state.conversationId = conversation.id;
            renderHistory(conversation.messages || []);
            state.loaded = true;
            humanButton.disabled = false;
        } catch (error) {
            messages.replaceChildren();
            showError(error.message);
        } finally {
            state.loading = false;
        }
    };

    const showTyping = () => {
        const typing = document.createElement('div');
        typing.className = 'esa-chat-message';
        typing.id = 'esa-chat-typing';
        typing.setAttribute('role', 'status');
        typing.setAttribute('aria-label', 'ESA Assistant is typing');

        const bubble = document.createElement('div');
        bubble.className = 'esa-chat-bubble';
        bubble.innerHTML = '<span class="esa-chat-typing" aria-hidden="true"><span></span><span></span><span></span></span>';
        typing.appendChild(bubble);
        messages.appendChild(typing);
        scrollToLatest();
    };

    const sendMessage = async () => {
        const message = input.value.trim();
        if (!message || state.sending) return;

        state.sending = true;
        sendButton.disabled = true;
        clearError();
        const userMessage = appendMessage(message, 'user');
        showTyping();

        try {
            const data = await request(config.messageUrl, {
                method: 'POST',
                body: JSON.stringify({
                    message,
                    conversation_id: state.conversationId,
                }),
            });

            state.conversationId = data.conversation?.id || state.conversationId;
            input.value = '';
            input.style.height = 'auto';
            if (data.ai_available === false) {
                showError('The AI assistant is temporarily unavailable. You can request human support or try again later.');
            }
            if (data.ai_message) {
                const reply = data.ai_message.message_content || data.ai_message.message || '';
                appendMessage(reply, 'ai');
                if (!root.classList.contains('esa-chat-open')) updateUnread(state.unread + 1);
            } else if (data.message) {
                appendMessage(data.message, 'system');
            }
        } catch (error) {
            userMessage.remove();
            input.value = message;
            showError(error.message);
            input.focus();
        } finally {
            messages.querySelector('#esa-chat-typing')?.remove();
            state.sending = false;
            sendButton.disabled = false;
            scrollToLatest();
        }
    };

    const requestHumanSupport = async () => {
        if (!state.conversationId || humanButton.disabled) return;

        humanButton.disabled = true;
        clearError();

        try {
            const data = await request(config.humanSupportUrl, {
                method: 'POST',
                body: JSON.stringify({ conversation_id: state.conversationId }),
            });
            appendMessage(data.message || 'Your request has been sent to the support team.', 'system');
        } catch (error) {
            humanButton.disabled = false;
            showError(error.message);
        }
    };

    root.addEventListener('click', (event) => {
        const action = event.target.closest('[data-chat-action]')?.dataset.chatAction;
        if (event.target.closest('.esa-chat-launcher')) {
            setOpen(!root.classList.contains('esa-chat-open'));
        } else if (action === 'close') {
            setOpen(false);
        } else if (action === 'minimize') {
            panel.classList.add('is-minimized');
            const button = root.querySelector('[data-chat-action="minimize"]');
            button.dataset.chatAction = 'maximize';
            button.setAttribute('aria-label', 'Expand chat');
            button.title = 'Expand';
            button.innerHTML = '<i class="fa-solid fa-up-right-and-down-left-from-center" aria-hidden="true"></i>';
        } else if (action === 'maximize') {
            panel.classList.remove('is-minimized');
            const button = root.querySelector('[data-chat-action="maximize"]');
            button.dataset.chatAction = 'minimize';
            button.setAttribute('aria-label', 'Minimize chat');
            button.title = 'Minimize';
            button.innerHTML = '<i class="fa-solid fa-minus" aria-hidden="true"></i>';
        } else if (action === 'send') {
            sendMessage();
        } else if (action === 'human') {
            requestHumanSupport();
        } else if (action === 'emoji') {
            emojiPicker.hidden = !emojiPicker.hidden;
            emojiButton.setAttribute('aria-expanded', String(!emojiPicker.hidden));
        } else if (event.target.closest('.esa-chat-emoji-picker button')) {
            const emoji = event.target.closest('.esa-chat-emoji-picker button').textContent;
            const start = input.selectionStart;
            const end = input.selectionEnd;
            input.setRangeText(emoji, start, end, 'end');
            input.focus();
            emojiPicker.hidden = true;
            emojiButton.setAttribute('aria-expanded', 'false');
        }
    });

    input.addEventListener('input', () => {
        input.style.height = 'auto';
        input.style.height = `${Math.min(input.scrollHeight, 100)}px`;
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            sendMessage();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && root.classList.contains('esa-chat-open')) {
            setOpen(false);
        }
    });
})();
</script>
