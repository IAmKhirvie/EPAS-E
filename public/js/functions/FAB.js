// Universal FAB Script
document.addEventListener('DOMContentLoaded', function() {
    const fabContainer = document.getElementById('fabContainer');
    const fabMain = document.getElementById('fabMain');
    const fabOptions = document.querySelectorAll('.fab-option');
    const overlay = document.getElementById('overlay');
    const fabBackdrop = document.getElementById('fab-backdrop'); // Get the fab backdrop
    
    // Sidebar elements
    const createCourseSidebar = document.getElementById('createCourseSidebar');
    const createModuleSidebar = document.getElementById('createModuleSidebar');
    const addUserSidebar = document.getElementById('addUserSidebar');
    
    // Close buttons
    const closeCourseSidebar = document.getElementById('closeCourseSidebar');
    const closeModuleSidebar = document.getElementById('closeModuleSidebar');
    const closeUserSidebar = document.getElementById('closeSidebar');

    // Only initialize if FAB exists (user is not student)
    if (fabContainer && fabMain) {

        // Add ARIA attributes for accessibility
        fabMain.setAttribute('aria-label', 'Open actions menu');
        fabMain.setAttribute('aria-expanded', 'false');
        fabMain.setAttribute('aria-haspopup', 'true');

        // Add ARIA labels to FAB option buttons
        var fabOptionCourse = document.getElementById('fabOptionCourse');
        var fabOptionModule = document.getElementById('fabOptionModule');
        var fabOptionEnroll = document.getElementById('fabOptionEnroll');
        if (fabOptionCourse) fabOptionCourse.setAttribute('aria-label', 'Create Course');
        if (fabOptionModule) fabOptionModule.setAttribute('aria-label', 'Create Module');
        if (fabOptionEnroll) fabOptionEnroll.setAttribute('aria-label', 'Enroll User');

        // Helper to update aria-expanded state
        function updateFabAriaState() {
            var isActive = fabContainer.classList.contains('active');
            fabMain.setAttribute('aria-expanded', isActive ? 'true' : 'false');
            fabMain.setAttribute('aria-label', isActive ? 'Close actions menu' : 'Open actions menu');
        }

        // Toggle FAB options and backdrop
        fabMain.addEventListener('click', function(e) {
            e.stopPropagation();
            fabContainer.classList.toggle('active');
            updateFabAriaState();

            // Toggle the backdrop
            if (fabBackdrop) {
                if (fabContainer.classList.contains('active')) {
                    fabBackdrop.classList.add('active');
                } else {
                    fabBackdrop.classList.remove('active');
                }
            }
        });

        // Keyboard support: Enter/Space to toggle FAB
        fabMain.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fabMain.click();
            }
        });

        // Handle all FAB option clicks
        fabOptions.forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                
                // Close FAB options and backdrop
                fabContainer.classList.remove('active');
                updateFabAriaState();
                if (fabBackdrop) {
                    fabBackdrop.classList.remove('active');
                }

                const optionId = this.id;
                
                switch(optionId) {
                    case 'fabOptionCourse':
                        if (createCourseSidebar) {
                            openSidebar(createCourseSidebar);
                        } else {
                            window.location.href = "{{ route('courses.create') }}";
                        }
                        break;
                    case 'fabOptionModule':
                        if (createModuleSidebar) {
                            openSidebar(createModuleSidebar);
                        } else {
                            window.location.href = "{{ route('modules.create') }}";
                        }
                        break;
                    case 'fabOptionEnroll':
                        if (addUserSidebar) {
                            openSidebar(addUserSidebar);
                        } else {
                            window.location.href = "{{ route('users.index') }}";
                        }
                        break;
                }
            });
        });

        // Generic sidebar open function
        function openSidebar(sidebar) {
            if (sidebar) {
                sidebar.classList.add('active');
                overlay.classList.add('active');
                document.body.classList.add('sidebar-open');
                
                // Lower FAB z-index when sidebar is open
                if (fabContainer) {
                    fabContainer.style.zIndex = '9999';
                }
            }
        }

        // Generic sidebar close function
        function closeSidebarFunc(sidebar) {
            if (sidebar) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.classList.remove('sidebar-open');
                
                // Restore FAB z-index
                if (fabContainer) {
                    fabContainer.style.zIndex = '10000';
                }
            }
        }

        // Close sidebars when close buttons are clicked
        if (closeCourseSidebar && createCourseSidebar) {
            closeCourseSidebar.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebarFunc(createCourseSidebar);
            });
        }

        if (closeModuleSidebar && createModuleSidebar) {
            closeModuleSidebar.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebarFunc(createModuleSidebar);
            });
        }

        if (closeUserSidebar && addUserSidebar) {
            closeUserSidebar.addEventListener('click', function(e) {
                e.stopPropagation();
                closeSidebarFunc(addUserSidebar);
            });
        }

        // Close sidebars when overlay is clicked
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeAllSidebars();
            }
        });

        // Close FAB and backdrop when clicking on backdrop
        if (fabBackdrop) {
            fabBackdrop.addEventListener('click', function() {
                fabContainer.classList.remove('active');
                updateFabAriaState();
                fabBackdrop.classList.remove('active');
            });
        }

        // Close FAB options when clicking outside
        document.addEventListener('click', function(e) {
            if (fabContainer && !fabContainer.contains(e.target)) {
                fabContainer.classList.remove('active');
                updateFabAriaState();
                if (fabBackdrop) {
                    fabBackdrop.classList.remove('active');
                }
            }
        });

        // Close sidebars with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAllSidebars();
                fabContainer.classList.remove('active');
                updateFabAriaState();
                if (fabBackdrop) {
                    fabBackdrop.classList.remove('active');
                }
            }
        });

        // Function to close all sidebars
        function closeAllSidebars() {
            if (createCourseSidebar) createCourseSidebar.classList.remove('active');
            if (createModuleSidebar) createModuleSidebar.classList.remove('active');
            if (addUserSidebar) addUserSidebar.classList.remove('active');
            
            overlay.classList.remove('active');
            document.body.classList.remove('sidebar-open');
            
            // Restore FAB z-index
            if (fabContainer) {
                fabContainer.style.zIndex = '10000';
            }
        }

        // Close sidebars when forms are submitted successfully
        const addUserForm = document.getElementById('addUserForm');
        if (addUserForm && addUserSidebar) {
            addUserForm.addEventListener('submit', function() {
                setTimeout(function() {
                    closeSidebarFunc(addUserSidebar);
                }, 1000);
            });
        }

        const createModuleForm = document.getElementById('createModuleForm');
        if (createModuleForm && createModuleSidebar) {
            createModuleForm.addEventListener('submit', function() {
                setTimeout(function() {
                    closeSidebarFunc(createModuleSidebar);
                }, 1000);
            });
        }

        const createCourseForm = document.getElementById('createCourseForm');
        if (createCourseForm && createCourseSidebar) {
            createCourseForm.addEventListener('submit', function() {
                setTimeout(function() {
                    closeSidebarFunc(createCourseSidebar);
                }, 1000);
            });
        }

        // Role-based field display in add user form
        const roleSelect = document.getElementById('role');
        const sectionField = document.getElementById('section')?.closest('.mb-3');
        const roomField = document.getElementById('room_number')?.closest('.mb-3');

        function toggleFieldsBasedOnRole() {
            if (!roleSelect || !sectionField || !roomField) return;
            
            const role = roleSelect.value;
            
            if (role === 'student') {
                sectionField.style.display = 'block';
                roomField.style.display = 'none';
            } else if (role === 'instructor') {
                sectionField.style.display = 'none';
                roomField.style.display = 'block';
            } else {
                sectionField.style.display = 'none';
                roomField.style.display = 'none';
            }
        }

        if (roleSelect) {
            roleSelect.addEventListener('change', toggleFieldsBasedOnRole);
            toggleFieldsBasedOnRole(); 
        }

        // Close FAB options when window loses focus
        window.addEventListener('blur', function() {
            fabContainer.classList.remove('active');
            updateFabAriaState();
            if (fabBackdrop) {
                fabBackdrop.classList.remove('active');
            }
        });
    }
});