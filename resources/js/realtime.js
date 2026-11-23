import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const appKey = import.meta.env.VITE_REVERB_APP_KEY;
const host = import.meta.env.VITE_REVERB_HOST;

if (appKey && host) {
    window.Pusher = Pusher;

    const port = Number(import.meta.env.VITE_REVERB_PORT || 8080);
    const scheme = import.meta.env.VITE_REVERB_SCHEME || 'http';

    window.Echo = new Echo({
        broadcaster: 'reverb',
        key: appKey,
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === 'https',
        enabledTransports: ['ws', 'wss'],
        authEndpoint: '/broadcasting/auth',
        auth: {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
            },
        },
    });

    let updateToast;
    let refreshTimer;
    let hasUnsavedChanges = false;

    const showUpdateNotice = () => {
        if (updateToast) return;

        updateToast = document.createElement('div');
        updateToast.className = 'esa-realtime-notice';
        updateToast.setAttribute('role', 'status');
        updateToast.innerHTML = `
            <span><i class="bi bi-arrow-repeat" aria-hidden="true"></i> New system updates are available.</span>
            <button type="button">Refresh view</button>
        `;
        Object.assign(updateToast.style, {
            position: 'fixed',
            right: '16px',
            bottom: '16px',
            zIndex: '2000',
            display: 'flex',
            alignItems: 'center',
            flexWrap: 'wrap',
            gap: '12px',
            maxWidth: 'calc(100vw - 32px)',
            padding: '12px 14px',
            border: '1px solid #d7e2ee',
            borderRadius: '12px',
            background: '#fff',
            color: '#18324f',
            boxShadow: '0 12px 34px rgba(15, 35, 60, .18)',
            font: '600 13px/1.4 system-ui, sans-serif',
        });

        const button = updateToast.querySelector('button');
        Object.assign(button.style, {
            border: '0',
            borderRadius: '8px',
            padding: '8px 10px',
            background: '#183d61',
            color: '#fff',
            font: 'inherit',
            cursor: 'pointer',
        });
        button.addEventListener('click', () => window.location.reload());
        document.body.appendChild(updateToast);
    };

    const formFieldChanged = event => {
        const field = event.target;
        if (!(field instanceof HTMLElement) || !field.closest('form')) return;
        if (field.matches('input[type="hidden"], input[type="submit"], input[type="button"], input[type="search"]')) return;
        if (field.matches('button, [contenteditable="true"]')) return;
        hasUnsavedChanges = true;
    };

    document.addEventListener('input', formFieldChanged, true);
    document.addEventListener('change', formFieldChanged, true);
    document.addEventListener('submit', event => {
        if (event.target instanceof HTMLFormElement) {
            hasUnsavedChanges = false;
        }
    }, true);

    window.Echo.private('system.updates').listen('.system.data.changed', () => {
        if (hasUnsavedChanges) {
            showUpdateNotice();
            return;
        }

        window.clearTimeout(refreshTimer);
        refreshTimer = window.setTimeout(() => window.location.reload(), 700);
    });
}
