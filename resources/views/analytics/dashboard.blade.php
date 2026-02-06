@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h3 mb-1"><i class="fas fa-chart-line me-2"></i>Analytics Dashboard</h1>
                    <p class="text-muted mb-0">Module performance and student engagement metrics</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('analytics.export.students') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Export Excel
                    </a>
                    <a href="{{ route('analytics.export.pdf') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>Export PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="row mb-4">
        <div class="col-6 col-lg-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 me-3">
                            <i class="fas fa-user-graduate text-primary"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Students</p>
                            <h4 class="mb-0 fw-bold">{{ $metrics['users']['total_students'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 me-3">
                            <i class="fas fa-check-circle text-success"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Pass Rate</p>
                            <h4 class="mb-0 fw-bold">{{ $metrics['modules']['overall_pass_rate'] ?? $metrics['performance']['pass_rate'] ?? 0 }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger bg-opacity-10 p-3 me-3">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Fail Rate</p>
                            <h4 class="mb-0 fw-bold">{{ $metrics['modules']['overall_fail_rate'] ?? 0 }}%</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-3">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info bg-opacity-10 p-3 me-3">
                            <i class="fas fa-book text-info"></i>
                        </div>
                        <div>
                            <p class="text-muted small mb-0">Modules</p>
                            <h4 class="mb-0 fw-bold">{{ $metrics['courses']['total_modules'] ?? 0 }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Module Pass/Fail Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Module Performance</h5>
                    <small class="text-muted">Pass vs Fail rates by module</small>
                </div>
                <div class="card-body">
                    <canvas id="modulePerformanceChart" height="250"></canvas>
                </div>
            </div>
        </div>

        <!-- Overall Pass/Fail Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Overall Results</h5>
                    <small class="text-muted">Total pass vs fail distribution</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="overallResultsChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Module Statistics Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-table me-2 text-info"></i>Module Statistics</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Module</th>
                                    <th class="text-center">Attempts</th>
                                    <th class="text-center">Passed</th>
                                    <th class="text-center">Failed</th>
                                    <th class="text-center">Pass Rate</th>
                                    <th class="text-center">Avg Score</th>
                                    <th>Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($metrics['modules']['modules_list'] ?? [] as $module)
                                <tr>
                                    <td>
                                        <strong>{{ $module['module_number'] }}</strong>
                                        <br><small class="text-muted">{{ Str::limit($module['name'], 30) }}</small>
                                    </td>
                                    <td class="text-center">{{ $module['total_attempts'] }}</td>
                                    <td class="text-center"><span class="badge bg-success">{{ $module['passed'] }}</span></td>
                                    <td class="text-center"><span class="badge bg-danger">{{ $module['failed'] }}</span></td>
                                    <td class="text-center">
                                        <span class="badge {{ $module['pass_rate'] >= 70 ? 'bg-success' : ($module['pass_rate'] >= 50 ? 'bg-warning text-dark' : 'bg-danger') }}">
                                            {{ $module['pass_rate'] }}%
                                        </span>
                                    </td>
                                    <td class="text-center">{{ $module['average_score'] }}%</td>
                                    <td>
                                        <div class="progress progress-mini">
                                            <div class="progress-bar bg-success" style="width: {{ $module['pass_rate'] }}%"></div>
                                            <div class="progress-bar bg-danger" style="width: {{ $module['fail_rate'] }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-chart-bar fa-2x mb-2 d-block"></i>
                                        No module data available yet
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Leaderboard & At-Risk -->
    <div class="row mb-4">
        <!-- Top Performers (Leaderboard) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2 text-warning"></i>Leaderboard - Top Performers</h5>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($metrics['performance']['top_performers'] ?? [] as $index => $performer)
                            <li class="list-group-item d-flex align-items-center py-3">
                                <div class="me-3">
                                    @if($index === 0)
                                        <span class="badge bg-warning text-dark rounded-circle p-2" class="badge-circle">
                                            <i class="fas fa-crown"></i>
                                        </span>
                                    @elseif($index === 1)
                                        <span class="badge bg-secondary rounded-circle p-2" class="badge-circle">2</span>
                                    @elseif($index === 2)
                                        <span class="badge bg-danger rounded-circle p-2" class="badge-circle">3</span>
                                    @else
                                        <span class="badge bg-light text-dark rounded-circle p-2" class="badge-circle">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $performer->first_name }} {{ $performer->last_name }}</strong>
                                </div>
                                <div>
                                    <span class="badge bg-primary rounded-pill px-3">{{ $performer->total_points ?? 0 }} pts</span>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">
                                <i class="fas fa-trophy fa-2x mb-2 d-block text-warning opacity-50"></i>
                                No data available
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- At-Risk Students (Needs Attention) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2 text-danger"></i>Needs Attention</h5>
                    <small class="text-muted">Students who haven't logged in recently</small>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($metrics['performance']['at_risk_students'] ?? [] as $student)
                            <li class="list-group-item d-flex align-items-center py-3">
                                <div class="rounded-circle bg-danger bg-opacity-10 p-2 me-3">
                                    <i class="fas fa-user text-danger"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                    <br><small class="text-muted">{{ $student->email }}</small>
                                </div>
                                <div class="text-end">
                                    <small class="text-danger">
                                        @if($student->last_login)
                                            {{ $student->last_login->diffForHumans() }}
                                        @else
                                            Never logged in
                                        @endif
                                    </small>
                                </div>
                            </li>
                        @empty
                            <li class="list-group-item text-center text-muted py-4">
                                <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                                All students are active!
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Engagement Chart -->
    <div class="row mb-4">
        <!-- Module Pass/Fail Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Module Performance</h5>
                    <small class="text-muted">Pass vs Fail rates by module</small>
                </div>
                <div class="card-body" class="chart-container">
                    <canvas id="modulePerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Overall Pass/Fail Pie Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pb-0">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-success"></i>Overall Results</h5>
                    <small class="text-muted">Total pass vs fail distribution</small>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center" class="chart-container">
                    <canvas id="overallResultsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Engagement Chart -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="fas fa-calendar-week me-2 text-info"></i>Weekly Engagement</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded bg-primary bg-opacity-10">
                                <h2 class="fw-bold text-primary mb-0">{{ $metrics['engagement']['activities_completed_week'] ?? 0 }}</h2>
                                <small class="text-muted"><i class="fas fa-tasks me-1"></i>Activities Completed</small>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3 mb-md-0">
                            <div class="p-3 rounded bg-success bg-opacity-10">
                                <h2 class="fw-bold text-success mb-0">{{ $metrics['engagement']['homework_submissions_week'] ?? 0 }}</h2>
                                <small class="text-muted"><i class="fas fa-book-open me-1"></i>Homework Submissions</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 rounded bg-info bg-opacity-10">
                                <h2 class="fw-bold text-info mb-0">{{ $metrics['engagement']['quiz_attempts_week'] ?? 0 }}</h2>
                                <small class="text-muted"><i class="fas fa-question-circle me-1"></i>Quiz Attempts</small>
                            </div>
                        </div>
                    </div>
                    <div class="chart-container-sm">
                        <canvas id="dailyActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Module Performance Chart (Bar)
    const moduleCtx = document.getElementById('modulePerformanceChart');
    if (moduleCtx) {
        const moduleData = @json($metrics['modules']['modules_list'] ?? []);

        new Chart(moduleCtx, {
            type: 'bar',
            data: {
                labels: moduleData.map(m => m.module_number || m.name.substring(0, 15)),
                datasets: [
                    {
                        label: 'Pass Rate %',
                        data: moduleData.map(m => m.pass_rate),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'Fail Rate %',
                        data: moduleData.map(m => m.fail_rate),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
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
                }
            }
        });
    }

    // Overall Results Pie Chart
    const pieCtx = document.getElementById('overallResultsChart');
    if (pieCtx) {
        const totalPassed = {{ $metrics['modules']['total_passed'] ?? 0 }};
        const totalFailed = {{ $metrics['modules']['total_failed'] ?? 0 }};

        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Passed', 'Failed'],
                datasets: [{
                    data: [totalPassed, totalFailed],
                    backgroundColor: [
                        'rgba(40, 167, 69, 0.8)',
                        'rgba(220, 53, 69, 0.8)'
                    ],
                    borderColor: [
                        'rgba(40, 167, 69, 1)',
                        'rgba(220, 53, 69, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }

    // Daily Activity Line Chart
    const dailyCtx = document.getElementById('dailyActivityChart');
    if (dailyCtx) {
        const dailyData = @json($metrics['engagement']['daily_active_users'] ?? []);

        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(d => d.date),
                datasets: [{
                    label: 'Active Users',
                    data: dailyData.map(d => d.count),
                    fill: true,
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
