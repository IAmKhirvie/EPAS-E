<?php

namespace App\Services;

use App\Models\Module;
use App\Models\InformationSheet;
use App\Models\SelfCheck;
use App\Models\Topic;
use App\Models\UserProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModuleService
{
    /**
     * Track topic progress for the authenticated user.
     */
    public function trackTopicProgress(Topic $topic): void
    {
        if (!$topic->informationSheet || !$topic->informationSheet->module_id) {
            return;
        }

        UserProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'module_id' => $topic->informationSheet->module_id,
                'progressable_type' => Topic::class,
                'progressable_id' => $topic->id
            ],
            [
                'status' => 'completed',
                'completed_at' => now()
            ]
        );

        UserProgress::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'module_id' => $topic->informationSheet->module_id,
                'progressable_type' => InformationSheet::class,
                'progressable_id' => $topic->informationSheet->id
            ],
            [
                'status' => 'in_progress',
                'started_at' => now()
            ]
        );
    }

    /**
     * Calculate score for a self-check submission.
     */
    public function calculateScore(SelfCheck $selfCheck, array $answers): int
    {
        $correctAnswers = json_decode($selfCheck->answer_key, true) ?? [];
        $score = 0;

        foreach ($answers as $questionId => $userAnswer) {
            if (isset($correctAnswers[$questionId])) {
                $correctAnswer = $correctAnswers[$questionId];

                if (is_array($correctAnswer)) {
                    if (in_array($userAnswer, $correctAnswer)) {
                        $score++;
                    }
                } else {
                    if ($correctAnswer === $userAnswer) {
                        $score++;
                    }
                }
            }
        }

        return $score;
    }

    /**
     * Get maximum possible score for a self-check.
     */
    public function getMaxScore(SelfCheck $selfCheck): int
    {
        $correctAnswers = json_decode($selfCheck->answer_key, true) ?? [];
        return count($correctAnswers);
    }

    /**
     * Get module progress for a user.
     */
    public function getProgress(Module $module, int $userId): array
    {
        $progress = UserProgress::where('user_id', $userId)
            ->where('module_id', $module->id)
            ->get();

        $completedSheets = $progress->where('progressable_type', InformationSheet::class)
            ->where('status', 'completed')->count();

        $totalSheets = $module->informationSheets()->count();
        $progressPercentage = $totalSheets > 0 ? ($completedSheets / $totalSheets) * 100 : 0;

        return [
            'overall_progress' => $progressPercentage,
            'completed_sheets' => $completedSheets,
            'total_sheets' => $totalSheets,
        ];
    }

    /**
     * Upload an image for a module.
     */
    public function uploadImage(Module $module, $imageFile, ?string $caption = null, ?string $section = null): array
    {
        $imageName = 'module_' . $module->id . '_' . time() . '.' . $imageFile->extension();
        $imageFile->storeAs('public/module-images', $imageName);

        $images = $module->images ?? [];
        $images[] = [
            'filename' => $imageName,
            'url' => asset('storage/module-images/' . $imageName),
            'caption' => $caption,
            'section' => $section ?? 'overview',
            'uploaded_at' => now()->toIso8601String(),
        ];

        $module->update(['images' => $images]);

        return $images;
    }

    /**
     * Delete an image from a module.
     */
    public function deleteImage(Module $module, int $imageIndex): void
    {
        $images = $module->images ?? [];

        if (isset($images[$imageIndex])) {
            $filename = $images[$imageIndex]['filename'] ?? null;
            if ($filename) {
                Storage::delete('public/module-images/' . $filename);
            }
            array_splice($images, $imageIndex, 1);
            $module->update(['images' => $images]);
        }
    }
}
