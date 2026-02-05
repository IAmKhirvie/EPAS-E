@extends('layouts.app')

@section('title', 'Student Grades')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="fas fa-graduation-cap me-2"></i>Student Grades
                    </h1>
                    <p class="text-muted mb-0">
                        @if($viewer->role === 'instructor' && $viewer->advisory_section)
                        Section: {{ $viewer->advisory_section }}
                        @else
                        All Students
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('analytics.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i>Analytics
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="{{ route('grades.export') }}">
                                    <i class="fas fa-file-csv me-2"></i>Export All Grades (CSV)
                                </a>
                            </li>
                            @if($viewer->role === 'instructor' && $viewer->advisory_section)
                            <li>
                                <a class="dropdown-item" href="{{ route('grades.export-class', $viewer->advisory_section) }}">
                                    <i class="fas fa-users me-2"></i>Export Class Grades ({{ $viewer->advisory_section }})
                                </a>
                            </li>
                            @elseif($viewer->role === 'admin')
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li class="dropdown-header">Export by Section</li>
                            @foreach($sections as $section)
                            <li>
                                <a class="dropdown-item" href="{{ route('grades.export-class', $section) }}">
                                    <i class="fas fa-user-graduate me-2"></i>{{ $section }}
                                </a>
                            </li>
                            @endforeach
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <form action="{{ route('grades.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Search Student</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="search" value="{{ $search ?? '' }}" placeholder="Name, ID, or Email">
                    </div>
                </div>
                @if($viewer->role === 'admin')
                <div class="col-md-3">
                    <label class="form-label">Filter by Section</label>
                    <select class="form-select" name="section">
                        <option value="">All Sections</option>
                        @foreach($sections as $section)
                        <option value="{{ $section }}" {{ ($sectionFilter ?? '') === $section ? 'selected' : '' }}>
                            {{ $section }}
                        </option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div class="col-md-3">
                    <label class="form-label">Filter by Module</label>
                    <select class="form-select" name="module">
                        <option value="">All Modules</option>
                        @foreach($modules as $module)
                        <option value="{{ $module->id }}" {{ ($moduleFilter ?? '') == $module->id ? 'selected' : '' }}>
                            {{ $module->module_name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Students ({{ $students->total() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Section</th>
                            <th class="text-center">Overall Average</th>
                            <th class="text-center">Grade</th>
                            <th class="text-center">Competency</th>
                            <th class="text-center">Self-Checks</th>
                            <th class="text-center">Homeworks</th>
                            <th class="text-center">Completed</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        @php
                        $gradeData = $student->grade_summary;
                        $percentage = $gradeData['overall_average'];
                        $gradeColor = $percentage >= 90 ? 'success' : ($percentage >= 85 ? 'info' : ($percentage >= 80 ? 'primary' : ($percentage >= 75 ? 'warning' : 'danger')));
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="{{ $student->profile_image_url }}" class="rounded-circle me-2" width="40" height="40" alt="{{ $student->full_name }}" onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div>
                                        <strong>{{ $student->full_name }}</strong>
                                        <br><small class="text-muted">{{ $student->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $student->student_id ?? 'N/A' }}</td>
                            <td>{{ $student->section ?? 'N/A' }}</td>
                            <td class="text-center">
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="progress me-2" style="width: 60px; height: 8px;">
                                        <div class="progress-bar bg-{{ $gradeColor }}"
                                            role="progressbar"
                                            style="width: {{ $percentage }}%">
                                        </div>
                                    </div>
                                    <strong>{{ $percentage }}%</strong>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-{{ $gradeColor }} fs-6">
                                    {{ $gradeData['grade_code'] }}
                                </span>
                                <br><small class="text-muted">{{ $gradeData['grade_descriptor'] }}</small>
                            </td>
                            <td class="text-center">
                                @if($gradeData['is_competent'])
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>Competent</span>
                                @else
                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Not Yet</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $gradeData['self_check_average'] }}%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark">{{ $gradeData['homework_average'] }}%</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $gradeData['completed_activities'] }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('grades.show', $student->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View Analysis
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5>No Students Found</h5>
                                <p class="text-muted">
                                    @if($search || $sectionFilter)
                                    No students match your search criteria.
                                    @else
                                    There are no students in the system yet.
                                    @endif
                                </p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($students->hasPages())
        <div class="card-footer">
            {{ $students->appends(request()->query())->links() }}
        </div>
        @endif
    </div>

    <!-- Legend - Philippine K-12 Grading Scale -->
    <div class="card mt-4 border-0 shadow-sm">
        <div class="card-header bg-light">
            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Philippine K-12 Grading Scale</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <span class="badge bg-success me-1">O</span> 90-100% Outstanding
                    <span class="text-success ms-1"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-info me-1">VS</span> 85-89% Very Satisfactory
                    <span class="text-success ms-1"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-primary me-1">S</span> 80-84% Satisfactory
                    <span class="text-success ms-1"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-warning me-1">FS</span> 75-79% Fairly Satisfactory
                    <span class="text-success ms-1"><i class="fas fa-check-circle"></i></span>
                </div>
                <div class="col-auto">
                    <span class="badge bg-danger me-1">DNM</span> Below 75% Did Not Meet
                    <span class="text-danger ms-1"><i class="fas fa-times-circle"></i></span>
                </div>
            </div>
            <div class="mt-2">
                <small class="text-muted">
                    <i class="fas fa-check-circle text-success me-1"></i> Competent (75% and above) |
                    <i class="fas fa-times-circle text-danger me-1"></i> Not Yet Competent (below 75%)
                </small>
            </div>
        </div>
    </div>
</div>
@endsection