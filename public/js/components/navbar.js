class TopNavbar {
    constructor() {
        this.currentUser = null;
        this.activePopover = null;
        
        this.init();
    }

    async init() {
        await this.loadUserData();
        this.setupEventListeners();
        this.setupTooltips();
        this.initializeDarkMode();
    }

    async loadUserData() {
        try {
            // In a real app, this would be an API call
            const avatarElement = document.getElementById('navbar-avatar');
            this.currentUser = {
                firstName: '{{ Auth::user()->first_name }}',
                lastName: '{{ Auth::user()->last_name }}',
                role: '{{ Auth::user()->role }}',
                avatar: avatarElement ? avatarElement.src : '/images/default-avatar.png'
            };
        } catch (error) {
            console.error('Failed to load user data:', error);
            // Set default user data if loading fails
            this.currentUser = {
                firstName: 'User',
                lastName: '',
                role: 'guest',
                avatar: '/images/default-avatar.png'
            };
        }
    }

    setupTooltips() {
        // ... keep existing tooltip code ...
    }

    setupEventListeners() {
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                this.toggleSidebar();
            });
        }

        // Dark mode toggle
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        if (darkModeToggle) {
            darkModeToggle.addEventListener('click', () => {
                this.toggleDarkMode();
            });
        }

        // Notifications
        const notificationsBtn = document.getElementById('notifications-btn');
        const notificationsPopover = document.getElementById('notifications-popover');

        if (notificationsBtn && notificationsPopover) {
            notificationsBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePopover('notifications-popover');
            });
        }

        // User menu
        const userMenuBtn = document.getElementById('user-menu-btn');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.togglePopover('user-dropdown');
            });
        }

        // Logout
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleLogout();
            });
        }

        // Close popovers when clicking outside
        document.addEventListener('click', () => {
            this.closeAllPopovers();
        });

        // Prevent popover close when clicking inside
        document.querySelectorAll('.popover, .dropdown').forEach(element => {
            element.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });

        // Add click event to document to close sidebar when clicking outside on mobile
        document.addEventListener('click', this.handleOutsideClick.bind(this));
    }

    // Dark Mode functionality
    initializeDarkMode() {
        const darkModeToggle = document.getElementById('dark-mode-toggle');
        const darkModeIcon = document.getElementById('dark-mode-icon');
        const body = document.body;

        // Check if elements exist
        if (!darkModeToggle || !darkModeIcon) {
            console.error('Dark mode elements not found');
            return;
        }

        // Improved theme detection with better persistence
        const getCurrentTheme = () => {
            // Try to get from localStorage first
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) return savedTheme;
            
            // Fallback to system preference
            return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        };

        const applyTheme = (theme) => {
            if (theme === 'dark') {
                body.classList.add('dark-mode');
                darkModeIcon.className = 'fas fa-sun';
                // Update localStorage
                localStorage.setItem('theme', 'dark');
            } else {
                body.classList.remove('dark-mode');
                darkModeIcon.className = 'fas fa-moon';
                // Update localStorage
                localStorage.setItem('theme', 'light');
            }
            
            // Dispatch event for other components to listen to
            window.dispatchEvent(new CustomEvent('themeChange', { detail: { theme } }));
        };

        // Apply theme on initial load
        const currentTheme = getCurrentTheme();
        applyTheme(currentTheme);

        // Listen for system theme changes (only if no manual preference is set)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            // Only apply system theme if user hasn't manually set a preference
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Listen for storage events (sync across tabs)
        window.addEventListener('storage', (e) => {
            if (e.key === 'theme') {
                applyTheme(e.newValue);
            }
        });
    }

    toggleDarkMode() {
        const darkModeIcon = document.getElementById('dark-mode-icon');
        const body = document.body;
        const isDarkMode = body.classList.contains('dark-mode');
        
        if (isDarkMode) {
            // Switching to light mode
            body.classList.remove('dark-mode');
            localStorage.setItem('theme', 'light');
            darkModeIcon.className = 'fas fa-moon';
        } else {
            // Switching to dark mode
            body.classList.add('dark-mode');
            localStorage.setItem('theme', 'dark');
            darkModeIcon.className = 'fas fa-sun';
        }
    }

    // Handle clicks outside the sidebar on mobile
    handleOutsideClick(event) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        // If we're on mobile and sidebar is visible
        if (window.innerWidth < 1032 &&
            sidebar && 
            !sidebar.classList.contains('mobile-hidden') &&
            !sidebar.contains(event.target) && 
            sidebarToggle && 
            !sidebarToggle.contains(event.target)) {
            
            this.hideSidebar();
        }
    }

    // Mobile sidebar toggle behavior
    toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const body = document.body;
        
        if (sidebar) {
            if (window.innerWidth < 1032) {
                const isHidden = sidebar.classList.contains('mobile-hidden');
                
                if (isHidden) {
                    // Show sidebar
                    this.showSidebar();
                } else {
                    // Hide sidebar
                    this.hideSidebar();
                }
            } else {
                // Desktop behavior
                sidebar.classList.toggle('collapsed');
                body.classList.toggle('sidebar-collapsed');
            }
        }
    }

    showSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.remove('mobile-hidden');
            this.showBackdrop();
        }
    }

    hideSidebar() {
        const sidebar = document.getElementById('sidebar');
        if (sidebar) {
            sidebar.classList.add('mobile-hidden');
            this.hideBackdrop();
        }
    }

    showBackdrop() {
        let backdrop = document.getElementById('sidebar-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'sidebar-backdrop';
            backdrop.className = 'sidebar-backdrop';
            document.body.appendChild(backdrop);
            
            // Simple click handler
            backdrop.onclick = () => {
                this.hideSidebar();
            };
        }
        
        setTimeout(() => {
            backdrop.classList.add('active');
        }, 10);
    }

    hideBackdrop() {
        const backdrop = document.getElementById('sidebar-backdrop');
        if (backdrop) {
            backdrop.classList.remove('active');
            setTimeout(() => {
                if (backdrop.parentNode) {
                    backdrop.parentNode.removeChild(backdrop);
                }
            }, 300);
        }
    }

    togglePopover(popoverId) {
        const popover = document.getElementById(popoverId);
        
        if (this.activePopover === popoverId) {
            this.closeAllPopovers();
            return;
        }

        this.closeAllPopovers();
        
        if (popover) {
            popover.classList.add('active');
            this.activePopover = popoverId;
        }
    }

    closeAllPopovers() {
        document.querySelectorAll('.popover, .dropdown').forEach(element => {
            element.classList.remove('active');
        });
        this.activePopover = null;
    }

    handleLogout() {
        if (confirm('Are you sure you want to logout?')) {
            document.getElementById('logout-form').submit();
        }
    }
}

// Global functions (needed for onclick handlers)
function markAsRead(event, announcementId)
{
    // Simply navigate to the announcement - read tracking removed
    window.location.href = event.currentTarget.href;
}

function updateNotificationBadge() {
    const badge = document.getElementById('notification-badge');
    if (badge) {
        const currentCount = parseInt(badge.textContent);
        if (currentCount > 1) {
            badge.textContent = currentCount - 1;
        } else {
            badge.remove();
        }
    }
}

// Function to update notification count
// Simplified since we no longer track read/unread status
function updateNotificationCount() {
    // Remove the notification badge since read tracking is disabled
    const badge = document.getElementById('notification-badge');
    if (badge) {
        badge.remove();
    }
}

function sortNotifications(sortBy) {
    const notificationsList = document.getElementById('notifications-list');
    const notificationItems = Array.from(notificationsList.querySelectorAll('.notification-item:not(.empty)'));

    notificationItems.sort((a, b) => {
        const deadlineA = a.getAttribute('data-deadline') || Number.MAX_SAFE_INTEGER;
        const deadlineB = b.getAttribute('data-deadline') || Number.MAX_SAFE_INTEGER;

        switch(sortBy) {
            case 'deadline':
                return parseInt(deadlineA) - parseInt(deadlineB);

            case 'unread':
                // Read tracking removed - all items considered "read", sort by date instead
                return parseInt(b.getAttribute('data-created-at')) - parseInt(a.getAttribute('data-created-at'));

            case 'newest':
            default:
                return parseInt(b.getAttribute('data-created-at')) - parseInt(a.getAttribute('data-created-at'));
        }
    });

    // Re-append sorted items
    notificationItems.forEach(item => notificationsList.appendChild(item));
}

function handleMobileMenu() {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth < 1032) {
        sidebar?.classList.add('mobile-hidden');
    } else {
        sidebar?.classList.remove('mobile-hidden');
        const body = document.body;
        if (sidebar?.classList.contains('collapsed')) {
            body.classList.add('sidebar-collapsed');
        } else {
            body.classList.remove('sidebar-collapsed');
        }
    }
}

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.topNavbar = new TopNavbar();
    
    // Notification sorting
    const sortSelect = document.getElementById('notification-sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            sortNotifications(this.value);
        });
    }
    
    // Initialize notification count
    updateNotificationCount();
    
    // Set up periodic updates (every 30 seconds)
    setInterval(updateNotificationCount, 30000);
});

// Handle window resize for mobile menu
window.addEventListener('resize', handleMobileMenu);
handleMobileMenu();