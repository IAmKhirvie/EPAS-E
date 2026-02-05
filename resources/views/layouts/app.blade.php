<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>@yield('title','EPAS-E - Electronic Products Assembly and Servicing')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ dynamic_asset('favicon.ico') }}">

    <!-- PWA Support -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0d6efd">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="EPAS-E">
    <link rel="apple-touch-icon" href="/images/icons/icon-192x192.png">

    <!-- CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <link rel="stylesheet" href="{{ dynamic_asset('css/app.css') }}">
    
    <!-- Base CSS-->
    <link rel="stylesheet" href="{{ dynamic_asset('css/base/reset.css') }}">
    
    <!-- Component CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/adduser.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/alerts.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/fab.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/forms.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/overlay.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/tables.css') }}">
    
    <!-- Layout CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/header.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/main-content.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/sidebar.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/layout/footer.css') }}">

    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/modules.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/users.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/index.css')}}">

    <!-- Mobile CSS -->
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/responsive-tables.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/components/touch-friendly.css') }}">
    <link rel="stylesheet" href="{{ dynamic_asset('css/pages/mobile.css') }}"  media="screen and (max-width: 1032px)">
    <script>
        // Immediately check and apply dark mode before page renders
        (function() {
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
    @stack('styles')
</head>
<body class="modern-layout">

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

                <button class="fab-option" id="fabOptionEnroll">
                    <i class="fas fa-user-plus"></i>
                    <span class="fab-label">Enroll User</span>
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
                        <label for="module_number" class="form-label required-field">Module Number</label>
                        <input type="text" name="module_number" id="module_number" 
                               class="form-control" value="{{ old('module_number') }}" placeholder="e.g., Module 1" required>
                    </div>

                    <div class="mb-3">
                        <label for="module_name" class="form-label required-field">Module Name</label>
                        <input type="text" name="module_name" id="module_name" 
                               class="form-control" value="{{ old('module_name', 'Competency based learning material') }}" required>
                    </div>

                    <div class="mb-3">
                        <label for="table_of_contents" class="form-label">Table of Contents</label>
                        <textarea name="table_of_contents" id="table_of_contents" 
                                  class="form-control" rows="4" placeholder="Enter the table of contents with page numbers...">{{ old('table_of_contents') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="how_to_use_cblm" class="form-label">How to Use CBLM</label>
                        <textarea name="how_to_use_cblm" id="how_to_use_cblm" 
                                  class="form-control" rows="3">{{ old('how_to_use_cblm') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="introduction" class="form-label">Introduction</label>
                        <textarea name="introduction" id="introduction" 
                                  class="form-control" rows="3">{{ old('introduction') }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label for="learning_outcomes" class="form-label">Learning Outcomes</label>
                        <textarea name="learning_outcomes" id="learning_outcomes" 
                                  class="form-control" rows="3">{{ old('learning_outcomes') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Create Module</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Universal Slide-in Sidebar for Add User -->
        @isset($departments)
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
                                    <label for="student_id" class="form-label required-field">student ID</label>
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

                        <!-- New Section and Room Number Fields -->
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Section (For Students)</label>
                                    <input type="text" name="section" id="section" class="form-control" value="{{ old('section') }}" placeholder="e.g., Section A, Grade 11">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="room_number" class="form-label">Room Number (For Instructors)</label>
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
        @endisset
    @endif
@endauth

  <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <!-- App Script -->
    <script src="{{ dynamic_asset('js/app.js') }}"></script>

    <!-- Utility Scripts -->
    <script src="{{ dynamic_asset('js/utils/dynamic-form.js') }}"></script>

    <!-- Component Script -->
    <script src="{{ dynamic_asset('js/components/navbar.js') }}"></script>

    <!-- Enhanced FAB Script -->
    <script src="{{ dynamic_asset('js/functions/FAB.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function() {
            navigator.serviceWorker.register('/sw.js')
                .then(function(registration) {
                    console.log('ServiceWorker registration successful');

                    // Check for updates and activate new service worker immediately
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                // New service worker is available, activate it immediately
                                console.log('New service worker available, activating...');
                                newWorker.postMessage({ type: 'SKIP_WAITING' });
                                // Reload the page to use the new service worker
                                window.location.reload();
                            }
                        });
                    });
                })
                .catch(function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });

        // Listen for controlling service worker changes
        navigator.serviceWorker.addEventListener('controllerchange', () => {
            console.log('Service worker controller changed, reloading page...');
            window.location.reload();
        });
    }

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
                        console.log('User accepted the install prompt');
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
        console.log('Back online');
    });

    window.addEventListener('offline', () => {
        document.body.classList.add('offline-mode');
        console.log('You are offline');
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
</body>
</html>