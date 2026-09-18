/**
 * Shared Dark Mode Manager
 * Used by both authenticated pages (navbar.js) and public pages (public-darkmode.js).
 *
 * Call window.initDarkMode() after DOM is ready.
 */
(function () {
    'use strict';

    function initDarkMode(toggleSelector, iconSelector) {
        toggleSelector = toggleSelector || '#dark-mode-toggle';
        iconSelector   = iconSelector   || '#dark-mode-icon';

        var toggle = document.querySelector(toggleSelector);
        var icon   = document.querySelector(iconSelector);
        var body   = document.body;
        var html   = document.documentElement;

        function getCurrentTheme() {
            var saved = localStorage.getItem('theme');
            if (saved === 'light' || saved === 'dark') return saved;
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }

        function applyTheme(theme, persist = true) {
            var isDark = theme === 'dark';
            body.classList.toggle('dark-mode', isDark);
            html.classList.toggle('dark-mode', isDark);
            if (icon) icon.className = isDark ? 'fas fa-sun' : 'fas fa-moon';
            if (persist) {
                localStorage.setItem('theme', theme);
                document.cookie = 'theme=' + theme + '; path=/; max-age=31536000; SameSite=Lax';
            }
            window.dispatchEvent(new CustomEvent('themeChange', { detail: { theme: theme } }));
        }

        // Apply on load
        applyTheme(getCurrentTheme(), false);

        // Toggle handler
        if (toggle) {
            toggle.addEventListener('click', function () {
                applyTheme(body.classList.contains('dark-mode') ? 'light' : 'dark');
            });
        }

        // Sync across tabs
        window.addEventListener('storage', function (e) {
            if (e.key === 'theme') applyTheme(getCurrentTheme(), false);
        });

        // Follow system preference when user hasn't chosen manually
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function (e) {
            if (!localStorage.getItem('theme') || localStorage.getItem('theme') === 'auto') {
                applyTheme(e.matches ? 'dark' : 'light', false);
            }
        });
    }

    window.initDarkMode = initDarkMode;
})();
