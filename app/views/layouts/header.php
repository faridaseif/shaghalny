<!-- React Header Container -->
<div id="shaghalny-header-root"></div>

<!-- Load Component (Relative Path) -->
<script src="/shaghalny8/shaghalny/public/assets/js/components/Header.js?v=<?php echo time(); ?>"></script>
<script src="/shaghalny8/shaghalny/public/assets/js/admin_shortcuts.js?v=<?php echo time(); ?>"></script>

<!-- Mount Component (Pure JS) -->
<script>
    (function() {
        // Wait for Header to be available
        const mountHeader = () => {
            if (typeof window.Header === 'function' && typeof ReactDOM !== 'undefined') {
                const rootEl = document.getElementById('shaghalny-header-root');
                if (rootEl) {
                    const username = "<?php echo isset($_SESSION['user_name']) ? addslashes($_SESSION['user_name']) : 'Guest'; ?>";
                    const role = "<?php echo isset($_SESSION['role']) ? addslashes($_SESSION['role']) : 'user'; ?>";
                    
                    const root = ReactDOM.createRoot(rootEl);
                    const h = React.createElement;
                    
                    root.render(h(window.Header, { username: username, role: role }));
                }
            } else {
                // Retry if scripts loading order is delayed
                setTimeout(mountHeader, 50);
            }
        };
        mountHeader();
    })();
</script>

<style>
/* Fallback/Correction for dropdown styling injected by React inline styles */
.dropdown-item:hover {
    background-color: #F1F5F9 !important;
    color: #0F172A !important;
}
</style>
