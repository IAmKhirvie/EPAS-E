@extends('layouts.app')

@section('title', 'My Grades')

@push('styles')
<style>
    .grade-ring {
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    .grade-ring-text {
        font-size: 2rem;
        font-weight: bold;
    }
    .competency-badge {
        font-size: 0.85rem;
        padding: 0.5rem 1rem;
    }
    .grade-scale-item {
        display: flex;
        align-items: center;
        padding: 0.25rem 0;
        font-size: 0.85rem;
    }
    .grade-scale-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-graduation-cap me-2"></i>
                        @if(Auth::user()->id !== $student->id)
                            {{ $student->full_name }}'s Grades
                        @else
                            My Grades
                        @endif
                    </h1>
                    @if($student->student_id)
                        <p class="text-muted mb-0">Student ID: {{ $student->student_id }} | Section: {{ $student->section ?? 'N/A' }}</p>
                    @endif
                </div>
                @if(Auth::user()->id !== $student->id)
                    <a href="{{ route('grades.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Back to Student List
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Overall Grade Summary with Chart -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-muted mb-3">Overall Grade</h6>
                    <div class="grade-ring">
                        <canvas id="overallGradeChart"></canvas>
                    </div>
                    <h4 class="mt-3 mb-1">{{ $overallStats['grade_code'] ?? 'N/A' }}</h4>
                    <p class="text-muted mb-2">{{ $overallStats['grade_descriptor'] ?? 'Not Graded' }}</p>
                    @if(isset($overallStats['is_competent']))
                        <span class="badge competency-badge {{ $overallStats['is_competent'] ? 'bg-success' : 'bg-danger' }}">
                            <i class="fas {{ $overallStats['is_competent'] ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
                            {{ $overallStats['is_competent'] ? 'Competent' : 'Not Yet Competent' }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4 mb-lg-0">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Progress Overview</h6>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Completed Activities</span>
                        <strong>{{ $overallStats['completed'] }}/{{ $overallStats['total_activities'] }}</strong>
                    </div>
                    <div class="progress mb-3" class="progress-thin">
                        <div class="progress-bar bg-success" style="width: {{ $overallStats['completion_rate'] }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Points Earned</span>
                        <strong>{{ $overallStats['total_score'] }}/{{ $overallStats['max_score'] }}</strong>
                    </div>
                    <div class="progress mb-3" class="progress-thin">
                        <div class="progress-bar bg-primary" style="width: {{ $overallStats['max_score'] > 0 ? ($overallStats['total_score'] / $overallStats['max_score'] * 100) : 0 }}%"></div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <span>Completion Rate</span>
                        <strong>{{ $overallStats['completion_rate'] }}%</strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-3">Philippine K-12 Grading Scale</h6>
                    <div class="grade-scale-item">
                        <span class="grade-scale-dot bg-success"></span>
                        <span class="flex-grow-1">90-100% - Outstanding (O)</span>
                        <span class="badge bg-success">Competent</span>
                    </div>
                    <div class="grade-scale-item">
                        <span class="grade-scale-dot" style="background: #20c997;"></span>
                        <span class="flex-grow-1">85-89% - Very Satisfactory (VS)</span>
                        <span class="badge bg-success">Competent</span>
                    </div>
                    <div class="grade-scale-item">
                        <span class="grade-scale-dot bg-info"></span>
                        <span class="flex-grow-1">80-84% - Satisfactory (S)</span>
                        <span class="badge bg-success">Competent</span>
                    </div>
                    <div class="grade-scale-item">
                        <span class="grade-scale-dot bg-warning"></span>
                        <span class="flex-grow-1">75-79% - Fairly Satisfactory (FS)</span>
                        <span class="badge bg-success">Competent</span>
                    </div>
                    <div class="grade-scale-item">
                        <span class="grade-scale-dot bg-danger"></span>
                        <span class="flex-grow-1">Below 75% - Did Not Meet (DNM)</span>
                        <span class="badge bg-danger">Not Competent</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance by Category Chart -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Performance by Category</h6>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="200"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Activity Distribution</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="distributionChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Grades by Module -->
    @forelse($gradesData as $moduleData)
        @if($moduleData['total_count'] > 0)
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-book me-2"></i>{{ $moduleData['module']->module_name }}
                    </h5>
                    <div>
                        @php
                            $moduleGrade = app(\App\Services\GradingService::class)->applyGradingScale($moduleData['module_average']);
                        @endphp
                        <span class="badge {{ $moduleData['module_average'] >= 90 ? 'bg-success' : ($moduleData['module_average'] >= 85 ? 'bg-info' : ($moduleData['module_average'] >= 80 ? 'bg-primary' : ($moduleData['module_average'] >= 75 ? 'bg-warning' : 'bg-danger'))) }} me-2">
                            {{ $moduleGrade['code'] }} - {{ $moduleData['module_average'] }}%
                        </span>
                        <span class="badge bg-secondary">{{ $moduleData['completed_count'] }}/{{ $moduleData['total_count'] }} Completed</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Self-Checks -->
                @if(count($moduleData['self_checks']) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-check-circle me-2"></i>Self-Checks (Quizzes)</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Information Sheet</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th>Completed</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleData['self_checks'] as $item)
                                <tr>
                                    <td>{{ $item['title'] }}</td>
                                    <td><small class="text-muted">{{ $item['information_sheet'] }}</small></td>
                                    <td class="text-center">
                                        @if($item['score'] !== null)
                                            <strong>{{ $item['score'] }}/{{ $item['max_score'] }}</strong>
                                            <br><small class="text-muted">{{ $item['percentage'] }}%</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['percentage'])
                                            @php
                                                $itemGrade = app(\App\Services\GradingService::class)->applyGradingScale($item['percentage']);
                                            @endphp
                                            <span class="badge {{ $item['percentage'] >= 90 ? 'bg-success' : ($item['percentage'] >= 85 ? 'bg-info' : ($item['percentage'] >= 80 ? 'bg-primary' : ($item['percentage'] >= 75 ? 'bg-warning' : 'bg-danger'))) }}">
                                                {{ $itemGrade['code'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['passed'])
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Competent</span>
                                        @elseif($item['submission'])
                                            <span class="badge bg-danger"><i class="fas fa-times"></i> Not Yet</span>
                                        @else
                                            <span class="badge bg-secondary">Not Attempted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['completed_at'])
                                            {{ $item['completed_at']->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Homeworks -->
                @if(count($moduleData['homeworks']) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-book-open me-2"></i>Homeworks</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Information Sheet</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th>Submitted</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleData['homeworks'] as $item)
                                <tr>
                                    <td>{{ $item['title'] }}</td>
                                    <td><small class="text-muted">{{ $item['information_sheet'] }}</small></td>
                                    <td class="text-center">
                                        @if($item['score'] !== null && $item['evaluated'])
                                            <strong>{{ $item['score'] }}/{{ $item['max_score'] }}</strong>
                                            <br><small class="text-muted">{{ $item['percentage'] }}%</small>
                                        @elseif($item['submission'])
                                            <span class="text-warning">Pending Review</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['percentage'] && $item['evaluated'])
                                            @php
                                                $itemGrade = app(\App\Services\GradingService::class)->applyGradingScale($item['percentage']);
                                            @endphp
                                            <span class="badge {{ $item['percentage'] >= 90 ? 'bg-success' : ($item['percentage'] >= 85 ? 'bg-info' : ($item['percentage'] >= 80 ? 'bg-primary' : ($item['percentage'] >= 75 ? 'bg-warning' : 'bg-danger'))) }}">
                                                {{ $itemGrade['code'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['evaluated'])
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Graded</span>
                                        @elseif($item['submission'])
                                            @if($item['is_late'])
                                                <span class="badge bg-warning"><i class="fas fa-clock"></i> Late</span>
                                            @else
                                                <span class="badge bg-info"><i class="fas fa-hourglass-half"></i> Pending</span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['submitted_at'])
                                            {{ $item['submitted_at']->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['evaluator_notes'])
                                            <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="{{ $item['evaluator_notes'] }}">
                                                <i class="fas fa-comment"></i>
                                            </button>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Task Sheets -->
                @if(count($moduleData['task_sheets']) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-clipboard-list me-2"></i>Task Sheets</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Information Sheet</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleData['task_sheets'] as $item)
                                <tr>
                                    <td>{{ $item['title'] }}</td>
                                    <td><small class="text-muted">{{ $item['information_sheet'] }}</small></td>
                                    <td class="text-center">
                                        @if($item['score'] !== null)
                                            <strong>{{ $item['score'] }}%</strong>
                                        @elseif($item['submission'])
                                            <span class="text-warning">Pending Evaluation</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['score'])
                                            @php
                                                $itemGrade = app(\App\Services\GradingService::class)->applyGradingScale($item['score']);
                                            @endphp
                                            <span class="badge {{ $item['score'] >= 90 ? 'bg-success' : ($item['score'] >= 85 ? 'bg-info' : ($item['score'] >= 80 ? 'bg-primary' : ($item['score'] >= 75 ? 'bg-warning' : 'bg-danger'))) }}">
                                                {{ $itemGrade['code'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['criteria'])
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Evaluated</span>
                                        @elseif($item['submission'])
                                            <span class="badge bg-info"><i class="fas fa-hourglass-half"></i> Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['submitted_at'])
                                            {{ \Carbon\Carbon::parse($item['submitted_at'])->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                <!-- Job Sheets -->
                @if(count($moduleData['job_sheets']) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-hard-hat me-2"></i>Job Sheets</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Information Sheet</th>
                                    <th class="text-center">Completion</th>
                                    <th class="text-center">Score</th>
                                    <th class="text-center">Grade</th>
                                    <th class="text-center">Status</th>
                                    <th>Submitted</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($moduleData['job_sheets'] as $item)
                                <tr>
                                    <td>{{ $item['title'] }}</td>
                                    <td><small class="text-muted">{{ $item['information_sheet'] }}</small></td>
                                    <td class="text-center">
                                        @if($item['completion_percentage'] !== null)
                                            <div class="progress" class="progress-completion">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $item['completion_percentage'] }}%">
                                                    {{ $item['completion_percentage'] }}%
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['score'] !== null)
                                            <strong>{{ $item['score'] }}%</strong>
                                        @elseif($item['submission'])
                                            <span class="text-warning">Pending</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['score'])
                                            @php
                                                $itemGrade = app(\App\Services\GradingService::class)->applyGradingScale($item['score']);
                                            @endphp
                                            <span class="badge {{ $item['score'] >= 90 ? 'bg-success' : ($item['score'] >= 85 ? 'bg-info' : ($item['score'] >= 80 ? 'bg-primary' : ($item['score'] >= 75 ? 'bg-warning' : 'bg-danger'))) }}">
                                                {{ $itemGrade['code'] }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($item['criteria'])
                                            <span class="badge bg-success"><i class="fas fa-check"></i> Evaluated</span>
                                        @elseif($item['submission'])
                                            <span class="badge bg-info"><i class="fas fa-hourglass-half"></i> Pending</span>
                                        @else
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item['submitted_at'])
                                            {{ \Carbon\Carbon::parse($item['submitted_at'])->format('M j, Y g:i A') }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
        @endif
    @empty
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                <h5>No Modules Available</h5>
                <p class="text-muted">There are no active modules with gradeable content yet.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Overall Grade Doughnut Chart
    const overallAverage = {{ $overallStats['average'] }};
    const gradeCtx = document.getElementById('overallGradeChart').getContext('2d');

    let gradeColor = '#dc3545'; // Red for below 75
    if (overallAverage >= 90) gradeColor = '#198754'; // Green - Outstanding
    else if (overallAverage >= 85) gradeColor = '#20c997'; // Teal - Very Satisfactory
    else if (overallAverage >= 80) gradeColor = '#0dcaf0'; // Cyan - Satisfactory
    else if (overallAverage >= 75) gradeColor = '#ffc107'; // Yellow - Fairly Satisfactory

    new Chart(gradeCtx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [overallAverage, 100 - overallAverage],
                backgroundColor: [gradeColor, '#e9ecef'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '75%',
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        },
        plugins: [{
            id: 'centerText',
            afterDraw: function(chart) {
                const ctx = chart.ctx;
                ctx.save();
                const centerX = chart.width / 2;
                const centerY = chart.height / 2;
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.font = 'bold 24px Arial';
                ctx.fillStyle = gradeColor;
                ctx.fillText(overallAverage + '%', centerX, centerY);
                ctx.restore();
            }
        }]
    });

    // Category Performance Chart
    @php
        $categoryData = [
            'Self-Checks' => 0,
            'Homeworks' => 0,
            'Task Sheets' => 0,
            'Job Sheets' => 0
        ];
        $categoryCounts = [
            'Self-Checks' => 0,
            'Homeworks' => 0,
            'Task Sheets' => 0,
            'Job Sheets' => 0
        ];

        foreach($gradesData as $moduleData) {
            foreach($moduleData['self_checks'] as $item) {
                if($item['percentage']) {
                    $categoryData['Self-Checks'] += $item['percentage'];
                    $categoryCounts['Self-Checks']++;
                }
            }
            foreach($moduleData['homeworks'] as $item) {
                if($item['percentage']) {
                    $categoryData['Homeworks'] += $item['percentage'];
                    $categoryCounts['Homeworks']++;
                }
            }
            foreach($moduleData['task_sheets'] as $item) {
                if($item['score']) {
                    $categoryData['Task Sheets'] += $item['score'];
                    $categoryCounts['Task Sheets']++;
                }
            }
            foreach($moduleData['job_sheets'] as $item) {
                if($item['score']) {
                    $categoryData['Job Sheets'] += $item['score'];
                    $categoryCounts['Job Sheets']++;
                }
            }
        }

        foreach($categoryData as $key => $value) {
            $categoryData[$key] = $categoryCounts[$key] > 0 ? round($value / $categoryCounts[$key], 1) : 0;
        }
    @endphp

    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: ['Self-Checks', 'Homeworks', 'Task Sheets', 'Job Sheets'],
            datasets: [{
                label: 'Average Score (%)',
                data: [{{ $categoryData['Self-Checks'] }}, {{ $categoryData['Homeworks'] }}, {{ $categoryData['Task Sheets'] }}, {{ $categoryData['Job Sheets'] }}],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    }
                }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });

    // Distribution Pie Chart
    const distributionCtx = document.getElementById('distributionChart').getContext('2d');
    new Chart(distributionCtx, {
        type: 'pie',
        data: {
            labels: ['Self-Checks', 'Homeworks', 'Task Sheets', 'Job Sheets'],
            datasets: [{
                data: [{{ $categoryCounts['Self-Checks'] }}, {{ $categoryCounts['Homeworks'] }}, {{ $categoryCounts['Task Sheets'] }}, {{ $categoryCounts['Job Sheets'] }}],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endpush
