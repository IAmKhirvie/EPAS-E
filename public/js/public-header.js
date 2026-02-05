// Dropdown functionality for login dropdown
const loginDropdownBtn = document.getElementById('login-dropdown-btn');
const loginDropdown = document.getElementById('login-dropdown');

if (loginDropdownBtn && loginDropdown) {
    loginDropdownBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        loginDropdown.classList.toggle('active');
        
        // Close other dropdowns
        closeAllDropdownsExcept(loginDropdown);
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown') && !e.target.closest('.navbar-item')) {
        closeAllDropdowns();
    }
});

function closeAllDropdowns() {
    const dropdowns = document.querySelectorAll('.dropdown, .popover');
    dropdowns.forEach(dropdown => {
        dropdown.classList.remove('active');
    });
}

function closeAllDropdownsExcept(exceptDropdown) {
    const dropdowns = document.querySelectorAll('.dropdown, .popover');
    dropdowns.forEach(dropdown => {
        if (dropdown !== exceptDropdown) {
            dropdown.classList.remove('active');
        }
    });
}

// Close dropdown when clicking on a dropdown item
document.addEventListener('click', function(e) {
    if (e.target.closest('.dropdown-item')) {
        closeAllDropdowns();
    }
});