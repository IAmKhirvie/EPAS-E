@extends('layouts.app')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="container-fluid py-4">
    {{-- Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-chart-pie me-2"></i>Analytics Dashboard</h1>
            <p>Module performance and student engagement metrics</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('analytics.export.students') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-excel me-1"></i>Export Excel
            </a>
            <a href="{{ route('analytics.export.pdf') }}" class="btn btn-danger btn-sm">
                <i class="fas fa-file-pdf me-1"></i>Export PDF
            </a>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="page-stat-cards">
        <div class="page-stat-card green">
            <div class="stat-decor"></div>
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-value">{{ $metrics['users']['total_students'] ?? 0 }}</div>
            <div class="stat-label">Students</div>
        </div>
        <div class="page-stat-card emerald">
            <div class="stat-decor"></div>
            <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="stat-value">{{ $metrics['modules']['overall_pass_rate'] ?? $metrics['performance']['pass_rate'] ?? 0 }}%</div>
            <div class="stat-label">Pass Rate</div>
        </div>
        <div class="page-stat-card red">
            <div class="stat-decor"></div>
            <div class="stat-icon"><i class="fas fa-times-circle"></i></div>
            <div class="stat-value">{{ $metrics['modules']['overall_fail_rate'] ?? 0 }}%</div>
            <div class="stat-label">Fail Rate</div>
        </div>
        <div class="page-stat-card blue">
            <div class="stat-decor"></div>
            <div class="stat-icon"><i class="fas fa-book"></i></div>
            <div class="stat-value">{{ $metrics['courses']['total_modules'] ?? 0 }}</div>
            <div class="stat-label">Modules</div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="page-grid cols-8-4">
        <div class="widget-card">
            <div class="widget-card-header">
                <h5><i class="fas fa-chart-bar"></i> Module Performance</h5>
                <small>Pass vs Fail rates by module</small>
            </div>
            <div class="widget-card-body">
                <div style="position:relative;height:280px;">
                    <canvas id="modulePerformanceChart"></canvas>
                </div>
            </div>
        </div>
        <div class="widget-card">
            <div class="widget-card-header">
                <h5><i class="fas fa-chart-pie"></i> Overall Results</h5>
                <small>Total pass vs fail distribution</small>
            </div>
            <div class="widget-card-body" style="display:flex;align-items:center;justify-content:center;min-height:280px;">
                <canvas id="overallResultsChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Module Statistics Table --}}
    <div class="widget-card">
        <div class="widget-card-header">
            <h5><i class="fas fa-table"></i> Module Statistics</h5>
        </div>
        <div class="widget-card-body no-pad">
            <div style="overflow-x:auto;">
                <table class="modern-table">
                    <thead>
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
                                <br><small style="color:var(--text-muted)">{{ Str::limit($module['name'], 30) }}</small>
                            </td>
                            <td class="text-center">{{ $module['total_attempts'] }}</td>
                            <td class="text-center"><span class="modern-badge success">{{ $module['passed'] }}</span></td>
                            <td class="text-center"><span class="modern-badge danger">{{ $module['failed'] }}</span></td>
                            <td class="text-center">
                                <span class="modern-badge {{ $module['pass_rate'] >= 70 ? 'success' : ($module['pass_rate'] >= 50 ? 'warning' : 'danger') }}">
                                    {{ $module['pass_rate'] }}%
                                </span>
                            </td>
                            <td class="text-center">{{ $module['average_score'] }}%</td>
                            <td>
                                <div style="display:flex;height:6px;border-radius:3px;overflow:hidden;background:var(--border);min-width:80px;">
                                    <div style="width:{{ $module['pass_rate'] }}%;background:#198754;"></div>
                                    <div style="width:{{ $module['fail_rate'] }}%;background:#dc3545;"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7">
                                <div class="page-empty">
                                    <i class="fas fa-chart-bar"></i>
                                    <p>No module data available yet</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Leaderboard & At-Risk --}}
    <div class="page-grid cols-2">
        <div class="widget-card" style="margin-bottom:0;">
            <div class="widget-card-header">
                <h5><i class="fas fa-trophy" style="color:#f59e0b"></i> Leaderboard</h5>
            </div>
            <div class="widget-card-body no-pad">
                @forelse($metrics['performance']['top_performers'] ?? [] as $index => $performer)
                <div style="display:flex;align-items:center;padding:0.75rem 1.25rem;border-bottom:1px solid var(--border);">
                    <div style="width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;font-weight:700;margin-right:0.75rem;flex-shrink:0;
                        @if($index === 0) background:rgba(245,158,11,0.15);color:#f59e0b; @elseif($index === 1) background:rgba(148,163,184,0.2);color:#64748b; @elseif($index === 2) background:rgba(180,83,9,0.15);color:#b45309; @else background:var(--background);color:var(--text-muted); @endif">
                        @if($index === 0)<i class="fas fa-crown"></i>@else {{ $index + 1 }} @endif
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:0.85rem;color:var(--text-primary);">{{ $performer->first_name }} {{ $performer->last_name }}</div>
                    </div>
                    <span class="modern-badge primary">{{ $performer->total_points ?? 0 }} pts</span>
                </div>
                @empty
                <div class="page-empty">
                    <i class="fas fa-trophy" style="color:#f59e0b"></i>
                    <p>No data available</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="widget-card" style="margin-bottom:0;">
            <div class="widget-card-header">
                <h5><i class="fas fa-exclamation-triangle" style="color:#dc3545"></i> Needs Attention</h5>
                <small>Students who haven't logged in recently</small>
            </div>
            <div class="widget-card-body no-pad">
                @forelse($metrics['performance']['at_risk_students'] ?? [] as $student)
                <div style="display:flex;align-items:center;padding:0.75rem 1.25rem;border-bottom:1px solid var(--border);">
                    <div style="width:32px;height:32px;border-radius:50%;background:rgba(220,53,69,0.1);color:#dc3545;display:flex;align-items:center;justify-content:center;margin-right:0.75rem;flex-shrink:0;">
                        <i class="fas fa-user"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-weight:600;font-size:0.85rem;color:var(--text-primary);">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $student->email }}</div>
                    </div>
                    <span style="font-size:0.75rem;color:#dc3545;white-space:nowrap;">
                        @if($student->last_login) {{ $student->last_login->diffForHumans() }} @else Never @endif
                    </span>
                </div>
                @empty
                <div class="page-empty">
                    <i class="fas fa-check-circle" style="color:#198754"></i>
                    <p>All students are active!</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Weekly Engagement --}}
    <div class="widget-card" style="margin-top:1.5rem;">
        <div class="widget-card-header">
            <h5><i class="fas fa-calendar-week"></i> Weekly Engagement</h5>
        </div>
        <div class="widget-card-body">
            <div class="page-stat-cards" style="grid-template-columns:repeat(3,1fr);margin-bottom:1.5rem;">
                <div class="page-stat-card blue" style="min-height:90px;padding:1rem;">
                    <div class="stat-icon" style="width:32px;height:32px;font-size:0.9rem;margin-bottom:0.5rem;"><i class="fas fa-tasks"></i></div>
                    <div class="stat-value" style="font-size:1.5rem;">{{ $metrics['engagement']['activities_completed_week'] ?? 0 }}</div>
                    <div class="stat-label">Activities Completed</div>
                </div>
                <div class="page-stat-card emerald" style="min-height:90px;padding:1rem;">
                    <div class="stat-icon" style="width:32px;height:32px;font-size:0.9rem;margin-bottom:0.5rem;"><i class="fas fa-book-open"></i></div>
                    <div class="stat-value" style="font-size:1.5rem;">{{ $metrics['engagement']['homework_submissions_week'] ?? 0 }}</div>
                    <div class="stat-label">Homework Submissions</div>
                </div>
                <div class="page-stat-card teal" style="min-height:90px;padding:1rem;">
                    <div class="stat-icon" style="width:32px;height:32px;font-size:0.9rem;margin-bottom:0.5rem;"><i class="fas fa-question-circle"></i></div>
                    <div class="stat-value" style="font-size:1.5rem;">{{ $metrics['engagement']['quiz_attempts_week'] ?? 0 }}</div>
                    <div class="stat-label">Quiz Attempts</div>
                </div>
            </div>
            <div style="position:relative;height:250px;">
                <canvas id="dailyActivityChart"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const moduleCtx = document.getElementById('modulePerformanceChart');
        if (moduleCtx) {
            const moduleData = @json($metrics['modules']['modules_list'] ?? []);
            new Chart(moduleCtx, {
                type: 'bar',
                data: {
                    labels: moduleData.map(m => m.module_number || m.name.substring(0, 15)),
                    datasets: [{
                        label: 'Pass Rate %',
                        data: moduleData.map(m => m.pass_rate),
                        backgroundColor: 'rgba(40, 167, 69, 0.7)',
                        borderColor: 'rgba(40, 167, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }, {
                        label: 'Fail Rate %',
                        data: moduleData.map(m => m.fail_rate),
                        backgroundColor: 'rgba(220, 53, 69, 0.7)',
                        borderColor: 'rgba(220, 53, 69, 1)',
                        borderWidth: 1,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'top' } },
                    scales: { y: { beginAtZero: true, max: 100, ticks: { callback: v => v + '%' } } }
                }
            });
        }

        const pieCtx = document.getElementById('overallResultsChart');
        if (pieCtx) {
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Passed', 'Failed'],
                    datasets: [{
                        data: [{{ $metrics['modules']['total_passed'] ?? 0 }}, {{ $metrics['modules']['total_failed'] ?? 0 }}],
                        backgroundColor: ['rgba(40, 167, 69, 0.8)', 'rgba(220, 53, 69, 0.8)'],
                        borderColor: ['rgba(40, 167, 69, 1)', 'rgba(220, 53, 69, 1)'],
                        borderWidth: 2
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });
        }

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
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    });
</script>
@endsection
