/**
 * ModalUI - A replacement for browser alert/confirm/prompt
 */
const ModalUI = (function() {
    let backdrop = null;
    let modal = null;
    let titleEl = null;
    let bodyEl = null;
    let footerEl = null;
    let iconEl = null;

    // Initialize the CSS and DOM elements
    function init() {
        if (document.getElementById('custom-modal-backdrop')) return;

        // Inject CSS if not present
        // ADAPTED: Check for support.css as it might already contain the styles, 
        // preventing unnecessary 404s if modal-ui.css isn't strictly used alone.
        // However, we will create modal-ui.css to be safe.
        if (!document.querySelector('link[href*="modal-ui.css"]') && !document.querySelector('link[href*="support.css"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            // Use relative path assuming we are in a view that has relative assets working
            // Fallback to absolute if needed, but relative usually safest in "standalone" views
            link.href = (window.ASSET_ROOT_JS || '../../public') + '/assets/css/modal-ui.css';
            document.head.appendChild(link);
        }

        // Create DOM Structure
        const html = `
            <div id="custom-modal-backdrop" class="custom-modal-backdrop">
                <div class="custom-modal">
                    <div class="custom-modal-header">
                        <div id="custom-modal-icon" class="custom-modal-icon"></div>
                        <div id="custom-modal-title" class="custom-modal-title"></div>
                    </div>
                    <div id="custom-modal-body" class="custom-modal-body"></div>
                    <div id="custom-modal-footer" class="custom-modal-footer"></div>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', html);

        backdrop = document.getElementById('custom-modal-backdrop');
        modal = backdrop.querySelector('.custom-modal');
        titleEl = document.getElementById('custom-modal-title');
        bodyEl = document.getElementById('custom-modal-body');
        footerEl = document.getElementById('custom-modal-footer');
        iconEl = document.getElementById('custom-modal-icon');

        // Close on backdrop click (optional, maybe not for crucial alerts)
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                // Shake effect to indicate modal is modal
                modal.classList.add('shake');
                setTimeout(() => modal.classList.remove('shake'), 400);
            }
        });
    }

    function show(options) {
        init(); // Ensure DOM exists

        const {
            title = 'Notification',
            message = '',
            type = 'info', // info, error, success, auth
            buttons = [] 
        } = options;

        // Set Icon
        let iconHtml = '';
        if (type === 'error') iconHtml = '⚠️';
        else if (type === 'success') iconHtml = '✅';
        else if (type === 'auth') iconHtml = '🔒';
        else iconHtml = 'ℹ️';

        iconEl.innerHTML = iconHtml;
        iconEl.className = 'custom-modal-icon icon-' + type;

        // Set Content
        titleEl.textContent = title;
        bodyEl.innerHTML = message;

        // Build Footer
        footerEl.innerHTML = '';
        footerEl.className = 'custom-modal-footer';
        if (type === 'auth') footerEl.classList.add('footer-center');

        buttons.forEach(btn => {
            const btnEl = document.createElement('button');
            btnEl.textContent = btn.text;
            btnEl.className = 'custom-btn ' + (btn.primary ? 'custom-btn-primary' : 'custom-btn-secondary');
            if (btn.link) btnEl.className = 'custom-btn custom-btn-link';
            
            btnEl.onclick = () => {
                if (btn.onClick) btn.onClick();
                if (btn.close !== false) close();
            };
            footerEl.appendChild(btnEl);
        });

        // Show
        backdrop.classList.add('active');
    }

    function close() {
        if (backdrop) backdrop.classList.remove('active');
    }

    // Dynamic Base URL for redirects
    function getBaseUrl() {
        if (window.APP_BASE_URL) return window.APP_BASE_URL;
        return '';
    }

    return {
        // Standard Alert Replacement
        alert: function(message, title = 'Fullstack App') {
            return new Promise((resolve) => {
                show({
                    title: title,
                    message: message,
                    type: 'info',
                    buttons: [
                        { text: 'OK', primary: true, onClick: resolve }
                    ]
                });
            });
        },

        // Standard Confirm Replacement
        confirm: function(message, title = 'Confirm Action') {
            return new Promise((resolve) => {
                show({
                    title: title,
                    message: message,
                    type: 'info',
                    buttons: [
                        { text: 'Cancel', primary: false, onClick: () => resolve(false) },
                        { text: 'Proceed', primary: true, onClick: () => resolve(true) }
                    ]
                });
            });
        },

        // Authentication Logic Replacement
        requireAuth: function(message = 'You need to sign in to continue.') {
            const baseUrl = getBaseUrl();
            show({
                title: 'Sign In Required',
                message: message,
                type: 'auth',
                buttons: [
                    { 
                        text: 'Log In', 
                        primary: true, 
                        // ADAPTED: Use /login
                        onClick: () => window.location.href = `${baseUrl}/login` 
                    },
                    { 
                        text: 'Create an Account', 
                        primary: false, 
                        // ADAPTED: Use /register
                        onClick: () => window.location.href = `${baseUrl}/register` 
                    },
                    { 
                        text: 'Not now', 
                        link: true, 
                        onClick: () => {} 
                    }
                ]
            });
        }
    };
})();

// Auto-init on load
document.addEventListener('DOMContentLoaded', () => {
    // We defer slightly to ensure body is ready
    setTimeout(() => {
        // Check if we can/should init (not strictly necessary but safe)
    }, 100);
});
