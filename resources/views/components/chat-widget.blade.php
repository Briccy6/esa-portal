@php
    $chatIdentity = null;

    foreach (['admin', 'staff', 'teacher', 'student'] as $guard) {
        $user = auth($guard)->user();

        if ($user) {
            $chatIdentity = [
                'name' => $user->full_name ?? $user->name ?? $user->teacher_name ?? ucfirst($guard),
                'role' => $guard === 'staff' ? ($user->role ?? 'Staff') : ucfirst($guard),
                'studentId' => $guard === 'student' ? ($user->reg_number ?? null) : null,
            ];
            break;
        }
    }
@endphp

@if ($chatIdentity)
    <div id="esa-chat-widget">
        <button class="esa-chat-launcher" type="button" aria-label="Open chat with ESA Assistant" aria-expanded="false" aria-controls="esa-chat-panel">
            <i class="fa-solid fa-comments" aria-hidden="true"></i>
            <span class="esa-chat-launcher-label">Ask ESA</span>
            <span class="esa-chat-unread" aria-label="Unread messages" hidden>0</span>
        </button>

        <section class="esa-chat-panel" id="esa-chat-panel" aria-label="ESA Assistant chat" aria-hidden="true">
            <header class="esa-chat-header">
                <div class="esa-chat-brand">
                    <span class="esa-chat-avatar" aria-hidden="true"><i class="fa-solid fa-graduation-cap"></i></span>
                    <div class="esa-chat-heading">
                        <strong>ESA Assistant</strong>
                        <span class="esa-chat-status"><span></span> Here to help</span>
                    </div>
                </div>
                <div class="esa-chat-controls">
                    <button class="esa-chat-icon-button" type="button" data-chat-action="minimize" aria-label="Minimize chat" title="Minimize">
                        <i class="fa-solid fa-minus" aria-hidden="true"></i>
                    </button>
                    <button class="esa-chat-icon-button" type="button" data-chat-action="close" aria-label="Close chat" title="Close">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </div>
            </header>

            <div class="esa-chat-content">
                <div class="esa-chat-welcome">
                    <span class="esa-chat-eyebrow">EXCELLENT SERVANT ACADEMY</span>
                    <p>Hi {{ $chatIdentity['name'] }}, how can we help?</p>
                    <small>Ask in English or Kinyarwanda. We’re here for school and learning questions.</small>
                    <div class="esa-chat-user">
                        <i class="fa-regular fa-user" aria-hidden="true"></i>
                        <span>{{ $chatIdentity['role'] }}@if ($chatIdentity['studentId']) · {{ $chatIdentity['studentId'] }}@endif</span>
                    </div>
                </div>

                <div class="esa-chat-messages" role="log" aria-live="polite" aria-relevant="additions text" aria-label="Conversation">
                    <div class="esa-chat-loading" role="status">
                        <span class="esa-chat-spinner" aria-hidden="true"></span>
                        <span>Loading your conversation…</span>
                    </div>
                </div>

                <div class="esa-chat-error" role="alert" hidden></div>

                <div class="esa-chat-emoji-picker" hidden aria-label="Choose an emoji">
                    <button type="button" aria-label="Smile">😊</button>
                    <button type="button" aria-label="Thumbs up">👍</button>
                    <button type="button" aria-label="Clap">👏</button>
                    <button type="button" aria-label="Thinking">🤔</button>
                    <button type="button" aria-label="Thank you">🙏</button>
                    <button type="button" aria-label="Book">📚</button>
                </div>

                <footer class="esa-chat-compose">
                    <div class="esa-chat-compose-box">
                        <textarea rows="1" maxlength="5000" placeholder="Write a message…" aria-label="Write a message"></textarea>
                        <div class="esa-chat-compose-actions">
                            <button class="esa-chat-emoji-button" type="button" data-chat-action="emoji" aria-label="Add emoji" aria-expanded="false">
                                <i class="fa-regular fa-face-smile" aria-hidden="true"></i>
                            </button>
                            <span class="esa-chat-enter-hint">Enter to send · Shift + Enter for a new line</span>
                            <button class="esa-chat-send" type="button" data-chat-action="send" aria-label="Send message">
                                <i class="fa-solid fa-arrow-up" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                    <button class="esa-chat-human" type="button" data-chat-action="human" disabled>
                        <i class="fa-solid fa-headset" aria-hidden="true"></i>
                        Request human support
                    </button>
                </footer>
            </div>
        </section>
    </div>

    @include('components.chat-widget-styles')
    @include('components.chat-widget-scripts', [
        'chatConfig' => [
            'conversationUrl' => route('api.chat.conversation'),
            'messageUrl' => route('api.chat.message'),
            'humanSupportUrl' => route('api.chat.request-human'),
        ],
    ])
@endif
