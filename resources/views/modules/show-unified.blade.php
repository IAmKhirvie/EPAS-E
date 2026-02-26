@extends('layouts.app')

@section('title', $module->module_number . ' - ' . $module->module_name . ' - EPAS-E')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/pages/module-unified.css') }}">
@endpush

@section('content')
{{-- Module Header --}}
<div class="container-fluid py-3 bg-white border-bottom mb-4 module-header-section">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb" class="mb-1">
                <ol class="breadcrumb mb-0 small">
                    <li class="breadcrumb-item"><a href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('courses.show', $course) }}">{{ $course->course_name }}</a></li>
                    <li class="breadcrumb-item active">{{ $module->module_number }}</li>
                </ol>
            </nav>
            <h4 class="mb-1">{{ $module->module_number }}: {{ $module->module_name }}</h4>
            <p class="text-muted mb-0 small">{{ $module->qualification_title }}</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-info btn-sm" id="enterFocusMode">
                <i class="fas fa-expand me-1"></i> Focus Mode
            </button>
            <a href="{{ route('courses.modules.print', [$course, $module]) }}" class="btn btn-outline-secondary btn-sm" target="_blank" title="Print Preview">
                <i class="fas fa-print me-1"></i> Print
            </a>
            <a href="{{ route('courses.modules.download', [$course, $module]) }}" class="btn btn-outline-success btn-sm" title="Download for Offline">
                <i class="fas fa-download me-1"></i> Download
            </a>
            <button class="btn btn-outline-warning btn-sm" id="saveOfflineBtn" title="Save for Offline Viewing">
                <i class="fas fa-cloud-download-alt me-1"></i> <span id="saveOfflineText">Save Offline</span>
            </button>
            <a href="{{ route('courses.show', $course) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </a>
            @if(Auth::user()->role !== 'student')
                <a href="{{ route('courses.modules.edit', [$course, $module]) }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit me-1"></i> Edit
                </a>
            @endif
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="module-unified-layout">
        {{-- Main Content Area --}}
        <div class="main-content-section">
            {{-- Progress Card --}}
            <div class="card border-0 shadow-sm mb-4 progress-card-section">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="position-relative progress-circle-container">
                                <svg viewBox="0 0 100 100" class="progress-circle-svg">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e9ecef" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#0d6efd" stroke-width="8"
                                            stroke-dasharray="251.2" stroke-dashoffset="251.2" id="progressCircle"/>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle text-center">
                                    <strong id="progressText">0%</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col">
                            <div class="row text-center">
                                <div class="col-6 col-md-3 mb-2 mb-md-0">
                                    <div class="fw-bold text-primary">{{ $module->informationSheets->count() }}</div>
                                    <small class="text-muted">Info Sheets</small>
                                </div>
                                <div class="col-6 col-md-3 mb-2 mb-md-0">
                                    <div class="fw-bold text-success">{{ $module->module_number }}</div>
                                    <small class="text-muted">Module #</small>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-info">{{ $module->sector ?? 'Electronics' }}</div>
                                    <small class="text-muted">Sector</small>
                                </div>
                                <div class="col-6 col-md-3">
                                    <div class="fw-bold text-warning" id="completedCount">0</div>
                                    <small class="text-muted">Completed</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Content Area --}}
            <div id="contentArea">
                {{-- Overview (Default) --}}
                <div class="content-section" id="overviewSection">
                    @if($module->learning_outcomes)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-bullseye text-primary me-2"></i>Learning Outcomes</h6>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($module->learning_outcomes)) !!}
                        </div>
                    </div>
                    @endif

                    @if($module->introduction)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-book-open text-primary me-2"></i>Introduction</h6>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($module->introduction)) !!}
                        </div>
                    </div>
                    @endif

                    @if($module->how_to_use_cblm)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-info-circle text-primary me-2"></i>How to Use This CBLM</h6>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($module->how_to_use_cblm)) !!}
                        </div>
                    </div>
                    @endif

                    {{-- Module Details --}}
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white">
                            <h6 class="mb-0"><i class="fas fa-info text-primary me-2"></i>Module Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">Qualification Title</small>
                                    <span>{{ $module->qualification_title }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">Unit of Competency</small>
                                    <span>{{ $module->unit_of_competency }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">Module Number</small>
                                    <span>{{ $module->module_number }}</span>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <small class="text-muted d-block">Sector</small>
                                    <span>{{ $module->sector ?? 'Electronics' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Dynamic Content (loaded via AJAX) --}}
                <div id="dynamicContent" style="display: none;"></div>
            </div>

            {{-- Footer Navigation --}}
            @php
                $courseModules = $course->modules()->where('is_active', true)->orderBy('order')->get();
                $currentIndex = $courseModules->search(fn($m) => $m->id === $module->id);
                $prevModule = $currentIndex > 0 ? $courseModules[$currentIndex - 1] : null;
                $nextModule = $currentIndex !== false && $currentIndex < $courseModules->count() - 1 ? $courseModules[$currentIndex + 1] : null;
            @endphp
            <div class="d-flex justify-content-between mt-4 mb-3">
                @if($prevModule)
                <a href="{{ route('courses.modules.show', [$course, $prevModule, $prevModule->slug]) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> {{ $prevModule->module_name }}
                </a>
                @else
                <div></div>
                @endif
                @if($nextModule)
                <a href="{{ route('courses.modules.show', [$course, $nextModule, $nextModule->slug]) }}" class="btn btn-primary">
                    {{ $nextModule->module_name }} <i class="fas fa-arrow-right ms-1"></i>
                </a>
                @endif
            </div>
        </div>

        {{-- Right Sidebar TOC --}}
        <div class="sidebar-section">
            <div class="card border-0 shadow-sm toc-sidebar">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Table of Contents</h6>
                    <span class="badge bg-primary" id="progressBadge">0%</span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        {{-- Overview --}}
                        <a href="#" class="list-group-item list-group-item-action active toc-link" data-section="overview">
                            <i class="fas fa-home me-2"></i> Module Overview
                        </a>

                        {{-- Information Sheets --}}
                        @foreach($module->informationSheets as $sheet)
                        <div class="toc-group">
                            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center toc-link sheet-link"
                               data-sheet-id="{{ $sheet->id }}">
                                <span>
                                    <i class="fas fa-file-alt me-2"></i>
                                    Info Sheet {{ $sheet->sheet_number }}
                                </span>
                                <i class="fas fa-chevron-down small toggle-icon"></i>
                            </a>
                            <div class="toc-subitems" style="display: none;">
                                <a href="#" class="list-group-item list-group-item-action small ps-5 toc-sublink"
                                   data-sheet-id="{{ $sheet->id }}" data-action="content">
                                    <i class="fas fa-align-left me-2"></i> {{ Str::limit($sheet->title, 25) }}
                                </a>
                                @if($sheet->topics && $sheet->topics->count() > 0)
                                    @foreach($sheet->topics as $topic)
                                    <a href="#" class="list-group-item list-group-item-action small ps-5 toc-sublink"
                                       data-topic-id="{{ $topic->id }}" data-sheet-id="{{ $sheet->id }}">
                                        <i class="fas fa-circle fa-xs me-2"></i> {{ Str::limit($topic->title, 25) }}
                                    </a>
                                    @endforeach
                                @endif
                                @if($sheet->selfChecks && $sheet->selfChecks->count() > 0)
                                <a href="#" class="list-group-item list-group-item-action small ps-5 toc-sublink toc-assessment-link"
                                   data-sheet-id="{{ $sheet->id }}" data-assessment="self-check">
                                    <i class="fas fa-question-circle me-2 text-warning"></i> Self-Check
                                </a>
                                @endif
                                @if($sheet->taskSheets && $sheet->taskSheets->count() > 0)
                                <a href="#" class="list-group-item list-group-item-action small ps-5 toc-sublink toc-assessment-link"
                                   data-sheet-id="{{ $sheet->id }}" data-assessment="task-sheet">
                                    <i class="fas fa-clipboard-list me-2 text-info"></i> Task Sheet
                                </a>
                                @endif
                                @if($sheet->jobSheets && $sheet->jobSheets->count() > 0)
                                <a href="#" class="list-group-item list-group-item-action small ps-5 toc-sublink toc-assessment-link"
                                   data-sheet-id="{{ $sheet->id }}" data-assessment="job-sheet">
                                    <i class="fas fa-hard-hat me-2 text-success"></i> Job Sheet
                                </a>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Mobile TOC Toggle --}}
<button class="btn btn-primary toc-mobile-toggle d-lg-none" id="tocMobileToggle">
    <i class="fas fa-list"></i>
</button>

{{-- Focus Mode Floating Button --}}
<button class="btn btn-primary focus-mode-btn" id="focusModeFloatingBtn" title="Enter Focus Mode">
    <i class="fas fa-expand"></i>
</button>

{{-- Focus Mode Container --}}
<div class="focus-mode-container" id="focusModeContainer">
    <div class="focus-mode-header">
        <div class="d-flex align-items-center">
            <h5><i class="fas fa-book-reader me-2"></i><span id="focusModeTitle">{{ $module->module_name }}</span></h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <span class="badge bg-light text-dark" id="focusProgressBadge">1 / 1</span>
            <button class="btn btn-light btn-sm" id="exitFocusMode">
                <i class="fas fa-times me-1"></i> Exit Focus Mode
            </button>
        </div>
    </div>
    <div class="focus-mode-body">
        <div class="focus-image-panel" id="focusImagePanel">
            <div class="no-image" id="focusNoImage">
                <i class="fas fa-image"></i>
                <p>No images for this section</p>
            </div>
            <img src="" alt="" id="focusImage" style="display: none;">
            <p class="image-caption" id="focusImageCaption"></p>
            <div class="image-nav" id="imageNav" style="display: none;">
                <button id="prevImage"><i class="fas fa-chevron-left"></i></button>
                <button id="nextImage"><i class="fas fa-chevron-right"></i></button>
            </div>
            <p class="image-counter" id="imageCounter"></p>
        </div>
        <div class="focus-content-panel" id="focusContentPanel">
            <h2 id="focusContentTitle">Content Title</h2>
            <div class="content-body" id="focusContentBody">
                <p>Loading content...</p>
            </div>
        </div>
    </div>
    <div class="focus-nav">
        <button class="btn-prev" id="focusPrevBtn"><i class="fas fa-arrow-left me-2"></i>Previous</button>
        <button class="btn-next" id="focusNextBtn">Next<i class="fas fa-arrow-right ms-2"></i></button>
    </div>
</div>

{{-- Data for JS --}}
<div id="moduleData"
     data-module-id="{{ $module->id }}"
     data-course-id="{{ $course->id }}"
     data-csrf="{{ csrf_token() }}"
     data-base-url="{{ url('/courses/' . $course->id . '/module-' . $module->id) }}"
     style="display: none;"></div>

{{-- Focus Mode Content Data --}}
<script type="application/json" id="focusModeData">
@php
$focusContent = [];

$focusContent[] = [
    'type' => 'overview',
    'title' => 'Module Overview: ' . $module->module_name,
    'content' => $module->introduction ?? $module->learning_outcomes ?? 'Welcome to ' . $module->module_name,
    'images' => $module->images ?? []
];

foreach($module->informationSheets as $sheet) {
    $focusContent[] = [
        'type' => 'sheet',
        'id' => $sheet->id,
        'title' => 'Info Sheet ' . $sheet->sheet_number . ': ' . $sheet->title,
        'content' => $sheet->content ?? '',
        'images' => $sheet->parts ? collect($sheet->parts)->pluck('image')->filter()->values()->toArray() : []
    ];

    if($sheet->topics) {
        foreach($sheet->topics as $topic) {
            $topicImages = [];
            if($topic->parts) {
                foreach($topic->parts as $part) {
                    if(!empty($part['image'])) {
                        $topicImages[] = [
                            'url' => $part['image'],
                            'caption' => $part['title'] ?? ''
                        ];
                    }
                }
            }

            $focusContent[] = [
                'type' => 'topic',
                'id' => $topic->id,
                'sheetId' => $sheet->id,
                'title' => $topic->title,
                'content' => $topic->content ?? '',
                'parts' => $topic->parts ?? [],
                'images' => $topicImages
            ];
        }
    }
}
@endphp
@json($focusContent)
</script>
@endsection

@push('scripts')
<script src="{{ asset('js/pages/module-unified.js') }}"></script>
@endpush
