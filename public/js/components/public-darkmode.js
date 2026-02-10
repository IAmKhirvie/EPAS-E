/**
 * Public Dark Mode Handler
 * Handles dark mode toggle for public pages (About, Contact, Lobby, etc.)
 * This is separate from navbar.js which handles authenticated pages
 */

(function() {
    'use strict';

    const darkModeToggle = document.getElementById('dark-mode-toggle');
    const darkModeIcon = document.getElementById('dark-mode-icon');
    const body = document.body;

    // Check if elements exist
    if (!darkModeToggle || !darkModeIcon) {
        console.warn('Dark mode elements not found on this page');
        return;
    }

    // Get current theme from localStorage or system preference
    function getCurrentTheme() {
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) return savedTheme;
        return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    }

    // Apply theme to the page
    function applyTheme(theme) {
        if (theme === 'dark') {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark-mode');
            darkModeIcon.className = 'fas fa-sun';
        } else {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark-mode');
            darkModeIcon.className = 'fas fa-moon';
        }
    }

    // Initialize dark mode on page load
    const currentTheme = getCurrentTheme();
    applyTheme(currentTheme);

    // Toggle dark mode on button click
    darkModeToggle.addEventListener('click', function() {
        const isDark = body.classList.contains('dark-mode');

        if (isDark) {
            body.classList.remove('dark-mode');
            document.documentElement.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
            darkModeIcon.className = 'fas fa-moon';
        } else {
            body.classList.add('dark-mode');
            document.documentElement.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
            darkModeIcon.className = 'fas fa-sun';
        }

        // Dispatch event for other components
        window.dispatchEvent(new CustomEvent('themeChange', {
            detail: { theme: isDark ? 'light' : 'dark' }
        }));
    });

    // Listen for system theme changes (only if no manual preference is set)
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', function(e) {
        if (!localStorage.getItem('theme')) {
            applyTheme(e.matches ? 'dark' : 'light');
        }
    });

    // Listen for storage events (sync across tabs)
    window.addEventListener('storage', function(e) {
        if (e.key === 'theme' && e.newValue) {
            applyTheme(e.newValue);
        }
    });
})();
