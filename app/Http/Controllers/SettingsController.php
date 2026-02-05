<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use App\Models\Setting;
use App\Services\PHPMailerService;

class SettingsController extends Controller
{
    /**
     * Hours to wait before email can be changed again.
     * Configured via config/joms.php → auth.email_change_cooldown_hours
     */

    /**
     * Display the settings page
     */
    public function index()
    {
        $user = Auth::user();
        $settings = $this->getUserSettings($user);
        $systemSettings = $this->getSystemSettings();

        return view('settings.index', compact('user', 'settings', 'systemSettings'));
    }

    /**
     * Resend email verification
     */
    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->back()->with('success', 'Your email is already verified.');
        }

        try {
            $mailer = new PHPMailerService();

            $verificationUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $user->getKey(),
                    'hash' => sha1($user->getEmailForVerification()),
                ]
            );

            $result = $mailer->sendVerificationEmail($user, $verificationUrl);

            if ($result) {
                Log::info("Verification email resent to: {$user->email}");
                return redirect()->back()->with('success', 'Verification email sent! Please check your inbox.');
            } else {
                Log::error("Failed to resend verification email to: {$user->email}");
                return redirect()->back()->withErrors(['email' => 'Failed to send verification email. Please try again.']);
            }
        } catch (\Exception $e) {
            Log::error("Exception resending verification email: " . $e->getMessage());
            return redirect()->back()->withErrors(['email' => 'An error occurred. Please try again.']);
        }
    }

    /**
     * Update profile settings
     */
    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $validated = $request->validated();

        $oldEmail = $user->email;
        $newEmail = $request->email;
        $emailChanged = $oldEmail !== $newEmail;

        // Check cooldown if email is being changed
        if ($emailChanged && $user->email_changed_at) {
            $cooldownHours = config('joms.auth.email_change_cooldown_hours', 24);
            $hoursSinceLastChange = now()->diffInHours($user->email_changed_at);
            $hoursRemaining = $cooldownHours - $hoursSinceLastChange;

            if ($hoursRemaining > 0) {
                return redirect()->back()
                    ->withErrors(['email' => "You can only change your email once every {$cooldownHours} hours. Please wait {$hoursRemaining} more hour(s)."])
                    ->withInput();
            }
        }

        // Update user data (without email first if it changed)
        $user->update($request->only(['first_name', 'last_name', 'phone', 'bio']));

        // If email changed, require re-verification
        if ($emailChanged) {
            $user->update([
                'email' => $newEmail,
                'email_verified_at' => null,
                'email_changed_at' => now(),
            ]);

            // Send verification email to new address
            try {
                $mailer = new PHPMailerService();
                $verificationUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(60),
                    [
                        'id' => $user->getKey(),
                        'hash' => sha1($user->getEmailForVerification()),
                    ]
                );

                $mailer->sendVerificationEmail($user, $verificationUrl);
                Log::info("Verification email sent to new address: {$newEmail}");

                return redirect()->back()->with('success', 'Profile updated! Please verify your new email address. Check your inbox for the verification link.');
            } catch (\Exception $e) {
                Log::error("Failed to send verification email after email change: " . $e->getMessage());
                return redirect()->back()->with('success', 'Profile updated! Please verify your new email address.')->with('warning', 'Could not send verification email automatically. Please use the resend option.');
            }
        }

        return redirect()->back()->with('success', 'Profile updated successfully!');
    }

    /**
     * Update profile picture
     */
    public function updateProfilePicture(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Delete old image if exists
        if ($user->profile_image) {
            Storage::delete('public/profile-images/' . $user->profile_image);
        }

        // Store new image
        $imageName = time() . '_' . $user->id . '.' . $request->profile_image->extension();
        $request->profile_image->storeAs('public/profile-images', $imageName);

        $user->update(['profile_image' => $imageName]);

        return redirect()->back()->with('success', 'Profile picture updated successfully!');
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        try {
            $user->update(['password' => Hash::make($request->password)]);

            return redirect()->back()->with('success', 'Password changed successfully!');
        } catch (\Exception $e) {
            Log::error('Password update failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Update notification preferences
     */
    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $notifications = [
            'email_announcements' => $request->has('email_announcements'),
            'email_grades' => $request->has('email_grades'),
            'email_reminders' => $request->has('email_reminders'),
            'push_enabled' => $request->has('push_enabled'),
        ];

        $this->saveUserSetting($user, 'notifications', json_encode($notifications));

        return redirect()->back()->with('success', 'Notification preferences updated!');
    }

    /**
     * Update appearance settings
     */
    public function updateAppearance(Request $request)
    {
        $user = Auth::user();

        $appearance = [
            'theme' => $request->input('theme', 'light'),
            'sidebar_compact' => $request->has('sidebar_compact'),
            'font_size' => $request->input('font_size', 'medium'),
        ];

        $this->saveUserSetting($user, 'appearance', json_encode($appearance));

        return redirect()->back()->with('success', 'Appearance settings updated!');
    }

    /**
     * Update privacy settings
     */
    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();

        $privacy = [
            'show_profile' => $request->has('show_profile'),
            'show_progress' => $request->has('show_progress'),
            'show_leaderboard' => $request->has('show_leaderboard'),
        ];

        $this->saveUserSetting($user, 'privacy', json_encode($privacy));

        return redirect()->back()->with('success', 'Privacy settings updated!');
    }

    /**
     * Admin: Update system settings
     */
    public function updateSystem(Request $request)
    {
        if (Auth::user()->role !== Roles::ADMIN) {
            abort(403);
        }

        $request->validate([
            'site_name' => 'required|string|max:255',
            'maintenance_mode' => 'boolean',
            'registration_enabled' => 'boolean',
            'require_approval' => 'boolean',
            'passing_score' => 'required|integer|min:50|max:100',
        ]);

        $this->saveSystemSetting('site_name', $request->site_name);
        $this->saveSystemSetting('maintenance_mode', $request->has('maintenance_mode') ? '1' : '0');
        $this->saveSystemSetting('registration_enabled', $request->has('registration_enabled') ? '1' : '0');
        $this->saveSystemSetting('require_approval', $request->has('require_approval') ? '1' : '0');
        $this->saveSystemSetting('passing_score', $request->passing_score);

        return redirect()->back()->with('success', 'System settings updated!');
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData()
    {
        $user = Auth::user();

        $data = [
            'profile' => $user->toArray(),
            'settings' => $this->getUserSettings($user),
            'exported_at' => now()->toIso8601String(),
        ];

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="my-data.json"');
    }

    /**
     * Delete account
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirmation' => 'required|in:DELETE',
            'password' => 'required',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->withErrors(['password' => 'Password is incorrect']);
        }

        try {
            // Log out first
            Auth::logout();

            // Soft delete or anonymize
            $user->update([
                'email' => 'deleted_' . $user->id . '@deleted.local',
                'first_name' => 'Deleted',
                'last_name' => 'User',
                'is_deleted' => true,
            ]);

            return redirect('/')->with('success', 'Your account has been deleted.');
        } catch (\Exception $e) {
            Log::error('Account deletion failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', 'Failed to delete account. Please try again.');
        }
    }

    // Helper methods
    private function getUserSettings($user)
    {
        $defaults = [
            'notifications' => [
                'email_announcements' => true,
                'email_grades' => true,
                'email_reminders' => true,
                'push_enabled' => false,
            ],
            'appearance' => [
                'theme' => 'light',
                'sidebar_compact' => false,
                'font_size' => 'medium',
            ],
            'privacy' => [
                'show_profile' => true,
                'show_progress' => true,
                'show_leaderboard' => true,
            ],
        ];

        // Try to get from database, fall back to defaults
        try {
            $stored = Setting::where('user_id', $user->id)->pluck('value', 'key')->toArray();

            foreach ($defaults as $key => $value) {
                if (isset($stored[$key])) {
                    $defaults[$key] = json_decode($stored[$key], true) ?? $value;
                }
            }
        } catch (\Exception $e) {
            // Use defaults if settings table doesn't exist
        }

        return $defaults;
    }

    private function saveUserSetting($user, $key, $value)
    {
        try {
            Setting::updateOrCreate(
                ['user_id' => $user->id, 'key' => $key],
                ['value' => $value]
            );
        } catch (\Exception $e) {
            // Silently fail if settings table doesn't exist
        }
    }

    private function getSystemSettings()
    {
        $defaults = [
            'site_name' => 'EPAS-E Learning Management System',
            'maintenance_mode' => false,
            'registration_enabled' => true,
            'require_approval' => true,
            'passing_score' => 75,
        ];

        try {
            $stored = Setting::whereNull('user_id')->pluck('value', 'key')->toArray();

            foreach ($defaults as $key => $value) {
                if (isset($stored[$key])) {
                    $defaults[$key] = is_bool($value) ? ($stored[$key] === '1') : $stored[$key];
                }
            }
        } catch (\Exception $e) {
            // Use defaults
        }

        return $defaults;
    }

    private function saveSystemSetting($key, $value)
    {
        try {
            Setting::updateOrCreate(
                ['user_id' => null, 'key' => $key],
                ['value' => $value]
            );
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}
