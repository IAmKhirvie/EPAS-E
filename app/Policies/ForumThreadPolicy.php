<?php

namespace App\Policies;

use App\Constants\Roles;
use App\Models\ForumThread;
use App\Models\User;

/**
 * Authorization policy for ForumThread model.
 *
 * Thread creation respects category permissions (admin_only_post).
 * Admin and instructor can pin, lock, and delete any thread.
 * The thread author or admin can edit a thread.
 *
 * - Admin: full access.
 * - Instructor: can create, pin, lock, delete any thread; can edit own threads.
 * - Student: can create threads (in allowed categories), edit own threads, view all.
 */
class ForumThreadPolicy
{
    /**
     * Determine whether the user can view any threads.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the thread.
     */
    public function view(User $user, ForumThread $thread): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create threads.
     * All authenticated users can create threads, but category-level
     * restrictions (admin_only_post) should be checked separately
     * when a specific category is involved.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the thread.
     * Admin or instructor can update any. Author can update their own.
     */
    public function update(User $user, ForumThread $thread): bool
    {
        if (in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR])) {
            return true;
        }

        return $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the thread.
     * Admin can delete any thread. Author can delete their own.
     */
    public function delete(User $user, ForumThread $thread): bool
    {
        if ($user->role === Roles::ADMIN) {
            return true;
        }

        return $thread->user_id === $user->id;
    }

    /**
     * Determine whether the user can pin the thread.
     * Only admin or instructor can pin/unpin threads.
     */
    public function pin(User $user, ForumThread $thread): bool
    {
        return in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR]);
    }

    /**
     * Determine whether the user can lock the thread.
     * Only admin or instructor can lock/unlock threads.
     */
    public function lock(User $user, ForumThread $thread): bool
    {
        return in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR]);
    }

    /**
     * Determine whether the user can reply to the thread.
     * All authenticated users can reply, unless the thread is locked.
     * Admin and instructor can still reply to locked threads.
     */
    public function reply(User $user, ForumThread $thread): bool
    {
        if (in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR])) {
            return true;
        }

        return !$thread->is_locked;
    }

    /**
     * Determine whether the user can restore the thread.
     */
    public function restore(User $user, ForumThread $thread): bool
    {
        return in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR]);
    }

    /**
     * Determine whether the user can permanently delete the thread.
     */
    public function forceDelete(User $user, ForumThread $thread): bool
    {
        return $user->role === Roles::ADMIN;
    }
}
