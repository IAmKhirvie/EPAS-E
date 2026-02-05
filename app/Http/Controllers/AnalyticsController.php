<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentProgressExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;
    protected AuditLogService $auditLogService;

    public function __construct(AnalyticsService $analyticsService, AuditLogService $auditLogService)
    {
        $this->analyticsService = $analyticsService;
        $this->auditLogService = $auditLogService;
    }

    public function dashboard()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();

        return view('analytics.dashboard', compact('metrics'));
    }

    public function users()
    {
        $metrics = $this->analyticsService->getUserMetrics();

        return view('analytics.users', compact('metrics'));
    }

    public function courses()
    {
        $metrics = $this->analyticsService->getCourseMetrics();

        return view('analytics.courses', compact('metrics'));
    }

    public function getMetricsApi()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();

        return response()->json($metrics);
    }

    public function exportStudentProgress(Request $request)
    {
        $this->auditLogService->logExport('student_progress', 0);

        return Excel::download(
            new StudentProgressExport($request->all()),
            'student-progress-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdfReport()
    {
        $metrics = $this->analyticsService->getDashboardMetrics();

        $pdf = Pdf::loadView('analytics.pdf-report', compact('metrics'))
            ->setPaper('a4', 'portrait');

        $this->auditLogService->logExport('analytics_report', 1);

        return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
    }

    public function refreshCache()
    {
        $this->analyticsService->clearCache();

        return response()->json(['message' => 'Analytics cache refreshed']);
    }

    public function topPerformers(Request $request)
    {
        $limit = $request->get('limit', 10);
        $performers = $this->analyticsService->getTopPerformers($limit);

        return response()->json($performers);
    }

    public function atRiskStudents()
    {
        $students = $this->analyticsService->getAtRiskStudents();

        return response()->json($students);
    }
}
