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

    // Sidebar toggle functionality - ADD THIS SECTION
    const hamburgerMenu = document.getElementById('hamburger-menu');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    
    if (hamburgerMenu && sidebar) {
        hamburgerMenu.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            document.body.classList.toggle('sidebar-collapsed'); // ADD THIS LINE
            
            // Change icon based on state
            const icon = this.querySelector('i');
            if (sidebar.classList.contains('collapsed')) {
                icon.className = 'fa-solid fa-chevron-right';
            } else {
                icon.className = 'fa-solid fa-chevron-left';
            }
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Load saved state - UPDATE THIS SECTION
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed'); // ADD THIS LINE
            const icon = hamburgerMenu.querySelector('i');
            icon.className = 'fa-solid fa-chevron-right';
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

