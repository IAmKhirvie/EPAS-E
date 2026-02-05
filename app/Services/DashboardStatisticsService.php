<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\Announcement;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\InformationSheet;
use App\Models\JobSheet;
use App\Models\JobSheetSubmission;
use App\Models\Module;
use App\Models\SelfCheck;
use App\Models\SelfCheckSubmission;
use App\Models\TaskSheet;
use App\Models\TaskSheetSubmission;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Provides dashboard statistics and data aggregation for all user roles.
 *
 * Extracted from DashboardController to keep controllers thin.
 * Handles student progress, instructor evaluations, and admin-level stats.
 */
class DashboardStatisticsService
{
    // =========================================================================
    // PUBLIC METHODS - Statistics
    // =========================================================================

    /**
     * Get statistics for admin/instructor dashboard.
     *
     * @param User $user
     * @return array
     */
    public function getAdminInstructorStats(User $user): array
    {
        return Cache::remember("dashboard_admin_stats_{$user->id}", 600, function () use ($user) {
            $isInstructor = $user->role === Roles::INSTRUCTOR;

            $totalStudents = $this->countStudents($user, $isInstructor);

            $totalInstructors = $user->role === Roles::ADMIN
                ? User::where('role', Roles::INSTRUCTOR)->where('stat', true)->count()
                : 0;

            $totalModules = $this->countModules($user, $isInstructor);
            $ongoingBatches = $this->countSections($user, $isInstructor);

            return [
                'totalStudents' => $totalStudents,
                'totalInstructors' => $totalInstructors,
                'totalModules' => $totalModules,
                'ongoingBatches' => $ongoingBatches,
            ];
        });
    }

    /**
     * Count students visible to the user.
     *
     * @param User $user
     * @param bool $isInstructor
     * @return int
     */
    public function countStudents(User $user, bool $isInstructor): int
    {
        $query = User::where('role', Roles::STUDENT)->where('stat', true);

        if ($isInstructor) {
            $sections = $user->getAllAccessibleSections();
            if ($sections->isNotEmpty()) {
                $query->whereIn('section', $sections);
            } else {
                return 0;
            }
        }

        return $query->count();
    }

    /**
     * Count modules visible to the user.
     *
     * @param User $user
     * @param bool $isInstructor
     * @return int
     */
    public function countModules(User $user, bool $isInstructor): int
    {
        $query = Module::where('is_active', true);

        if ($isInstructor) {
            $query->whereHas('course', function ($q) use ($user) {
                $q->where('instructor_id', $user->id);
            });
        }

        return $query->count();
    }

    /**
     * Count sections/batches visible to the user.
     *
     * @param User $user
     * @param bool $isInstructor
     * @return int
     */
    public function countSections(User $user, bool $isInstructor): int
    {
        if ($isInstructor) {
            return $user->getAllAccessibleSections()->count();
        }

        return User::where('role', Roles::STUDENT)
            ->whereNotNull('section')
            ->distinct('section')
            ->count('section');
    }

    /**
     * Get progress summary for a student.
     *
     * @param User $user
     * @return array
     */
    public function getProgressSummary(User $user): array
    {
        return Cache::remember("dashboard_progress_{$user->id}", 300, function () use ($user) {
            $completedModules = UserProgress::where('user_id', $user->id)
                ->where('progressable_type', Module::class)
                ->where('status', 'completed')
                ->count();

            $inProgressModules = UserProgress::where('user_id', $user->id)
                ->where('progressable_type', Module::class)
                ->where('status', 'in_progress')
                ->count();

            $totalModules = Module::where('is_active', true)->count();

            $averageScore = UserProgress::where('user_id', $user->id)
                ->whereNotNull('score')
                ->avg('score') ?? 0;

            return [
                'completed_modules' => $completedModules,
                'in_progress_modules' => $inProgressModules,
                'total_modules' => $totalModules,
                'average_score' => round($averageScore, 1)
            ];
        });
    }

    // =========================================================================
    // PUBLIC METHODS - Activities
    // =========================================================================

    /**
     * Get pending activities for a student.
     *
     * Returns unsubmitted self-checks, homeworks, task sheets, and job sheets.
     *
     * @param User $user
     * @return Collection
     */
    public function getPendingActivitiesForStudent(User $user): Collection
    {
        $pending = collect();

        $pending = $pending->merge($this->getPendingSelfChecks($user));
        $pending = $pending->merge($this->getPendingHomeworks($user));
        $pending = $pending->merge($this->getPendingTaskSheets($user));
        $pending = $pending->merge($this->getPendingJobSheets($user));

        return $pending->sortByDesc('created_at')->take(10)->values();
    }

    /**
     * Get completed activities for a student.
     *
     * @param User $user
     * @return Collection
     */
    public function getCompletedActivitiesForStudent(User $user): Collection
    {
        $completed = collect();

        $selfCheckSubmissions = SelfCheckSubmission::where('user_id', $user->id)
            ->with('selfCheck.informationSheet.module')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($s) => $this->formatCompletedSelfCheck($s));

        $completed = $completed->merge($selfCheckSubmissions);

        $homeworkSubmissions = HomeworkSubmission::where('user_id', $user->id)
            ->with('homework.informationSheet.module')
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($s) => $this->formatCompletedHomework($s));

        $completed = $completed->merge($homeworkSubmissions);

        return $completed->sortByDesc('completed_at')->take(5)->values();
    }

    /**
     * Get recent submissions for instructor review.
     *
     * @param User $user
     * @return Collection
     */
    public function getRecentSubmissionsForInstructor(User $user): Collection
    {
        $studentIds = $this->getStudentIdsForInstructor($user);
        $submissions = collect();

        $homeworkSubmissions = HomeworkSubmission::whereIn('user_id', $studentIds)
            ->whereNull('evaluated_at')
            ->with(['user', 'homework.informationSheet.module'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn($s) => $this->formatInstructorHomeworkSubmission($s));

        $submissions = $submissions->merge($homeworkSubmissions);

        $taskSheetSubmissions = TaskSheetSubmission::whereIn('user_id', $studentIds)
            ->with(['user', 'taskSheet.informationSheet.module'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn($s) => $this->formatInstructorTaskSheetSubmission($s));

        $submissions = $submissions->merge($taskSheetSubmissions);

        $jobSheetSubmissions = JobSheetSubmission::whereIn('user_id', $studentIds)
            ->whereNull('evaluated_at')
            ->with(['user', 'jobSheet.informationSheet.module'])
            ->latest('submitted_at')
            ->limit(5)
            ->get()
            ->map(fn($s) => $this->formatInstructorJobSheetSubmission($s));

        $submissions = $submissions->merge($jobSheetSubmissions);

        return $submissions->sortByDesc('submitted_at')->take(10)->values();
    }

    /**
     * Get count of pending evaluations for instructor.
     *
     * @param User $user
     * @return int
     */
    public function getPendingEvaluationsCount(User $user): int
    {
        return Cache::remember("dashboard_pending_evals_{$user->id}", 300, function () use ($user) {
            $studentIds = $this->getStudentIdsForInstructor($user);

            $homeworkCount = HomeworkSubmission::whereIn('user_id', $studentIds)
                ->whereNull('evaluated_at')
                ->count();

            $jobSheetCount = JobSheetSubmission::whereIn('user_id', $studentIds)
                ->whereNull('evaluated_at')
                ->count();

            return $homeworkCount + $jobSheetCount;
        });
    }

    // =========================================================================
    // PUBLIC METHODS - Utility Helpers
    // =========================================================================

    /**
     * Check if user is admin or instructor.
     *
     * @param User $user
     * @return bool
     */
    public function isAdminOrInstructor(User $user): bool
    {
        return in_array($user->role, [Roles::ADMIN, Roles::INSTRUCTOR]);
    }

    /**
     * Get student IDs visible to an instructor.
     *
     * @param User $user
     * @return Collection
     */
    public function getStudentIdsForInstructor(User $user): Collection
    {
        $query = User::where('role', Roles::STUDENT);

        if ($user->role === Roles::INSTRUCTOR) {
            $sections = $user->getAllAccessibleSections();
            if ($sections->isNotEmpty()) {
                $query->whereIn('section', $sections);
            } else {
                return collect();
            }
        }

        return $query->pluck('id');
    }

    /**
     * Get recent announcements.
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentAnnouncements(int $limit = 3): Collection
    {
        return Cache::remember("dashboard_announcements_{$limit}", 300, function () use ($limit) {
            return Announcement::with(['user', 'comments'])
                ->where(function ($query) {
                    $query->whereNull('publish_at')
                        ->orWhere('publish_at', '<=', now());
                })
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();
        });
    }

    /**
     * Get count of recent announcements.
     *
     * @return int
     */
    public function getRecentAnnouncementsCount(): int
    {
        return Cache::remember('dashboard_announcements_count', 300, function () {
            return Announcement::where(function ($query) {
                $query->whereNull('publish_at')
                    ->orWhere('publish_at', '<=', now());
            })->count();
        });
    }

    /**
     * Get recent activity for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getRecentActivity(User $user): Collection
    {
        return UserProgress::where('user_id', $user->id)
            ->with('progressable')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->map(fn($progress) => [
                'type' => $this->getActivityType($progress),
                'title' => $this->getActivityTitle($progress),
                'timestamp' => $progress->updated_at->toISOString()
            ]);
    }

    /**
     * Get module progress for a user.
     *
     * @param User $user
     * @return Collection
     */
    public function getModuleProgress(User $user): Collection
    {
        return Module::where('is_active', true)
            ->get()
            ->map(function ($module) use ($user) {
                $progress = $this->calculateModuleProgress($module, $user);
                return [
                    'id' => $module->id,
                    'name' => $module->module_name,
                    'progress' => $progress['percentage'],
                    'status' => $progress['status']
                ];
            })
            ->filter(fn($module) => $module['progress'] > 0)
            ->sortByDesc('progress')
            ->values();
    }

    /**
     * Calculate progress for a specific module.
     *
     * @param Module $module
     * @param User $user
     * @return array
     */
    public function calculateModuleProgress(Module $module, User $user): array
    {
        $totalSheets = $module->informationSheets->count();

        if ($totalSheets === 0) {
            return ['percentage' => 0, 'status' => 'Not Started'];
        }

        $completedSheets = UserProgress::where('user_id', $user->id)
            ->where('progressable_type', InformationSheet::class)
            ->whereIn('progressable_id', $module->informationSheets->pluck('id'))
            ->where('status', 'completed')
            ->count();

        $percentage = ($completedSheets / $totalSheets) * 100;

        $status = match (true) {
            $percentage === 0.0 => 'Not Started',
            $percentage === 100.0 => 'Completed',
            default => 'In Progress',
        };

        return [
            'percentage' => round($percentage),
            'status' => $status
        ];
    }

    /**
     * Format seconds into human-readable time string.
     *
     * @param int $seconds
     * @return string
     */
    public function formatTime(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }

    // =========================================================================
    // PROTECTED METHODS - Pending Activity Helpers
    // =========================================================================

    /**
     * Get pending self-checks for a student.
     *
     * @param User $user
     * @return Collection
     */
    protected function getPendingSelfChecks(User $user): Collection
    {
        $submittedIds = SelfCheckSubmission::where('user_id', $user->id)->pluck('self_check_id');

        return SelfCheck::whereNotIn('id', $submittedIds)
            ->with('informationSheet.module')
            ->get()
            ->map(fn($item) => [
                'type' => 'self_check',
                'icon' => 'fas fa-clipboard-check',
                'color' => '#0d6efd',
                'title' => $item->title,
                'subtitle' => $item->informationSheet?->module?->module_name ?? 'Unknown Module',
                'url' => route('self-checks.show', $item),
                'created_at' => $item->created_at,
            ]);
    }

    /**
     * Get pending homeworks for a student.
     *
     * @param User $user
     * @return Collection
     */
    protected function getPendingHomeworks(User $user): Collection
    {
        $submittedIds = HomeworkSubmission::where('user_id', $user->id)->pluck('homework_id');

        return Homework::whereNotIn('id', $submittedIds)
            ->with('informationSheet.module')
            ->get()
            ->map(fn($item) => [
                'type' => 'homework',
                'icon' => 'fas fa-home',
                'color' => '#198754',
                'title' => $item->title,
                'subtitle' => $item->informationSheet?->module?->module_name ?? 'Unknown Module',
                'url' => route('homeworks.show', $item),
                'created_at' => $item->created_at,
                'deadline' => $item->due_date,
            ]);
    }

    /**
     * Get pending task sheets for a student.
     *
     * @param User $user
     * @return Collection
     */
    protected function getPendingTaskSheets(User $user): Collection
    {
        $submittedIds = TaskSheetSubmission::where('user_id', $user->id)->pluck('task_sheet_id');

        return TaskSheet::whereNotIn('id', $submittedIds)
            ->with('informationSheet.module')
            ->get()
            ->map(fn($item) => [
                'type' => 'task_sheet',
                'icon' => 'fas fa-tasks',
                'color' => '#fd7e14',
                'title' => $item->title,
                'subtitle' => $item->informationSheet?->module?->module_name ?? 'Unknown Module',
                'url' => route('task-sheets.show', $item),
                'created_at' => $item->created_at,
            ]);
    }

    /**
     * Get pending job sheets for a student.
     *
     * @param User $user
     * @return Collection
     */
    protected function getPendingJobSheets(User $user): Collection
    {
        $submittedIds = JobSheetSubmission::where('user_id', $user->id)->pluck('job_sheet_id');

        return JobSheet::whereNotIn('id', $submittedIds)
            ->with('informationSheet.module')
            ->get()
            ->map(fn($item) => [
                'type' => 'job_sheet',
                'icon' => 'fas fa-briefcase',
                'color' => '#6f42c1',
                'title' => $item->title,
                'subtitle' => $item->informationSheet?->module?->module_name ?? 'Unknown Module',
                'url' => route('job-sheets.show', $item),
                'created_at' => $item->created_at,
            ]);
    }

    // =========================================================================
    // PROTECTED METHODS - Formatting Helpers
    // =========================================================================

    /**
     * Format completed self-check submission for display.
     *
     * @param SelfCheckSubmission $submission
     * @return array
     */
    protected function formatCompletedSelfCheck(SelfCheckSubmission $submission): array
    {
        return [
            'type' => 'self_check',
            'icon' => 'fas fa-clipboard-check',
            'color' => '#0d6efd',
            'title' => $submission->selfCheck?->title ?? 'Unknown',
            'subtitle' => $submission->selfCheck?->informationSheet?->module?->module_name ?? 'Unknown Module',
            'score' => $submission->percentage . '%',
            'passed' => $submission->passed,
            'completed_at' => $submission->completed_at ?? $submission->created_at,
        ];
    }

    /**
     * Format completed homework submission for display.
     *
     * @param HomeworkSubmission $submission
     * @return array
     */
    protected function formatCompletedHomework(HomeworkSubmission $submission): array
    {
        $passThreshold = config('joms.grading.homework_pass_threshold', 0.6);

        return [
            'type' => 'homework',
            'icon' => 'fas fa-home',
            'color' => '#198754',
            'title' => $submission->homework?->title ?? 'Unknown',
            'subtitle' => $submission->homework?->informationSheet?->module?->module_name ?? 'Unknown Module',
            'score' => $submission->score ? $submission->percentage . '%' : 'Pending evaluation',
            'passed' => $submission->score >= ($submission->max_points * $passThreshold),
            'completed_at' => $submission->submitted_at ?? $submission->created_at,
        ];
    }

    /**
     * Format homework submission for instructor view.
     *
     * @param HomeworkSubmission $submission
     * @return array
     */
    protected function formatInstructorHomeworkSubmission(HomeworkSubmission $submission): array
    {
        return [
            'type' => 'homework',
            'icon' => 'fas fa-home',
            'color' => '#198754',
            'student_name' => $submission->user?->full_name ?? 'Unknown',
            'student_avatar' => $submission->user?->profile_image_url,
            'title' => $submission->homework?->title ?? 'Unknown',
            'module' => $submission->homework?->informationSheet?->module?->module_name ?? 'Unknown',
            'submitted_at' => $submission->submitted_at,
            'status' => 'Pending Evaluation',
            'url' => route('homework-submissions.evaluate', $submission),
        ];
    }

    /**
     * Format task sheet submission for instructor view.
     *
     * @param TaskSheetSubmission $submission
     * @return array
     */
    protected function formatInstructorTaskSheetSubmission(TaskSheetSubmission $submission): array
    {
        return [
            'type' => 'task_sheet',
            'icon' => 'fas fa-tasks',
            'color' => '#fd7e14',
            'student_name' => $submission->user?->full_name ?? 'Unknown',
            'student_avatar' => $submission->user?->profile_image_url,
            'title' => $submission->taskSheet?->title ?? 'Unknown',
            'module' => $submission->taskSheet?->informationSheet?->module?->module_name ?? 'Unknown',
            'submitted_at' => $submission->submitted_at ?? $submission->created_at,
            'status' => 'Submitted',
            'url' => '#',
        ];
    }

    /**
     * Format job sheet submission for instructor view.
     *
     * @param JobSheetSubmission $submission
     * @return array
     */
    protected function formatInstructorJobSheetSubmission(JobSheetSubmission $submission): array
    {
        return [
            'type' => 'job_sheet',
            'icon' => 'fas fa-briefcase',
            'color' => '#6f42c1',
            'student_name' => $submission->user?->full_name ?? 'Unknown',
            'student_avatar' => $submission->user?->profile_image_url,
            'title' => $submission->jobSheet?->title ?? 'Unknown',
            'module' => $submission->jobSheet?->informationSheet?->module?->module_name ?? 'Unknown',
            'submitted_at' => $submission->submitted_at ?? $submission->created_at,
            'status' => 'Pending Evaluation',
            'url' => '#',
        ];
    }

    // =========================================================================
    // PROTECTED METHODS - Internal Helpers
    // =========================================================================

    /**
     * Get activity type string for a progress record.
     *
     * @param UserProgress $progress
     * @return string
     */
    protected function getActivityType(UserProgress $progress): string
    {
        return match ($progress->progressable_type) {
            Module::class => 'module_completed',
            InformationSheet::class => 'sheet_completed',
            default => match ($progress->status) {
                'passed' => 'quiz_passed',
                'failed' => 'quiz_failed',
                default => 'started',
            },
        };
    }

    /**
     * Get activity title for a progress record.
     *
     * @param UserProgress $progress
     * @return string
     */
    protected function getActivityTitle(UserProgress $progress): string
    {
        $progressable = $progress->progressable;

        if (!$progressable) {
            return 'Unknown Activity';
        }

        return match ($progress->progressable_type) {
            Module::class => "Completed module: {$progressable->module_name}",
            InformationSheet::class => "Completed sheet: {$progressable->title}",
            default => 'Updated progress',
        };
    }

    // =========================================================================
    // CACHE MANAGEMENT
    // =========================================================================

    public function clearUserCache(User $user): void
    {
        Cache::forget("dashboard_admin_stats_{$user->id}");
        Cache::forget("dashboard_progress_{$user->id}");
        Cache::forget("dashboard_pending_evals_{$user->id}");
    }

    public function clearAnnouncementCache(): void
    {
        Cache::forget('dashboard_announcements_count');
        // Announcement list caches use dynamic limit keys
        foreach ([3, 5, 10] as $limit) {
            Cache::forget("dashboard_announcements_{$limit}");
        }
    }
}
