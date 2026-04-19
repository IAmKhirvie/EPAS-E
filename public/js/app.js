document.addEventListener('DOMContentLoaded', function() {
    // Function to handle image preview
    function handleImagePreview(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    
    // Avatar upload in sidebar
    const avatarUpload = document.getElementById('avatar-upload');
    const avatarForm = document.getElementById('avatar-form');
    
    if (avatarUpload && avatarForm) {
        avatarUpload.addEventListener('change', function() {
            handleImagePreview(this, 'avatar-preview');
            avatarForm.submit();
        });
    }

    // Sidebar toggle functionality — uses event delegation so it survives Livewire SPA navigation
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');

    // Sidebar toggle logic:
    // - Click profile area at top of sidebar = toggle collapse/expand
    // - Click outside expanded sidebar = collapse
    function collapseSidebar() {
        sidebar.classList.add('collapsed');
        document.body.classList.add('sidebar-collapsed');
        document.documentElement.classList.add('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'true');
    }
    function expandSidebar() {
        sidebar.classList.remove('collapsed');
        document.body.classList.remove('sidebar-collapsed');
        document.documentElement.classList.remove('sidebar-collapsed');
        localStorage.setItem('sidebarCollapsed', 'false');
    }

    document.addEventListener('click', function(e) {
        if (!sidebar) return;

        // Click on sidebar profile area = toggle
        if (e.target.closest('.sidebar-profile')) {
            if (sidebar.classList.contains('collapsed')) {
                expandSidebar();
            } else {
                collapseSidebar();
            }
            return;
        }

        // Click outside sidebar = collapse if expanded
        if (!sidebar.classList.contains('collapsed') && !e.target.closest('.sidebar')) {
            if (!e.target.closest('.popover') && !e.target.closest('.dropdown') && !e.target.closest('.fab-container')) {
                collapseSidebar();
            }
        }
    });

    // Event delegation: clicking any sidebar-toggle button anywhere in the document
    document.addEventListener('click', function(e) {
        const toggleBtn = e.target.closest('#sidebar-toggle, #hamburger-menu');
        if (!toggleBtn || !sidebar) return;

        sidebar.classList.toggle('collapsed');
        document.body.classList.toggle('sidebar-collapsed');

        const icon = toggleBtn.querySelector('i');
        if (icon) {
            icon.className = sidebar.classList.contains('collapsed')
                ? 'fa-solid fa-chevron-right'
                : 'fa-solid fa-chevron-left';
        }

        localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
    });

    // Apply saved state on load — default to collapsed if no preference saved
    if (sidebar) {
        const savedState = localStorage.getItem('sidebarCollapsed');
        const shouldExpand = savedState === 'false'; // only expand if user explicitly chose to

        if (shouldExpand) {
            sidebar.classList.remove('collapsed');
            document.body.classList.remove('sidebar-collapsed');
            document.documentElement.classList.remove('sidebar-collapsed');
            const icon = document.querySelector('#sidebar-toggle i, #hamburger-menu i');
            if (icon) icon.className = 'fa-solid fa-chevron-left';
        } else {
            // Ensure collapsed state is applied
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
            document.documentElement.classList.add('sidebar-collapsed');
            const icon = document.querySelector('#sidebar-toggle i, #hamburger-menu i');
            if (icon) icon.className = 'fa-solid fa-chevron-right';
        }
    }

    // Mobile sidebar functionality
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('sidebar-open');
            overlay.classList.remove('active');
            if (hamburgerMenu) {
                hamburgerMenu.classList.remove('active');
            }
            document.body.classList.remove('sidebar-open');
        });
    }

    // Close sidebar when clicking on a link (mobile only)
    const sidebarLinks = document.querySelectorAll('.sidebar .nav-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            // Only close on mobile (screen width less than 1032px)
            if (window.innerWidth < 1032) {
                sidebar.classList.remove('sidebar-open');
                if (overlay) {
                    overlay.classList.remove('active');
                }
                if (hamburgerMenu) {
                    hamburgerMenu.classList.remove('active');
                }
                document.body.classList.remove('sidebar-open');
            }
        });
    });

    // Close sidebar when pressing Escape key (mobile only)
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && window.innerWidth < 1032) {
            sidebar.classList.remove('sidebar-open');
            if (overlay) {
                overlay.classList.remove('active');
            }
            if (hamburgerMenu) {
                hamburgerMenu.classList.remove('active');
            }
            document.body.classList.remove('sidebar-open');
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        // On desktop, ensure sidebar is visible (not in mobile slide-out mode)
        if (window.innerWidth >= 1032 && sidebar) {
            sidebar.classList.remove('sidebar-open');
            if (overlay) {
                overlay.classList.remove('active');
            }
            document.body.classList.remove('sidebar-open');
        }
    });
});

// Search bar expand functionality
const searchNav = document.getElementById('search-nav');
if (searchNav) {
    searchNav.addEventListener('click', function(e) {
        // Don't toggle if clicking on the input
        if (e.target.classList.contains('search-input')) return;
        
        this.classList.toggle('expanded');
        
        // Focus input when expanded
        if (this.classList.contains('expanded')) {
            const input = this.querySelector('.search-input');
            setTimeout(() => input.focus(), 300);
        }
    });

    // Close search when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchNav.contains(e.target) && searchNav.classList.contains('expanded')) {
            searchNav.classList.remove('expanded');
        }
    });

    // Close search on escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && searchNav.classList.contains('expanded')) {
            searchNav.classList.remove('expanded');
        }
    });
}

