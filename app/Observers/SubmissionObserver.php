<?php

namespace App\Observers;

use App\Services\GradingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Observes all submission models to auto-invalidate grade caches.
 *
 * Applied to: SelfCheckSubmission, HomeworkSubmission,
 *             TaskSheetSubmission, JobSheetSubmission
 */
class SubmissionObserver
{
    public function created(Model $submission): void
    {
        $this->invalidateCaches($submission);
    }

    public function updated(Model $submission): void
    {
        $this->invalidateCaches($submission);
    }

    public function deleted(Model $submission): void
    {
        $this->invalidateCaches($submission);
    }

    protected function invalidateCaches(Model $submission): void
    {
        $userId = $submission->user_id;
        $moduleId = $this->resolveModuleId($submission);

        if ($userId && $moduleId) {
            Cache::forget("module_grade_{$userId}_{$moduleId}");
            Cache::forget("module_ranking_{$userId}_{$moduleId}");
        }
    }

    protected function resolveModuleId(Model $submission): ?int
    {
        // Each submission type links to a parent that belongs to an InformationSheet → Module
        $parent = match (true) {
            method_exists($submission, 'selfCheck') => $submission->selfCheck,
            method_exists($submission, 'homework') => $submission->homework,
            method_exists($submission, 'taskSheet') => $submission->taskSheet,
            method_exists($submission, 'jobSheet') => $submission->jobSheet,
            default => null,
        };

        if (!$parent) {
            return null;
        }

        $sheet = $parent->informationSheet ?? null;

        return $sheet?->module_id;
    }
}
