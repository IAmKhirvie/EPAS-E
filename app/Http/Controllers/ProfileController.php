<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'student_id' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        
        try {
            if ($request->has('student_id')) {
                $user->student_id = $request->student_id;
            }

            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->profile_image) {
                    Storage::delete('public/profile-images/' . $user->profile_image);
                }

                // Store new avatar
                $avatarPath = $request->file('avatar')->store('profile-images', 'public');
                $user->profile_image = basename($avatarPath);
            }

            $user->save();

            return back()->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return back()->with('error', 'Failed to update profile. Please try again.');
        }
    }
    
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $user = Auth::user();

        try {
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->profile_image) {
                    Storage::delete('public/profile-images/' . $user->profile_image);
                }

                // Store new avatar
                $avatarPath = $request->file('avatar')->store('profile-images', 'public');
                $user->profile_image = basename($avatarPath);
                $user->save();
            }

            return back()->with('success', 'Profile picture updated successfully!');
        } catch (\Exception $e) {
            Log::error('Avatar update failed', ['error' => $e->getMessage(), 'user_id' => Auth::id()]);
            return back()->with('error', 'Failed to update profile picture. Please try again.');
        }
    }
}