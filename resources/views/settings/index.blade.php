@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <!-- Settings Navigation -->
        <div class="col-lg-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush rounded">
                        <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="list">
                            <i class="fas fa-user me-2"></i> Profile
                        </a>
                        <a href="#password" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-lock me-2"></i> Password & Security
                        </a>
                        <a href="#notifications" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-bell me-2"></i> Notifications
                        </a>
                        <a href="#appearance" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-palette me-2"></i> Appearance
                        </a>
                        <a href="#privacy" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-shield-alt me-2"></i> Privacy
                        </a>
                        @if(Auth::user()->role === 'admin')
                        <a href="#system" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-cog me-2"></i> System Settings
                        </a>
                        @endif
                        <a href="#data" class="list-group-item list-group-item-action" data-bs-toggle="list">
                            <i class="fas fa-database me-2"></i> Data & Account
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Content -->
        <div class="col-lg-9">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="tab-content">
                <!-- Profile Settings -->
                <div class="tab-pane fade show active" id="profile">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>Profile Settings</h5>
                        </div>
                        <div class="card-body">
                            <!-- Profile Picture -->
                            <form action="{{ route('settings.profile-picture') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                                @csrf
                                <div class="d-flex align-items-center gap-4">
                                    <div class="position-relative">
                                        <img src="{{ $user->profile_image_url }}" alt="Profile" class="rounded-circle" width="100" height="100" id="profilePreview" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($user->initials) }}&background=007fc9&color=fff&size=120'">
                                        <label class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle p-2" style="cursor: pointer;">
                                            <i class="fas fa-camera"></i>
                                            <input type="file" name="profile_image" class="d-none" accept="image/*" id="profileImageInput">
                                        </label>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">Profile Picture</h6>
                                        <p class="text-muted small mb-2">JPG, PNG or GIF. Max 2MB.</p>
                                        <button type="submit" class="btn btn-sm btn-outline-primary">Upload Photo</button>
                                    </div>
                                </div>
                            </form>

                            <hr>

                            <!-- Profile Form -->
                            <form action="{{ route('settings.profile') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name</label>
                                        <input type="text" name="first_name" class="form-control" value="{{ $user->first_name }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" name="last_name" class="form-control" value="{{ $user->last_name }}" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email Address</label>
                                    <div class="input-group">
                                        <input type="email" name="email" class="form-control" value="{{ $user->email }}" required>
                                        @if($user->hasVerifiedEmail())
                                            <span class="input-group-text bg-success text-white">
                                                <i class="fas fa-check-circle me-1"></i> Verified
                                            </span>
                                        @else
                                            <span class="input-group-text bg-warning text-dark">
                                                <i class="fas fa-exclamation-circle me-1"></i> Unverified
                                            </span>
                                        @endif
                                    </div>
                                    @if(!$user->hasVerifiedEmail())
                                        <div class="mt-2">
                                            <form action="{{ route('settings.resend-verification') }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-envelope me-1"></i> Resend Verification Email
                                                </button>
                                            </form>
                                            <small class="text-muted d-block mt-1">
                                                Check your inbox and spam folder for the verification email.
                                            </small>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $user->phone ?? '' }}" placeholder="Optional">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bio</label>
                                    <textarea name="bio" class="form-control" rows="3" placeholder="Tell us about yourself...">{{ $user->bio ?? '' }}</textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password & Security -->
                <div class="tab-pane fade" id="password">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-lock me-2 text-primary"></i>Password & Security</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.password') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="current_password" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" required minlength="8">
                                    <small class="text-muted">Minimum 8 characters</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-1"></i> Change Password
                                </button>
                            </form>

                            <hr class="my-4">

                            <h6 class="mb-3">Login Sessions</h6>
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                You are currently logged in on this device.
                                <br>
                                <small>Last login: {{ $user->last_login ? $user->last_login->diffForHumans() : 'N/A' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="tab-pane fade" id="notifications">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-bell me-2 text-primary"></i>Notification Preferences</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.notifications') }}" method="POST">
                                @csrf
                                <h6 class="text-muted mb-3">Email Notifications</h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_announcements" id="emailAnnouncements"
                                           {{ $settings['notifications']['email_announcements'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emailAnnouncements">
                                        <strong>Announcements</strong>
                                        <br><small class="text-muted">Receive email when new announcements are posted</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_grades" id="emailGrades"
                                           {{ $settings['notifications']['email_grades'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emailGrades">
                                        <strong>Grade Updates</strong>
                                        <br><small class="text-muted">Receive email when grades are posted</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="email_reminders" id="emailReminders"
                                           {{ $settings['notifications']['email_reminders'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="emailReminders">
                                        <strong>Deadline Reminders</strong>
                                        <br><small class="text-muted">Receive reminders for upcoming deadlines</small>
                                    </label>
                                </div>

                                <hr>

                                <h6 class="text-muted mb-3">Push Notifications</h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="push_enabled" id="pushEnabled"
                                           {{ $settings['notifications']['push_enabled'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pushEnabled">
                                        <strong>Enable Push Notifications</strong>
                                        <br><small class="text-muted">Receive browser push notifications</small>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Preferences
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Appearance -->
                <div class="tab-pane fade" id="appearance">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-palette me-2 text-primary"></i>Appearance</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.appearance') }}" method="POST">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label">Theme</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" value="light" id="themeLight"
                                                   {{ ($settings['appearance']['theme'] ?? 'light') === 'light' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="themeLight">
                                                <i class="fas fa-sun me-1"></i> Light
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" value="dark" id="themeDark"
                                                   {{ ($settings['appearance']['theme'] ?? '') === 'dark' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="themeDark">
                                                <i class="fas fa-moon me-1"></i> Dark
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="theme" value="auto" id="themeAuto"
                                                   {{ ($settings['appearance']['theme'] ?? '') === 'auto' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="themeAuto">
                                                <i class="fas fa-adjust me-1"></i> Auto
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label">Font Size</label>
                                    <select name="font_size" class="form-select" style="max-width: 200px;">
                                        <option value="small" {{ ($settings['appearance']['font_size'] ?? '') === 'small' ? 'selected' : '' }}>Small</option>
                                        <option value="medium" {{ ($settings['appearance']['font_size'] ?? 'medium') === 'medium' ? 'selected' : '' }}>Medium (Default)</option>
                                        <option value="large" {{ ($settings['appearance']['font_size'] ?? '') === 'large' ? 'selected' : '' }}>Large</option>
                                    </select>
                                </div>

                                <div class="form-check form-switch mb-4">
                                    <input class="form-check-input" type="checkbox" name="sidebar_compact" id="sidebarCompact"
                                           {{ $settings['appearance']['sidebar_compact'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="sidebarCompact">
                                        <strong>Compact Sidebar</strong>
                                        <br><small class="text-muted">Use icons only in the sidebar</small>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Appearance
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Privacy -->
                <div class="tab-pane fade" id="privacy">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-shield-alt me-2 text-primary"></i>Privacy Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.privacy') }}" method="POST">
                                @csrf
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_profile" id="showProfile"
                                           {{ $settings['privacy']['show_profile'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showProfile">
                                        <strong>Show Profile to Others</strong>
                                        <br><small class="text-muted">Allow other users to view your profile</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_progress" id="showProgress"
                                           {{ $settings['privacy']['show_progress'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showProgress">
                                        <strong>Show Progress to Classmates</strong>
                                        <br><small class="text-muted">Allow classmates to see your course progress</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_leaderboard" id="showLeaderboard"
                                           {{ $settings['privacy']['show_leaderboard'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showLeaderboard">
                                        <strong>Appear on Leaderboard</strong>
                                        <br><small class="text-muted">Show your name on public leaderboards</small>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save Privacy Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- System Settings (Admin Only) -->
                @if(Auth::user()->role === 'admin')
                <div class="tab-pane fade" id="system">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-cog me-2 text-primary"></i>System Settings</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('settings.system') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="form-label">Site Name</label>
                                    <input type="text" name="site_name" class="form-control"
                                           value="{{ $systemSettings['site_name'] ?? 'EPAS-E Learning Management System' }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Default Passing Score (%)</label>
                                    <input type="number" name="passing_score" class="form-control" style="max-width: 150px;"
                                           value="{{ $systemSettings['passing_score'] ?? 75 }}" min="50" max="100">
                                </div>

                                <hr>

                                <h6 class="text-muted mb-3">System Toggles</h6>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="registration_enabled" id="registrationEnabled"
                                           {{ $systemSettings['registration_enabled'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="registrationEnabled">
                                        <strong>Enable Registration</strong>
                                        <br><small class="text-muted">Allow new users to register</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="require_approval" id="requireApproval"
                                           {{ $systemSettings['require_approval'] ?? true ? 'checked' : '' }}>
                                    <label class="form-check-label" for="requireApproval">
                                        <strong>Require Admin Approval</strong>
                                        <br><small class="text-muted">New registrations require admin approval</small>
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenanceMode"
                                           {{ $systemSettings['maintenance_mode'] ?? false ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenanceMode">
                                        <strong class="text-danger">Maintenance Mode</strong>
                                        <br><small class="text-muted">Put site in maintenance mode (only admins can access)</small>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Save System Settings
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Data & Account -->
                <div class="tab-pane fade" id="data">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0"><i class="fas fa-database me-2 text-primary"></i>Your Data</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Download a copy of your data in JSON format.</p>
                            <a href="{{ route('settings.export') }}" class="btn btn-outline-primary">
                                <i class="fas fa-download me-1"></i> Download My Data
                            </a>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm border-danger">
                        <div class="card-header bg-white border-0 py-3">
                            <h5 class="mb-0 text-danger"><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Once you delete your account, there is no going back. Please be certain.</p>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                                <i class="fas fa-trash me-1"></i> Delete My Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Delete Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('settings.delete-account') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> This action cannot be undone. All your data will be permanently deleted.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Type DELETE to confirm</label>
                        <input type="text" name="confirmation" class="form-control" placeholder="DELETE" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Enter your password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete My Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Profile image preview
    const profileImageInput = document.getElementById('profileImageInput');
    const profilePreview = document.getElementById('profilePreview');

    if (profileImageInput) {
        profileImageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Hash navigation for tabs
    const hash = window.location.hash;
    if (hash) {
        const tab = document.querySelector(`a[href="${hash}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }

    // Update hash on tab change
    document.querySelectorAll('[data-bs-toggle="list"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(e) {
            history.replaceState(null, null, e.target.getAttribute('href'));
        });
    });
});
</script>
@endsection
