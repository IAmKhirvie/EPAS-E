<?php

namespace App\Http\Controllers;

use App\Constants\Roles;
use App\Models\User;
use App\Models\Module;
use App\Models\UserProgress;
use App\Models\HomeworkSubmission;
use App\Models\SelfCheckSubmission;
use App\Models\TaskSheetSubmission;
use App\Models\JobSheetSubmission;
use App\Models\PerformanceCriteria;
use App\Services\GradingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Handles grade viewing and management for the JOMS LMS.
 *
 * Provides different views based on user role:
 * - Students see their own grades
 * - Instructors see students in their advisory section
 * - Admins see all students system-wide
 */
class GradesController extends Controller
{
    protected GradingService $gradingService;

    public function __construct(GradingService $gradingService)
    {
        $this->gradingService = $gradingService;
    }

    /**
     * Display grades view based on user role.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if ($user->role === Roles::STUDENT) {
            return $this->studentGrades($user);
        }

        return $this->instructorAdminGrades($request, $user);
    }

    /**
     * Show detailed grades for a specific student.
     *
     * Students can only view their own grades.
     * Instructors can view students in their advisory section.
     * Admins can view any student.
     *
     * @param User $student
     * @return View
     */
    public function show(User $student): View
    {
        $viewer = Auth::user();

        // Students can only view their own grades
        if ($viewer->role === Roles::STUDENT && $viewer->id !== $student->id) {
            abort(403, 'You can only view your own grades.');
        }

        // Instructors can only view students in their assigned sections
        if ($viewer->role === Roles::INSTRUCTOR && !$viewer->isAssignedToSection($student->section)) {
            abort(403, 'You can only view grades for students in your assigned sections.');
        }

        return $this->studentGrades($student);
    }

    /**
     * API endpoint for student grades data.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getStudentGradesApi(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->role !== Roles::STUDENT) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $summary = $this->calculateStudentGradeSummary($user);

        return response()->json([
            'summary' => $summary,
            'student' => [
                'name' => $user->full_name,
                'student_id' => $user->student_id,
                'section' => $user->section,
            ],
        ]);
    }

    /**
     * Export grades to CSV.
     *
     * @param Request $request
     * @return Response
     */
    public function exportGrades(Request $request): Response
    {
        $this->authorizeInstructor();

        $viewer = Auth::user();
        $section = $request->get('section');
        $courseId = $request->get('course_id');

        // Instructors can only export sections they are assigned to
        if ($viewer->role === Roles::INSTRUCTOR) {
            $assignedSections = $viewer->getAllAccessibleSections();
            if ($section && !$assignedSections->contains($section)) {
                abort(403, 'You can only export grades for your assigned sections.');
            }
            // If no section specified, use the first assigned section
            if (!$section && $assignedSections->isNotEmpty()) {
                $section = $assignedSections->first();
            }
        }

        $export = new \App\Exports\GradesExport($section, $courseId);
        $csv = $export->generateCSV();
        $filename = 'grades_' . ($section ?? 'all') . '_' . date('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Export class grades to CSV.
     *
     * @param string $section
     * @return Response
     */
    public function exportClassGrades(string $section): Response
    {
        $this->authorizeInstructor();

        $viewer = Auth::user();

        // Instructors can only export sections they are assigned to
        if ($viewer->role === Roles::INSTRUCTOR && !$viewer->isAssignedToSection($section)) {
            abort(403, 'You can only export grades for your assigned sections.');
        }

        $export = new \App\Exports\ClassGradesExport($section);
        $csv = $export->generateCSV();
        $filename = 'class_grades_' . $section . '_' . date('Y-m-d') . '.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // =========================================================================
    // PRIVATE METHODS - Grade Calculation
    // =========================================================================

    /**
     * Build student grades view with all module activities.
     *
     * @param User $student
     * @return View
     */
    private function studentGrades(User $student): View
    {
        $modules = Module::where('is_active', true)
            ->with([
                'informationSheets.selfChecks',
                'informationSheets.homeworks',
                'informationSheets.taskSheets',
                'informationSheets.jobSheets'
            ])
            ->get();

        $gradesData = [];
        $overallStats = $this->initializeOverallStats();

        foreach ($modules as $module) {
            $moduleGrades = $this->processModuleGrades($module, $student, $overallStats);
            $gradesData[] = $moduleGrades;
        }

        $this->finalizeOverallStats($overallStats);

        return view('grades.student', compact('gradesData', 'overallStats', 'student'));
    }

    /**
     * Build instructor/admin grades view with student list.
     *
     * @param Request $request
     * @param User $viewer
     * @return View
     */
    private function instructorAdminGrades(Request $request, User $viewer): View
    {
        $search = $request->get('search');
        $moduleFilter = $request->get('module');
        $sectionFilter = $request->get('section');

        $studentsQuery = User::where('role', Roles::STUDENT)->where('stat', true);

        // Apply section filter based on role
        if ($viewer->role === Roles::INSTRUCTOR) {
            $instructorSections = $viewer->getAllAccessibleSections();
            if ($instructorSections->isNotEmpty()) {
                // If section filter provided, ensure it's one of their sections
                if ($sectionFilter && $instructorSections->contains($sectionFilter)) {
                    $studentsQuery->where('section', $sectionFilter);
                } else {
                    // Show all students in their assigned sections
                    $studentsQuery->whereIn('section', $instructorSections);
                }
            } else {
                // No sections assigned - show no students
                $studentsQuery->where('id', 0);
            }
        } elseif ($sectionFilter) {
            $studentsQuery->where('section', $sectionFilter);
        }

        // Apply search filter
        if ($search) {
            $studentsQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('student_id', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $students = $studentsQuery
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->paginate(20);

        // Pre-fetch all submissions for current page to avoid N+1
        $studentIds = $students->getCollection()->pluck('id');

        $selfCheckAvgs = SelfCheckSubmission::whereIn('user_id', $studentIds)
            ->whereNotNull('percentage')
            ->selectRaw('user_id, AVG(percentage) as avg_percentage')
            ->groupBy('user_id')
            ->pluck('avg_percentage', 'user_id');

        $homeworkAvgs = HomeworkSubmission::whereIn('user_id', $studentIds)
            ->whereNotNull('score')
            ->selectRaw('user_id, AVG(score / (SELECT max_points FROM homeworks WHERE homeworks.id = homework_submissions.homework_id) * 100) as avg_score')
            ->groupBy('user_id')
            ->pluck('avg_score', 'user_id');

        $completedCounts = UserProgress::whereIn('user_id', $studentIds)
            ->where('status', 'completed')
            ->selectRaw('user_id, COUNT(*) as completed_count')
            ->groupBy('user_id')
            ->pluck('completed_count', 'user_id');

        // Add grade summary to each student using pre-fetched data
        $students->getCollection()->transform(function ($student) use ($selfCheckAvgs, $homeworkAvgs, $completedCounts) {
            $student->grade_summary = $this->calculateStudentGradeSummary(
                $student,
                $selfCheckAvgs->get($student->id, 0),
                $homeworkAvgs->get($student->id, 0),
                $completedCounts->get($student->id, 0)
            );
            return $student;
        });

        $modules = Module::where('is_active', true)->get();
        $sections = User::where('role', Roles::STUDENT)
            ->whereNotNull('section')
            ->distinct()
            ->pluck('section')
            ->sort();

        return view('grades.instructor', compact(
            'students', 'modules', 'sections',
            'search', 'moduleFilter', 'sectionFilter', 'viewer'
        ));
    }

    // =========================================================================
    // PRIVATE METHODS - Helper Functions
    // =========================================================================

    /**
     * Initialize the overall statistics array.
     *
     * @return array
     */
    private function initializeOverallStats(): array
    {
        return [
            'total_activities' => 0,
            'completed' => 0,
            'total_score' => 0,
            'max_score' => 0,
        ];
    }

    /**
     * Process all grades for a single module.
     *
     * @param Module $module
     * @param User $student
     * @param array &$overallStats Reference to overall statistics
     * @return array Module grades data
     */
    private function processModuleGrades(Module $module, User $student, array &$overallStats): array
    {
        $moduleGrades = [
            'module' => $module,
            'self_checks' => [],
            'homeworks' => [],
            'task_sheets' => [],
            'job_sheets' => [],
            'module_average' => 0,
            'completion_rate' => 0,
        ];

        $moduleScore = 0;
        $moduleMaxScore = 0;
        $moduleCompleted = 0;
        $moduleTotal = 0;

        foreach ($module->informationSheets as $sheet) {
            // Process each activity type
            $this->processSelfChecks($sheet, $student, $moduleGrades, $overallStats, $moduleScore, $moduleMaxScore, $moduleCompleted, $moduleTotal);
            $this->processHomeworks($sheet, $student, $moduleGrades, $overallStats, $moduleScore, $moduleMaxScore, $moduleCompleted, $moduleTotal);
            $this->processTaskSheets($sheet, $student, $moduleGrades, $overallStats, $moduleScore, $moduleMaxScore, $moduleCompleted, $moduleTotal);
            $this->processJobSheets($sheet, $student, $moduleGrades, $overallStats, $moduleScore, $moduleMaxScore, $moduleCompleted, $moduleTotal);
        }

        // Calculate module statistics
        $moduleGrades['module_average'] = $moduleMaxScore > 0
            ? round(($moduleScore / $moduleMaxScore) * 100, 1)
            : 0;
        $moduleGrades['completion_rate'] = $moduleTotal > 0
            ? round(($moduleCompleted / $moduleTotal) * 100, 1)
            : 0;
        $moduleGrades['completed_count'] = $moduleCompleted;
        $moduleGrades['total_count'] = $moduleTotal;

        return $moduleGrades;
    }

    /**
     * Process self-check submissions for a student.
     *
     * @param mixed $sheet Information sheet
     * @param User $student
     * @param array &$moduleGrades
     * @param array &$overallStats
     * @param float &$moduleScore
     * @param float &$moduleMaxScore
     * @param int &$moduleCompleted
     * @param int &$moduleTotal
     */
    private function processSelfChecks($sheet, User $student, array &$moduleGrades, array &$overallStats, float &$moduleScore, float &$moduleMaxScore, int &$moduleCompleted, int &$moduleTotal): void
    {
        foreach ($sheet->selfChecks as $selfCheck) {
            $moduleTotal++;
            $overallStats['total_activities']++;

            $submission = SelfCheckSubmission::where('user_id', $student->id)
                ->where('self_check_id', $selfCheck->id)
                ->latest()
                ->first();

            $moduleGrades['self_checks'][] = [
                'title' => $selfCheck->title,
                'information_sheet' => $sheet->title,
                'submission' => $submission,
                'score' => $submission?->score,
                'max_score' => $submission?->total_points ?? $selfCheck->total_points,
                'percentage' => $submission?->percentage,
                'grade' => $submission?->grade,
                'passed' => $submission?->passed ?? false,
                'completed_at' => $submission?->created_at,
            ];

            if ($submission) {
                $moduleCompleted++;
                $overallStats['completed']++;
                $moduleScore += $submission->score ?? 0;
                $moduleMaxScore += $submission->total_points ?? 0;
                $overallStats['total_score'] += $submission->score ?? 0;
                $overallStats['max_score'] += $submission->total_points ?? 0;
            }
        }
    }

    /**
     * Process homework submissions for a student.
     *
     * @param mixed $sheet Information sheet
     * @param User $student
     * @param array &$moduleGrades
     * @param array &$overallStats
     * @param float &$moduleScore
     * @param float &$moduleMaxScore
     * @param int &$moduleCompleted
     * @param int &$moduleTotal
     */
    private function processHomeworks($sheet, User $student, array &$moduleGrades, array &$overallStats, float &$moduleScore, float &$moduleMaxScore, int &$moduleCompleted, int &$moduleTotal): void
    {
        foreach ($sheet->homeworks as $homework) {
            $moduleTotal++;
            $overallStats['total_activities']++;

            $submission = HomeworkSubmission::where('user_id', $student->id)
                ->where('homework_id', $homework->id)
                ->latest()
                ->first();

            $percentage = null;
            if ($submission && $homework->max_points > 0) {
                $percentage = round(($submission->score / $homework->max_points) * 100, 1);
            }

            $moduleGrades['homeworks'][] = [
                'title' => $homework->title,
                'information_sheet' => $sheet->title,
                'submission' => $submission,
                'score' => $submission?->score,
                'max_score' => $homework->max_points,
                'percentage' => $percentage,
                'grade' => $submission?->grade,
                'evaluated' => $submission?->evaluated_at !== null,
                'is_late' => $submission?->is_late ?? false,
                'submitted_at' => $submission?->created_at,
                'evaluator_notes' => $submission?->evaluator_notes,
            ];

            if ($submission && $submission->evaluated_at) {
                $moduleCompleted++;
                $overallStats['completed']++;
                $moduleScore += $submission->score ?? 0;
                $moduleMaxScore += $homework->max_points;
                $overallStats['total_score'] += $submission->score ?? 0;
                $overallStats['max_score'] += $homework->max_points;
            }
        }
    }

    /**
     * Process task sheet submissions for a student.
     *
     * @param mixed $sheet Information sheet
     * @param User $student
     * @param array &$moduleGrades
     * @param array &$overallStats
     * @param float &$moduleScore
     * @param float &$moduleMaxScore
     * @param int &$moduleCompleted
     * @param int &$moduleTotal
     */
    private function processTaskSheets($sheet, User $student, array &$moduleGrades, array &$overallStats, float &$moduleScore, float &$moduleMaxScore, int &$moduleCompleted, int &$moduleTotal): void
    {
        foreach ($sheet->taskSheets as $taskSheet) {
            $moduleTotal++;
            $overallStats['total_activities']++;

            $submission = TaskSheetSubmission::where('user_id', $student->id)
                ->where('task_sheet_id', $taskSheet->id)
                ->latest()
                ->first();

            $criteria = null;
            if ($submission) {
                $criteria = PerformanceCriteria::where('evaluable_type', TaskSheetSubmission::class)
                    ->where('evaluable_id', $submission->id)
                    ->first();
            }

            $moduleGrades['task_sheets'][] = [
                'title' => $taskSheet->title,
                'information_sheet' => $sheet->title,
                'submission' => $submission,
                'criteria' => $criteria,
                'score' => $criteria?->score,
                'grade' => $criteria?->grade,
                'submitted_at' => $submission?->submitted_at,
                'evaluator_notes' => $criteria?->evaluator_notes,
            ];

            if ($submission && $criteria) {
                $moduleCompleted++;
                $overallStats['completed']++;
                $moduleScore += $criteria->score ?? 0;
                $moduleMaxScore += 100; // Task sheets are scored out of 100
                $overallStats['total_score'] += $criteria->score ?? 0;
                $overallStats['max_score'] += 100;
            }
        }
    }

    /**
     * Process job sheet submissions for a student.
     *
     * @param mixed $sheet Information sheet
     * @param User $student
     * @param array &$moduleGrades
     * @param array &$overallStats
     * @param float &$moduleScore
     * @param float &$moduleMaxScore
     * @param int &$moduleCompleted
     * @param int &$moduleTotal
     */
    private function processJobSheets($sheet, User $student, array &$moduleGrades, array &$overallStats, float &$moduleScore, float &$moduleMaxScore, int &$moduleCompleted, int &$moduleTotal): void
    {
        foreach ($sheet->jobSheets as $jobSheet) {
            $moduleTotal++;
            $overallStats['total_activities']++;

            $submission = JobSheetSubmission::where('user_id', $student->id)
                ->where('job_sheet_id', $jobSheet->id)
                ->latest()
                ->first();

            $criteria = null;
            if ($submission) {
                $criteria = PerformanceCriteria::where('evaluable_type', JobSheetSubmission::class)
                    ->where('evaluable_id', $submission->id)
                    ->first();
            }

            $moduleGrades['job_sheets'][] = [
                'title' => $jobSheet->title,
                'information_sheet' => $sheet->title,
                'submission' => $submission,
                'criteria' => $criteria,
                'score' => $criteria?->score,
                'grade' => $criteria?->grade,
                'completion_percentage' => $submission?->completion_percentage,
                'submitted_at' => $submission?->created_at,
                'evaluator_notes' => $criteria?->evaluator_notes ?? $submission?->evaluator_notes,
            ];

            if ($submission && $criteria) {
                $moduleCompleted++;
                $overallStats['completed']++;
                $moduleScore += $criteria->score ?? 0;
                $moduleMaxScore += 100; // Job sheets are scored out of 100
                $overallStats['total_score'] += $criteria->score ?? 0;
                $overallStats['max_score'] += 100;
            }
        }
    }

    /**
     * Finalize overall statistics with averages and grades.
     *
     * @param array &$overallStats
     */
    private function finalizeOverallStats(array &$overallStats): void
    {
        $overallStats['average'] = $overallStats['max_score'] > 0
            ? round(($overallStats['total_score'] / $overallStats['max_score']) * 100, 1)
            : 0;

        $overallStats['completion_rate'] = $overallStats['total_activities'] > 0
            ? round(($overallStats['completed'] / $overallStats['total_activities']) * 100, 1)
            : 0;

        $grade = $this->gradingService->applyGradingScale($overallStats['average']);
        $overallStats['grade'] = $grade;
        $overallStats['grade_descriptor'] = $grade['descriptor'];
        $overallStats['grade_code'] = $grade['code'];
        $overallStats['is_competent'] = $grade['is_competent'];
    }

    /**
     * Calculate a quick grade summary for a student (used in list views).
     *
     * When called from batch context, pre-fetched values are passed to avoid N+1 queries.
     *
     * @param User $student
     * @param float|null $prefetchedSelfCheckAvg
     * @param float|null $prefetchedHomeworkAvg
     * @param int|null $prefetchedCompletedCount
     * @return array
     */
    private function calculateStudentGradeSummary(
        User $student,
        ?float $prefetchedSelfCheckAvg = null,
        ?float $prefetchedHomeworkAvg = null,
        ?int $prefetchedCompletedCount = null
    ): array {
        // Use pre-fetched data if available, otherwise query per-student
        $selfCheckAvg = $prefetchedSelfCheckAvg ?? (SelfCheckSubmission::where('user_id', $student->id)
            ->whereNotNull('percentage')
            ->avg('percentage') ?? 0);

        $homeworkAvg = $prefetchedHomeworkAvg ?? (HomeworkSubmission::where('user_id', $student->id)
            ->whereNotNull('score')
            ->selectRaw('AVG(score / (SELECT max_points FROM homeworks WHERE homeworks.id = homework_submissions.homework_id) * 100) as avg')
            ->value('avg') ?? 0);

        $completedActivities = $prefetchedCompletedCount ?? UserProgress::where('user_id', $student->id)
            ->where('status', 'completed')
            ->count();

        // Calculate overall average
        $overallAvg = ($selfCheckAvg + $homeworkAvg) / 2;
        $grade = $this->gradingService->applyGradingScale($overallAvg);

        return [
            'overall_average' => round($overallAvg, 1),
            'grade' => $grade,
            'grade_descriptor' => $grade['descriptor'],
            'grade_code' => $grade['code'],
            'is_competent' => $grade['is_competent'],
            'competency_status' => $grade['competency_status'],
            'self_check_average' => round($selfCheckAvg, 1),
            'homework_average' => round($homeworkAvg, 1),
            'completed_activities' => $completedActivities,
            'total_points' => $student->total_points ?? 0,
        ];
    }
}
