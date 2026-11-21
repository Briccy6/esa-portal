<style>
    #esa-chat-widget {
        --esa-chat-green: #245c43;
        --esa-chat-green-dark: #193f2e;
        --esa-chat-green-soft: #eaf3ed;
        --esa-chat-gold: #e2b85f;
        --esa-chat-ink: #1d2c24;
        --esa-chat-muted: #748078;
        --esa-chat-border: #e8ede9;
        --esa-chat-surface: #fff;
        --esa-chat-shadow: 0 24px 70px rgba(20, 43, 30, .2), 0 4px 14px rgba(20, 43, 30, .08);
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 1040;
        color: var(--esa-chat-ink);
        font-family: "DM Sans", "Segoe UI", sans-serif;
        font-size: 14px;
        line-height: 1.45;
    }

    #esa-chat-widget *,
    #esa-chat-widget *::before,
    #esa-chat-widget *::after {
        box-sizing: border-box;
    }

    #esa-chat-widget button,
    #esa-chat-widget textarea {
        font: inherit;
    }

    #esa-chat-widget button {
        -webkit-tap-highlight-color: transparent;
    }

    #esa-chat-widget .esa-chat-launcher {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 62px;
        height: 62px;
        padding: 0 19px;
        border: 0;
        border-radius: 999px;
        background: var(--esa-chat-green);
        color: #fff;
        box-shadow: 0 8px 24px rgba(36, 92, 67, .3);
        cursor: pointer;
        transition: transform .2s ease, box-shadow .2s ease, background .2s ease, opacity .2s ease;
    }

    #esa-chat-widget .esa-chat-launcher:hover {
        transform: translateY(-3px);
        background: var(--esa-chat-green-dark);
        box-shadow: 0 12px 30px rgba(36, 92, 67, .36);
    }

    #esa-chat-widget .esa-chat-launcher > i {
        font-size: 21px;
    }

    #esa-chat-widget .esa-chat-launcher-label {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    #esa-chat-widget .esa-chat-unread {
        position: absolute;
        top: -5px;
        right: -4px;
        display: grid;
        min-width: 21px;
        height: 21px;
        padding: 0 5px;
        place-items: center;
        border: 2px solid #fff;
        border-radius: 999px;
        background: #c34939;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
    }

    #esa-chat-widget .esa-chat-panel {
        position: absolute;
        right: 0;
        bottom: 78px;
        display: flex;
        width: min(390px, calc(100vw - 32px));
        height: min(620px, calc(100dvh - 112px));
        min-height: 420px;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid rgba(36, 92, 67, .12);
        border-radius: 20px;
        background: var(--esa-chat-surface);
        box-shadow: var(--esa-chat-shadow);
        opacity: 0;
        pointer-events: none;
        transform: translateY(14px) scale(.985);
        transform-origin: bottom right;
        transition: opacity .2s ease, transform .2s ease, visibility .2s ease;
        visibility: hidden;
    }

    #esa-chat-widget.esa-chat-open .esa-chat-panel {
        opacity: 1;
        pointer-events: auto;
        transform: translateY(0) scale(1);
        visibility: visible;
    }

    #esa-chat-widget.esa-chat-open .esa-chat-launcher {
        opacity: 0;
        pointer-events: none;
        transform: scale(.8);
    }

    #esa-chat-widget .esa-chat-header {
        display: flex;
        min-height: 76px;
        align-items: center;
        justify-content: space-between;
        padding: 13px 17px;
        background: var(--esa-chat-green);
        color: #fff;
    }

    #esa-chat-widget .esa-chat-brand,
    #esa-chat-widget .esa-chat-status,
    #esa-chat-widget .esa-chat-controls {
        display: flex;
        align-items: center;
    }

    #esa-chat-widget .esa-chat-brand {
        gap: 11px;
        min-width: 0;
    }

    #esa-chat-widget .esa-chat-avatar {
        display: grid;
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        place-items: center;
        border: 1px solid rgba(255, 255, 255, .25);
        border-radius: 14px;
        background: rgba(255, 255, 255, .13);
        color: var(--esa-chat-gold);
        font-size: 18px;
    }

    #esa-chat-widget .esa-chat-heading {
        display: grid;
        gap: 3px;
        min-width: 0;
    }

    #esa-chat-widget .esa-chat-heading strong {
        overflow: hidden;
        font-size: 15px;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    #esa-chat-widget .esa-chat-status {
        gap: 6px;
        color: rgba(255, 255, 255, .78);
        font-size: 11px;
    }

    #esa-chat-widget .esa-chat-status > span {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #89d2a6;
        box-shadow: 0 0 0 3px rgba(137, 210, 166, .15);
    }

    #esa-chat-widget .esa-chat-controls {
        gap: 5px;
        margin-left: 8px;
    }

    #esa-chat-widget .esa-chat-icon-button,
    #esa-chat-widget .esa-chat-emoji-button {
        display: grid;
        width: 34px;
        height: 34px;
        flex: 0 0 auto;
        place-items: center;
        border: 0;
        border-radius: 10px;
        background: transparent;
        color: #fff;
        cursor: pointer;
        transition: background .15s ease;
    }

    #esa-chat-widget .esa-chat-icon-button:hover {
        background: rgba(255, 255, 255, .14);
    }

    #esa-chat-widget .esa-chat-content {
        display: flex;
        min-height: 0;
        flex: 1;
        flex-direction: column;
        background:
            radial-gradient(ellipse at 100% 0, rgba(226, 184, 95, .09), transparent 34%),
            #f8faf8;
    }

    #esa-chat-widget .esa-chat-welcome {
        padding: 18px 19px 13px;
        border-bottom: 1px solid var(--esa-chat-border);
        background: rgba(255, 255, 255, .78);
    }

    #esa-chat-widget .esa-chat-eyebrow {
        color: #9a7a37;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .12em;
    }

    #esa-chat-widget .esa-chat-welcome p {
        margin: 5px 0 3px;
        color: var(--esa-chat-ink);
        font-family: "Newsreader", Georgia, serif;
        font-size: 20px;
        font-weight: 600;
        line-height: 1.25;
    }

    #esa-chat-widget .esa-chat-welcome small {
        display: block;
        color: var(--esa-chat-muted);
        font-size: 12px;
        line-height: 1.5;
    }

    #esa-chat-widget .esa-chat-user {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 10px;
        padding: 4px 9px;
        border: 1px solid #e6ece7;
        border-radius: 999px;
        background: #f7faf7;
        color: #526158;
        font-size: 10px;
        font-weight: 600;
    }

    #esa-chat-widget .esa-chat-user i {
        color: var(--esa-chat-green);
    }

    #esa-chat-widget .esa-chat-messages {
        display: flex;
        min-height: 0;
        flex: 1;
        flex-direction: column;
        gap: 13px;
        overflow-y: auto;
        overscroll-behavior: contain;
        padding: 18px 16px;
        scroll-behavior: smooth;
    }

    #esa-chat-widget .esa-chat-messages::-webkit-scrollbar {
        width: 5px;
    }

    #esa-chat-widget .esa-chat-messages::-webkit-scrollbar-thumb {
        border-radius: 99px;
        background: #d7dfd9;
    }

    #esa-chat-widget .esa-chat-loading {
        display: flex;
        min-height: 70px;
        align-items: center;
        justify-content: center;
        gap: 9px;
        color: var(--esa-chat-muted);
        font-size: 12px;
    }

    #esa-chat-widget .esa-chat-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid #dfe8e1;
        border-top-color: var(--esa-chat-green);
        border-radius: 50%;
        animation: esa-chat-spin .7s linear infinite;
    }

    #esa-chat-widget .esa-chat-message {
        display: flex;
        max-width: 88%;
        flex-direction: column;
        align-self: flex-start;
        animation: esa-chat-in .18s ease-out both;
    }

    #esa-chat-widget .esa-chat-message.is-user {
        align-self: flex-end;
    }

    #esa-chat-widget .esa-chat-message.is-system {
        max-width: 100%;
        align-self: center;
    }

    #esa-chat-widget .esa-chat-bubble {
        padding: 10px 12px;
        border: 1px solid #e8ede9;
        border-radius: 14px 14px 14px 4px;
        background: #fff;
        color: #2b3a31;
        font-size: 13px;
        line-height: 1.55;
        overflow-wrap: anywhere;
        white-space: pre-wrap;
    }

    #esa-chat-widget .is-user .esa-chat-bubble {
        border-color: var(--esa-chat-green);
        border-radius: 14px 14px 4px 14px;
        background: var(--esa-chat-green);
        color: #fff;
    }

    #esa-chat-widget .is-system .esa-chat-bubble {
        padding: 6px 10px;
        border: 0;
        border-radius: 8px;
        background: #edf2ee;
        color: #65746a;
        font-size: 11px;
        text-align: center;
    }

    #esa-chat-widget .esa-chat-time {
        margin: 4px 3px 0;
        color: #89958d;
        font-size: 10px;
    }

    #esa-chat-widget .is-user .esa-chat-time {
        text-align: right;
    }

    #esa-chat-widget .esa-chat-typing {
        display: flex;
        align-items: center;
        gap: 4px;
        min-height: 34px;
        padding: 0 3px;
    }

    #esa-chat-widget .esa-chat-typing span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #94a49a;
        animation: esa-chat-bounce 1.1s infinite ease-in-out;
    }

    #esa-chat-widget .esa-chat-typing span:nth-child(2) {
        animation-delay: .14s;
    }

    #esa-chat-widget .esa-chat-typing span:nth-child(3) {
        animation-delay: .28s;
    }

    #esa-chat-widget .esa-chat-error {
        margin: 0 14px 8px;
        padding: 8px 10px;
        border: 1px solid #f1d2cb;
        border-radius: 9px;
        background: #fff4f1;
        color: #9b4134;
        font-size: 12px;
    }

    #esa-chat-widget .esa-chat-emoji-picker {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        margin: 0 15px 8px;
        padding: 7px;
        border: 1px solid var(--esa-chat-border);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 15px rgba(20, 43, 30, .08);
    }

    #esa-chat-widget .esa-chat-emoji-picker[hidden],
    #esa-chat-widget .esa-chat-error[hidden],
    #esa-chat-widget .esa-chat-unread[hidden] {
        display: none;
    }

    #esa-chat-widget .esa-chat-emoji-picker button {
        display: grid;
        width: 34px;
        height: 34px;
        place-items: center;
        border: 0;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        font-size: 19px;
    }

    #esa-chat-widget .esa-chat-emoji-picker button:hover {
        background: var(--esa-chat-green-soft);
    }

    #esa-chat-widget .esa-chat-compose {
        padding: 10px 13px 12px;
        border-top: 1px solid var(--esa-chat-border);
        background: #fff;
    }

    #esa-chat-widget .esa-chat-compose-box {
        padding: 6px 7px 5px 12px;
        border: 1px solid #dfe7e1;
        border-radius: 14px;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    #esa-chat-widget .esa-chat-compose-box:focus-within {
        border-color: #8eae98;
        box-shadow: 0 0 0 3px rgba(36, 92, 67, .08);
    }

    #esa-chat-widget textarea {
        display: block;
        width: 100%;
        min-height: 27px;
        max-height: 100px;
        padding: 4px 1px;
        resize: none;
        border: 0;
        outline: 0;
        background: transparent;
        color: var(--esa-chat-ink);
        font-size: 13px;
        line-height: 1.45;
    }

    #esa-chat-widget textarea::placeholder {
        color: #9aa59d;
    }

    #esa-chat-widget .esa-chat-compose-actions {
        display: flex;
        min-height: 34px;
        align-items: center;
        gap: 5px;
    }

    #esa-chat-widget .esa-chat-emoji-button {
        width: 30px;
        height: 30px;
        color: #728078;
        font-size: 16px;
    }

    #esa-chat-widget .esa-chat-emoji-button:hover,
    #esa-chat-widget .esa-chat-emoji-button[aria-expanded="true"] {
        background: #f0f5f1;
        color: var(--esa-chat-green);
    }

    #esa-chat-widget .esa-chat-enter-hint {
        flex: 1;
        color: #9aa59d;
        font-size: 9px;
    }

    #esa-chat-widget .esa-chat-send {
        display: grid;
        width: 32px;
        height: 32px;
        flex: 0 0 auto;
        place-items: center;
        border: 0;
        border-radius: 10px;
        background: var(--esa-chat-green);
        color: #fff;
        cursor: pointer;
        transition: background .15s ease, transform .15s ease;
    }

    #esa-chat-widget .esa-chat-send:hover:not(:disabled) {
        transform: translateY(-1px);
        background: var(--esa-chat-green-dark);
    }

    #esa-chat-widget .esa-chat-send:disabled {
        cursor: wait;
        opacity: .55;
    }

    #esa-chat-widget .esa-chat-human {
        display: flex;
        align-items: center;
        gap: 6px;
        margin: 9px auto 0;
        padding: 3px 7px;
        border: 0;
        background: transparent;
        color: var(--esa-chat-green);
        cursor: pointer;
        font-size: 11px;
        font-weight: 700;
    }

    #esa-chat-widget .esa-chat-human:hover:not(:disabled) {
        color: var(--esa-chat-green-dark);
        text-decoration: underline;
        text-underline-offset: 3px;
    }

    #esa-chat-widget .esa-chat-human:disabled {
        cursor: default;
        opacity: .55;
    }

    #esa-chat-widget .esa-chat-panel.is-minimized {
        height: 76px;
        min-height: 76px;
    }

    #esa-chat-widget .esa-chat-panel.is-minimized .esa-chat-content {
        display: none;
    }

    @keyframes esa-chat-spin {
        to { transform: rotate(360deg); }
    }

    @keyframes esa-chat-in {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes esa-chat-bounce {
        0%, 60%, 100% { transform: translateY(0); }
        30% { transform: translateY(-4px); }
    }

    @media (max-width: 600px) {
        #esa-chat-widget {
            right: 16px;
            bottom: 16px;
        }

        #esa-chat-widget .esa-chat-launcher {
            width: 56px;
            min-width: 56px;
            height: 56px;
            padding: 0;
        }

        #esa-chat-widget .esa-chat-launcher-label {
            display: none;
        }

        #esa-chat-widget .esa-chat-panel {
            position: fixed;
            inset: 0;
            width: 100%;
            height: 100%;
            height: 100dvh;
            min-height: 0;
            border: 0;
            border-radius: 0;
            transform: translateY(12px);
        }

        #esa-chat-widget.esa-chat-open .esa-chat-panel {
            transform: translateY(0);
        }

        #esa-chat-widget .esa-chat-header {
            min-height: 68px;
            padding-top: max(12px, env(safe-area-inset-top));
        }

        #esa-chat-widget .esa-chat-compose {
            padding-bottom: max(12px, env(safe-area-inset-bottom));
        }

        #esa-chat-widget .esa-chat-panel.is-minimized {
            inset: auto 12px 12px;
            width: calc(100% - 24px);
            height: 68px;
            min-height: 68px;
            border: 1px solid rgba(36, 92, 67, .12);
            border-radius: 16px;
        }
    }

    @media (max-width: 360px) {
        #esa-chat-widget .esa-chat-enter-hint {
            font-size: 8px;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        #esa-chat-widget *,
        #esa-chat-widget *::before,
        #esa-chat-widget *::after {
            animation-duration: .01ms !important;
            animation-iteration-count: 1 !important;
            scroll-behavior: auto !important;
            transition-duration: .01ms !important;
        }
    }
</style>
