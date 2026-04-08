<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\Badge;
use App\Models\User;
use App\Models\UserBadge;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BadgeController extends Controller
{
    /**
     * Badge management index (admin only).
     */
    public function index()
    {
        $badges = Badge::withCount('users')
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Store a new badge.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:achievement,milestone,streak,special',
            'points_required' => 'nullable|integer|min:0',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = true;
        $validated['order'] = Badge::max('order') + 1;
        $validated['points_required'] = $validated['points_required'] ?? 0;

        Badge::create($validated);

        return back()->with('success', "Badge \"{$validated['name']}\" created successfully!");
    }

    /**
     * Update a badge.
     */
    public function update(Request $request, Badge $badge)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'icon' => 'required|string|max:100',
            'color' => 'required|string|max:20',
            'type' => 'required|string|in:achievement,milestone,streak,special',
            'points_required' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['points_required'] = $validated['points_required'] ?? 0;
        $validated['is_active'] = $request->boolean('is_active', true);

        $badge->update($validated);

        return back()->with('success', "Badge \"{$badge->name}\" updated successfully!");
    }

    /**
     * Delete a badge.
     */
    public function destroy(Badge $badge)
    {
        $name = $badge->name;
        $badge->delete();

        return back()->with('success', "Badge \"{$name}\" deleted.");
    }

    /**
     * Manually award a badge to a student (from user management).
     */
    public function awardToUser(Request $request, User $user)
    {
        $request->validate([
            'badge_id' => 'required|exists:badges,id',
        ]);

        $badge = Badge::findOrFail($request->badge_id);

        // Check if already awarded
        $existing = UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->first();

        if ($existing) {
            return back()->with('error', "{$user->full_name} already has the \"{$badge->name}\" badge.");
        }

        app(GamificationService::class)->awardBadge($user, $badge);

        return back()->with('success', "Badge \"{$badge->name}\" awarded to {$user->full_name}!");
    }

    /**
     * Revoke a badge from a student.
     */
    public function revokeFromUser(User $user, Badge $badge)
    {
        UserBadge::where('user_id', $user->id)
            ->where('badge_id', $badge->id)
            ->delete();

        return back()->with('success', "Badge \"{$badge->name}\" revoked from {$user->full_name}.");
    }
}
