<?php

namespace App\Services;

use App\Models\Module;
use App\Models\ModulePrerequisite;
use App\Models\User;
use Illuminate\Support\Collection;

class PrerequisiteService
{
    /**
     * Check if a user can access a module (all prerequisites met).
     */
    public function canAccessModule(User $user, Module $module): bool
    {
        $prerequisites = $module->prerequisites;

        if ($prerequisites->isEmpty()) {
            return true;
        }

        foreach ($prerequisites as $prerequisite) {
            if ($prerequisite->is_mandatory && !$prerequisite->isSatisfiedBy($user)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get unmet prerequisites for a user and module.
     */
    public function getUnmetPrerequisites(User $user, Module $module): Collection
    {
        return $module->prerequisites->filter(function ($prerequisite) use ($user) {
            return !$prerequisite->isSatisfiedBy($user);
        });
    }

    /**
     * Get met prerequisites for a user and module.
     */
    public function getMetPrerequisites(User $user, Module $module): Collection
    {
        return $module->prerequisites->filter(function ($prerequisite) use ($user) {
            return $prerequisite->isSatisfiedBy($user);
        });
    }

    /**
     * Get prerequisite progress for a user and module.
     */
    public function getPrerequisiteProgress(User $user, Module $module): array
    {
        $prerequisites = $module->prerequisites;
        $total = $prerequisites->count();
        $met = $prerequisites->filter(fn($p) => $p->isSatisfiedBy($user))->count();

        return [
            'total' => $total,
            'met' => $met,
            'unmet' => $total - $met,
            'percentage' => $total > 0 ? round(($met / $total) * 100, 1) : 100,
            'can_access' => $this->canAccessModule($user, $module),
        ];
    }

    /**
     * Add a prerequisite to a module.
     */
    public function addPrerequisite(
        Module $module,
        Module $prerequisiteModule,
        string $requirementType = 'completion',
        ?float $minimumScore = null,
        bool $isMandatory = true
    ): ModulePrerequisite {
        // Prevent circular dependencies
        if ($this->wouldCreateCircularDependency($module, $prerequisiteModule)) {
            throw new \InvalidArgumentException('This would create a circular dependency.');
        }

        return ModulePrerequisite::create([
            'module_id' => $module->id,
            'prerequisite_module_id' => $prerequisiteModule->id,
            'requirement_type' => $requirementType,
            'minimum_score' => $minimumScore,
            'is_mandatory' => $isMandatory,
        ]);
    }

    /**
     * Remove a prerequisite from a module.
     */
    public function removePrerequisite(Module $module, Module $prerequisiteModule): bool
    {
        return ModulePrerequisite::where('module_id', $module->id)
            ->where('prerequisite_module_id', $prerequisiteModule->id)
            ->delete() > 0;
    }

    /**
     * Check if adding a prerequisite would create a circular dependency.
     */
    public function wouldCreateCircularDependency(Module $module, Module $prerequisiteModule): bool
    {
        // If the prerequisite module requires the original module, it's circular
        if ($prerequisiteModule->prerequisites->contains('prerequisite_module_id', $module->id)) {
            return true;
        }

        // Check deeper dependencies
        return $this->hasIndirectDependency($prerequisiteModule, $module->id, []);
    }

    /**
     * Check for indirect dependencies recursively.
     */
    protected function hasIndirectDependency(Module $module, int $targetId, array $visited): bool
    {
        if (in_array($module->id, $visited)) {
            return false;
        }

        $visited[] = $module->id;

        foreach ($module->prerequisites as $prerequisite) {
            if ($prerequisite->prerequisite_module_id === $targetId) {
                return true;
            }

            $prereqModule = $prerequisite->prerequisiteModule;
            if ($this->hasIndirectDependency($prereqModule, $targetId, $visited)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all available modules for a user (prerequisites met).
     */
    public function getAvailableModules(User $user): Collection
    {
        return Module::where('is_active', true)->get()->filter(function ($module) use ($user) {
            return $this->canAccessModule($user, $module);
        });
    }

    /**
     * Get locked modules for a user (prerequisites not met).
     */
    public function getLockedModules(User $user): Collection
    {
        return Module::where('is_active', true)->get()->filter(function ($module) use ($user) {
            return !$this->canAccessModule($user, $module);
        });
    }

    /**
     * Get suggested next modules for a user.
     */
    public function getSuggestedNextModules(User $user, int $limit = 5): Collection
    {
        $completedModuleIds = $user->completedModules()->pluck('module_id');

        return Module::where('is_active', true)
            ->whereNotIn('id', $completedModuleIds)
            ->get()
            ->filter(function ($module) use ($user) {
                return $this->canAccessModule($user, $module);
            })
            ->take($limit);
    }
}
