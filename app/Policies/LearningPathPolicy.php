<?php

namespace App\Policies;

use App\Models\LearningPath;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LearningPathPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any learning paths.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the learning path.
     */
    public function view(User $user, LearningPath $learningPath): bool
    {
        if ($learningPath->is_published) {
            return true;
        }

        return $user->id === $learningPath->created_by ||
               in_array(strtolower($user->role), ['admin']);
    }

    /**
     * Determine whether the user can create learning paths.
     */
    public function create(User $user): bool
    {
        return in_array(strtolower($user->role), ['admin', 'instructor']);
    }

    /**
     * Determine whether the user can update the learning path.
     */
    public function update(User $user, LearningPath $learningPath): bool
    {
        if (strtolower($user->role) === 'admin') {
            return true;
        }

        return $user->id === $learningPath->created_by;
    }

    /**
     * Determine whether the user can delete the learning path.
     */
    public function delete(User $user, LearningPath $learningPath): bool
    {
        if (strtolower($user->role) === 'admin') {
            return true;
        }

        return $user->id === $learningPath->created_by;
    }

    /**
     * Determine whether the user can enroll in the learning path.
     */
    public function enroll(User $user, LearningPath $learningPath): bool
    {
        return $learningPath->is_published && !$learningPath->isEnrolledBy($user);
    }
}
