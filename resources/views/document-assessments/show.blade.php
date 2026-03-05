@extends('layouts.app')

@section('title', $assessment->title)

@push('styles')
<style>
    /* Document viewer */
    .doc-viewer {
        position: relative;
        background: #f0f0f0;
        border-radius: 8px;
        padding: 1.5rem 1.5rem 0;
    }
    .doc-viewer__page {
        background: #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        border-radius: 4px;
        padding: 2.5rem 2rem;
        min-height: 500px;
        max-height: 600px;
        overflow: hidden;
        position: relative;
        line-height: 1.8;
        font-size: 0.95rem;
    }
    .doc-viewer__page--scrollable {
        overflow-y: auto;
        scroll-behavior: smooth;
    }
    .doc-viewer__page h1, .doc-viewer__page h2, .doc-viewer__page h3 {
        margin-top: 0.8em;
        margin-bottom: 0.4em;
        color: #1a1a1a;
    }
    .doc-viewer__page table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
    }
    .doc-viewer__page table th,
    .doc-viewer__page table td {
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    .doc-viewer__page table th {
        background: #f8f9fa;
        font-weight: 600;
    }
    .doc-viewer__page ul, .doc-viewer__page ol {
        padding-left: 1.5rem;
    }
    .doc-viewer__page p {
        margin-bottom: 0.75rem;
    }
    .doc-viewer__page img {
        max-width: 100% !important;
        height: auto !important;
    }
    .doc-viewer__page * {
        max-width: 100% !important;
        box-sizing: border-box;
    }
    .doc-viewer__page table {
        table-layout: fixed;
        word-wrap: break-word;
    }
    .doc-viewer__nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 0.75rem 0;
        user-select: none;
    }
    .doc-viewer__nav-btn {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #495057;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.15s;
        font-size: 0.85rem;
    }
    .doc-viewer__nav-btn:hover:not(:disabled) {
        background: #e9ecef;
        border-color: #adb5bd;
    }
    .doc-viewer__nav-btn:disabled {
        opacity: 0.35;
        cursor: default;
    }
    .doc-viewer__page-info {
        font-size: 0.85rem;
        color: #6c757d;
        min-width: 90px;
        text-align: center;
    }
    .doc-viewer__fade-bottom {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 40px;
        background: linear-gradient(transparent, #fff);
        pointer-events: none;
        border-radius: 0 0 4px 4px;
    }
    .doc-viewer__logical-page {
        padding: 0.5rem 0;
    }
    .doc-viewer__logical-page table {
        width: 100%;
        border-collapse: collapse;
    }
    .doc-viewer__logical-page table th,
    .doc-viewer__logical-page table td {
        border: 1px solid #dee2e6;
        padding: 0.4rem 0.6rem;
        font-size: 0.85rem;
    }
    .doc-viewer__logical-page table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    /* Side-by-side layout */
    .da-header {
        background: var(--cb-surface, #fff);
        border-radius: var(--cb-radius-lg, 12px);
        box-shadow: var(--cb-shadow-card, 0 1px 3px rgba(0,0,0,0.08));
        padding: 1.25rem 1.5rem;
        margin-bottom: 1rem;
    }
    .da-content {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 1rem;
        align-items: start;
    }
    .da-viewer {
        min-width: 0;
    }
    .da-panel {
        position: sticky;
        top: 1rem;
        background: var(--cb-surface, #fff);
        border-radius: var(--cb-radius-lg, 12px);
        box-shadow: var(--cb-shadow-card, 0 1px 3px rgba(0,0,0,0.08));
        padding: 1.25rem;
        max-height: calc(100vh - 2rem);
        overflow-y: auto;
    }
    .da-panel__title {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--cb-text-hint, #6c757d);
        padding-bottom: 0.5rem;
        margin-bottom: 0.75rem;
        border-bottom: 1px solid var(--cb-border, #e9ecef);
    }
    .da-panel__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }
    .da-panel__instructions {
        background: #e3f2fd;
        border-left: 3px solid #2196f3;
        border-radius: 4px;
        padding: 0.75rem;
        font-size: 0.85rem;
        color: #1565c0;
        margin-bottom: 1rem;
    }
    .da-panel__divider {
        border: 0;
        border-top: 1px solid var(--cb-border, #e9ecef);
        margin: 1rem 0;
    }
    .da-grading-section {
        background: var(--cb-surface, #fff);
        border-radius: var(--cb-radius-lg, 12px);
        box-shadow: var(--cb-shadow-card, 0 1px 3px rgba(0,0,0,0.08));
        padding: 1.25rem 1.5rem;
        margin-top: 1rem;
    }

    /* Assessment Timer */
    .assessment-timer {
        position: fixed;
        top: 1rem;
        right: 1rem;
        z-index: 1050;
        background: #1a1a2e;
        color: #fff;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
        font-weight: 700;
        font-size: 1.1rem;
        font-family: 'Courier New', monospace;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.3s;
    }
    .assessment-timer.warning { background: #f59e0b; color: #1a1a2e; }
    .assessment-timer.danger { background: #dc3545; animation: timer-pulse 1s infinite; }
    @keyframes timer-pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

    /* Dark mode */
    .dark-mode .da-header,
    .dark-mode .da-panel,
    .dark-mode .da-grading-section {
        background: var(--card-bg);
        color: var(--card-text);
    }
    .dark-mode .da-panel__instructions {
        background: rgba(33, 150, 243, 0.1);
        color: #90caf9;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .da-content {
            grid-template-columns: 1fr;
        }
        .da-panel {
            position: static;
            max-height: none;
        }
    }
</style>
@endpush

@section('content')
<div class="content-area">
    <nav aria-label="breadcrumb" class="mb-2">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('content.management') }}">Content</a></li>
            <li class="breadcrumb-item active">{{ $assessment->title }}</li>
        </ol>
    </nav>

    {{-- ═══════ Header ═══════ --}}
    <div class="da-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">
                    <i class="fas fa-file-alt me-2"></i>{{ $assessment->title }}
                    @if($assessment->file_path)
                    <a href="{{ route('document-assessments.download', $assessment) }}" class="badge bg-secondary text-decoration-none ms-2" style="font-size: 0.6em; vertical-align: middle;">
                        <i class="fas fa-download me-1"></i>{{ $assessment->original_filename }}
                    </a>
                    @endif
                </h4>
                <p class="mb-0 text-muted">{{ $assessment->assessment_number }}</p>
            </div>
            @if(auth()->id() === $assessment->created_by || auth()->user()->role === \App\Constants\Roles::ADMIN)
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="{{ route('document-assessments.edit', [$assessment->informationSheet, $assessment]) }}">
                            <i class="fas fa-edit me-2"></i>Edit
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('document-assessments.destroy', [$assessment->informationSheet, $assessment]) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this document assessment?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fas fa-trash me-2"></i>Delete
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @endif
        </div>

        @if($assessment->description)
        <p class="mt-2 mb-0" style="color: var(--cb-text-label, #495057);">{{ $assessment->description }}</p>
        @endif
    </div>

    {{-- ═══════ Side-by-Side: Document + Answer Panel ═══════ --}}
    <div class="da-content">
        {{-- Left: Document Viewer --}}
        <div class="da-viewer">
            @if($assessment->document_content)
            <div class="doc-viewer" id="docViewer">
                <div class="doc-viewer__page" id="docPage">
                    <div id="docContent">
                        {!! $assessment->document_content !!}
                    </div>
                    <div class="doc-viewer__fade-bottom" id="docFade"></div>
                </div>
                <div class="doc-viewer__nav" id="docNav">
                    <button class="doc-viewer__nav-btn" id="docPrev" title="Previous page">
                        <i class="fas fa-chevron-left"></i>
                    </button>
                    <span class="doc-viewer__page-info" id="docPageInfo">Page 1 of 1</span>
                    <button class="doc-viewer__nav-btn" id="docNext" title="Next page">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
            @elseif($assessment->is_pdf && !$assessment->document_content)
            <div class="doc-viewer" style="padding-bottom: 1.5rem;">
                <div class="doc-viewer__page d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 300px;">
                    <i class="fas fa-file-pdf" style="font-size: 3rem; color: #dc3545; margin-bottom: 1rem;"></i>
                    <h5>PDF Document</h5>
                    <p class="text-muted mb-3">This document could not be previewed. Download to view.</p>
                    <a href="{{ route('document-assessments.download', $assessment) }}" class="btn btn-primary">
                        <i class="fas fa-download me-1"></i>Download PDF
                    </a>
                </div>
            </div>
            @else
            <div class="doc-viewer" style="padding-bottom: 1.5rem;">
                <div class="doc-viewer__page d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 200px;">
                    <i class="fas fa-file-circle-exclamation" style="font-size: 2.5rem; color: #6c757d; margin-bottom: 1rem;"></i>
                    <p class="text-muted mb-0">No document content available.<br>Download the original file using the button in the header.</p>
                </div>
            </div>
            @endif
        </div>

        {{-- Right: Answer Panel --}}
        <div class="da-panel">
            <div class="da-panel__title">
                <i class="fas fa-comment-dots me-1"></i> Assessment Panel
            </div>

            {{-- Compact metadata badges --}}
            <div class="da-panel__meta">
                <span class="badge bg-primary"><i class="fas fa-star me-1"></i>{{ $assessment->max_points }} pts</span>
                <span class="badge bg-secondary"><i class="fas fa-file me-1"></i>{{ strtoupper($assessment->file_type ?? 'N/A') }}</span>
                @if($assessment->time_limit)
                <span class="badge bg-info text-dark"><i class="fas fa-stopwatch me-1"></i>{{ $assessment->time_limit }} min</span>
                @endif
                @if($assessment->due_date)
                <span class="badge bg-{{ now()->gt($assessment->due_date) ? 'danger' : 'warning text-dark' }}">
                    <i class="fas fa-calendar me-1"></i>{{ $assessment->due_date->format('M d, Y h:i A') }}
                </span>
                @endif
            </div>

            {{-- Instructions --}}
            @if($assessment->instructions)
            <div class="da-panel__instructions">
                <i class="fas fa-info-circle me-1"></i>
                {{ $assessment->instructions }}
            </div>
            @endif

            {{-- ═══════ STUDENT Section ═══════ --}}
            @if(auth()->user()->role === \App\Constants\Roles::STUDENT)
                @php $existingSubmission = $assessment->submissions->where('user_id', auth()->id())->first(); @endphp

                @if($existingSubmission)
                {{-- Already submitted --}}
                <hr class="da-panel__divider">
                <h6 class="mb-2"><i class="fas fa-check-circle text-success me-1"></i>Your Submission</h6>
                <div class="p-2 rounded mb-2" style="background: var(--cb-surface-alt, #f8f9fa); max-height: 200px; overflow-y: auto; font-size: 0.9rem;">
                    {!! nl2br(e($existingSubmission->answer_text)) !!}
                </div>
                <small class="text-muted d-block mb-2">
                    <i class="fas fa-clock me-1"></i>{{ $existingSubmission->submitted_at->format('M d, Y h:i A') }}
                    @if($existingSubmission->is_late)
                    <span class="badge bg-danger ms-1">Late</span>
                    @endif
                </small>

                @if($existingSubmission->score !== null)
                <div class="p-2 rounded" style="background: {{ $existingSubmission->percentage >= 70 ? '#f0fff4' : '#fffbeb' }}; border: 1px solid {{ $existingSubmission->percentage >= 70 ? '#c6f6d5' : '#fef3c7' }};">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size: 1.25rem; font-weight: 700; color: {{ $existingSubmission->percentage >= 70 ? '#198754' : '#f59e0b' }};">
                            {{ $existingSubmission->score }}/{{ $assessment->max_points }}
                        </span>
                        <span class="badge bg-{{ $existingSubmission->percentage >= 70 ? 'success' : 'warning' }}">
                            {{ number_format($existingSubmission->percentage, 1) }}%
                        </span>
                    </div>
                    @if($existingSubmission->feedback)
                    <div class="mt-2" style="font-size: 0.85rem;">
                        <strong>Feedback:</strong><br>
                        {!! nl2br(e($existingSubmission->feedback)) !!}
                    </div>
                    @endif
                </div>
                @else
                <span class="badge bg-secondary">Awaiting grading</span>
                @endif

                @elseif($assessment->due_date && now()->gt($assessment->due_date))
                {{-- Deadline passed --}}
                <hr class="da-panel__divider">
                <div class="alert alert-danger mb-0" style="font-size: 0.85rem;">
                    <i class="fas fa-clock me-2"></i>
                    <strong>Deadline passed.</strong> Submissions are no longer accepted.
                </div>

                @else
                {{-- Answer form --}}
                @if($assessment->time_limit)
                <div class="assessment-timer" id="assessmentTimer">
                    <i class="fas fa-stopwatch me-1"></i>
                    <span id="timerDisplay">{{ $assessment->time_limit }}:00</span>
                </div>
                @endif

                <hr class="da-panel__divider">
                <form action="{{ route('document-assessments.submit', $assessment) }}" method="POST" id="docAssessmentForm">
                    @csrf
                    <h6 class="mb-2"><i class="fas fa-pen me-1"></i>Your Answer</h6>
                    <textarea class="form-control @error('answer_text') is-invalid @enderror" name="answer_text" rows="10" required
                              placeholder="Read the document and write your answer here..." id="answerText" style="font-size: 0.9rem;">{{ old('answer_text') }}</textarea>
                    @error('answer_text')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted d-block mt-1 mb-2"><i class="fas fa-info-circle me-1"></i>One submission only. Min 10 characters.</small>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-paper-plane me-1"></i>Submit Answer
                    </button>
                </form>
                @endif

            @elseif(auth()->id() === $assessment->created_by || auth()->user()->role === \App\Constants\Roles::ADMIN)
            {{-- Instructor: Quick stats in panel --}}
            <hr class="da-panel__divider">
            <h6 class="mb-2"><i class="fas fa-chart-bar me-1"></i>Quick Stats</h6>
            <div class="d-flex gap-2 flex-wrap">
                <span class="badge bg-primary">{{ $assessment->submissions->count() }} submissions</span>
                <span class="badge bg-success">{{ $assessment->submissions->whereNotNull('score')->count() }} graded</span>
                <span class="badge bg-secondary">{{ $assessment->submissions->whereNull('score')->count() }} pending</span>
            </div>
            <a href="{{ route('information-sheets.show', ['module' => $assessment->informationSheet->module_id, 'informationSheet' => $assessment->informationSheet->id]) }}"
               class="btn btn-outline-secondary w-100 btn-sm mt-3">
                <i class="fas fa-arrow-left me-1"></i>Back to Info Sheet
            </a>
            @endif
        </div>
    </div>

    {{-- ═══════ INSTRUCTOR: Submissions & Grading (Full Width) ═══════ --}}
    @if(auth()->id() === $assessment->created_by || auth()->user()->role === \App\Constants\Roles::ADMIN)
    <div class="da-grading-section">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>Student Submissions
                <span class="badge bg-primary ms-1">{{ $assessment->submissions->count() }}</span>
            </h5>
        </div>

        @forelse($assessment->submissions as $submission)
        <div class="cb-item-card mb-3">
            <div class="cb-item-card__header">
                <div class="left-section">
                    <span class="cb-item-card__number"><i class="fas fa-user"></i></span>
                    <span class="cb-item-card__title">{{ $submission->user->full_name }}</span>
                    @if($submission->is_late)
                    <span class="badge bg-danger" style="font-size: 0.65rem;">Late</span>
                    @endif
                </div>
                <div class="right-section">
                    <small class="text-muted me-2">{{ $submission->submitted_at->format('M d, Y h:i A') }}</small>
                    @if($submission->score !== null)
                    <span class="badge bg-{{ $submission->percentage >= 70 ? 'success' : 'warning' }}">
                        {{ $submission->score }}/{{ $assessment->max_points }}
                    </span>
                    @else
                    <span class="badge bg-secondary">Ungraded</span>
                    @endif
                </div>
            </div>
            <div class="cb-item-card__body">
                {{-- Student's answer --}}
                <div class="p-3 rounded mb-3" style="background: var(--cb-surface-alt, #f8f9fa); max-height: 200px; overflow-y: auto;">
                    {!! nl2br(e($submission->answer_text)) !!}
                </div>

                {{-- Grading form --}}
                <form action="{{ route('document-assessment-submissions.grade', $submission) }}" method="POST">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-auto">
                            <label class="form-label mb-0 small fw-bold">Score (0-{{ $assessment->max_points }})</label>
                            <input type="number" class="form-control form-control-sm" name="score"
                                   min="0" max="{{ $assessment->max_points }}"
                                   value="{{ $submission->score }}" required style="width: 100px;">
                        </div>
                        <div class="col">
                            <label class="form-label mb-0 small fw-bold">Feedback <span class="text-muted fw-normal">(optional)</span></label>
                            <input type="text" class="form-control form-control-sm" name="feedback"
                                   value="{{ $submission->feedback }}" placeholder="Write feedback...">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-check me-1"></i>{{ $submission->score !== null ? 'Update' : 'Grade' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @empty
        <div class="text-center py-4" style="color: var(--cb-text-hint, #6c757d);">
            <i class="fas fa-inbox d-block mb-2" style="font-size: 2rem;"></i>
            <p><strong>No submissions yet</strong><br>Students will appear here after they submit their answers.</p>
        </div>
        @endforelse
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var page = document.getElementById('docPage');
    if (!page) return;

    var content = document.getElementById('docContent');
    var fade = document.getElementById('docFade');
    var nav = document.getElementById('docNav');
    var prevBtn = document.getElementById('docPrev');
    var nextBtn = document.getElementById('docNext');
    var pageInfo = document.getElementById('docPageInfo');
    var currentPage = 1;

    // Check for logical pages (PPTX slides, PDF pages, XLSX sheets)
    var logicalPages = content.querySelectorAll('.doc-viewer__logical-page');
    var useLogicalPages = logicalPages.length > 1;

    if (useLogicalPages) {
        var totalPages = logicalPages.length;
        fade.style.display = 'none';
        page.style.overflowY = 'auto';
        page.style.overflowX = 'hidden';
        page.style.scrollbarWidth = 'thin';

        function showLogicalPage() {
            logicalPages.forEach(function(lp, i) {
                lp.style.display = (i === currentPage - 1) ? 'block' : 'none';
            });
            if (totalPages <= 1) { nav.style.display = 'none'; return; }
            nav.style.display = 'flex';
            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
            page.scrollTop = 0;
        }
        showLogicalPage();
        prevBtn.addEventListener('click', function() { if (currentPage > 1) { currentPage--; showLogicalPage(); } });
        nextBtn.addEventListener('click', function() { if (currentPage < totalPages) { currentPage++; showLogicalPage(); } });
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === 'ArrowLeft' && currentPage > 1) { currentPage--; showLogicalPage(); }
            if (e.key === 'ArrowRight' && currentPage < totalPages) { currentPage++; showLogicalPage(); }
        });
    } else {
        // Fallback: scroll-based pagination for DOCX and other single-flow content
        var PAGE_HEIGHT, totalHeight, totalPages;
        function update() {
            totalHeight = content.scrollHeight;
            PAGE_HEIGHT = page.clientHeight;
            totalPages = Math.max(1, Math.ceil(totalHeight / PAGE_HEIGHT));
            if (totalPages <= 1) { nav.style.display = 'none'; fade.style.display = 'none'; return; }
            nav.style.display = 'flex';
            pageInfo.textContent = 'Page ' + currentPage + ' of ' + totalPages;
            prevBtn.disabled = currentPage <= 1;
            nextBtn.disabled = currentPage >= totalPages;
            page.scrollTop = (currentPage - 1) * PAGE_HEIGHT;
            fade.style.display = currentPage < totalPages ? 'block' : 'none';
        }
        page.classList.add('doc-viewer__page--scrollable');
        page.style.scrollbarWidth = 'none';
        var s = document.createElement('style');
        s.textContent = '#docPage::-webkit-scrollbar{display:none}';
        document.head.appendChild(s);
        prevBtn.addEventListener('click', function() { if (currentPage > 1) { currentPage--; update(); } });
        nextBtn.addEventListener('click', function() { if (currentPage < totalPages) { currentPage++; update(); } });
        document.addEventListener('keydown', function(e) {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
            if (e.key === 'ArrowLeft' && currentPage > 1) { currentPage--; update(); }
            if (e.key === 'ArrowRight' && currentPage < totalPages) { currentPage++; update(); }
        });
        update();
        window.addEventListener('resize', update);
    }
});
</script>

{{-- Countdown Timer --}}
@if($assessment->time_limit && auth()->user()->role === \App\Constants\Roles::STUDENT && !($assessment->submissions->where('user_id', auth()->id())->first()) && !($assessment->due_date && now()->gt($assessment->due_date)))
<script>
document.addEventListener('DOMContentLoaded', function() {
    var timerEl = document.getElementById('assessmentTimer');
    var displayEl = document.getElementById('timerDisplay');
    var form = document.getElementById('docAssessmentForm');
    if (!timerEl || !displayEl || !form) return;

    var totalSeconds = {{ $assessment->time_limit }} * 60;
    var storageKey = 'da_timer_{{ $assessment->id }}_' + '{{ auth()->id() }}';

    var saved = localStorage.getItem(storageKey);
    if (saved) {
        var elapsed = Math.floor((Date.now() - parseInt(saved)) / 1000);
        totalSeconds = Math.max(0, totalSeconds - elapsed);
    } else {
        localStorage.setItem(storageKey, Date.now().toString());
    }

    var submitted = false;

    function tick() {
        if (totalSeconds <= 0) {
            displayEl.textContent = '0:00';
            timerEl.className = 'assessment-timer danger';
            if (!submitted) {
                submitted = true;
                localStorage.removeItem(storageKey);
                var textarea = document.getElementById('answerText');
                if (textarea && textarea.value.trim().length >= 10) {
                    alert('Time is up! Your answer will be submitted automatically.');
                    form.submit();
                } else {
                    alert('Time is up! You did not provide a sufficient answer.');
                }
            }
            return;
        }

        var m = Math.floor(totalSeconds / 60);
        var s = totalSeconds % 60;
        displayEl.textContent = m + ':' + (s < 10 ? '0' : '') + s;

        var pct = totalSeconds / ({{ $assessment->time_limit }} * 60);
        if (pct <= 0.1) {
            timerEl.className = 'assessment-timer danger';
        } else if (pct <= 0.25) {
            timerEl.className = 'assessment-timer warning';
        }

        totalSeconds--;
        setTimeout(tick, 1000);
    }

    tick();

    form.addEventListener('submit', function() {
        submitted = true;
        localStorage.removeItem(storageKey);
    });
});
</script>
@endif
@endpush
