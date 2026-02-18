<?php

namespace App\Services;

use App\Constants\Roles;
use App\Models\User;
use App\Models\Module;
use App\Models\Course;
use App\Models\HomeworkSubmission;
use App\Models\SelfCheckSubmission;
use App\Models\TaskSheetSubmission;
use App\Models\JobSheetSubmission;
use App\Models\UserProgress;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Service for calculating grades using the Philippine K-12 grading scale.
 *
 * Handles grade calculation for:
 * - Individual modules (self-checks, homeworks, task sheets, job sheets)
 * - Courses (aggregated module grades)
 * - Overall GPA (4.0 scale conversion)
 *
 * Grade Scale:
 * - 90-100: Outstanding (O) - Competent
 * - 85-89: Very Satisfactory (VS) - Competent
 * - 80-84: Satisfactory (S) - Competent
 * - 75-79: Fairly Satisfactory (FS) - Competent
 * - 0-74: Did Not Meet Expectations (DNM) - Not Yet Competent
 */
class GradingService
{
    /**
     * Philippine K-12 Grading Scale with Competency Status
     */
    protected array $gradingScale = [
        ['min' => 90, 'max' => 100, 'descriptor' => 'Outstanding', 'code' => 'O', 'competent' => true],
        ['min' => 85, 'max' => 89, 'descriptor' => 'Very Satisfactory', 'code' => 'VS', 'competent' => true],
        ['min' => 80, 'max' => 84, 'descriptor' => 'Satisfactory', 'code' => 'S', 'competent' => true],
        ['min' => 75, 'max' => 79, 'descriptor' => 'Fairly Satisfactory', 'code' => 'FS', 'competent' => true],
        ['min' => 0, 'max' => 74, 'descriptor' => 'Did Not Meet Expectations', 'code' => 'DNM', 'competent' => false],
    ];

    protected float $passingThreshold = 75.0;

    /**
     * Calculate the overall grade for a module.
     */
    public function calculateModuleGrade(User $user, Module $module): array
    {
        return Cache::remember(
            "module_grade_{$user->id}_{$module->id}",
            config('joms.cache.grades_ttl', 300),
            fn() => $this->computeModuleGrade($user, $module)
        );
    }

    /**
     * Invalidate cached grade for a user/module pair.
     */
    public function invalidateModuleGrade(int $userId, int $moduleId): void
    {
        Cache::forget("module_grade_{$userId}_{$moduleId}");
    }

    protected function computeModuleGrade(User $user, Module $module): array
    {
        $components = $this->getGradeComponents($user, $module);

        // Weight distribution (can be customized)
        $weights = [
            'self_checks' => 0.20,    // 20% - Quizzes
            'homeworks' => 0.30,      // 30% - Homework
            'task_sheets' => 0.25,    // 25% - Task Sheets
            'job_sheets' => 0.25,     // 25% - Job Sheets
        ];

        $totalWeight = 0;
        $weightedScore = 0;

        foreach ($components as $type => $data) {
            if ($data['count'] > 0 && isset($weights[$type])) {
                $weightedScore += $data['percentage'] * $weights[$type];
                $totalWeight += $weights[$type];
            }
        }

        // Normalize if not all component types have submissions
        $finalPercentage = $totalWeight > 0 ? $weightedScore / $totalWeight : 0;

        return [
            'percentage' => round($finalPercentage, 2),
            'components' => $components,
            'grade' => $this->applyGradingScale($finalPercentage),
            'is_competent' => $finalPercentage >= $this->passingThreshold,
        ];
    }

    /**
     * Calculate the overall grade for a course.
     */
    public function calculateCourseGrade(User $user, Course $course): array
    {
        $modules = $course->modules;
        $moduleGrades = [];
        $totalPercentage = 0;
        $completedModules = 0;

        foreach ($modules as $module) {
            $grade = $this->calculateModuleGrade($user, $module);
            $moduleGrades[$module->id] = $grade;

            if ($grade['components']['total_submissions'] > 0) {
                $totalPercentage += $grade['percentage'];
                $completedModules++;
            }
        }

        $averagePercentage = $completedModules > 0 ? $totalPercentage / $completedModules : 0;

        return [
            'percentage' => round($averagePercentage, 2),
            'module_grades' => $moduleGrades,
            'completed_modules' => $completedModules,
            'total_modules' => $modules->count(),
            'grade' => $this->applyGradingScale($averagePercentage),
            'is_competent' => $averagePercentage >= $this->passingThreshold,
        ];
    }

    /**
     * Calculate GPA for a user across all courses.
     */
    public function calculateGPA(User $user): array
    {
        $courses = Course::where('is_active', true)->get();
        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($courses as $course) {
            $courseGrade = $this->calculateCourseGrade($user, $course);

            if ($courseGrade['completed_modules'] > 0) {
                // Convert percentage to GPA points (4.0 scale)
                $gpaPoints = $this->percentageToGPA($courseGrade['percentage']);
                $credits = $course->modules->count(); // Using module count as credits

                $totalPoints += $gpaPoints * $credits;
                $totalCredits += $credits;
            }
        }

        $gpa = $totalCredits > 0 ? $totalPoints / $totalCredits : 0;

        return [
            'gpa' => round($gpa, 2),
            'total_credits' => $totalCredits,
            'gpa_scale' => '4.0',
        ];
    }

    /**
     * Get detailed grade components for a module.
     */
    public function getGradeComponents(User $user, Module $module): array
    {
        $components = [];
        $totalSubmissions = 0;

        // Get all information sheet IDs for this module (single query)
        $sheetIds = $module->informationSheets()->pluck('id');

        // Self Checks (Quizzes) — use sheetIds to avoid nested whereHas
        $selfCheckScores = SelfCheckSubmission::where('user_id', $user->id)
            ->whereHas('selfCheck', function ($q) use ($sheetIds) {
                $q->whereIn('information_sheet_id', $sheetIds);
            })
            ->get();

        $components['self_checks'] = $this->calculateComponentStats($selfCheckScores, 'percentage');
        $totalSubmissions += $components['self_checks']['count'];

        // Homeworks
        $homeworkScores = HomeworkSubmission::where('user_id', $user->id)
            ->whereNotNull('score')
            ->whereHas('homework', function ($q) use ($sheetIds) {
                $q->whereIn('information_sheet_id', $sheetIds);
            })
            ->with('homework:id,max_points')
            ->get();

        $components['homeworks'] = $this->calculateHomeworkStats($homeworkScores);
        $totalSubmissions += $components['homeworks']['count'];

        // Task Sheets
        $taskSheetScores = TaskSheetSubmission::where('user_id', $user->id)
            ->whereHas('taskSheet', function ($q) use ($sheetIds) {
                $q->whereIn('information_sheet_id', $sheetIds);
            })
            ->get();

        $components['task_sheets'] = $this->calculateTaskStats($taskSheetScores);
        $totalSubmissions += $components['task_sheets']['count'];

        // Job Sheets
        $jobSheetScores = JobSheetSubmission::where('user_id', $user->id)
            ->whereHas('jobSheet', function ($q) use ($sheetIds) {
                $q->whereIn('information_sheet_id', $sheetIds);
            })
            ->get();

        $components['job_sheets'] = $this->calculateTaskStats($jobSheetScores);
        $totalSubmissions += $components['job_sheets']['count'];

        $components['total_submissions'] = $totalSubmissions;

        return $components;
    }

    /**
     * Apply the grading scale to get descriptor.
     */
    public function applyGradingScale(float $percentage): array
    {
        foreach ($this->gradingScale as $grade) {
            if ($percentage >= $grade['min']) {
                return [
                    'percentage' => round($percentage, 2),
                    'descriptor' => $grade['descriptor'],
                    'code' => $grade['code'],
                    'is_competent' => $grade['competent'],
                    'competency_status' => $grade['competent'] ? 'Competent' : 'Not Yet Competent',
                ];
            }
        }

        return [
            'percentage' => 0,
            'descriptor' => 'No Grade',
            'code' => 'NG',
            'is_competent' => false,
            'competency_status' => 'Not Yet Competent',
        ];
    }

    /**
     * Convert percentage to GPA (4.0 scale).
     */
    protected function percentageToGPA(float $percentage): float
    {
        if ($percentage >= 97) return 4.0;
        if ($percentage >= 93) return 3.7;
        if ($percentage >= 90) return 3.3;
        if ($percentage >= 87) return 3.0;
        if ($percentage >= 83) return 2.7;
        if ($percentage >= 80) return 2.3;
        if ($percentage >= 77) return 2.0;
        if ($percentage >= 73) return 1.7;
        if ($percentage >= 70) return 1.3;
        if ($percentage >= 67) return 1.0;
        return 0.0;
    }

    /**
     * Calculate statistics for a component.
     */
    protected function calculateComponentStats(Collection $submissions, string $scoreField): array
    {
        if ($submissions->isEmpty()) {
            return [
                'count' => 0,
                'total_score' => 0,
                'max_score' => 0,
                'percentage' => 0,
                'average' => 0,
            ];
        }

        $totalPercentage = $submissions->sum($scoreField);
        $count = $submissions->count();

        return [
            'count' => $count,
            'total_score' => $totalPercentage,
            'max_score' => $count * 100,
            'percentage' => round($totalPercentage / $count, 2),
            'average' => round($totalPercentage / $count, 2),
        ];
    }

    /**
     * Calculate homework statistics.
     */
    protected function calculateHomeworkStats(Collection $submissions): array
    {
        if ($submissions->isEmpty()) {
            return [
                'count' => 0,
                'total_score' => 0,
                'max_score' => 0,
                'percentage' => 0,
                'average' => 0,
            ];
        }

        $totalScore = $submissions->sum('score');
        $maxScore = $submissions->sum('max_points');
        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        return [
            'count' => $submissions->count(),
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => round($percentage, 2),
            'average' => round($totalScore / $submissions->count(), 2),
        ];
    }

    /**
     * Calculate task/job sheet statistics (completion-based).
     */
    protected function calculateTaskStats(Collection $submissions): array
    {
        if ($submissions->isEmpty()) {
            return [
                'count' => 0,
                'completed' => 0,
                'percentage' => 0,
            ];
        }

        // For task/job sheets, we count completion
        $completed = $submissions->filter(function ($sub) {
            return $sub->submitted_at !== null;
        })->count();

        $percentage = ($completed / $submissions->count()) * 100;

        return [
            'count' => $submissions->count(),
            'completed' => $completed,
            'percentage' => round($percentage, 2),
        ];
    }

    /**
     * Get student ranking in a module.
     */
    public function getModuleRanking(User $user, Module $module): array
    {
        $students = User::where('role', Roles::STUDENT)->where('stat', 1)->get();
        $rankings = [];

        foreach ($students as $student) {
            $grade = $this->calculateModuleGrade($student, $module);
            $rankings[] = [
                'user_id' => $student->id,
                'percentage' => $grade['percentage'],
            ];
        }

        // Sort by percentage descending
        usort($rankings, fn($a, $b) => $b['percentage'] <=> $a['percentage']);

        // Find user's rank
        $rank = 1;
        foreach ($rankings as $r) {
            if ($r['user_id'] === $user->id) {
                break;
            }
            $rank++;
        }

        return [
            'rank' => $rank,
            'total_students' => count($rankings),
            'percentile' => round((count($rankings) - $rank + 1) / count($rankings) * 100, 1),
        ];
    }

    /**
     * Get progress summary for a student.
     */
    public function getProgressSummary(User $user): array
    {
        $courses = Course::where('is_active', true)->with('modules')->get();
        $summary = [];

        foreach ($courses as $course) {
            $courseGrade = $this->calculateCourseGrade($user, $course);
            $summary[] = [
                'course' => $course,
                'grade' => $courseGrade,
                'progress' => ($courseGrade['completed_modules'] / max($courseGrade['total_modules'], 1)) * 100,
            ];
        }

        return $summary;
    }
}
