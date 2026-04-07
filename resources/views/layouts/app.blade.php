<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>@yield('title','EPAS-E - Electronic Products Assembly and Servicing')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ dynamic_asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ dynamic_asset('favicon.png') }}">

    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap" rel="stylesheet">

    <!-- PWA Support -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0c3a2d">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EPAS-E">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">

    <!-- CSS (local) -->
    <link rel="stylesheet" href="{{ dynamic_asset('vendor/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('vendor/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('vendor/css/animate.min.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/app.css') }}">
    
    <!-- Base CSS-->
    <link rel="stylesheet" href="{{ dynamic_asset('css/base/reset.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/base/typography.css') }}">
    
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/adduser.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/alerts.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/fab.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/forms.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/overlay.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/tables.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/utilities.css') }}">

    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/header.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/main-content.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/footer.css') }}">

    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/modules.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/users.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/index.css')}}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/dashboard.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/grades.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/analytics.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/content-builder.css') }}">

    <!-- Mobile CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/responsive-tables.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/touch-friendly.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/mobile.css') }}"  media="screen and (max-width: 1032px)">
    <script>
        // Immediately check and apply dark mode before page renders
        (function() {
            // Sync server-side theme cookie to localStorage
            var cookieMatch = document.cookie.match(/(?:^|; )theme=([^;]*)/);
            if (cookieMatch && cookieMatch[1] && cookieMatch[1] !== localStorage.getItem('theme')) {
                localStorage.setItem('theme', cookieMatch[1]);
            }

            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Apply theme immediately to prevent flash
            if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
                document.documentElement.classList.add('dark-mode');
            } else {
                document.documentElement.classList.remove('dark-mode');
            }

            // Store the initial theme for reference
            window.initialTheme = savedTheme || (systemPrefersDark ? 'dark' : 'light');
        })();
    </script>
    @livewireStyles
    @stack('styles')
</head>
<body class="modern-layout" data-user-role="{{ auth()->user()->role ?? '' }}">

  {{-- Page Loader - Shows during page transitions --}}
  @include('components.page-loader')

  {{-- Header --}}
  @include('partials.navbar')

  <div class="overlay" id="overlay"></div>

  <div class="layout-wrapper">
    {{-- Sidebar --}}
    @include('partials.sidebar')

    {{-- Main Content --}}
    <main class="main-content" role="main" tabindex="-1">
        @yield('content')
    </main>
  </div>

  <!-- Bottom Navigation for Mobile -->
  @auth
  @include('components.bottom-nav')
  @endauth

  <!-- Universal Floating Action Button (FAB) with options -->
@auth
    @if(auth()->user()->role != 'student')
        <div class="fab-container" id="fabContainer">
            <button class="fab-main" id="fabMain">
                <i class="fas fa-plus" id="fabIcon"></i>
            </button>
            <div class="fab-options">
                <button class="fab-option" id="fabOptionCourse">
                    <i class="fas fa-graduation-cap"></i>
                    <span class="fab-label">Create Course</span>
                </button>

                <button class="fab-option" id="fabOptionModule">
                    <i class="fas fa-book"></i>
                    <span class="fab-label">Create Module</span>
                </button>

                <button class="fab-option" id="fabOptionAnnouncement">
                    <i class="fas fa-bullhorn"></i>
                    <span class="fab-label">Create Announcement</span>
                </button>

                <button class="fab-option" id="fabOptionEnroll">
                    <i class="fas fa-user-plus"></i>
                    <span class="fab-label">Add User</span>
                </button>
            </div>
        </div>
        
        <!-- Create Course Sidebar -->
        <div class="slide-sidebar" id="createCourseSidebar">
            <div class="slide-sidebar-header">
                <h5>Create New Course</h5>
                <button class="close-sidebar" id="closeCourseSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="slide-sidebar-content">
                <form method="POST" action="{{ route('courses.store') }}" id="createCourseForm">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="course_name" class="form-label required-field">Course Name</label>
                        <input type="text" name="course_name" id="course_name" 
                            class="form-control" value="{{ old('course_name') }}" 
                            placeholder="e.g., Electronic Products Assembly and Servicing" required>
                    </div>

                    <div class="mb-3">
                        <label for="course_code" class="form-label required-field">Course Code</label>
                        <input type="text" name="course_code" id="course_code" 
                            class="form-control" value="{{ old('course_code') }}" 
                            placeholder="e.g., EPAS-NCII" required>
                    </div>

                    <div class="mb-3">
                        <label for="sector" class="form-label">Sector</label>
                        <input type="text" name="sector" id="sector" 
                            class="form-control" value="{{ old('sector') }}" 
                            placeholder="e.g., Electronics Sector">
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" 
                                class="form-control" rows="3" 
                                placeholder="Enter course description...">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Course</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- Create Module Sidebar -->
        @php $courses = $courses ?? \App\Models\Course::withCount('modules')->orderBy('course_name')->get(); @endphp
        <div class="slide-sidebar" id="createModuleSidebar">
            <div class="slide-sidebar-header">
                <h5>Create New Module</h5>
                <button class="close-sidebar" id="closeModuleSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="slide-sidebar-content">
                <form method="POST" action="{{ route('modules.store') }}" id="createModuleForm">
                    @csrf

                    <div class="mb-3">
                        <label for="fab_course_id" class="form-label required-field">Course</label>
                        <select name="course_id" id="fab_course_id" class="form-select" required>
                            <option value="" disabled selected>Select a Course</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" data-module-count="{{ $course->modules_count ?? $course->modules->count() }}">
                                    {{ $course->course_code }} - {{ $course->course_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="fab_module_order" class="form-label required-field">Order</label>
                                <input type="number" name="order" id="fab_module_order"
                                       class="form-control" value="{{ old('order', 1) }}" min="1" required>
                                <small class="text-muted">Position in course</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="module_number" class="form-label required-field">Module Number</label>
                                <input type="text" name="module_number" id="module_number"
                                       class="form-control" value="{{ old('module_number') }}" placeholder="e.g., Module 1" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="qualification_title" class="form-label required-field">Qualification Title</label>
                        <input type="text" name="qualification_title" id="qualification_title"
                               class="form-control" value="{{ old('qualification_title', 'Electronic Products Assembly And Servicing NCII') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="unit_of_competency" class="form-label required-field">Unit of Competency</label>
                        <input type="text" name="unit_of_competency" id="unit_of_competency"
                               class="form-control" value="{{ old('unit_of_competency', 'Assemble Electronic Products') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="module_title" class="form-label required-field">Module Title</label>
                        <input type="text" name="module_title" id="module_title"
                               class="form-control" value="{{ old('module_title', 'Assembling Electronic Products') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="module_name" class="form-label required-field">Module Name</label>
                        <input type="text" name="module_name" id="module_name"
                               class="form-control" value="{{ old('module_name', 'Competency based learning material') }}" required>
                    </div>

                    <div class="mb-3">
                        <x-rich-editor
                            name="table_of_contents"
                            label="Table of Contents"
                            placeholder="Enter the table of contents..."
                            :value="old('table_of_contents')"
                            toolbar="standard"
                            :height="100"
                        />
                    </div>

                    <div class="mb-3">
                        <x-rich-editor
                            name="how_to_use_cblm"
                            label="How to Use CBLM"
                            :value="old('how_to_use_cblm')"
                            toolbar="standard"
                            :height="80"
                        />
                    </div>

                    <div class="mb-3">
                        <x-rich-editor
                            name="introduction"
                            label="Introduction"
                            :value="old('introduction')"
                            toolbar="standard"
                            :height="80"
                        />
                    </div>

                    <div class="mb-3">
                        <x-rich-editor
                            name="learning_outcomes"
                            label="Learning Outcomes"
                            :value="old('learning_outcomes')"
                            toolbar="standard"
                            :height="80"
                        />
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Module</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Create Announcement Sidebar -->
        <div class="slide-sidebar" id="createAnnouncementSidebar">
            <div class="slide-sidebar-header">
                <h5>Create Announcement</h5>
                <button class="close-sidebar" id="closeAnnouncementSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="slide-sidebar-content">
                <form method="POST" action="{{ route('private.announcements.store') }}" id="createAnnouncementForm">
                    @csrf

                    <div class="mb-3">
                        <label for="fab_title" class="form-label required-field">Title</label>
                        <input type="text" name="title" id="fab_title" class="form-control"
                               value="{{ old('title') }}" placeholder="Announcement title" required>
                    </div>

                    <div class="mb-3">
                        <x-rich-editor
                            name="content"
                            id="fab_content"
                            label="Content"
                            placeholder="Write your announcement..."
                            :value="old('content')"
                            toolbar="standard"
                            :height="150"
                            :required="true"
                        />
                    </div>

                    <div class="mb-3">
                        <label for="fab_publish_at" class="form-label">Publish Date</label>
                        <input type="datetime-local" name="publish_at" id="fab_publish_at" class="form-control"
                               value="{{ old('publish_at') }}">
                        <small class="form-text text-muted">Leave empty to publish immediately.</small>
                    </div>

                    <div class="mb-3">
                        <label for="fab_deadline" class="form-label">Deadline (Optional)</label>
                        <input type="datetime-local" name="deadline" id="fab_deadline" class="form-control"
                               value="{{ old('deadline') }}">
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fab_is_urgent" name="is_urgent" value="1"
                                       {{ old('is_urgent') ? 'checked' : '' }}>
                                <label class="form-check-label" for="fab_is_urgent">Mark as Urgent</label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="fab_is_pinned" name="is_pinned" value="1"
                                       {{ old('is_pinned') ? 'checked' : '' }}>
                                <label class="form-check-label" for="fab_is_pinned">Pin to Top</label>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Announcement</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Add User Sidebar -->
        @php $departments = $departments ?? \App\Models\Department::all(); @endphp
        <div class="slide-sidebar" id="addUserSidebar">
            <div class="slide-sidebar-header">
                <h5>Add New User</h5>
                <button class="close-sidebar" id="closeSidebar">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="slide-sidebar-content">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please fix the following errors:</strong>
                        <ul class="mb-0 mt-2 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('private.users.store') }}" id="addUserForm">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="first_name" class="form-label required-field">First Name</label>
                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ old('first_name') }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="middle_name" class="form-label">Middle Name</label>
                                <input type="text" name="middle_name" id="middle_name" class="form-control" value="{{ old('middle_name') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="last_name" class="form-label required-field">Last Name</label>
                                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ old('last_name') }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="ext_name" class="form-label">Extension Name</label>
                                <input type="text" name="ext_name" id="ext_name" class="form-control" value="{{ old('ext_name') }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="student_id" class="form-label required-field">Student ID</label>
                                <input type="text" name="student_id" id="student_id" class="form-control" value="{{ old('student_id') }}" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="email" class="form-label required-field">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="password" class="form-label required-field">Password</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label required-field">Confirm Password</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="department_id" class="form-label required-field">Department</label>
                                <select name="department_id" id="department_id" class="form-select" required>
                                    <option value="" disabled {{ old('department_id') ? '' : 'selected' }}>Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="role" class="form-label required-field">Role</label>
                                <select name="role" id="role" class="form-select" required>
                                    <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select Role</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                                    <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row" id="student-fields">
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="school_year" class="form-label">School Year</label>
                                <input type="text" name="school_year" id="school_year" class="form-control" value="{{ old('school_year') }}" placeholder="e.g., 2025-2026">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label for="section" class="form-label">Section / Batch</label>
                                <input type="text" name="section" id="section" class="form-control" value="{{ old('section') }}" placeholder="e.g., Batch 1, EPAS-B1">
                            </div>
                        </div>
                    </div>
                    <div class="row" id="instructor-fields">
                        <div class="col-12">
                            <div class="mb-3">
                                <label for="room_number" class="form-label">Room Number</label>
                                <input type="text" name="room_number" id="room_number" class="form-control" value="{{ old('room_number') }}" placeholder="e.g., Room 101, Lab 2">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="stat" class="form-label required-field">Status</label>
                        <select name="stat" id="stat" class="form-select" required>
                            <option value="1" {{ old('stat') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('stat') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Add User</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="fab-backdrop" id="fab-backdrop"></div>
    @endif
@endauth

  <!-- Scripts (local) - Critical -->
    <script src="{{ dynamic_asset('vendor/js/bootstrap.bundle.min.js') }}"></script>

    <!-- App Script -->
    <script src="{{ dynamic_asset('js/app.js') }}"></script>

    <!-- Utility Scripts -->
    <script src="{{ dynamic_asset('js/utils/dark-mode.js') }}"></script>

    <!-- Component Script -->
    <script src="{{ dynamic_asset('js/components/navbar.js') }}"></script>

    <!-- Deferred Scripts (non-critical) -->
    <script src="{{ dynamic_asset('vendor/js/gsap.min.js') }}" defer></script>
    <script src="{{ dynamic_asset('js/utils/dynamic-form.js') }}" defer></script>
    <script src="{{ dynamic_asset('js/functions/FAB.js') }}" defer></script>
  @yield('scripts')
  @stack('scripts')

  <!-- Global Error Popup Handler -->
  <link rel="stylesheet" href="{{ dynamic_asset('css/components/error-popup.css') }}">

  <div class="error-popup-overlay" id="errorPopupOverlay">
    <div class="error-popup">
      <div class="error-popup-header">
        <div class="error-popup-icon error" id="errorPopupIcon">
          <i class="fas fa-exclamation-circle"></i>
        </div>
        <h3 class="error-popup-title" id="errorPopupTitle">Error</h3>
      </div>
      <div class="error-popup-body" id="errorPopupBody">
        An error occurred.
      </div>
      <div class="error-popup-footer">
        <button class="error-popup-btn error-popup-btn-primary" id="errorPopupCloseBtn">OK</button>
      </div>
    </div>
  </div>

  <script src="{{ dynamic_asset('js/error-popup.js') }}"></script>
  <script>
    // Show server-side flash messages via error popup
    @if(session('error_popup'))
      window.showErrorPopup(
        @json(session('error_popup')),
        @json(session('error_code') ? 'Error ' . session('error_code') : 'Error'),
        'error'
      );
    @endif
    @if(session('error'))
      window.showErrorPopup(@json(session('error')), 'Error', 'error');
    @endif
    @if(session('success'))
      window.showErrorPopup(@json(session('success')), 'Success', 'info');
    @endif
    @if(session('warning'))
      window.showErrorPopup(@json(session('warning')), 'Warning', 'warning');
    @endif
    {{-- Debug info → console only (never visible in DOM) --}}
    @if(session('error_debug'))
      console.groupCollapsed('%c[JOMS Server Debug]%c Error details (not shown to user)',
        'color: #dc3545; font-weight: bold;', 'color: #6c757d;'
      );
      console.error(@json(session('error_debug')));
      console.log('Page:', @json(request()->fullUrl()));
      console.log('Time:', new Date().toISOString());
      console.groupEnd();
    @endif
  </script>

  <!-- PWA Service Worker Registration -->
  <script>
    // Service worker temporarily disabled for debugging
    // if ('serviceWorker' in navigator) {
    //     navigator.serviceWorker.register('/sw.js')
    //         .then(function(registration) {
    //             console.log('[SW] Service Worker registered:', registration.scope);
    //         })
    //         .catch(function(err) {
    //             console.log('[SW] Service Worker registration failed:', err);
    //         });
    // }

    // PWA Install Prompt Handler
    let deferredPrompt;
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        // Show install button if exists
        const installBtn = document.getElementById('pwa-install-btn');
        if (installBtn) {
            installBtn.style.display = 'block';
            installBtn.addEventListener('click', () => {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                    }
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                });
            });
        }
    });

    // Offline/Online status handlers
    window.addEventListener('online', () => {
        document.body.classList.remove('offline-mode');
    });

    window.addEventListener('offline', () => {
        document.body.classList.add('offline-mode');
    });

    // Module caching helper function
    window.cacheModuleForOffline = async function(moduleId, moduleUrl) {
        if (!navigator.serviceWorker.controller) {
            console.error('Service worker not ready');
            return { success: false, error: 'Service worker not ready' };
        }

        return new Promise((resolve) => {
            const messageChannel = new MessageChannel();
            messageChannel.port1.onmessage = (event) => {
                resolve(event.data);
            };

            // Collect URLs to cache for this module
            const urlsToCache = [
                moduleUrl,
                `/modules/${moduleId}`,
                `/modules/${moduleId}/download`
            ];

            navigator.serviceWorker.controller.postMessage(
                { type: 'CACHE_MODULE', moduleId, urls: urlsToCache },
                [messageChannel.port2]
            );
        });
    };
  </script>
  @livewireScripts
</body>
</html>